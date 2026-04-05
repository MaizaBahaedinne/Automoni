<?php

namespace App\Models;

use CodeIgniter\Model;

class SkillModel extends Model
{
    protected $table         = 'user_skills';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'skill_id', 'skill_name', 'level'];

    public function getByUserId(int $userId): array
    {
        return $this->where('user_id', $userId)->findAll();
    }

    public function syncSkills(int $userId, array $skills): void
    {
        $this->where('user_id', $userId)->delete();
        foreach ($skills as $skill) {
            $name = trim($skill['name'] ?? $skill);
            if (empty($name)) {
                continue;
            }
            $this->insert([
                'user_id'    => $userId,
                'skill_name' => $name,
                'level'      => $skill['level'] ?? 'intermediate',
            ]);
        }
    }
}
