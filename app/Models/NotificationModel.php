<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    public function notifyLevel($level)
    {
        $UserUpdatesModel = model('UserUpdatesModel');
        $notification = [
            'user_id' => session()->get('user_id'),
            'code' => 'level',
            'data' => json_encode([
                'title' => 'Новый уровень!', 
                'description' => 'Вы достигли уровня '.$level['level'].'!',
                'image' => base_url('image/index.php/quests_rocket.png'),
                'data' => ['reward' => $level['reward']],
                'link' => '/user'
            ]),
            'status' => 'created'
        ];
        $UserUpdatesModel->set($notification)->insert();
    }
    public function notifyAchievement($achievement)
    {
        $UserUpdatesModel = model('UserUpdatesModel');
        $notification = [
            'user_id' => session()->get('user_id'),
            'code' => 'achievement',
            'data' => json_encode([
                'title' => 'Новое достижение!', 
                'description' => 'Вы получили достижение "'.$achievement['title'].'"!',
                'image' => base_url('image/index.php'.$achievement['image']),
                'data' => [],
                'link' => '/achievements'
            ]),
            'status' => 'created'
        ];
        $UserUpdatesModel->set($notification)->insert();
    }
    public function notifyQuest($quest)
    {
        $UserUpdatesModel = model('UserUpdatesModel');
        $notification = [
            'user_id' => session()->get('user_id'),
            'code' => 'quest',
            'data' => json_encode([
                'title' => 'Задание выполнено!', 
                'description' => 'Вы выполнили задание "'.$quest['group']['title'].'"!',
                'image' => base_url($quest['group']['image_full']),
                'data' => [],
                'link' => null
            ]),
            'status' => 'created'
        ];
        $UserUpdatesModel->set($notification)->insert();
    }
    public function notifyInvitation($user)
    {
        $UserUpdatesModel = model('UserUpdatesModel');
        $notification = [
            'user_id' => $user['invited_by'],
            'code' => 'invitation',
            'data' => json_encode([
                'title' => 'Награда за приглашение!', 
                'description' => 'Вы получили награду за приглашение "'.$user['name'].'"!',
                'image' => null,
                'data' => [],
                'link' => null
            ]),
            'status' => 'created'
        ];
        $UserUpdatesModel->set($notification)->insert();
    }
    
    

}