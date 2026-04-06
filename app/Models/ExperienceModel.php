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
        'user_id', 'org_id', 'title', 'company', 'location', 'contract', 'level', 'department',
        'start_date', 'end_date', 'is_current', 'description',
        'manager_user_id', 'manager_name', 'skills_gained', 'sort_order',
    ];

    protected $validationRules = [
        'title'      => 'required|max_length[200]',
        'company'    => 'required|max_length[200]',
        'start_date' => 'required|valid_date[Y-m]',
    ];

    public function getByUserId(int $userId): array
    {
        return $this->select('experiences.*, organizations.logo as org_logo, organizations.name as org_name')
                    ->join('organizations', 'organizations.id = experiences.org_id', 'left')
                    ->where('experiences.user_id', $userId)
                    ->orderBy('experiences.is_current', 'DESC')
                    ->orderBy('experiences.start_date', 'DESC')
                    ->findAll();
    }
}
