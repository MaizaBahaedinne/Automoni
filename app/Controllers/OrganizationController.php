<?php

namespace App\Controllers;

use App\Models\{OrganizationModel, OrganizationTypeModel, OrganizationMemberModel};
use App\Services\OrganizationService;
use CodeIgniter\HTTP\ResponseInterface;

class OrganizationController extends BaseController
{
    private OrganizationModel $organizationModel;
    private OrganizationTypeModel $typeModel;
    private OrganizationMemberModel $memberModel;
    private OrganizationService $organizationService;
    private int $userId;

    public function __construct()
    {
        $this->organizationModel = model(OrganizationModel::class);
        $this->typeModel = model(OrganizationTypeModel::class);
        $this->memberModel = model(OrganizationMemberModel::class);
        $this->organizationService = new OrganizationService();
        $this->userId = (int) (session()->get('user_id') ?? 0);
    }

    /**
     * GET /organizations
     * List all organizations with filtering and pagination
     */
    public function index()
    {
        if ($this->request->getHeaderLine('accept') === 'application/json') {
            return $this->listJson();
        }

        $page = $this->request->getVar('page') ?? 1;
        $filters = [
            'keyword'    => $this->request->getVar('keyword'),
            'type_id'    => $this->request->getVar('type_id'),
            'industry'   => $this->request->getVar('industry'),
            'is_verified' => $this->request->getVar('is_verified'),
        ];

        $result = $this->organizationModel->search($filters, 12);

        return view('organizations/index', [
            'title' => 'Organizations',
            'organizations' => $result['data'],
            'pager' => $this->organizationModel->pager,
            'types' => $this->typeModel->findAll(),
            'filters' => $filters,
        ]);
    }

