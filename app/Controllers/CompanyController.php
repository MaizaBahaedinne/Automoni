<?php

namespace App\Controllers;

use App\Models\{CompanyModel, JobModel, OrganizationModel};
use CodeIgniter\HTTP\RedirectResponse;

class CompanyController extends BaseController
{
    private CompanyModel $companyModel;
    private int $userId;

    public function __construct()
    {
        $this->companyModel = model(CompanyModel::class);
        $this->userId       = (int) session()->get('user_id');
    }

    public function myDashboard(): string
    {
        $company = $this->companyModel->getByUserId($this->userId);
        $orgs    = model(OrganizationModel::class)->getManagedByUser($this->userId);

        return view('company/dashboard', [
            'title'   => 'Mon espace recruteur',
            'company' => $company,
            'orgs'    => $orgs,
        ]);
    }

    public function create(): string|RedirectResponse
    {
        $company = $this->companyModel->getByUserId($this->userId);
        if ($company) {
            return redirect()->to('/company/edit');
        }
        return view('company/form', ['title' => 'Create Company Profile', 'company' => null]);
    }

    public function store(): RedirectResponse
    {
        $rules = [
            'name'        => 'required|min_length[2]|max_length[200]',
            'website'     => 'permit_empty|valid_url_strict|max_length[255]',
            'description' => 'permit_empty|max_length[5000]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = array_merge(
            $this->request->getPost(['name', 'website', 'industry', 'size', 'country', 'city', 'description', 'linkedin']),
            ['user_id' => $this->userId]
        );

        // Handle logo upload
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
            if (in_array($logo->getMimeType(), $allowed) && $logo->getSize() <= 2 * 1024 * 1024) {
                $newName    = 'logo_' . $this->userId . '_' . time() . '.' . $logo->getClientExtension();
                $dest       = WRITEPATH . 'uploads/logos/';
                if (!is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
                $logo->move($dest, $newName);
                $data['logo'] = $newName;
            }
        }

        $this->companyModel->insert($data);
        return redirect()->to('/dashboard')->with('success', 'Company profile created!');
    }

    public function edit(): string|RedirectResponse
    {
        $company = $this->companyModel->getByUserId($this->userId);
        if (!$company) {
            return redirect()->to('/company/create');
        }
        return view('company/form', ['title' => 'Edit Company Profile', 'company' => $company]);
    }

    public function update(): RedirectResponse
    {
        $company = $this->companyModel->getByUserId($this->userId);
        if (!$company) {
            return redirect()->to('/company/create');
        }

        $rules = [
            'name'    => 'required|min_length[2]|max_length[200]',
            'website' => 'permit_empty|valid_url_strict|max_length[255]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost(['name', 'website', 'industry', 'size', 'country', 'city', 'description', 'linkedin']);

        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
            if (in_array($logo->getMimeType(), $allowed) && $logo->getSize() <= 2 * 1024 * 1024) {
                // Remove old logo
                if ($company->logo) {
                    $oldPath = WRITEPATH . 'uploads/logos/' . $company->logo;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $newName = 'logo_' . $this->userId . '_' . time() . '.' . $logo->getClientExtension();
                $dest    = WRITEPATH . 'uploads/logos/';
                $logo->move($dest, $newName);
                $data['logo'] = $newName;
            }
        }

        $this->companyModel->update($company->id, $data);
        return redirect()->to('/dashboard')->with('success', 'Company profile updated.');
    }

    public function show(string $slug): string
    {
        $company = $this->companyModel->where('slug', $slug)->first();
        if (!$company) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $jobs = model(JobModel::class)->where('company_id', $company->id)
                                       ->where('status', 'active')
                                       ->orderBy('created_at', 'DESC')
                                       ->findAll();
        return view('company/show', ['title' => $company->name, 'company' => $company, 'jobs' => $jobs]);
    }
}
