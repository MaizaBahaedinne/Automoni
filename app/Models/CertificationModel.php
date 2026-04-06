<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificationModel extends Model
{
    protected $table         = 'certifications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id', 'name', 'organization', 'issue_date', 'expiry_date',
        'credential_url', 'logo_file', 'sort_order',
    ];
    protected $validationRules = [
        'name' => 'required|max_length[255]',
    ];

    public function getByUserId(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('issue_date', 'DESC')->findAll();
    }
}
