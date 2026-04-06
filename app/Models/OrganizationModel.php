<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationModel extends Model
{
    protected $table            = 'organizations';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'parent_id', 'type_id', 'name', 'legal_name', 'slug', 'description', 'logo', 'website',
        'phone', 'phone_country_code', 'phone_number', 'email', 'address', 'street_address', 
        'city', 'postal_code', 'country', 'country_code', 'latitude', 'longitude', 
        'map_link', 'employee_count', 'industry', 'sectors', 'founded_at', 'tax_id', 
        'status', 'is_verified',
    ];

    protected $validationRules  = [
        'type_id'              => 'required|integer|greater_than[0]',
        'name'                 => 'required|min_length[3]|max_length[255]|is_unique[organizations.name,id,{id}]',
        'legal_name'           => 'max_length[255]',
        'slug'                 => 'required|min_length[3]|max_length[255]|is_unique[organizations.slug,id,{id}]|regex_match[/^[a-z0-9-]+$/]',
        'description'          => 'max_length[10000]',
        'website'              => 'required|valid_url_strict',
        'email'                => 'required|valid_email',
        'phone'                => 'required',
        'phone_country_code'   => 'regex_match[/^[\+]?[0-9]{1,5}$/]',
        'phone_number'         => 'required|regex_match[/^[0-9\s\-\(\)]+$/]|min_length[7]|max_length[15]',
        'street_address'       => 'required|min_length[5]|max_length[255]',
        'city'                 => 'required|min_length[2]|max_length[100]',
        'postal_code'          => 'required|min_length[2]|max_length[20]',
        'country'              => 'required|min_length[2]|max_length[100]',
        'country_code'         => 'required|regex_match[/^[A-Z]{2}$/]',
        'latitude'             => 'numeric|greater_than_equal_to[-90]|less_than_equal_to[90]',
        'longitude'            => 'numeric|greater_than_equal_to[-180]|less_than_equal_to[180]',
        'tax_id'               => 'max_length[50]',
        'employee_count'       => 'integer|greater_than_equal_to[0]',
        'founded_at'           => 'valid_date[Y-m-d]',
        'status'               => 'required|in_list[active,inactive,archived]',
        'is_verified'          => 'in_list[0,1]',
    ];

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    /**
     * Generate slug from name if not provided
     */
    protected function generateSlug(array $data): array
    {
        if (empty($data['data']['slug']) && isset($data['data']['name'])) {
            $base = strtolower(preg_replace('/[^a-z0-9]+/', '-', $data['data']['name']));
            $slug = trim($base, '-');
            $data['data']['slug'] = $slug . '-' . time();
        }
        return $data;
    }

    /**
     * Get organization with type information
     */
    public function getWithType(int $id)
    {
        return $this->select('o.*, ot.name as type_name, ot.slug as type_slug')
                    ->from($this->table . ' as o')
                    ->join('organization_types as ot', 'ot.id = o.type_id')
                    ->where('o.id', $id)
                    ->first();
    }

    /**
     * Get all subsidiaries (children) of an organization
     */
    public function getSubsidiaries(int $parentId): array
    {
        return $this->where('parent_id', $parentId)
                    ->where('status', 'active')
                    ->findAll();
    }

    /**
     * Get parent organization
     */
    public function getParent(int $id)
    {
        $organization = $this->find($id);
        if (!$organization || !$organization->parent_id) {
            return null;
        }
        return $this->find($organization->parent_id);
    }

    /**
     * Get full hierarchy (recursive tree)
     */
    public function getHierarchy(int $id): ?object
    {
        $organization = $this->getWithType($id);
        if (!$organization) {
            return null;
        }

        // Get children
        $children = $this->select('id, name, slug, logo, type_id')
                         ->where('parent_id', $id)
                         ->where('status', 'active')
                         ->findAll();

        $organization->children = [];
        foreach ($children as $child) {
            $organization->children[] = $this->getHierarchy($child->id);
        }

        return $organization;
    }

    /**
     * Search organizations by filters
     */
    public function search(array $filters = [], int $perPage = 15): array
    {
        $builder = $this->select('o.*, ot.name as type_name')
                        ->from($this->table . ' as o')
                        ->join('organization_types as ot', 'ot.id = o.type_id')
                        ->where('o.status', 'active')
                        ->orderBy('o.name', 'ASC');

        if (!empty($filters['keyword'])) {
            $kw = esc($filters['keyword']);
            $builder->like('o.name', $kw)
                    ->orLike('o.description', $kw)
                    ->orLike('o.industry', $kw);
        }

        if (!empty($filters['type_id'])) {
            $builder->where('o.type_id', (int)$filters['type_id']);
        }

        if (!empty($filters['industry'])) {
            $builder->where('o.industry', esc($filters['industry']));
        }

        if (!empty($filters['is_verified'])) {
            $builder->where('o.is_verified', $filters['is_verified']);
        }

        if (!empty($filters['parent_id'])) {
            $builder->where('o.parent_id', (int)$filters['parent_id']);
        } else {
            // Show only parent organizations by default
            $builder->where('o.parent_id IS NULL', null, false);
        }

        return [
            'total'  => $builder->countAllResults(false),
            'data'   => $builder->paginate($perPage),
            'page'   => $this->pager->getCurrentPage(),
            'pages'  => ceil($builder->countAllResults(false) / $perPage),
        ];
    }

    /**
     * Get organizations managed by a user
     */
    public function getManagedByUser(int $userId)
    {
        return $this->select('o.*')
                    ->from($this->table . ' as o')
                    ->join('organization_members as om', 'om.organization_id = o.id')
                    ->where('om.user_id', $userId)
                    ->where('om.role IN ("owner", "manager")', null, false)
                    ->findAll();
    }
}
