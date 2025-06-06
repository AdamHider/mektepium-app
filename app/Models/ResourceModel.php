<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;
use CodeIgniter\Events\Events;

class ResourceModel extends Model
{
    use ResourceTrait;
    protected $table      = 'resources';
    protected $useAutoIncrement = true;

    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'image',
        'code', 
        'is_restorable'
    ];
    protected $useTimestamps = false;

    private $settings;
    public function getList ($data) 
    {
        $SettingsModel = model('SettingsModel');
        $this->settings = $SettingsModel->getList(['user_id' => $data['user_id']]);
        $this->recalculateRestoration($data['user_id']);

        $DescriptionModel = model('DescriptionModel');
        $resources = $this->select('resources.*, COALESCE(resources_usermap.quantity, 0) quantity, resources_usermap.consumed_at')
        ->join('resources_usermap', 'resources_usermap.item_id = resources.id AND resources_usermap.user_id = '.$data['user_id'], 'left')->get()->getResultArray();
        
        $result = [];
        
        foreach($resources as &$resource){
            $resource = array_merge($resource, $DescriptionModel->getItem('resource', $resource['id']));
            $result[$resource['code']] = [
                'quantity'      => $resource['quantity'],
                'image'         => base_url('image/index.php'.$resource['image']),
                'title'         => $resource['title'],
                'description'   => $resource['description'],
                'color'         => $resource['color'],
                'is_restorable' => (bool) $resource['is_restorable'],
                'restoration'   =>  $this->getItemRestoration($resource)
            ];
        }
        return $result;
    }
    
    public function getItem ($code, $user_id, $item_id) 
    {
        $SettingsModel = model('SettingsModel');
        $this->settings = $SettingsModel->getList(['user_id' => $user_id]);
        $this->recalculateRestoration($user_id);
        return $this->where('user_id', $user_id)->where('item_id', $item_id)->where('code', $code)->get()->getResultArray();
    }

    public function getItemRestoration ($resource)
    {   
        if(!(bool)$resource['is_restorable'] || !$resource['consumed_at']) return null;

        $restorationTime = $this->settings[$resource['code'].'RestorationTime']['value'];
        $maxValue = $this->settings[$resource['code'].'MaxValue']['value'];

        return [
            'nextRestoration' => $this->getNextRestoration($restorationTime, Time::parse($resource['consumed_at'], Time::now()->getTimezone())),
            'restorationTime' => (int) $restorationTime,
            'maxValue' => (int) $maxValue
        ];
    }
    
    private function recalculateRestoration ($user_id)
    {
        $resources = $this->join('resources_usermap', 'resources_usermap.item_id = resources.id AND resources_usermap.user_id = '.$user_id)
        ->where('is_restorable', 1)->get()->getResultArray();

        $ResourceUsermapModel = model('ResourceUsermapModel');
        foreach($resources as $resource){
            if(!$resource['consumed_at']) $resource['consumed_at'] = Time::now()->toDateTimeString();

            $restorationTime = $this->settings[$resource['code'].'RestorationTime']['value'];
            $maxValue = $this->settings[$resource['code'].'MaxValue']['value'];
    
            $consumptionTime = Time::parse($resource['consumed_at'], Time::now()->getTimezone());
            $timeDifference = $consumptionTime->difference(Time::now())->getSeconds();
            
            if($timeDifference < 0) continue;
            
            $restoratedValue = floor($timeDifference / $restorationTime);
            $newValue = $resource['quantity'] + $restoratedValue;
            if($newValue >= $maxValue){
                $ResourceUsermapModel->set(['quantity' => $maxValue, 'consumed_at' => null])
                ->where('user_id', session()->get('user_id'))->where('item_id', $resource['id'])->update();
            } else {
                $consumptionTime = $consumptionTime->addSeconds($restoratedValue * $restorationTime);
                $ResourceUsermapModel->set(['quantity' => $newValue, 'consumed_at' => $consumptionTime])
                ->where('user_id', session()->get('user_id'))->where('item_id', $resource['id'])->update();
            }
        }
    }

    public function getNextRestoration ($restorationTime, $consumptionTime)
    {
        if($consumptionTime){
            $consumptionTime = Time::parse($consumptionTime, Time::now()->getTimezone());
            $nextRestoration = $consumptionTime->addSeconds($restorationTime);
            return Time::now()->difference($nextRestoration)->getSeconds();
        } 
        return 0;
    }

    public function proccessItemCost ($cost_config)
    {   
        if(empty($cost_config)) return [];
        $DescriptionModel = model('DescriptionModel');
        $resources = $this->join('resources_usermap', 'resources_usermap.item_id = resources.id AND resources_usermap.user_id = '.session()->get('user_id'), 'left')
        ->whereIn('code', array_keys($cost_config))->get()->getResultArray();
        foreach($resources as &$resource){
            $resource = array_merge($resource, $DescriptionModel->getItem('resource', $resource['id']));
            $resource['quantity'] = (int) $resource['quantity'];
            /*if($mode == 'deep'){
                $resource['quantity_cost'] = $this->recalculateValue($resource['code'], (int) $cost_config[$resource['code']]);
            }*/
            $resource['quantity_cost'] = (int) $cost_config[$resource['code']];
                        
            $resource['image'] = base_url('image/index.php'.$resource['image']);
        }
        return $resources;
    }
    public function proccessItemGroupReward ($reward_config)
    {
        if(!$reward_config) return [];
        $result = [];
        foreach ($reward_config as $starQuantity => $resourceGroup){
            $resources = $this->proccessItemReward($resourceGroup);
            $result[$starQuantity] = $resources;
        }
        return $result;
    }
    public function proccessItemReward ($resourceGroup)
    {
        $DescriptionModel = model('DescriptionModel');
        $resources = $this->whereIn('code', array_keys($resourceGroup))->get()->getResultArray();
        foreach($resources as &$resource){
            $resource = array_merge($resource, $DescriptionModel->getItem('resource', $resource['id']));
            //$resource['quantity'] = $this->recalculateValue($resource['code'], (int) $resourceGroup[$resource['code']]);
            $resource['quantity'] = (int) $resourceGroup[$resource['code']];
            $resource['image'] = base_url('image/index.php'.$resource['image']);
        }
        return $resources;
    }

    public function enrollUserList ($user_id, $resources, $mode = 'add')
    {
        foreach($resources as $code => &$quantity){
            if($mode == 'substract') $quantity = $quantity * -1;
        }
        if(!$this->checkListQuantity($user_id, $resources)) return false;
        $ok = $this->saveUserList($user_id, $resources);
        return $ok;
    }

    public function checkListQuantity($user_id, $resources)
    {
        if(empty($resources)) return true;
        $list = $this->join('resources_usermap', 'resources_usermap.item_id = resources.id AND resources_usermap.user_id = '.$user_id)
        ->whereIn('code', array_keys($resources))->get()->getResultArray();
        foreach($list as &$item){
            if(($item['quantity'] + $resources[$item['code']]) < 0) return false;
        }
        return true;
    }

    public function createUserItem($data)
    {
        $ResourceUsermapModel = model('ResourceUsermapModel');
        $resource = $this->where('code', $data['code'])->get()->getRowArray();
        if(empty($resource)){
            return false;
        }
        $data = [
            'item_id' => $resource['id'],
            'user_id' => $data['user_id'],
            'quantity' => $data['quantity']
        ];
        $ResourceUsermapModel->insert($data, true);
    }

    public function saveUserList ($user_id, $resources)
    {
        foreach($resources as $code => $quantity){
            $resource = $this->join('resources_usermap', 'resources_usermap.item_id = resources.id AND resources_usermap.user_id = '.$user_id)->where('code', $code)->get()->getRowArray();
            if(isset($resource['id'])){
                if(!$this->updateUserItem([
                    'code' => $code, 
                    'user_id' => $user_id, 
                    'quantity' => $quantity
                ])){ 
                    return false;
                };
            } else {
                $this->createUserItem([
                    'code' => $code, 
                    'user_id' => $user_id, 
                    'quantity' => $quantity
                ]);
            }
        }
        return true;        
    }
    
    public function updateUserItem($data)
    {
        $ResourceUsermapModel = model('ResourceUsermapModel');
        $resource = $this->join('resources_usermap', 'resources_usermap.item_id = resources.id AND resources_usermap.user_id = '.$data['user_id'], 'left')
        ->where('code', $data['code'])->get()->getRowArray();
        //$data['quantity'] = $this->recalculateValue($data['code'], $data['quantity']);
        $ResourceUsermapModel->set('quantity', 'quantity+'.$data['quantity'], false);
        if($resource['quantity'] < 0){
            $ResourceUsermapModel->set('consumed_at', Time::now()->toDateTimeString(), false);
        }
        $ok = $ResourceUsermapModel->where(['item_id' => $resource['id'], 'user_id' => $data['user_id']])->update(); 
        if($ok){
            Events::trigger('resourceEnrolled', $resource['id'], $resource['code'], $data['quantity']);
        }
        return $ok;
    }
    

}