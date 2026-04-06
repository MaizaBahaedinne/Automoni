<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationSocialLinkModel extends Model
{
    protected $table            = 'organization_social_links';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['organization_id', 'platform', 'url'];

    protected $validationRules  = [
        'organization_id' => 'required|integer|greater_than[0]',
        'platform'        => 'required|in_list[facebook,twitter,linkedin,instagram,youtube,github,website]',
        'url'             => 'required|valid_url_strict|max_length[500]',
    ];

    /**
     * Get social links for an organization
     */
    public function getLinks(int $organizationId)
    {
        return $this->where('organization_id', $organizationId)->findAll();
    }

    /**
     * Get social links by platform
     */
    public function getByPlatform(int $organizationId, string $platform)
    {
        return $this->where('organization_id', $organizationId)
                    ->where('platform', $platform)
                    ->first();
    }

    /**
     * Add social link
     */
    public function addLink(int $organizationId, string $platform, string $url): bool
    {
        return (bool) $this->insert([
            'organization_id' => $organizationId,
            'platform'        => $platform,
            'url'             => $url,
        ]);
    }
}
