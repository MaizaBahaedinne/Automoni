<?php

namespace App\Controllers;

use App\Models\{JobModel, CompanyModel, ApplicationModel, JobAlertModel, UserModel};
use App\Libraries\AlertMailer;
use CodeIgniter\HTTP\RedirectResponse;

class JobController extends BaseController
{
    private JobModel $jobModel;

    public function __construct()
    {
        $this->jobModel = model(JobModel::class);
    }

    // ─── Public: List & Search ───────────────────────────────────────────

    public function index(): string
    {
        $filters = [
            'keyword'          => $this->request->getGet('keyword'),
            'location'         => $this->request->getGet('location'),
            'contract_type'    => $this->request->getGet('contract_type'),
            'remote'           => $this->request->getGet('remote'),
            'experience_level' => $this->request->getGet('experience_level'),
        ];

        $jobs  = $this->jobModel->search($filters, 12);
        $pager = $this->jobModel->pager;

        return view('jobs/index', [
            'title'   => 'Find a Job',
            'jobs'    => $jobs,
            'pager'   => $pager,
            'filters' => $filters,
        ]);
    }

    public function show(string $slug): string
    {
        $job = $this->jobModel
                    ->select('jobs.*, companies.name as company_name, companies.logo as company_logo,
                              companies.website as company_website, companies.description as company_description,
                              companies.city as company_city, companies.country as company_country')
                    ->join('companies', 'companies.id = jobs.company_id')
                    ->where('jobs.slug', $slug)
                    ->where('jobs.status', 'active')
                    ->first();

        if (!$job) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->jobModel->incrementViews($job->id);

        $userId     = session()->get('user_id');
        $hasApplied = $userId ? model(ApplicationModel::class)->hasApplied($userId, $job->id) : false;

        // Fetch required skills for this job
        $db         = \Config\Database::connect();
        $jobSkills  = $db->table('job_skills')->where('job_id', $job->id)->get()->getResultArray();

        return view('jobs/show', [
            'title'      => $job->title,
            'job'        => $job,
            'hasApplied' => $hasApplied,
            'jobSkills'  => $jobSkills,
        ]);
    }

    // ─── Recruiter: Post a Job ───────────────────────────────────────────

    public function create(): string
    {
        $company = model(CompanyModel::class)->getByUserId(session()->get('user_id'));
        if (!$company) {
            return redirect()->to('/company/create')->with('error', 'You must create a company profile first.');
        }
        return view('jobs/create', ['title' => 'Post a Job', 'company' => $company]);
    }

    public function store(): RedirectResponse
    {
        $rules = [
            'title'         => 'required|min_length[5]|max_length[255]',
            'description'   => 'required|min_length[50]',
            'contract_type' => 'required|in_list[CDI,CDD,Freelance,Internship,PartTime]',
            'location'      => 'permit_empty|max_length[200]',
            'salary_min'    => 'permit_empty|integer',
            'salary_max'    => 'permit_empty|integer',
            'expires_at'    => 'permit_empty|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId  = session()->get('user_id');
        $company = model(CompanyModel::class)->getByUserId($userId);
        if (!$company) {
            return redirect()->to('/company/create')->with('error', 'Company profile required.');
        }

        $data = array_merge(
            $this->request->getPost([
                'title', 'description', 'requirements', 'benefits',
                'contract_type', 'location', 'remote', 'salary_min', 'salary_max',
                'salary_currency', 'experience_level', 'expires_at',
            ]),
            [
                'company_id' => $company->id,
                'user_id'    => $userId,
                'status'     => 'active',
            ]
        );

        $jobId = $this->jobModel->insert($data);

        // Save job skills
        $rawSkills = $this->request->getPost('skills');
        if ($rawSkills) {
            $this->saveJobSkills($jobId, $rawSkills);
        }

        // Fire job alerts
        $job = $this->jobModel->find($jobId);
        $this->dispatchAlerts($job);

        return redirect()->to('/jobs/' . $data['slug'] ?? '/dashboard')
                         ->with('success', 'Job posted successfully!');
    }

    public function edit(int $id): string
    {
        $job = $this->ownerJob($id);
        $db  = \Config\Database::connect();
        $jobSkills = $db->table('job_skills')->where('job_id', $id)->get()->getResultArray();
        $skillsList = implode(', ', array_column($jobSkills, 'skill_name'));
        return view('jobs/edit', ['title' => 'Edit Job', 'job' => $job, 'skillsList' => $skillsList]);
    }

    public function update(int $id): RedirectResponse
    {
        $job = $this->ownerJob($id);

        $rules = [
            'title'         => 'required|min_length[5]|max_length[255]',
            'description'   => 'required|min_length[50]',
            'contract_type' => 'required|in_list[CDI,CDD,Freelance,Internship,PartTime]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost([
            'title', 'description', 'requirements', 'benefits',
            'contract_type', 'location', 'remote', 'salary_min', 'salary_max',
            'salary_currency', 'experience_level', 'status', 'expires_at',
        ]);
        $this->jobModel->update($id, $data);

        // Sync job skills
        $db = \Config\Database::connect();
        $db->table('job_skills')->where('job_id', $id)->delete();
        $rawSkills = $this->request->getPost('skills');
        if ($rawSkills) {
            $this->saveJobSkills($id, $rawSkills);
        }

        return redirect()->to('/dashboard')->with('success', 'Job updated.');
    }

    public function delete(int $id): RedirectResponse
    {
        $this->ownerJob($id);
        $this->jobModel->delete($id);
        return redirect()->to('/dashboard')->with('success', 'Job deleted.');
    }

    // ─── Application ────────────────────────────────────────────────────

    public function apply(int $jobId): RedirectResponse
    {
        $userId   = session()->get('user_id');
        $appModel = model(ApplicationModel::class);

        if ($appModel->hasApplied($userId, $jobId)) {
            return redirect()->back()->with('error', 'You have already applied to this job.');
        }

        $rules = [
            'cover_letter' => 'permit_empty|max_length[3000]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $cvFile = null;
        $file   = $this->request->getFile('cv_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowed = ['application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!in_array($file->getMimeType(), $allowed, true)) {
                return redirect()->back()->with('error', 'Invalid CV format.');
            }
            if ($file->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->with('error', 'CV must be under 5 MB.');
            }
            $cvFile = 'app_' . $userId . '_' . $jobId . '_' . time() . '.' . $file->getClientExtension();
            $dest   = WRITEPATH . 'uploads/applications/';
            if (!is_dir($dest)) {
                mkdir($dest, 0755, true);
            }
            $file->move($dest, $cvFile);
        } else {
            // Use profile CV if available
            $profile = model(\App\Models\ProfileModel::class)->getByUserId($userId);
            if ($profile) {
                $cvFile = $profile->cv_file;
            }
        }

        $appModel->insert([
            'job_id'       => $jobId,
            'user_id'      => $userId,
            'cover_letter' => strip_tags($this->request->getPost('cover_letter')),
            'cv_file'      => $cvFile,
        ]);

        return redirect()->back()->with('success', 'Application submitted!');
    }

    // ─── Recruiter: Manage Applications ─────────────────────────────────

    public function updateApplicationStatus(int $appId): RedirectResponse
    {
        $appModel = model(ApplicationModel::class);
        $app      = $appModel->find($appId);

        if (!$app) {
            return redirect()->back()->with('error', 'Application not found.');
        }

        $job = $this->jobModel->find($app->job_id);
        if (!$job || (int) $job->user_id !== (int) session()->get('user_id')) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $status = $this->request->getPost('status');
        $allowed = ['pending', 'reviewed', 'shortlisted', 'rejected', 'hired'];
        if (!in_array($status, $allowed, true)) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $appModel->update($appId, [
            'status'        => $status,
            'recruiter_note' => strip_tags($this->request->getPost('recruiter_note') ?? ''),
        ]);

        return redirect()->back()->with('success', 'Application status updated.');
    }

    // ─── Private Helpers ─────────────────────────────────────────────────

    private function ownerJob(int $id): object
    {
        $job = $this->jobModel->find($id);
        if (!$job || (int) $job->user_id !== (int) session()->get('user_id')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found or access denied.');
        }
        return $job;
    }

    private function saveJobSkills(int $jobId, string $rawSkills): void
    {
        $db     = \Config\Database::connect();
        $skills = array_filter(array_map('trim', explode(',', strip_tags($rawSkills))));
        foreach ($skills as $skill) {
            $db->table('job_skills')->insert(['job_id' => $jobId, 'skill_name' => $skill]);
        }
    }

    private function dispatchAlerts(object $job): void
    {
        try {
            $alertModel = model(JobAlertModel::class);
            $alerts     = $alertModel->getActiveAlertsForJob($job);
            if (empty($alerts)) {
                return;
            }
            $mailer    = new AlertMailer();
            $userModel = model(UserModel::class);
            foreach ($alerts as $alert) {
                $user = $userModel->find($alert->user_id);
                if ($user) {
                    $mailer->sendJobAlert($user, $job, $alert);
                }
                $alertModel->update($alert->id, ['last_sent' => date('Y-m-d H:i:s')]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Alert dispatch error: ' . $e->getMessage());
        }
    }
}
