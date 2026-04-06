<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationPartnerModel extends Model
{
    protected $table            = 'organization_partners';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['organization_id', 'partner_id', 'partnership_type', 'description', 'started_at', 'ended_at', 'is_active'];

    protected $validationRules  = [
        'organization_id' => 'required|integer|greater_than[0]',
        'partner_id'      => 'required|integer|greater_than[0]',
        'partnership_type' => 'max_length[100]',
        'started_at'      => 'valid_date[Y-m-d]',
        'ended_at'        => 'valid_date[Y-m-d]',
    ];

    /**
     * Get partners of an organization
     */
    public function getPartners(int $organizationId, bool $activeOnly = true)
    {
        $builder = $this->select('op.*, o.name as partner_name, o.logo, o.website')
                        ->from($this->table . ' as op')
                        ->join('organizations as o', 'o.id = op.partner_id')
                        ->where('op.organization_id', $organizationId);

        if ($activeOnly) {
            $builder->where('op.is_active', true);
        }

        return $builder->findAll();
    }

    /**
     * Get organizations that are partnered with this one (reverse relationship)
     */
    public function getPartneredWith(int $organizationId, bool $activeOnly = true)
    {
        $builder = $this->select('op.*, o.name as organization_name, o.logo, o.website')
                        ->from($this->table . ' as op')
                        ->join('organizations as o', 'o.id = op.organization_id')
                        ->where('op.partner_id', $organizationId);

        if ($activeOnly) {
            $builder->where('op.is_active', true);
        }

        return $builder->findAll();
    }

    /**
     * Create partnership
     */
    public function createPartnership(int $orgId1, int $orgId2, string $type = null, string $description = null): bool
    {
        return (bool) $this->insert([
            'organization_id' => $orgId1,
            'partner_id'      => $orgId2,
            'partnership_type' => $type,
            'description'     => $description,
            'started_at'      => date('Y-m-d'),
            'is_active'       => true,
        ]);
    }

    /**
     * Remove partnership
     */
    public function breakPartnership(int $orgId1, int $orgId2): bool
    {
        return (bool) $this->where('organization_id', $orgId1)
                           ->where('partner_id', $orgId2)
                           ->update(['is_active' => false, 'ended_at' => date('Y-m-d')]);
    }
}
