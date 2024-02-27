<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
class Skill extends BaseController
{
    use ResponseTrait;

    public function getItem()
    {
        
        $SkillModel = model('SkillModel');

        $quest_id = $this->request->getVar('quest_id');

        $quest = $SkillModel->getItem($quest_id);

        if ($quest == 'not_found') {
            return $this->failNotFound('not_found');
        }
        if ($quest == 'forbidden') {
            return $this->failForbidden();
        }

        return $this->respond($quest);
    }
    public function getList()
    {
        $SkillModel = model('SkillModel');

        $mode = $this->request->getVar('mode');
        $active_only = $this->request->getVar('active_only');
        $limit = $this->request->getVar('limit');
        $offset = $this->request->getVar('offset');

        $data = [];
        if($limit && $offset){
            $data['limit'] = $limit;
            $data['offset'] = $offset;
        }
        if($mode == 'by_user'){
            $data['user_id'] = session()->get('user_id');
        }
        if($active_only){
            $data['active_only'] = $active_only;
        }

        $skills = $SkillModel->getList($data);
        
        if ($skills == 'not_found') {
            return $this->failNotFound('not_found');
        }

        return $this->respond($skills);
    }
    public function saveItem()
    {
        $SkillModel = model('SkillModel');
        $data = $this->request->getJSON(true);

        $result = $SkillModel->updateItem($data);

        if ($result === 'forbidden') {
            return $this->failForbidden();
        }
        if($SkillModel->errors()){
            return $this->failValidationErrors($SkillModel->errors());
        }
        return $this->respond($result);
    }
    public function createItem()
    {
        $SkillModel = model('SkillModel');
        //$ClassroomUsermapModel = model('ClassroomUsermapModel');

        $classroom_id = $this->request->getVar('classroom_id');

        $data = [
            'classroom_id' => $classroom_id
        ];

        $quest_id = $SkillModel->createItem($data);

        if ($quest_id === 'forbidden') {
            return $this->failForbidden();
        }

        if($SkillModel->errors()){
            return $this->failValidationErrors($SkillModel->errors());
        }
        //$ClassroomUsermapModel->itemCreate(session()->get('user_id'), $quest_id);

        return $this->respond($quest_id);
    }
    public function claimItem()
    {
        $SkillModel = model('SkillModel');

        $quest_id = $this->request->getVar('quest_id');

        $result = $SkillModel->claimItem($quest_id);
        
        if ($result == 'not_found') {
            return $this->failNotFound('not_found');
        }
        if ($result == 'forbidden') {
            return $this->failForbidden();
        }
        if($SkillModel->errors()){
            return $this->failValidationErrors($SkillModel->errors());
        }
        return $this->respond($result);
    }
    
}