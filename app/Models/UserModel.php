<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'first_name', 'last_name', 'email', 'password',
        'role', 'avatar', 'email_verified', 'status', 'remember_token', 'linkedin_id',
    ];

    protected $validationRules = [
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name'  => 'required|min_length[2]|max_length[100]',
        'email'      => 'required|valid_email|max_length[255]|is_unique[users.email,id,{id}]',
        'password'   => 'required|min_length[8]',
        'role'       => 'in_list[job_seeker,recruiter,admin]',
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPasswordOnUpdate'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    protected function hashPasswordOnUpdate(array $data): array
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    public function verifyPassword(string $plain, string $hashed): bool
    {
        return password_verify($plain, $hashed);
    }

    public function findByEmail(string $email): ?object
    {
        $result = $this->asObject()->where('email', $email)->first();
        if ($result === null || $result === false) {
            return null;
        }
        return is_array($result) ? (object) $result : $result;
    }

    public function getFullName(array|object $user): string
    {
        $u = (object) $user;
        return trim($u->first_name . ' ' . $u->last_name);
    }
}
