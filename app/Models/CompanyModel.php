<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table          = 'companies';
    protected $primaryKey     = 'id';
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'user_id', 'name', 'slug', 'logo', 'website',
        'industry', 'size', 'country', 'city', 'description', 'linkedin',
    ];

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[200]',
    ];

    public function getByUserId(int $userId): ?object
    {
        $result = $this->asObject()->where('user_id', $userId)->first();
        if ($result === null || $result === false) {
            return null;
        }
        return is_array($result) ? (object) $result : $result;
    }

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlugOnUpdate'];

    protected function generateSlug(array $data): array
    {
        if (isset($data['data']['name'])) {
            $data['data']['slug'] = $this->makeUniqueSlug($data['data']['name']);
        }
        return $data;
    }

    protected function generateSlugOnUpdate(array $data): array
    {
        if (isset($data['data']['name'])) {
            $id   = $data['id'] ?? null;
            $data['data']['slug'] = $this->makeUniqueSlug($data['data']['name'], $id);
        }
        return $data;
    }

    private function makeUniqueSlug(string $name, $excludeId = null): string
    {
        $base  = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        $slug  = $base;
        $i     = 1;
        $query = $this->where('slug', $slug);
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }
        while ($query->countAllResults(false) > 0) {
            $slug  = $base . '-' . $i++;
            $query = $this->where('slug', $slug);
            if ($excludeId) {
                $query->where('id !=', $excludeId);
            }
        }
        return $slug;
    }
}
