<?php

namespace App\Models;

use CodeIgniter\Model;

class ResourceTransactionModel extends Model
{
    protected $table      = 'resource_transactions';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'resource_id',
        'user_id',
        'quantity'
    ];
    
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getItem ($character_id) 
    {
        $DescriptionModel = model('DescriptionModel');
        $character = $this->where('characters.id', $character_id)->get()->getRowArray();
        if ($character) {
            $character['avatar'] = base_url('image/' . $character['avatar']);
            $character['image'] = base_url('image/' . $character['image']);
            $character['description'] = $DescriptionModel->getItem('character', $character['id']);
        }
        return $character;
    }
    public function getList ($data) 
    {
        if($data['user_id']){
            $this->join('achievements_usermap', 'achievements_usermap.item_id = achievements.id')
            ->where('achievements_usermap.user_id', $data['user_id']);
        }
        $achievements = $this->limit($data['limit'], $data['offset'])->orderBy('code, value')->get()->getResultArray();
        
        if(empty($achievements)){
            return false;
        }
        foreach($achievements as &$achievement){
            $achievement = array_merge($achievement, $DescriptionModel->getItem('achievement', $achievement['id']));
            $achievement['image'] = base_url('image/' . $achievement['image']);
            $achievement['progress'] = $this->calculateProgress($achievement);
            $achievement['params'] = json_decode($achievement['params']);
        }
        return $achievements;
    }
        
    public function itemCreate ($image)
    {
        $this->transBegin();
        $data = [
            'image' => $image
        ];
        $character_id = $this->insert($data, true);
        $this->transCommit();

        return $character_id;        
    }



}