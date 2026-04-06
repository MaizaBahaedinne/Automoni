<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationCertificationModel extends Model
{
    protected $table            = 'organization_certifications';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['organization_id', 'name', 'issuer', 'issued_at', 'expires_at', 'url'];

    protected $validationRules  = [
        'organization_id' => 'required|integer|greater_than[0]',
        'name'            => 'required|min_length[3]|max_length[255]',
        'issuer'          => 'max_length[255]',
        'issued_at'       => 'valid_date[Y-m-d]',
        'expires_at'      => 'valid_date[Y-m-d]',
        'url'             => 'valid_url_strict',
    ];

    /**
     * Get certifications for an organization
     */
    public function getCertifications(int $organizationId)
    {
        return $this->where('organization_id', $organizationId)
                    ->orderBy('issued_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get active certifications (not expired)
     */
    public function getActiveCertifications(int $organizationId)
    {
        return $this->where('organization_id', $organizationId)
                    ->where('expires_at >= CURDATE() OR expires_at IS NULL', null, false)
                    ->orderBy('issued_at', 'DESC')
                    ->findAll();
    }

    /**
     * Add certification
     */
    public function addCertification(int $organizationId, array $data): bool
    {
        $data['organization_id'] = $organizationId;
        return (bool) $this->insert($data);
    }
}
