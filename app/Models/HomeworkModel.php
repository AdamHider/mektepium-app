<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class HomeworkModel extends Model
{
    use PermissionTrait;
    protected $table      = 'homeworks';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'image'
    ];

    public function getItem ($homework_id) 
    {
        $this->useSharedOf('classrooms', 'classroom_id');

        if(!$this->hasPermission($homework_id, 'r')){
            return 'forbidden';
        }

        $CourseSectionModel = model('CourseSectionModel');
        $LessonModel = model('LessonModel');
        $ExerciseModel = model('ExerciseModel');
        
        $homework = $this->join('lessons', 'lessons.id = homeworks.lesson_id')
        ->join('exercises', 'exercises.lesson_id = lessons.id AND exercises.user_id = '.session()->get('user_id'), 'left')
        ->select("homeworks.*, exercises.finished_at, exercises.id as exercise_id, lessons.title, lessons.description, lessons.image as image, lessons.course_section_id, lessons.unblock_after")
        ->where('homeworks.id', $homework_id)
        ->get()->getRowArray();

        if(empty($homework)){
            return 'not_found';
        }

        $homework['course_section'] = $CourseSectionModel->getItem($homework['course_section_id']);
        $homework['image'] = base_url('image/' . $homework['image']);
        $homework['exercise'] = $ExerciseModel->getItem($homework['exercise_id'], 'lite');
        $homework['is_blocked'] = $LessonModel->checkBlocked($homework['unblock_after']);
        if($homework['date_start']){
            $homework['date_start_humanized'] = Time::parse($homework['date_start'], Time::now()->getTimezone())->humanize();
        }
        if($homework['date_end']){
            $date_end = Time::parse($homework['date_end'], Time::now()->getTimezone());
            $time_diff = Time::now()->difference($date_end);
            $homework['time_left'] = $time_diff->getDays();
            $homework['time_left_humanized'] = Time::now()->difference($date_end)->humanize();
            $homework['date_end_humanized'] = $date_end->humanize();
            $homework['is_finished'] = $time_diff->getSeconds() <= 0;
        }
        return $homework;
    }
    public function getList ($data) 
    {
        /*
        $ClassroomModel = model('ClassroomModel');
        if(!$ClassroomModel->hasPermission($data['classroom_id'], 'r')){
            return 'forbidden';
        }*/
        $this->useSharedOf('classrooms', 'classroom_id');
        
        $CourseSectionModel = model('CourseSectionModel');
        $LessonModel = model('LessonModel');
        $ExerciseModel = model('ExerciseModel');
        
        $this->join('lessons', 'lessons.id = homeworks.lesson_id')
        ->join('exercises', 'exercises.lesson_id = lessons.id AND exercises.user_id = '.session()->get('user_id'), 'left')
        ->select("homeworks.*, exercises.finished_at, exercises.id as exercise_id, lessons.image as image, lessons.title, lessons.description, lessons.course_section_id, lessons.unblock_after");
        
        if(isset($data['classroom_id'])){
            $this->where('homeworks.classroom_id', $data['classroom_id']);
        }
        if(isset($data['user_id'])){
            $this->join('classrooms', 'classrooms.id = homeworks.classroom_id')
            ->join('classrooms_usermap', 'classrooms_usermap.item_id = classrooms.id')
            ->where('classrooms_usermap.user_id', $data['user_id']);
        }
        $this->whereHasPermission('r');

        if(isset($data['limit'])){
            $this->limit($data['limit'], $data['offset']);
        }
        $homeworks =  $this->orderBy('date_end DESC')->get()->getResultArray();

        if(empty($homeworks)){
            return 'not_found';
        }
        
        foreach($homeworks as &$homework){

            $homework['course_section'] = $CourseSectionModel->getItem($homework['course_section_id']);
            $homework['image'] = base_url('image/' . $homework['image']);
            $homework['exercise'] = $ExerciseModel->getItem($homework['exercise_id'], 'lite');
            $homework['is_blocked'] = $LessonModel->checkBlocked($homework['unblock_after']);
            if($homework['date_end']){
                $date_end = Time::parse($homework['date_end'], Time::now()->getTimezone());
                $time_diff = Time::now()->difference($date_end);
                $homework['time_left'] = $time_diff->getDays();
                $homework['time_left_humanized'] = Time::now()->difference($date_end)->humanize();
                $homework['is_finished'] = $time_diff->getSeconds() <= 0;
            }
        }
        return $homeworks;
    }
    public function getTotal ($data) 
    {
        $this->useSharedOf('classrooms', 'classroom_id');
        
        $homeworks = $this->join('lessons', 'lessons.id = homeworks.lesson_id')
        ->join('exercises', 'exercises.lesson_id = lessons.id AND exercises.user_id = '.session()->get('user_id'), 'left')
        ->select("homeworks.*, exercises.finished_at, exercises.id as exercise_id, lessons.image as image, lessons.title, lessons.description, lessons.course_section_id, lessons.unblock_after")
        ->where('homeworks.classroom_id', $data['classroom_id'])->whereHasPermission('r')
        ->get()->getResultArray();

        if(empty($homeworks)){
            return 0;
        }
        return count($homeworks);
    }
}