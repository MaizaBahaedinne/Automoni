<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationMemberModel extends Model
{
    protected $table            = 'organization_members';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['organization_id', 'user_id', 'role', 'is_active', 'joined_at'];

    protected $validationRules  = [
        'organization_id' => 'required|integer|greater_than[0]',
        'user_id'         => 'required|integer|greater_than[0]',
        'role'            => 'required|in_list[owner,manager,viewer]',
    ];

    /**
     * Get members of an organization
     */
    public function getMembers(int $organizationId)
    {
        return $this->db->table('organization_members as om')
                    ->select('om.id, om.organization_id, om.user_id, om.role, om.is_active, om.joined_at, u.first_name, u.last_name, u.email, u.avatar')
                    ->join('users as u', 'u.id = om.user_id')
                    ->where('om.organization_id', $organizationId)
                    ->orderBy('om.is_active', 'DESC')
                    ->orderBy('om.joined_at', 'ASC')
                    ->get()
                    ->getResult();
    }

    /**
     * Toggle is_active for a member
     */
    public function toggleActive(int $organizationId, int $userId): ?bool
    {
        $member = $this->where('organization_id', $organizationId)
                       ->where('user_id', $userId)
                       ->first();
        if (!$member) {
            return null;
        }
        $newState = $member->is_active ? 0 : 1;
        $this->where('organization_id', $organizationId)
             ->where('user_id', $userId)
             ->update(['is_active' => $newState]);
        return (bool) $newState;
    }

    /**
     * Get user's role in an organization
     */
    public function getUserRole(int $organizationId, int $userId): ?string
    {
        $member = $this->select('role')
                       ->where('organization_id', $organizationId)
                       ->where('user_id', $userId)
                       ->first();
        return $member ? $member->role : null;
    }

    /**
     * Check if user is member of organization
     */
    public function isMember(int $organizationId, int $userId): bool
    {
        return (bool) $this->where('organization_id', $organizationId)
                           ->where('user_id', $userId)
                           ->first();
    }

    /**
     * Check if user has permission level
     */
    public function hasPermission(int $organizationId, int $userId, string $requiredRole): bool
    {
        $roleHierarchy = ['owner' => 3, 'manager' => 2, 'viewer' => 1];
        $userRole = $this->getUserRole($organizationId, $userId);

        if (!$userRole) {
            return false;
        }

        return ($roleHierarchy[$userRole] ?? 0) >= ($roleHierarchy[$requiredRole] ?? 0);
    }

    /**
     * Add member to organization
     */
    public function addMember(int $organizationId, int $userId, string $role = 'viewer'): bool
    {
        return (bool) $this->insert([
            'organization_id' => $organizationId,
            'user_id'         => $userId,
            'role'            => $role,
            'joined_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Update member role
     */
    public function updateRole(int $organizationId, int $userId, string $role): bool
    {
        return (bool) $this->where('organization_id', $organizationId)
                           ->where('user_id', $userId)
                           ->update(['role' => $role]);
    }

    /**
     * Remove member
     */
    public function removeMember(int $organizationId, int $userId): bool
    {
        return (bool) $this->where('organization_id', $organizationId)
                           ->where('user_id', $userId)
                           ->delete();
    }
}
