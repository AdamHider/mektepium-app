<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table      = 'courses';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'image'
    ];
    
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getItem ($course_id) 
    {
        $DescriptionModel = model('DescriptionModel');
        
        $course = $this->where('id', $course_id)->get()->getRowArray();
        if(!empty($course)){
            $course['description'] = $DescriptionModel->getItem('course', $course['id']);
            $course['image'] = base_url('image/' . $course['image']);
            $course['background_image'] = base_url('image/' . $course['background_image']);
            $course['progress'] = $this->getProgress($course['id']);
            $course['progress']['percentage'] = ceil($course['progress']['total_exercises'] * 100 / $course['progress']['total_lessons']);
        }
        return $course;
    }
    public function getList () 
    {
        $DescriptionModel = model('DescriptionModel');
        
        $courses = $this->get()->getResultArray();


        foreach($courses as &$course){
            $course['description'] = $DescriptionModel->getItem('course', $course['id']);
            $course['image'] = base_url('image/' . $course['image']);
            $course['background_image'] = base_url('image/' . $course['background_image']);
            $course['progress'] = $this->getProgress($course['id']);
            $course['progress']['percentage'] = ceil($course['progress']['total_exercises'] * 100 / $course['progress']['total_lessons']);
            $course['is_active'] = session()->get('user_data')->profile['course_id'] == $course['id'];
        }
        return $courses;
    }
    private function getProgress($course_id)
    {
        $progress = $this->join('lessons', 'lessons.course_id = courses.id')
        ->join('exercises', 'exercises.lesson_id = lessons.id', 'left')
        ->select("COALESCE(sum(exercises.points), 0) as total_points, COALESCE(COUNT(exercises.points), 0) as total_exercises, COALESCE(COUNT(lessons.id), 0) as total_lessons")
        ->where('courses.id', $course_id)
        ->get()->getRowArray();

        return $progress;
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