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
        'status', 'is_verified', 'size', 'markets_targeted', 'budget_annual', 'revenue_annual', 
        'reputation_score',
    ];

    // Validation is handled by the controller — no model-level rules needed.
    protected $validationRules  = [];

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
        return $this->select('organizations.*, ot.name as type_name, ot.slug as type_slug')
                    ->join('organization_types as ot', 'ot.id = organizations.type_id', 'left')
                    ->where('organizations.id', $id)
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
        $builder = $this->select('organizations.*, ot.name as type_name')
                        ->join('organization_types as ot', 'ot.id = organizations.type_id', 'left')
                        ->where('organizations.status', 'active')
                        ->orderBy('organizations.name', 'ASC');

        if (!empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $builder->groupStart()
                    ->like('organizations.name', $kw)
                    ->orLike('organizations.description', $kw)
                    ->orLike('organizations.industry', $kw)
                    ->groupEnd();
        }

        if (!empty($filters['type_id'])) {
            $builder->where('organizations.type_id', (int)$filters['type_id']);
        }

        if (!empty($filters['industry'])) {
            $builder->where('organizations.industry', $filters['industry']);
        }

        if (!empty($filters['country_code'])) {
            $builder->where('organizations.country_code', strtoupper($filters['country_code']));
        }

        if (!empty($filters['is_verified'])) {
            $builder->where('organizations.is_verified', $filters['is_verified']);
        }

        if (!empty($filters['parent_id'])) {
            $builder->where('organizations.parent_id', (int)$filters['parent_id']);
        } else {
            // Show only parent organizations by default
            $builder->where('organizations.parent_id IS NULL', null, false);
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
        return $this->select('organizations.*')
                    ->join('organization_members as om', 'om.organization_id = organizations.id')
                    ->where('om.user_id', $userId)
                    ->where('om.role IN ("owner", "manager")', null, false)
                    ->findAll();
    }
}
