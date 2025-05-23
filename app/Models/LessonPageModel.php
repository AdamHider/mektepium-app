<?php

namespace App\Models;

use CodeIgniter\Model;

class LessonPageModel extends LessonModel
{
    private $currentPage = 0;
    public function getPage($lesson_id, $index)
    {
        $lesson = $this->join('exercises', 'exercises.lesson_id = lessons.id AND exercises.user_id ='.session()->get('user_id'), 'left')
        ->select('exercises.*, COALESCE(exercises.exercise_pending, exercises.exercise_submitted) as exercise_data')
        ->select('JSON_EXTRACT(exercises.pages, "$['.$index.']") as page')
        ->where('lessons.id', $lesson_id)->get()->getRowArray();

        if(!empty($lesson['page'])){
            $lesson['page'] = json_decode($lesson['page'], true);
            $lesson['exercise_data'] = json_decode($lesson['exercise_data'], true);
            $this->currentPage  = $lesson['exercise_data']['current_page'];
            $page['data']       = $this->composeItemData($lesson['page']);
            $page['fields']     = $this->composeItemFields($lesson['page'], $lesson['exercise_data']);
            $page['actions']    = $this->composeItemActions($lesson['page'], $lesson['exercise_data']);
            $page['totals']     = $lesson['exercise_data']['totals'];
            $page['answer']     = $lesson['exercise_data']['answers'][$this->currentPage]['totals'] ?? [];
            $page['current']    = $lesson['exercise_data']['current_page'];
            $page['progress']   = $this->getItemProgress($lesson['exercise_data']);
    
            unset($lesson['page']['template_config']);
            $page['header']     = $lesson['page'];
            return $page;
        } 
        
        return false;
    }
    private function composeItemData($page_data)
    {
        if(isset($page_data['form_template']) && $page_data['form_template'] == 'match'){
            $page_data['template_config']['match_variants'] = $this->composeMatchVariants($page_data['template_config']['input_list']);
        }
        unset($page_data['template_config']['input_list']);
        return $page_data['template_config'];
    }
    private function composeItemActions($page_data, $exercise)
    {
        $isStart    = $this->currentPage == 0;
        $isEnd      = $exercise['total_pages']-1 == $this->currentPage;
        $isAnswered = isset($exercise['answers'][$this->currentPage]) && $exercise['answers'][$this->currentPage]['totals']['is_finished'];
        $hasInput   = isset($page_data['template_config']['input_list']);

        if(!$isAnswered && $hasInput)   $exercise['actions']['main'] = 'confirm';
        if($isAnswered && $isEnd)       $exercise['actions']['main'] = 'finish';
        if(!$hasInput && $isEnd)        $exercise['actions']['main'] = 'finish';
        if($isStart)                    $exercise['actions']['back_attempts'] = 0;
        
        return $exercise['actions'];
    }
    private function composeItemFields($page_data, $exercise)
    {
        $result = [];
        $user_answers = [];
        if(empty($page_data['template_config']['input_list'])) return false;
        if(!empty($exercise['answers'][$this->currentPage])){
            $user_answers = $exercise['answers'][$this->currentPage]['answers'];
        }
        foreach($page_data['template_config']['input_list'] as $key => $input){
            $field = [
                'index'     => $input['index'],
                'mode'      => $input['mode'],
            ];
            if(isset($input['variants']))   $field['variants']  = $input['variants'];
            if(isset($input['label']))      $field['label']     = $input['label'];
            if(isset($user_answers[$key]))  $field['answer']    = $user_answers[$key];

            $result[] = $field;
        }
        return $result;
    }
    private function composeMatchVariants($input_list)
    {
        $result = [];
        foreach($input_list as $key => $input){
            $match_variant = [
                'index'     => $input['index'],
                'answer'    => $input['answer']
            ];
            $result[] = $match_variant;
        }
        shuffle($result);
        return $result;
    }
    public function getCurrentIndex($lesson_id, $action)
    {
        $ExerciseModel = model('ExerciseModel');
        $result = [
            'available' => true
        ];
        $exercise = $ExerciseModel->getItemByLesson($lesson_id);
        if(empty($exercise)){
            $result['available']  = false;
            $result['message']    = 'No such exercise';
            return $result;
        }
        
        if($action == 'next'){
            $exercise['data']['current_page']++;
            $exercise['data']['totals']['points'] = (int) $exercise['data']['totals']['points'] + 20;
        }
        if($action == 'finish'){
            $ExerciseModel->updateItem($exercise, 'finish');
            $result['available']  = false;
            $result['message']    = 'finish';
            return $result;
        }
        $result['index']  = $exercise['data']['current_page'];
        $ExerciseModel->updateItem($exercise);
        return $result;
    }
    
}