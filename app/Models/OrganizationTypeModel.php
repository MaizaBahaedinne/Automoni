<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationTypeModel extends Model
{
    protected $table            = 'organization_types';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['name', 'slug', 'description'];

    protected $validationRules  = [
        'name'        => 'required|min_length[3]|max_length[100]|is_unique[organization_types.name]',
        'slug'        => 'required|min_length[3]|max_length[100]|is_unique[organization_types.slug]|regex_match[/^[a-z0-9-]+$/]',
        'description' => 'max_length[500]',
    ];

    protected $beforeInsert = ['slugify'];
    protected $beforeUpdate = ['slugify'];

    /**
     * Transform name to slug if not provided
     */
    protected function slugify(array $data): array
    {
        if (empty($data['data']['slug']) && isset($data['data']['name'])) {
            $data['data']['slug'] = strtolower(preg_replace('/[^a-z0-9]+/', '-', $data['data']['name']));
            $data['data']['slug'] = trim($data['data']['slug'], '-');
        }
        return $data;
    }
}
