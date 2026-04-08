<?php

namespace App\Controllers;

use App\Models\{
    JobModel, CompanyModel, ApplicationModel, JobAlertModel, UserModel,
    JobLanguageModel, JobCertificationModel, JobPrescreeningModel, JobRecruitmentStepModel,
    SkillModel, LanguageModel, ExperienceModel
};
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

        // Fetch related data
        $db        = \Config\Database::connect();
        $jobSkills = $db->table('job_skills')->where('job_id', $job->id)->get()->getResultArray();
        $languages = model(JobLanguageModel::class)->getByJob($job->id);

        // ── Profile match score (job_seeker only) ─────────────────────────────
        $matchScore   = null;
        $matchDetails = [];

        if ($userId && session()->get('user_role') === 'job_seeker') {
            $userSkills = array_map(
                fn($s) => strtolower(trim($s->skill_name)),
                model(SkillModel::class)->getByUserId($userId)
            );
            $userLangs = array_map(
                fn($l) => strtolower(trim($l->name)),
                model(LanguageModel::class)->getByUserId($userId)
            );
            $userExps = model(ExperienceModel::class)->getByUserId($userId);

            // Total years of experience
            $totalExpMonths = 0;
            foreach ($userExps as $exp) {
                $start = strtotime(($exp->start_date ?? '') . '-01');
                $end   = (!empty($exp->is_current) || empty($exp->end_date))
                         ? time()
                         : strtotime($exp->end_date . '-01');
                if ($start && $end > $start) {
                    $totalExpMonths += ($end - $start) / (30 * 24 * 3600);
                }
            }
            $totalExpYears = $totalExpMonths / 12;

            // Skills score (50 pts)
            $jobSkillNames = array_map(fn($s) => strtolower(trim($s['skill_name'])), $jobSkills);
            $skillsTotal   = count($jobSkillNames);
            $skillsMatched = 0;
            foreach ($jobSkillNames as $jsk) {
                if (in_array($jsk, $userSkills, true)) {
                    $skillsMatched++;
                }
            }
            $skillsScore = $skillsTotal > 0 ? round(($skillsMatched / $skillsTotal) * 50) : 25;

            // Experience score (30 pts)
            $minYears = (int) ($job->min_experience_years ?? 0);
            $expScore = $minYears === 0
                ? 30
                : min(30, (int) round(($totalExpYears / $minYears) * 30));

            // Languages score (20 pts)
            $requiredLangs = array_filter($languages, fn($l) => !empty($l->is_required));
            $langsTotal    = count($requiredLangs);
            $langsMatched  = 0;
            foreach ($requiredLangs as $rl) {
                if (in_array(strtolower(trim($rl->language)), $userLangs, true)) {
                    $langsMatched++;
                }
            }
            $langsScore = $langsTotal > 0 ? round(($langsMatched / $langsTotal) * 20) : 20;

            $matchScore   = $skillsScore + $expScore + $langsScore;
            $matchDetails = [
                'skills' => ['score' => $skillsScore, 'max' => 50, 'matched' => $skillsMatched, 'total' => $skillsTotal],
                'exp'    => ['score' => $expScore,    'max' => 30, 'years'   => round($totalExpYears, 1), 'required' => $minYears],
                'langs'  => ['score' => $langsScore,  'max' => 20, 'matched' => $langsMatched, 'total' => $langsTotal],
            ];
        }

        return view('jobs/show', [
            'title'        => $job->title,
            'job'          => $job,
            'hasApplied'   => $hasApplied,
            'jobSkills'    => $jobSkills,
            'languages'    => $languages,
            'certs'        => model(JobCertificationModel::class)->getByJob($job->id),
            'questions'    => model(JobPrescreeningModel::class)->getByJob($job->id),
            'steps'        => model(JobRecruitmentStepModel::class)->getByJob($job->id),
            'matchScore'   => $matchScore,
            'matchDetails' => $matchDetails,
        ]);
    }

    // ─── Recruiter: Post a Job ───────────────────────────────────────────

    public function create(): string|RedirectResponse
    {
        $userId  = (int) session()->get('user_id');
        $company = model(CompanyModel::class)->resolveForUser($userId);
        if (!$company) {
            return redirect()->to('/organizations/create')->with('error', 'Créez d\'abord une organisation pour publier des offres.');
        }
        return view('jobs/create', [
            'title'   => 'Publier une offre',
            'company' => $company,
            'isEdit'  => false,
        ]);
    }

    public function store(): RedirectResponse
    {
        $rules = [
            'title'            => 'required|min_length[5]|max_length[255]',
            'description'      => 'required|min_length[30]',
            'contract_type'    => 'required|in_list[CDI,CDD,Freelance,Internship,PartTime]',
            'location'         => 'permit_empty|max_length[200]',
            'salary_min'       => 'permit_empty|integer',
            'salary_max'       => 'permit_empty|integer',
            'expires_at'       => 'permit_empty|valid_date',
            'num_positions'    => 'permit_empty|integer|greater_than[0]',
            'min_experience_years' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId  = (int) session()->get('user_id');
        $company = model(CompanyModel::class)->resolveForUser($userId);
        if (!$company) {
            return redirect()->to('/organizations/create')->with('error', 'Créez d\'abord une organisation pour publier des offres.');
        }

        $data = array_merge(
            $this->request->getPost([
                'title', 'description', 'mission_context', 'requirements', 'benefits',
                'contract_type', 'location', 'remote', 'salary_min', 'salary_max',
                'salary_currency', 'salary_period', 'experience_level', 'expires_at',
                'internal_ref', 'department', 'num_positions', 'hierarchical_level',
                'direct_manager', 'min_experience_years', 'education_level', 'education_field',
                'recruitment_notes', 'visibility_level',
            ]),
            [
                'company_id'       => $company->id,
                'user_id'          => $userId,
                'status'           => 'active',
                'salary_public'         => (int) ($this->request->getPost('salary_public')         ?? 0),
                'salary_variable'       => (int) ($this->request->getPost('salary_variable')       ?? 0),
                'salary_bonus_pct'      => $this->request->getPost('salary_bonus_pct') ?: null,
                'internal_only'         => (int) ($this->request->getPost('internal_only')         ?? 0),
                'require_cv'            => (int) ($this->request->getPost('require_cv')            ?? 0),
                'require_cover_letter'  => (int) ($this->request->getPost('require_cover_letter')  ?? 0),
            ]
        );

        $jobId = $this->jobModel->insert($data);

        $this->syncRelated($jobId);

        // Fire job alerts
        $job = $this->jobModel->find($jobId);
        $this->dispatchAlerts($job);

        return redirect()->to(base_url('jobs/' . ($job->slug ?? '')))
                         ->with('success', 'Offre publiée avec succès !');
    }

    public function edit(int $id): string
    {
        $job  = $this->ownerJob($id);
        $db   = \Config\Database::connect();

        $skillsList = implode(', ', array_column(
            $db->table('job_skills')->where('job_id', $id)->get()->getResultArray(),
            'skill_name'
        ));

        return view('jobs/create', [
            'title'       => 'Modifier l\'offre',
            'company'     => model(CompanyModel::class)->getByUserId(session()->get('user_id')),
            'job'         => $job,
            'skillsList'  => $skillsList,
            'languages'   => model(JobLanguageModel::class)->getByJob($id),
            'certs'       => model(JobCertificationModel::class)->getByJob($id),
            'questions'   => model(JobPrescreeningModel::class)->getByJob($id),
            'steps'       => model(JobRecruitmentStepModel::class)->getByJob($id),
            'isEdit'      => true,
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $this->ownerJob($id);

        $rules = [
            'title'         => 'required|min_length[5]|max_length[255]',
            'description'   => 'required|min_length[30]',
            'contract_type' => 'required|in_list[CDI,CDD,Freelance,Internship,PartTime]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = array_merge(
            $this->request->getPost([
                'title', 'description', 'mission_context', 'requirements', 'benefits',
                'contract_type', 'location', 'remote', 'salary_min', 'salary_max',
                'salary_currency', 'salary_period', 'experience_level', 'status', 'expires_at',
                'internal_ref', 'department', 'num_positions', 'hierarchical_level',
                'direct_manager', 'min_experience_years', 'education_level', 'education_field',
                'recruitment_notes', 'visibility_level',
            ]),
            [
                'salary_public'         => (int) ($this->request->getPost('salary_public')         ?? 0),
                'salary_variable'       => (int) ($this->request->getPost('salary_variable')       ?? 0),
                'salary_bonus_pct'      => $this->request->getPost('salary_bonus_pct') ?: null,
                'internal_only'         => (int) ($this->request->getPost('internal_only')         ?? 0),
                'require_cv'            => (int) ($this->request->getPost('require_cv')            ?? 0),
                'require_cover_letter'  => (int) ($this->request->getPost('require_cover_letter')  ?? 0),
            ]
        );
        $this->jobModel->update($id, $data);

        $this->syncRelated($id);

        return redirect()->to(base_url('dashboard'))->with('success', 'Offre mise à jour.');
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

        $job = $this->jobModel->find($jobId);
        if (!$job) {
            return redirect()->back()->with('error', 'Offre introuvable.');
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

        // Enforce recruiter document requirements
        if (!empty($job->require_cv) && empty($cvFile)) {
            return redirect()->back()->with('error', 'Le CV est obligatoire pour postuler à cette offre.');
        }
        if (!empty($job->require_cover_letter) && empty(trim((string) $this->request->getPost('cover_letter')))) {
            return redirect()->back()->with('error', 'La lettre de motivation est obligatoire pour postuler à cette offre.');
        }

        $appModel->insert([
            'job_id'       => $jobId,
            'user_id'      => $userId,
            'cover_letter' => strip_tags($this->request->getPost('cover_letter')),
            'cv_file'      => $cvFile,
        ]);

        // Save prescreening answers
        $appId   = $appModel->db->insertID();
        $rawAnswers = $this->request->getPost('answers') ?? [];
        if ($appId && is_array($rawAnswers)) {
            $db = \Config\Database::connect();
            foreach ($rawAnswers as $ans) {
                $qId  = (int) ($ans['question_id'] ?? 0);
                $text = trim((string) ($ans['answer'] ?? ''));
                if ($qId > 0 && $text !== '') {
                    $db->table('application_answers')->insert([
                        'application_id' => $appId,
                        'question_id'    => $qId,
                        'answer_text'    => mb_substr($text, 0, 2000),
                    ]);
                }
            }
        }

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

        $rejectionReason = strip_tags(trim($this->request->getPost('rejection_reason') ?? ''));
        if ($status === 'rejected' && $rejectionReason === '') {
            return redirect()->to(base_url('applications/' . $appId))->with('error', 'Veuillez indiquer un motif de refus.');
        }

        $appModel->update($appId, [
            'status'           => $status,
            'recruiter_note'   => strip_tags($this->request->getPost('recruiter_note') ?? ''),
            'rejection_reason' => $status === 'rejected' ? mb_substr($rejectionReason, 0, 1000) : null,
        ]);

        return redirect()->to(base_url('applications/' . $appId))->with('success', 'Statut mis à jour avec succès.');
    }

    public function saveApplicationNote(int $appId): RedirectResponse
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

        $appModel->update($appId, [
            'recruiter_note' => strip_tags($this->request->getPost('note') ?? ''),
        ]);

        return redirect()->to(base_url('applications/' . $appId))->with('success', 'Note enregistrée.');
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

    /**
     * Sync all related one-to-many tables from POST data.
     * Called after insert or update.
     */
    private function syncRelated(int $jobId): void
    {
        // Skills
        $db = \Config\Database::connect();
        $db->table('job_skills')->where('job_id', $jobId)->delete();
        $rawSkills = $this->request->getPost('skills');
        if ($rawSkills) {
            $this->saveJobSkills($jobId, $rawSkills);
        }

        // Languages
        model(JobLanguageModel::class)->syncForJob(
            $jobId,
            (array) ($this->request->getPost('languages') ?? [])
        );

        // Certifications
        model(JobCertificationModel::class)->syncForJob(
            $jobId,
            (array) ($this->request->getPost('certs') ?? [])
        );

        // Pre-screening questions
        model(JobPrescreeningModel::class)->syncForJob(
            $jobId,
            (array) ($this->request->getPost('questions') ?? [])
        );

        // Recruitment steps
        model(JobRecruitmentStepModel::class)->syncForJob(
            $jobId,
            (array) ($this->request->getPost('steps') ?? [])
        );
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
