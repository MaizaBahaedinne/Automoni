<?php

namespace App\Controllers;

use App\Models\{OrganizationMemberModel, OrganizationModel};
use CodeIgniter\HTTP\ResponseInterface;

class OrganizationMemberController extends BaseController
{
    private OrganizationMemberModel $memberModel;
    private OrganizationModel $organizationModel;
    private int $userId;

    public function __construct()
    {
        $this->memberModel = model(OrganizationMemberModel::class);
        $this->organizationModel = model(OrganizationModel::class);
        $this->userId = (int) session()->get('user_id') ?? 0;
    }

    /**
     * GET /organizations/:id/members
     * List members of an organization
     */
    public function index(int $id)
    {
        $organization = $this->organizationModel->find($id);
        if (!$organization) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Not found']);
        }

        $members = $this->memberModel->getMembers($id);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $members,
        ]);
    }

    /**
     * POST /organizations/:id/members
     * Add member to organization (owner only)
     */
    public function add(int $id)
    {
        $organization = $this->organizationModel->find($id);
        if (!$organization) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Not found']);
        }

        // Check permission
        if (!$this->memberModel->hasPermission($id, $this->userId, 'owner')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $rules = [
            'user_id' => 'required|integer',
            'role' => 'required|in_list[owner,manager,viewer]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $userId = (int) $this->request->getPost('user_id');
        $role = $this->request->getPost('role');

        // Check if user already member
        if ($this->memberModel->isMember($id, $userId)) {
            return $this->response->setStatusCode(409)->setJSON([
                'status' => 'error',
                'message' => 'User is already a member',
            ]);
        }

        $this->memberModel->addMember($id, $userId, $role);

        return $this->response->setStatusCode(201)->setJSON([
            'status' => 'success',
            'message' => 'Member added successfully',
        ]);
    }

    /**
     * POST /organizations/:id/members/:userId/role
     * Update member role (owner only)
     */
    public function updateRole(int $id, int $userId)
    {
        // Check permission
        if (!$this->memberModel->hasPermission($id, $this->userId, 'owner')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        if (!$this->memberModel->isMember($id, $userId)) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Member not found']);
        }

        $role = $this->request->getPost('role');
        if (!in_array($role, ['owner', 'manager', 'viewer'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Invalid role',
            ]);
        }

        $this->memberModel->updateRole($id, $userId, $role);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Member role updated',
        ]);
    }

    /**
     * DELETE /organizations/:id/members/:userId
     * Remove member (owner only)
     */
    public function remove(int $id, int $userId)
    {
        // Check permission
        if (!$this->memberModel->hasPermission($id, $this->userId, 'owner')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        if (!$this->memberModel->isMember($id, $userId)) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Member not found']);
        }

        $this->memberModel->removeMember($id, $userId);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Member removed',
        ]);
    }

    /**
     * GET /organizations/:id/members/search-users?q=email
     * Search users by email (owner only, for add-member modal)
     */
    public function searchUsers(int $id)
    {
        if (!$this->memberModel->hasPermission($id, $this->userId, 'owner')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $q = trim($this->request->getGet('q') ?? '');
        if (strlen($q) < 2) {
            return $this->response->setJSON(['status' => 'success', 'data' => []]);
        }

        $users = db_connect()
            ->table('users')
            ->select('id, first_name, last_name, email, avatar')
            ->like('email', $q)
            ->orLike('first_name', $q)
            ->orLike('last_name', $q)
            ->where('deleted_at IS NULL')
            ->limit(8)
            ->get()
            ->getResult();

        return $this->response->setJSON(['status' => 'success', 'data' => $users]);
    }
}
