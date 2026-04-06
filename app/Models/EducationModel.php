<?php

namespace App\Models;

use CodeIgniter\Model;

class EducationModel extends Model
{
    protected $table         = 'education';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'org_id', 'degree', 'niveau', 'field', 'institution', 'location',
        'start_year', 'end_year', 'is_current', 'description', 'sort_order',
    ];

    protected $validationRules = [
        'degree'      => 'required|max_length[200]',
        'institution' => 'required|max_length[200]',
        'start_year'  => 'required|integer|min_length[4]|max_length[4]',
    ];

    public function getByUserId(int $userId): array
    {
        return $this->select('education.*, organizations.logo as org_logo, organizations.name as org_name')
                    ->join('organizations', 'organizations.id = education.org_id', 'left')
                    ->where('education.user_id', $userId)
                    ->orderBy('education.is_current', 'DESC')
                    ->orderBy('education.start_year', 'DESC')
                    ->findAll();
    }
}