    /**
     * JSON API: List organizations
     */
    private function listJson()
    {
        $filters = [
            'keyword'    => $this->request->getVar('keyword'),
            'type_id'    => $this->request->getVar('type_id'),
            'industry'   => $this->request->getVar('industry'),
            'is_verified' => $this->request->getVar('is_verified'),
            'parent_id'  => $this->request->getVar('parent_id'),
        ];

        $perPage = (int) $this->request->getVar('per_page') ?? 15;
        $result = $this->organizationModel->search($filters, $perPage);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $perPage,
                'current_page' => $result['page'],
                'total_pages' => $result['pages'],
            ],
        ]);
    }

    /**
     * GET /organizations/:id
     * Show organization details
     */
    public function show(int $id)
    {
        $organization = $this->organizationModel->getWithType($id);
        if (!$organization) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getHeaderLine('accept') === 'application/json') {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $this->enrichOrganizationData($organization),
            ]);
        }

        $members = $this->memberModel->getMembers($id);
        $subsidiaries = $this->organizationModel->getSubsidiaries($id);
        $breadcrumbs = $this->organizationService->getBreadcrumbs($id);
        $stats = $this->organizationService->getStats($id);

        $socialModel = model('OrganizationSocialLinkModel');
        $certModel = model('OrganizationCertificationModel');
        $partnerModel = model('OrganizationPartnerModel');

        return view('organizations/show', [
            'title' => $organization->name,
            'organization' => $organization,
            'logo_url' => $this->organizationService->getLogoUrl($organization->logo),
            'members' => $members,
            'subsidiaries' => $subsidiaries,
            'breadcrumbs' => $breadcrumbs,
            'stats' => $stats,
            'social_links' => $socialModel->getLinks($id),
            'certifications' => $certModel->getCertifications($id),
            'partners' => $partnerModel->getPartners($id),
            'can_edit' => $this->organizationService->canEdit($id, $this->userId),
            'can_manage' => $this->organizationService->canManageMembers($id, $this->userId),
        ]);
    }

    /**
     * GET /organizations/:id/hierarchy
     * Get full hierarchy tree
     */
    public function hierarchy(int $id)
    {
        $hierarchy = $this->organizationService->getHierarchyTree($id);
        if (!$hierarchy) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Organization not found',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $hierarchy,
        ]);
    }

    /**
     * GET /organizations/create
     * Show create form (authenticated users only)
     */
    public function create()
    {
        if (!$this->userId) {
            return redirect()->to('/login');
        }

        return view('organizations/create_enhanced', [
            'title' => 'Create Organization',
            'organization' => null,
            'logo_url' => null,
            'social_links' => [],
            'organizations' => $this->organizationModel->where('status', 'active')->findAll(),
        ]);
    }

    /**
     * POST /organizations
     * Create new organization
     */
    public function store()
    {
        if (!$this->userId) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthenticated']);
        }

        $rules = [
            'type_id' => 'required|in_list[1,2,3,4]',
            'name' => 'required|min_length[3]|max_length[255]',
            'legal_name' => 'max_length[255]',
            'street_address' => 'required|min_length[5]|max_length[255]',
            'city' => 'required|min_length[2]|max_length[100]',
            'postal_code' => 'required|min_length[2]|max_length[20]',
            'country_code' => 'required|regex_match[/^[A-Z]{2}$/]',
            'email' => 'required|valid_email',
            'phone_number' => 'required|regex_match[/^[0-9\s\-\(\)]+$/]|min_length[7]|max_length[15]',
            'phone_country_code' => 'regex_match[/^[\+]?[0-9]{1,5}$/]',
            'website' => 'required|valid_url_strict',
            'tax_id' => 'max_length[50]',
            'employee_count' => 'integer|greater_than_equal_to[0]',
            'founded_at' => 'valid_date[Y-m-d]',
            'size' => 'in_list[startup,pme,grande_entreprise]',
            'markets_targeted.*' => 'in_list[local,international]',
        ];

        if (!$this->validate($rules)) {
            if ($this->wantsJson()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'type_id'              => (int) $this->request->getPost('type_id'),
            'name'                 => $this->request->getPost('name'),
            'legal_name'           => $this->request->getPost('legal_name'),
            'description'          => $this->request->getPost('description'),
            'parent_id'            => $this->request->getPost('parent_id') ?? null,
            'street_address'       => $this->request->getPost('street_address'),
            'city'                 => $this->request->getPost('city'),
            'postal_code'          => $this->request->getPost('postal_code'),
            'country_code'         => strtoupper($this->request->getPost('country_code')),
            'email'                => $this->request->getPost('email'),
            'phone_country_code'   => $this->request->getPost('phone_country_code'),
            'phone_number'         => $this->request->getPost('phone_number'),
            'phone'                => $this->request->getPost('phone_country_code') . ' ' . $this->request->getPost('phone_number'),
            'website'              => $this->request->getPost('website'),
            'tax_id'               => $this->request->getPost('tax_id'),
            'industry'             => $this->request->getPost('industry'),
            'size'                 => $this->request->getPost('size'),
            'markets_targeted'     => json_encode($this->request->getPost('markets_targeted') ?? []),
            'employee_count'       => $this->request->getPost('employee_count'),
            'founded_at'           => $this->request->getPost('founded_at'),
            'status'               => 'active',
        ];

        $orgId = $this->organizationModel->insert($data);

        // Add creator as owner
        $this->memberModel->addMember($orgId, $this->userId, 'owner');

        // Handle logo upload
        if ($logoFile = $this->request->getFile('logo')) {
            try {
                $this->organizationService->uploadLogo($orgId, $logoFile);
            } catch (\Exception $e) {
                log_message('error', 'Logo upload failed: ' . $e->getMessage());
            }
        }

        // Handle social links
        for ($i = 0; $i < 10; $i++) {
            $platform = $this->request->getPost("social_platform_$i");
            $url = $this->request->getPost("social_url_$i");

            if ($platform && $url) {
                $socialModel = model('OrganizationSocialLinkModel');
                $socialModel->addLink($orgId, $platform, $url);
            }
        }

        if ($this->wantsJson()) {
            return $this->response->setStatusCode(201)->setJSON([
                'status' => 'success',
                'message' => 'Organization created successfully',
                'data' => ['id' => $orgId],
            ]);
        }

        return redirect()->to("/organizations/$orgId")->with('success', 'Organization created successfully');
    }

    /**
     * GET /organizations/:id/edit
     * Show edit form
     */
    public function edit(int $id)
    {
        $organization = $this->organizationModel->find($id);
        if (!$organization) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (!$this->organizationService->canEdit($id, $this->userId)) {
            return redirect()->to("/organizations/$id")->with('error', 'Unauthorized');
        }

        $socialModel = model('OrganizationSocialLinkModel');

        return view('organizations/form', [
            'title' => 'Edit Organization',
            'organization' => $organization,
            'logo_url' => $this->organizationService->getLogoUrl($organization->logo),
            'types' => $this->typeModel->findAll(),
            'organizations' => $this->organizationModel->where('status', 'active')->where('id !=', $id)->findAll(),
            'social_links' => $socialModel->getLinks($id),
        ]);
    }

    /**
     * PUT/POST /organizations/:id/update
     * Update organization
     */
    public function update(int $id)
    {
        $organization = $this->organizationModel->find($id);
        if (!$organization) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Not found']);
        }

        if (!$this->organizationService->canEdit($id, $this->userId)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $data = [
            'name'          => $this->request->getPost('name') ?? $organization->name,
            'description'   => $this->request->getPost('description'),
            'parent_id'     => $this->request->getPost('parent_id'),
            'type_id'       => $this->request->getPost('type_id') ?? $organization->type_id,
            'website'       => $this->request->getPost('website'),
            'email'         => $this->request->getPost('email'),
            'phone'         => $this->request->getPost('phone'),
            'address'       => $this->request->getPost('address'),
            'latitude'      => $this->request->getPost('latitude'),
            'longitude'     => $this->request->getPost('longitude'),
            'industry'      => $this->request->getPost('industry'),
            'employee_count' => $this->request->getPost('employee_count'),
            'founded_at'    => $this->request->getPost('founded_at'),
            'status'        => $this->request->getPost('status') ?? $organization->status,
        ];

        $this->organizationModel->update($id, $data);

        // Handle logo upload if provided
        if ($logoFile = $this->request->getFile('logo')) {
            if ($logoFile->isValid()) {
                try {
                    $this->organizationService->uploadLogo($id, $logoFile);
                } catch (\Exception $e) {
                    log_message('error', 'Logo upload failed: ' . $e->getMessage());
                }
            }
        }

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Organization updated successfully',
            ]);
        }

        return redirect()->to("/organizations/$id")->with('success', 'Organization updated successfully');
    }

    /**
     * GET /api/organizations/search
     * Search organizations (for parent organization selection)
     */
    public function search()
    {
        $query = $this->request->getVar('q');
        
        if (strlen($query) < 2) {
            return $this->response->setJSON([]);
        }

        $organizations = $this->organizationModel
            ->select('organizations.id, organizations.name, organization_types.name as type_name')
            ->join('organization_types', 'organization_types.id = organizations.type_id', 'left')
            ->like('organizations.name', $query)
            ->where('organizations.status', 'active')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($organizations);
    }

    /**
     * DELETE /organizations/:id
     * Delete organization (soft delete)
     */
    public function delete(int $id)
    {
        $organization = $this->organizationModel->find($id);
        if (!$organization) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Not found']);
        }

        if (!$this->organizationService->canManageMembers($id, $this->userId)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $this->organizationModel->delete($id);

        if ($this->wantsJson()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Organization deleted']);
        }

        return redirect()->to('/organizations')->with('success', 'Organization deleted');
    }

    /**
     * Helper: Enrich organization data with related info
     */
    private function enrichOrganizationData(object $org): object
    {
        $org->logo_url = $this->organizationService->getLogoUrl($org->logo);
        $org->stats = $this->organizationService->getStats($org->id);

        $socialModel = model('OrganizationSocialLinkModel');
        $org->social_links = $socialModel->getLinks($org->id);

        return $org;
    }

    /**
     * Check if client wants JSON response
     */
    private function wantsJson(): bool
    {
        return $this->request->getHeaderLine('accept') === 'application/json' ||
               $this->request->getPost('_format') === 'json';
    }
}
