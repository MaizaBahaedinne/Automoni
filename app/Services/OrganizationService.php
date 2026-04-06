<?php

namespace App\Services;

use App\Models\{OrganizationModel, OrganizationMemberModel};

class OrganizationService
{
    private OrganizationModel $organizationModel;
    private OrganizationMemberModel $memberModel;

    public function __construct()
    {
        $this->organizationModel = model(OrganizationModel::class);
        $this->memberModel = model(OrganizationMemberModel::class);
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
}
