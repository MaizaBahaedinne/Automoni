<?php

namespace App\Services;

use App\Models\{
    OrganizationModel,
    OrganizationMemberModel,
    OrganizationCertificationModel,
    OrganizationPartnerModel,
    OrganizationTypeModel,
};

class OrganizationService
{
    private OrganizationModel $organizationModel;
    private OrganizationMemberModel $memberModel;
    private OrganizationCertificationModel $certificationModel;
    private OrganizationPartnerModel $partnerModel;
    private OrganizationTypeModel $typeModel;

    public function __construct()
    {
        $this->organizationModel = model(OrganizationModel::class);
        $this->memberModel = model(OrganizationMemberModel::class);
        $this->certificationModel = model(OrganizationCertificationModel::class);
        $this->partnerModel = model(OrganizationPartnerModel::class);
        $this->typeModel = model(OrganizationTypeModel::class);
    }

    /**
     * Get complete organization hierarchy as tree
     * Useful for UI rendering (menus, dropdowns, tree views)
     */
    public function getHierarchyTree(int $organizationId): ?array
    {
        $org = $this->organizationModel->getHierarchy($organizationId);
        if (!$org) {
            return null;
        }
        return $this->flattenHierarchy($org);
    }

    /**
     * Flatten hierarchy for breadcrumb navigation
     */
    public function getBreadcrumbs(int $organizationId): array
    {
        $breadcrumbs = [];
        $current = $this->organizationModel->find($organizationId);

        while ($current) {
            array_unshift($breadcrumbs, $current);
            $current = $current->parent_id 
                ? $this->organizationModel->find($current->parent_id) 
                : null;
        }

        return $breadcrumbs;
    }

    /**
     * Get all descendants (all children at all levels)
     */
    public function getAllDescendants(int $organizationId): array
    {
        $descendants = [];
        $children = $this->organizationModel->getSubsidiaries($organizationId);

        foreach ($children as $child) {
            $descendants[] = $child;
            // Recursive call
            $descendants = array_merge($descendants, $this->getAllDescendants($child->id));
        }

        return $descendants;
    }

    /**
     * Count organization tree depth
     */
    public function getTreeDepth(int $organizationId): int
    {
        $depth = 1;
        $current = $this->organizationModel->find($organizationId);

        while ($current && $current->parent_id) {
            $depth++;
            $current = $this->organizationModel->find($current->parent_id);
        }

        return $depth;
    }

    /**
     * Move organization to new parent (change hierarchy)
     * @throws Exception if move would create a cycle
     */
    public function moveToParent(int $organizationId, ?int $newParentId): bool
    {
        // Prevent moving to itself or to a descendant (creates cycle)
        if ($newParentId === $organizationId) {
            throw new \Exception('Cannot move organization to itself');
        }

        if ($newParentId) {
            $descendants = $this->getAllDescendants($organizationId);
            $descendantIds = array_column($descendants, 'id');

            if (in_array($newParentId, $descendantIds)) {
                throw new \Exception('Cannot move organization to its own child');
            }
        }

        return $this->organizationModel->update($organizationId, ['parent_id' => $newParentId]);
    }

    /**
     * Handle logo upload with security
     */
    public function uploadLogo(int $organizationId, $file): ?string
    {
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload: ' . $file->getErrorString());
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Invalid file type. Allowed: JPEG, PNG, WebP, SVG');
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file->getSize() > $maxSize) {
            throw new \Exception('File too large. Maximum size: 5MB');
        }

        $fileName = 'org_' . $organizationId . '_' . time() . '.' . $file->getExtension();
        $uploadPath = WRITEPATH . 'uploads/organizations/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $fileName);

        // Remove old logo if exists
        $org = $this->organizationModel->find($organizationId);
        if ($org && $org->logo && file_exists($uploadPath . $org->logo)) {
            unlink($uploadPath . $org->logo);
        }

        // Save filename to database
        $this->organizationModel->update($organizationId, ['logo' => $fileName]);

