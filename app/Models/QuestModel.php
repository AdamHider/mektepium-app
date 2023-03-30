<?php

namespace App\Models;

use CodeIgniter\BaseBuilder;
use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class QuestModel extends Model
{
    use PermissionTrait;
    protected $table      = 'quests';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'classroom_id', 
        'lesson_id', 
        'code', 
        'value', 
        'image', 
        'date_start', 
        'date_end', 
        'reward', 
        'owner_id', 
        'is_disabled', 
        'is_private'
    ];
    protected $validationRules    = [
        'code'     => [
            'label' =>'code',
            'rules' =>'required',
            'errors'=>[
                'required'=>'required'
            ]
        ],
        'value'     => [
            'label' =>'value',
            'rules' =>'required|greater_than[0]',
            'errors'=>[
                'required'=>'required',
                'greater_than'=>'greater_than'
            ]
        ]
    ];
    
    public function getItem ($quest_id) 
    {
        $this->useSharedOf('classrooms', 'classroom_id');
        if(!$this->hasPermission($quest_id, 'r')){
            return 'forbidden';
        }
        
        $quest = $this->join('lessons', 'lessons.id = quests.lesson_id', 'left')
        ->select('quests.*, lessons.title as lesson_title, lessons.image as lesson_image')
        ->where('quests.id', $quest_id)->get()->getRowArray();
        
        if(empty($quest)){
            return 'not_found';
        }
        $quest['title'] = lang('App.quest.title.'.$quest['code'], [$quest['value']]);
        $quest['image'] = base_url('image/' . $quest['image']);
        $quest['value'] = (int) $quest['value'];
        $quest['is_private'] = (bool) $quest['is_private'];
        $quest['is_disabled'] = (bool) $quest['is_disabled'];
        $quest['is_owner'] = $quest['owner_id'] == session()->get('user_id');
        $quest['reward'] = json_decode($quest['reward'], true);
        $quest['progress'] = $this->getProgress($quest);
        $quest['is_completed'] = $this->checkCompleted($quest);
        $quest['is_outdated'] = $this->checkOutdated($quest);
        $quest['is_rewarded'] = $this->checkRewarded($quest);
        if($quest['lesson_id']){
            $quest['title'] = lang('App.quest.title.'.$quest['code'], [$quest['lesson_title']]);
            $quest['image'] = base_url('image/' . $quest['lesson_image']);
        }
        $quest['goal'] = [
            'title' => lang('App.quest.goal.'.$quest['code'].'.title'),
            'description' => lang('App.quest.goal.'.$quest['code'].'.description'),
            'value' => lang('App.quest.goal.'.$quest['code'].'.value', [$quest['value']])
        ];
        if($quest['date_start']){
            $quest['date_start_humanized'] = Time::parse($quest['date_start'], Time::now()->getTimezone())->humanize();
        }
        if($quest['date_end']){
            $date_end = Time::parse($quest['date_end'], Time::now()->getTimezone());
            $quest['time_left'] = Time::now()->difference($date_end)->getDays();
            $quest['date_end_humanized'] = $date_end->humanize();
            $quest['time_left_humanized'] = Time::now()->difference($date_end)->humanize();
        }
        return $quest;
    }
    public function getList ($data) 
    {
        
        $this->join('lessons', 'lessons.id = quests.lesson_id', 'left')
        ->select('quests.*, lessons.title as lesson_title, lessons.image as lesson_image');

        if(isset($data['classroom_id'])){
            $this->useSharedOf('classrooms', 'classroom_id');
            $this->where('quests.classroom_id', $data['classroom_id']);
        }
        if(isset($data['user_id'])){
            $this->join('classrooms', 'classrooms.id = quests.classroom_id')
            ->join('classrooms_usermap', 'classrooms_usermap.item_id = classrooms.id')
            ->where('classrooms_usermap.user_id', $data['user_id']);
        }
        if(isset($data['active_only'])){
            $this->join('user_resources_expenses', 'user_resources_expenses.item_id = quests.id AND user_resources_expenses.item_code = "quest" AND user_resources_expenses.user_id = '.session()->get('user_id'), 'left')
            ->where('user_resources_expenses.id', NULL);
            $this->where('IF(quests.date_end, quests.date_end > NOW(), 1)');
        }

        $this->whereHasPermission('r')->groupBy('quests.id');
        
        if(isset($data['limit'])){
            $this->limit($data['limit'], $data['offset']);
        }

        $quests = $this->orderBy('COALESCE(date_end, NOW()) DESC')->get()->getResultArray();

        if(empty($quests)){
            return 'not_found';
        }
        
        foreach($quests as &$quest){
            $quest['title'] = lang('App.quest.title.'.$quest['code'], [$quest['value']]);
            $quest['image'] = base_url('image/' . $quest['image']);
            $quest['reward'] = json_decode($quest['reward'], true);
            $quest['progress'] = $this->getProgress($quest);
            $quest['is_completed'] = $this->checkCompleted($quest);
            $quest['is_outdated'] = $this->checkOutdated($quest);
            $quest['is_rewarded'] = $this->checkRewarded($quest);
            if($quest['lesson_id']){
                $quest['title'] = lang('App.quest.title.'.$quest['code'], [$quest['lesson_title']]);
                $quest['image'] = base_url('image/' . $quest['lesson_image']);
            }
            $quest['goal'] = [
                'title' => lang('App.quest.goal.'.$quest['code'].'.title'),
                'description' => lang('App.quest.goal.'.$quest['code'].'.description'),
                'value' => lang('App.quest.goal.'.$quest['code'].'.value', [$quest['value']])
            ];
            if($quest['date_start']){
                $quest['date_start_humanized'] = Time::parse($quest['date_start'], Time::now()->getTimezone())->humanize();
            }
            if($quest['date_end']){
                $time = Time::parse($quest['date_end'], Time::now()->getTimezone());
                $quest['time_left'] = Time::now()->difference($time)->getDays();
                $quest['date_end_humanized'] = $time->humanize();
                $quest['time_left_humanized'] = Time::now()->difference($time)->humanize();
            }
        }
        return $quests;
    }
    public function getTotal ($data) 
    {
        if(isset($data['classroom_id'])){
            $this->useSharedOf('classrooms', 'classroom_id');
        }
        
        $quests = $this->where('quests.classroom_id', $data['classroom_id'])->whereHasPermission('r')
        ->groupBy('quests.id')->orderBy('date_end')->get()->getResultArray();
        
        if(empty($quests)){
            return 0;
        }
        return count($quests);
    }
    public function getProgress($data)
    {
        $ExerciseModel = model('ExerciseModel');
        $current_total = 0;
        if($data['code'] == 'total_points' || $data['code'] == 'total_points_first'){
            $current_total = $ExerciseModel->getTotal($data, 'sum');
        }
        if($data['code'] == 'lesson' || $data['code'] == 'total_lessons'){
            $current_total = $ExerciseModel->getTotal($data, 'count');
        }
        $result = [
            'value' => $current_total,
            'total' => $data['value'],
            'percentage' => 0
        ];
        if($result['total'] != 0){
            $result['percentage'] = ceil($result['value'] * 100 / $result['total']);
            if($result['percentage'] > 100){
                $result['percentage'] = 100;
            }
        }
        $result['percentage_text'] = lang('App.quest.progress.'.$data['code'].'.percentage_text', [$result['percentage'], $result['value'], $result['total']]);
        return $result;
    }
    private function checkCompleted($quest)
    {
        return $quest['progress']['value'] >= $quest['value'];
    }
    private function checkOutdated($quest)
    {
        $is_outdated = false;
        if($quest['date_end']){
            $is_outdated = strtotime($quest['date_end']) <= strtotime('now');
        } 
        return $is_outdated;
    }
    private function checkRewarded($quest)
    {
        $is_rewarded = false;
        if(!empty($quest['reward'])){
            $UserResourcesExpensesModel = model('UserResourcesExpensesModel');
            foreach($quest['reward'] as $resource_title => $resource_quantity){
                $is_rewarded = !empty($UserResourcesExpensesModel->getItem($resource_title, 'quest', $quest['id'], session()->get('user_id')));
            }
        }
        return $is_rewarded;
    }
    public function claimReward($quest_id)
    {
        $this->useSharedOf('classrooms', 'classroom_id');
        if(!$this->hasPermission($quest_id, 'r')){
            return 'forbidden';
        }
        
        $quest = $this->where('id', $quest_id)->get()->getRowArray();

        if(empty($quest)){
            return 'not_found';
        }
        $quest['reward'] = json_decode($quest['reward'], true);
        $quest['progress'] = $this->getProgress($quest);
        $quest['is_completed'] = $this->checkCompleted($quest);
        $quest['is_outdated'] = $this->checkOutdated($quest);
        $quest['is_rewarded'] = $this->checkRewarded($quest);
        if($this->checkCompleted($quest) && !$this->checkOutdated($quest) && !$this->checkRewarded($quest)){
            $UserResourcesExpensesModel = model('UserResourcesExpensesModel');
            foreach($quest['reward'] as $resource_title => $resource_quantity){
                $data = [
                    'user_id' => session()->get('user_id'),
                    'code' => $resource_title,
                    'item_code' => 'quest',
                    'item_id' => $quest['id'],
                    'quantity' => $resource_quantity
                ];
                $UserResourcesExpensesModel->createItem($data);
            }
            return $quest;
        } else {
            return 'forbidden';
        }
    }
    public function createItem ($data)
    {
        $ClassroomModel = model('ClassroomModel');
        
        if(!$ClassroomModel->hasPermission($data['classroom_id'], 'w')){
            return 'forbidden';
        }
        $classroom = $ClassroomModel->where('id', $data['classroom_id'])->get()->getRowArray();
        $this->validationRules = [];
        $data = [
            'classroom_id' => $data['classroom_id'], 
            'lesson_id' => NULL, 
            'code' => NULL, 
            'value' => NULL, 
            'image' => NULL, 
            'date_start' => NULL,
            'date_end' => NULL, 
            'reward' => '{}', 
            'owner_id' => session()->get('user_id'), 
            'is_disabled' => false, 
            'is_private' => $classroom['is_private']
        ];
        $this->transBegin();
        $quest_id = $this->insert($data, true);

        $this->transCommit();

        return $quest_id;        
    }
    public function updateItem ($data)
    {
        if(!$this->hasPermission($data['id'], 'w')){
            return 'forbidden';
        }
        if($data['code'] == 'lesson'){
            $this->validationRules['lesson_id'] = [
                'label' =>'lesson_id',
                'rules' =>'required',
                'errors'=>[
                    'required'=>'required'
                ]
            ];
        }
        $this->transBegin();
        
        $this->update(['id'=>$data['id']], $data);

        $this->transCommit();

        return true;        
    }

    public function getAvailableLessons ($data) 
    {
        $LessonModel = model('LessonModel');

        $lessons = $LessonModel->like('title', $data['title'])->limit(10)->orderBy('id')->get()->getResultArray();
        $result = [];
        foreach($lessons as $key => $lesson){
            $result[] = [
                'id'    => $lesson['id'],
                'title' => $lesson['title'],
                'image' => base_url('image/' . $lesson['image'])
            ];
        }
        return $result;
    }

    
    
}