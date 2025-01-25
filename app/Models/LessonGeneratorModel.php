<?php

namespace App\Models;

use CodeIgniter\Model;
use stdClass;

class LessonGeneratorModel extends LessonModel
{
    private $currentPage = 0;
    
    public function generateList($lesson_id) 
    {
        $lesson = $this->where('lessons.id', $lesson_id)->get()->getRowArray();
        $pages = json_decode($lesson['pages'], true);
        if($lesson['type'] == 'common'){
            return $pages;
        } else 
        if($lesson['type'] == 'lexis') {
            return $this->generateLexisItem($pages);
        } else 
        if($lesson['type'] == 'chat') {
            return [$pages];
        }
    }

    private function generateLexisItem($page_config)
    {
        $seed = rand(1, 10);
        $pages = [];
        $word_list = $this->seedShuffle($page_config['word_list'], $seed);
        $pages_scenery = [
            [ 'type' => 'read', 'referent_index' => '0'],
            [ 'type' => 'read', 'referent_index' => '1'],
            [ 'type' => 'quiz', 'referent_index' => '0'],
            [ 'type' => 'read', 'referent_index' => '2'],
            [ 'type' => 'quiz', 'referent_index' => '1'],
            [ 'type' => 'read', 'referent_index' => '3'],
            [ 'type' => 'quiz', 'referent_index' => '2'],
            [ 'type' => 'quiz', 'referent_index' => '3']
        ];
        $word_list_chunks = array_chunk($word_list, 4);
        $word_object = $word_list[0];
        foreach($word_list_chunks as $word_chunk){
            $word_chunk = $this->seedShuffle($word_chunk, $seed);
            foreach($pages_scenery as $key => $scenery_item){
                $word_object = $word_chunk[$scenery_item['referent_index']];
                if($scenery_item['type'] == 'read'){
                    $page = $page_config['template']['read_page'];
                    $page['title'] = sprintf($page['title'], "<b>".$word_object['label']."</b>");
                    $page['template_config'] = [
                        'image' => $word_object['image'],
                        'text'  => $word_object['text']
                    ];
                    $pages[] = $page;
                } else {
                    $page = $page_config['template']['quiz_page'];
                    $variants = [];
                    $variants[] = [
                        'image' => $word_object['image'],
                        'text' => $word_object['text']
                    ];
                    $word_list_randomized = $this->seedShuffle($word_list, $key);
                    foreach($word_list_randomized as $key => $word){
                        if($word['index'] !== $word_object['index']){
                            $variants[] = [
                                'image' => $word['image'],
                                'text' => $word['text']
                            ];
                        }
                        if(count($variants) == 4){
                            shuffle($variants);
                            break;
                        }
                    }
                    $input_object = [
                        'index' => $word_object['index'],
                        'answer' => $word_object['text'],
                        'variants' => $variants,
                        'mode' => 'image',
                        'type' => 'input'
                    ];
                    $page['title'] = sprintf($page['title'], "<b>".$word_object['label']."</b>");
                    $page['template_config'] = [
                        'input_list' => [$input_object],
                        'text' => "{{input".$word_object['index']."}}",
                    ];
                    $pages[] = $page;
                }
            }
        }
        return $pages;
    }

    public function seedShuffle($array, $seed) {
        $tmp = array();
        for ($rest = $count = count($array);$count>0;$count--) {
            $seed %= $count;
            $t = array_splice($array,$seed,1);
            $tmp[] = $t[0];
            $seed = $seed*$seed + $rest;
        }
        return $tmp;
    }
    
}