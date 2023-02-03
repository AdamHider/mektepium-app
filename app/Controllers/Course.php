<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
class Course extends BaseController
{
    use ResponseTrait;
    public function getItem()
    {
        
        $UserModel = model('UserModel');

        $user_id = $this->request->getVar('user_id');

        if( !$user_id ){
            $user_id = session()->get('user_id');
        }

        $user = $UserModel->getItem($user_id);

        if ($user == 'not_found') {
            return $this->failNotFound('not_found');
        }

        return $this->respond($user);
    }
    public function getList()
    {
        $CourseModel = model('CourseModel');
        $result = $CourseModel->getList();
        return $this->respond($result, 200);
    }

}
