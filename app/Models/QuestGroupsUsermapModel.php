<?php
namespace App\Models;
use CodeIgniter\Model;
class QuestGroupsUsermapModel extends Model
{
    protected $table      = 'quest_groups';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unclock_after'
    ];
}