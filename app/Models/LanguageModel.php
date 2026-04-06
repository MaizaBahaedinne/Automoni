<?php

namespace App\Models;

use CodeIgniter\Model;

class LanguageModel extends Model
{
    protected $table         = 'user_languages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'name', 'level', 'sort_order'];
    protected $validationRules = [
        'name'  => 'required|max_length[100]',
        'level' => 'required|max_length[20]',
    ];

    public function getByUserId(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('sort_order', 'ASC')->findAll();
    }
}
