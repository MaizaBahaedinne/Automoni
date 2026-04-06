<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectMemberModel extends Model
{
    protected $table         = 'project_members';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = ['project_id', 'user_id'];

    public function getMembersByProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)->findAll();
    }

    public function syncMembers(int $projectId, array $userIds): void
    {
        $this->where('project_id', $projectId)->delete();
        foreach (array_unique(array_filter(array_map('intval', $userIds))) as $uid) {
            $this->insert(['project_id' => $projectId, 'user_id' => $uid]);
        }
    }
}
