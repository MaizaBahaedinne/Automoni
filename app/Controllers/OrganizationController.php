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
            'country_code' => $this->request->getVar('country_code'),
            'is_verified' => $this->request->getVar('is_verified'),
        ];

        $result = $this->organizationModel->search($filters, 12);

        // Sectors list
        $sectors = [
            'Technologie', 'Finance', 'Santé', 'Industrie', 'Commerce de détail', 'Immobilier',
            'Énergie', 'Transport', 'Éducation', 'Médias', 'Hôtellerie', 'Non-profit',
            'Gouvernement', 'Services professionnels', 'Agriculture', 'Télécommunications', 'Utilitaires', 'Conseil'
        ];

        // Countries
        $countries = [
            'DZ' => 'Algérie',
            'TN' => 'Tunisie',
            'MA' => 'Maroc',
            'FR' => 'France',
            'US' => 'États-Unis',
            'GB' => 'Royaume-Uni',
            'ES' => 'Espagne',
            'IT' => 'Italie',
            'CH' => 'Suisse',
            'CA' => 'Canada',
            'BE' => 'Belgique',
            'DE' => 'Allemagne',
            'NL' => 'Pays-Bas',
            'SE' => 'Suède',
            'NO' => 'Norvège',
        ];

        return view('organizations/index', [
            'title' => 'Organizations',
            'organizations' => $result['data'],
            'pager' => $this->organizationModel->pager,
            'types' => $this->typeModel->findAll(),
            'sectors' => $sectors,
            'countries' => $countries,
            'countriesWithFlags' => $this->getCountriesWithFlags(),
            'filters' => $filters,
        ]);
    }

    /**
     * Get countries with their flag emojis
     */
    private function getCountriesWithFlags(): array
    {
        return [
            'DZ' => ['name' => 'Algérie', 'flag' => '🇩🇿'],
            'TN' => ['name' => 'Tunisie', 'flag' => '🇹🇳'],
            'MA' => ['name' => 'Maroc', 'flag' => '🇲🇦'],
            'FR' => ['name' => 'France', 'flag' => '🇫🇷'],
            'US' => ['name' => 'États-Unis', 'flag' => '🇺🇸'],
            'GB' => ['name' => 'Royaume-Uni', 'flag' => '🇬🇧'],
            'ES' => ['name' => 'Espagne', 'flag' => '🇪🇸'],
            'IT' => ['name' => 'Italie', 'flag' => '🇮🇹'],
            'CH' => ['name' => 'Suisse', 'flag' => '🇨🇭'],
            'CA' => ['name' => 'Canada', 'flag' => '🇨🇦'],
            'BE' => ['name' => 'Belgique', 'flag' => '🇧🇪'],
            'DE' => ['name' => 'Allemagne', 'flag' => '🇩🇪'],
            'NL' => ['name' => 'Pays-Bas', 'flag' => '🇳🇱'],
            'SE' => ['name' => 'Suède', 'flag' => '🇸🇪'],
            'NO' => ['name' => 'Norvège', 'flag' => '🇳🇴'],
        ];
    }

    /**
     * Get country flag by code
     */
    private function getCountryFlag(string $countryCode): string
    {
        $countriesWithFlags = $this->getCountriesWithFlags();
        return $countriesWithFlags[strtoupper($countryCode)]['flag'] ?? '🌍';
    }

    /**
     * Get country name by code
     */
    private function getCountryName(string $countryCode): string
    {
        $countriesWithFlags = $this->getCountriesWithFlags();
        return $countriesWithFlags[strtoupper($countryCode)]['name'] ?? $countryCode;
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
            'country_code' => $this->request->getVar('country_code'),
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

        $orgJobs = model(\App\Models\JobModel::class)
                        ->where('organization_id', $id)
                        ->where('status', 'active')
                        ->orderBy('created_at', 'DESC')
                        ->findAll();

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
            'org_jobs' => $orgJobs,
            'can_edit' => $this->organizationService->canEdit($id, $this->userId),
            'can_manage' => $this->organizationService->canManageMembers($id, $this->userId),
            'is_member' => $this->userId ? $this->memberModel->isMember($id, $this->userId) : false,
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
            'title'         => 'Create Organization',
            'organization'  => null,
            'logo_url'      => null,
            'social_links'  => [],
            'types'         => $this->typeModel->findAll(),
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
            'type_id'            => 'required|integer|greater_than[0]',
            'name'               => 'required|min_length[3]|max_length[255]',
            'legal_name'         => 'permit_empty|max_length[255]',
            'street_address'     => 'required|min_length[5]|max_length[255]',
            'city'               => 'required|min_length[2]|max_length[100]',
            'postal_code'        => 'required|min_length[2]|max_length[20]',
            'country_code'       => 'required|regex_match[/^[A-Z]{2}$/]',
            'email'              => 'required|valid_email',
            'phone_number'       => 'required|regex_match[/^[0-9\s\-\(\)]+$/]|min_length[7]|max_length[15]',
            'phone_country_code' => 'permit_empty|regex_match[/^[\+]?[0-9]{1,5}$/]',
            'website'            => 'required|valid_url_strict',
            'tax_id'             => 'permit_empty|max_length[50]',
            'latitude'           => 'permit_empty|numeric|greater_than_equal_to[-90]|less_than_equal_to[90]',
            'longitude'          => 'permit_empty|numeric|greater_than_equal_to[-180]|less_than_equal_to[180]',
            'employee_count'     => 'permit_empty|integer|greater_than_equal_to[0]',
            'founded_at'         => 'permit_empty|valid_date[Y-m-d]',
            'industry'           => 'permit_empty|max_length[100]',
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

        $countryCode = strtoupper($this->request->getPost('country_code'));
        $countryName = $this->getCountryName($countryCode);

        $data = [
            'type_id'            => (int) $this->request->getPost('type_id'),
            'name'               => $this->request->getPost('name'),
            'legal_name'         => $this->request->getPost('legal_name') ?: null,
            'description'        => $this->request->getPost('description') ?: null,
            'parent_id'          => ($this->request->getPost('parent_id') ?: null),
            'street_address'     => $this->request->getPost('street_address'),
            'city'               => $this->request->getPost('city'),
            'postal_code'        => $this->request->getPost('postal_code'),
            'country_code'       => $countryCode,
            'country'            => $countryName,
            'email'              => $this->request->getPost('email'),
            'industry'           => $this->request->getPost('industry') ?: null,
            'phone_country_code' => $this->request->getPost('phone_country_code') ?: null,
            'phone_number'       => $this->request->getPost('phone_number'),
            'phone'              => trim($this->request->getPost('phone_country_code') . ' ' . $this->request->getPost('phone_number')),
            'website'            => $this->request->getPost('website'),
            'tax_id'             => $this->request->getPost('tax_id') ?: null,
            'latitude'           => $this->request->getPost('latitude') !== '' ? $this->request->getPost('latitude') : null,
            'longitude'          => $this->request->getPost('longitude') !== '' ? $this->request->getPost('longitude') : null,
            'employee_count'     => $this->request->getPost('employee_count') !== '' ? (int) $this->request->getPost('employee_count') : null,
            'founded_at'         => $this->request->getPost('founded_at') ?: null,
            'status'             => 'active',
        ];

        $orgId = $this->organizationModel->skipValidation(true)->insert($data);
        if (!$orgId) {
            log_message('error', 'Organization insert failed. Data: ' . json_encode($data) . '. Errors: ' . json_encode($this->organizationModel->errors()));
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create organization. Please try again.');
        }

        // Add creator as owner
        $this->memberModel->addMember($orgId, $this->userId, 'owner');

        // Handle logo upload
        $logoFile = $this->request->getFile('logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
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
        $organization = $this->organizationModel->withDeleted()->find($id);
        if (!$organization) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $isAdmin = session()->get('user_role') === 'admin';
        if (!$isAdmin && !$this->organizationService->canEdit($id, $this->userId)) {
            return redirect()->to(base_url("organizations/$id"))->with('error', 'Vous n\'avez pas la permission de modifier cette organisation.');
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
        $organization = $this->organizationModel->withDeleted()->find($id);
        if (!$organization) {
            return redirect()->back()->with('error', 'Organisation introuvable.');
        }

        $isAdmin = session()->get('user_role') === 'admin';
        if (!$isAdmin && !$this->organizationService->canEdit($id, $this->userId)) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de modifier cette organisation.');
        }

        $data = [
            'name'           => $this->request->getPost('name') ?: $organization->name,
            'legal_name'     => $this->request->getPost('legal_name') ?: null,
            'description'    => $this->request->getPost('description') ?: null,
            'parent_id'      => $this->request->getPost('parent_id') ?: null,
            'type_id'        => (int) ($this->request->getPost('type_id') ?: $organization->type_id),
            'website'        => $this->request->getPost('website') ?: null,
            'email'          => $this->request->getPost('email') ?: null,
            'phone'          => $this->request->getPost('phone') ?: null,
            'address'        => $this->request->getPost('address') ?: null,
            'latitude'       => $this->request->getPost('latitude') !== '' ? $this->request->getPost('latitude') : null,
            'longitude'      => $this->request->getPost('longitude') !== '' ? $this->request->getPost('longitude') : null,
            'industry'       => $this->request->getPost('industry') ?: null,
            'employee_count' => $this->request->getPost('employee_count') !== '' ? (int) $this->request->getPost('employee_count') : null,
            'founded_at'     => $this->request->getPost('founded_at') ?: null,
            'tax_id'         => $this->request->getPost('tax_id') ?: null,
            'status'         => $this->request->getPost('status') ?: $organization->status,
        ];

        $this->organizationModel->skipValidation(true)->update($id, $data);

        // Handle logo upload if provided
        $logoFile = $this->request->getFile('logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            try {
                $this->organizationService->uploadLogo($id, $logoFile);
            } catch (\Exception $e) {
                log_message('error', 'Logo upload failed: ' . $e->getMessage());
            }
        }

        // Handle social links
        $socialModel = model('OrganizationSocialLinkModel');
        $socialModel->where('organization_id', $id)->delete();
        for ($i = 0; $i < 20; $i++) {
            $platform = $this->request->getPost("social_platform_$i");
            $url      = $this->request->getPost("social_url_$i");
            if ($platform && $url) {
                $socialModel->addLink($id, $platform, $url);
            }
        }

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Organization updated successfully',
            ]);
        }

        return redirect()->to("/organizations/$id")->with('success', 'Organisation mise à jour avec succès.');
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
            ->select('organizations.id, organizations.name, organizations.logo, organization_types.name as type_name')
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
