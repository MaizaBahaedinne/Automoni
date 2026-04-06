<?php

namespace App\Models;

use CodeIgniter\Model;

class VolunteeringModel extends Model
{
    protected $table         = 'volunteering';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id', 'organization', 'position', 'start_date', 'end_date',
        'is_current', 'description', 'sort_order',
    ];
    protected $validationRules = [
        'organization' => 'required|max_length[255]',
    ];

    public function getByUserId(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('start_date', 'DESC')->findAll();
    }
}