        return $fileName;
    }

    /**
     * Get logo URL
     */
    public function getLogoUrl(?string $logo): ?string
    {
        if (!$logo) {
            return null;
        }
        return base_url('uploads/organizations/' . $logo);
    }

    /**
     * Delete logo
     */
    public function deleteLogo(int $organizationId): bool
    {
        $org = $this->organizationModel->find($organizationId);
        if (!$org || !$org->logo) {
            return false;
        }

        $uploadPath = WRITEPATH . 'uploads/organizations/';
        $filePath = $uploadPath . $org->logo;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return $this->organizationModel->update($organizationId, ['logo' => null]);
    }

    /**
     * Add member to organization
     */
    public function addMember(int $organizationId, int $userId, string $role = 'viewer'): bool
    {
        if (!$this->memberModel->isMember($organizationId, $userId)) {
            return $this->memberModel->addMember($organizationId, $userId, $role);
        }
        return false;
    }

    /**
     * Check if user can edit organization
     */
    public function canEdit(int $organizationId, int $userId): bool
    {
        return $this->memberModel->hasPermission($organizationId, $userId, 'manager');
    }

    /**
     * Check if user can manage members
     */
    public function canManageMembers(int $organizationId, int $userId): bool
    {
        return $this->memberModel->hasPermission($organizationId, $userId, 'owner');
    }

    /**
     * Get organization statistics
     */
    public function getStats(int $organizationId): array
    {
        $org = $this->organizationModel->find($organizationId);
        if (!$org) {
            return [];
        }

        $certificationsModel = model('OrganizationCertificationModel');
        $partnersModel = model('OrganizationPartnerModel');
        $socialModel = model('OrganizationSocialLinkModel');

        return [
            'members_count' => $this->memberModel->where('organization_id', $organizationId)->countAllResults(),
            'subsidiaries_count' => $this->organizationModel->where('parent_id', $organizationId)->countAllResults(),
            'descendants_count' => count($this->getAllDescendants($organizationId)),
            'certifications_count' => $certificationsModel->where('organization_id', $organizationId)->countAllResults(),
            'partners_count' => $partnersModel->where('organization_id', $organizationId)->where('is_active', true)->countAllResults(),
            'social_links_count' => $socialModel->where('organization_id', $organizationId)->countAllResults(),
        ];
    }

    /**
     * Helper method: flatten hierarchy array for JSON
     */
    private function flattenHierarchy(object $node, int $level = 0): array
    {
        $result = [];

        $node_array = (array)$node;
        $children = $node_array['children'] ?? [];
        unset($node_array['children']);

        $result[] = [
            'level' => $level,
            'node' => (object)$node_array,
        ];

        foreach ($children as $child) {
            $result = array_merge($result, $this->flattenHierarchy($child, $level + 1));
        }

        return $result;
    }

    /**
     * ─────────────────────────────────────────────────────────────────────
     * COMPLETE ORGANIZATION CREATION WITH ALL RELATIONS
     * ─────────────────────────────────────────────────────────────────────
     */

    /**
     * Create organization with all related data in a transaction
     *
     * @param array $data Organization data with optional nested relations
     * @param int $creatorId User ID creating the organization
     * @return array ['success' => bool, 'data' => object|null, 'message' => string]
     */
    public function createCompleteOrganization(array $data, int $creatorId): array
    {
        try {
            // Validation
            if (!$this->organizationModel->validate($data)) {
                return [
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $this->organizationModel->errors()),
                ];
            }

            // Extract nested relations before main insert
            $certifications = $data['certifications'] ?? [];
            $markets = $data['markets'] ?? [];
            $pricing = $data['pricing'] ?? [];
            $quality_labels = $data['quality_labels'] ?? [];
            $partners = $data['partners'] ?? [];

            unset($data['certifications'], $data['markets'], $data['pricing'], $data['quality_labels'], $data['partners']);

            // Convert array fields to JSON
            if (isset($data['markets_targeted']) && is_array($data['markets_targeted'])) {
                $data['markets_targeted'] = json_encode($data['markets_targeted']);
            }
            if (isset($data['sectors']) && is_array($data['sectors'])) {
                $data['sectors'] = json_encode($data['sectors']);
            }

            $db = \Config\Database::connect();
            $db->transBegin();

            // 1. Insert main organization
            if (!$this->organizationModel->insert($data)) {
                throw new \Exception('Failed to create organization');
            }

            $organizationId = $this->organizationModel->getInsertID();

            // 2. Add creator as owner
            $this->memberModel->addMember($organizationId, $creatorId, 'owner', isAdmin: true);

            // 3. Add certifications
            if (!empty($certifications)) {
                foreach ($certifications as $cert) {
                    $cert['organization_id'] = $organizationId;
                    $this->certificationModel->insert($cert);
                }
            }

            // 4. Add partnerships
            if (!empty($partners)) {
                foreach ($partners as $partner) {
                    $partner['organization_id'] = $organizationId;
                    $this->partnerModel->insert($partner);
                }
            }

            $db->transCommit();

            return [
                'success' => true,
                'data' => $this->getOrganizationWithRelations($organizationId),
                'message' => 'Organization created successfully',
            ];
        } catch (\Exception $e) {
            $db = \Config\Database::connect();
            $db->transRollback();

            return [
                'success' => false,
                'message' => 'Error creating organization: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get organization with all relations loaded
     */
    public function getOrganizationWithRelations(int $organizationId)
    {
        $org = $this->organizationModel->find($organizationId);
        if (!$org) {
            return null;
        }

        // Decode JSON fields
        if ($org->markets_targeted) {
            $org->markets_targeted = json_decode($org->markets_targeted, true);
        }
        if ($org->sectors) {
            $org->sectors = json_decode($org->sectors, true);
        }

        // Load relations
        $org->type = $this->typeModel->find($org->type_id);
        $org->parent = $org->parent_id ? $this->organizationModel->find($org->parent_id) : null;
        $org->certifications = $this->certificationModel->getCertifications($organizationId);
        $org->partners = $this->partnerModel->getPartners($organizationId);
        $org->members = $this->memberModel->getMembers($organizationId);

        return $org;
    }

    /**
     * Validate parent organization (prevent circular hierarchy)
     */
    public function isValidParent(int $organizationId, ?int $parentId): bool
    {
        if (!$parentId) {
            return true;
        }

        // Parent cannot be the organization itself
        if ($organizationId === $parentId) {
            return false;
        }

        // Parent cannot be a descendant of this organization
        $descendants = $this->getAllDescendants($organizationId);
        $descendantIds = array_column($descendants, 'id');

        return !in_array($parentId, $descendantIds, strict: true);
    }

    /**
     * Search for parent organization candidates
     */
    public function searchParentOrganizations(string $query, int $limit = 10): array
    {
        $query = trim($query);

        if (strlen($query) < 2) {
            return [];
        }

        return $this->organizationModel
            ->select('id, name, type_id, slug')
            ->where('status', 'active')
            ->like('name', $query)
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get organizations filtered by size
     */
    public function getBySize(string $size): array
    {
        return $this->organizationModel
            ->where('size', $size)
            ->where('status', 'active')
            ->findAll();
    }

    /**
     * Get organizations with reputation score >= threshold
     */
    public function getByReputationScore(float $minScore): array
    {
        return $this->organizationModel
            ->where('reputation_score >=', $minScore, false)
            ->where('status', 'active')
            ->orderBy('reputation_score', 'DESC')
            ->findAll();
    }

    /**
     * Get top organizations by reputation
     */
    public function getTopOrganizations(int $limit = 10): array
    {
        return $this->organizationModel
            ->where('status', 'active')
            ->where('reputation_score IS NOT NULL', null, false)
            ->orderBy('reputation_score', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get organizations operating in specific country
     */
    public function getOrganizationsByCountry(string $countryCode): array
    {
        return $this->marketModel
            ->where('country_code', strtoupper($countryCode))
            ->where('is_active', 1)
            ->select('DISTINCT organization_id')
            ->findAll();
    }

    /**
     * Get count statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->organizationModel->countAllResults(),
            'active' => $this->organizationModel->where('status', 'active')->countAllResults(),
            'by_size' => $this->organizationModel
                ->select('size, COUNT(*) as count')
                ->where('status', 'active')
                ->groupBy('size')
                ->findAll(),
            'by_type' => $this->organizationModel
                ->select('type_id, COUNT(*) as count')
                ->where('status', 'active')
                ->groupBy('type_id')
                ->findAll(),
        ];
    }

}
