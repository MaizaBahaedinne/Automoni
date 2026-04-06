<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table         = 'projects';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id', 'name', 'start_date', 'end_date', 'is_current', 'description', 'sort_order',
    ];
    protected $validationRules = [
        'name' => 'required|max_length[255]',
    ];

    public function getByUserId(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('start_date', 'DESC')->findAll();
    }

    /**
     * Return project with its member user IDs.
     */
    public function getWithMembers(int $projectId): ?object
    {
        return $this->find($projectId);
    }
}
