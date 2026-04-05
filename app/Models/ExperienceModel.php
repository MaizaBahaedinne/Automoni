<?php

namespace App\Models;

use CodeIgniter\Model;

class ExperienceModel extends Model
{
    protected $table         = 'experiences';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'title', 'company', 'location', 'contract', 'level', 'department',
        'start_date', 'end_date', 'is_current', 'description',
        'manager_user_id', 'manager_name', 'skills_gained', 'sort_order',
    ];

    protected $validationRules = [
        'title'      => 'required|max_length[200]',
        'company'    => 'required|max_length[200]',
        'start_date' => 'required|valid_date',
    ];

    public function getByUserId(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('is_current', 'DESC')
                    ->orderBy('start_date', 'DESC')
                    ->findAll();
    }
}
