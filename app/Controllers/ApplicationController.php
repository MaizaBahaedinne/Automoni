<?php

namespace App\Controllers;

use App\Models\{
    ProfileModel, SkillModel, ExperienceModel, LanguageModel,
    JobPrescreeningModel, JobLanguageModel, EducationModel, InterviewModel
};
use CodeIgniter\HTTP\RedirectResponse;

class ApplicationController extends BaseController
{
    /**
     * Recruiter view: split-screen candidate detail page.
     * GET applications/(:num)
     */
    public function show(int $appId): string|RedirectResponse
    {
        $recruiterId = (int) session()->get('user_id');
        $userRole    = session()->get('user_role');

        $db = \Config\Database::connect();

        // Fetch full application row with job + company + candidate user info
        $app = $db->table('applications')
            ->select('
                applications.*,
                jobs.id          AS job_id,
                jobs.title       AS job_title,
                jobs.description AS job_description,
                jobs.requirements AS job_requirements,
                jobs.benefits    AS job_benefits,
                jobs.slug        AS job_slug,
                jobs.contract_type,
                jobs.location    AS job_location,
                jobs.remote,
                jobs.salary_min, jobs.salary_max,
                jobs.salary_currency, jobs.salary_period,
                jobs.experience_level, jobs.min_experience_years,
                jobs.education_level, jobs.education_field,
                jobs.user_id     AS recruiter_id,
                jobs.num_positions,
                users.first_name, users.last_name, users.email,
                users.avatar,
                companies.name   AS company_name,
                companies.logo   AS company_logo
            ')
            ->join('jobs',      'jobs.id      = applications.job_id')
            ->join('users',     'users.id     = applications.user_id')
            ->join('companies', 'companies.id = jobs.company_id', 'left')
            ->where('applications.id', $appId)
            ->get()->getRowObject();

        if (!$app) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Only the job's recruiter (or admin) may view this page
        if ((int) $app->recruiter_id !== $recruiterId && $userRole !== 'admin') {
            return redirect()->to('dashboard')->with('error', 'Accès non autorisé.');
        }

        $candidateId = (int) $app->user_id;

        // Candidate profile, skills, experiences, education, languages
        $profile     = model(ProfileModel::class)->getByUserId($candidateId);
        $skills      = model(SkillModel::class)->getByUserId($candidateId);
        $experiences = model(ExperienceModel::class)->getByUserId($candidateId);
        $education   = model(EducationModel::class)->getByUserId($candidateId);
        $userLangs   = model(LanguageModel::class)->getByUserId($candidateId);

        // Prescreening questions for this job
        $questions = model(JobPrescreeningModel::class)->getByJob((int) $app->job_id);

        // Answers this candidate submitted
        $rawAnswers = $db->table('application_answers')
            ->where('application_id', $appId)
            ->get()->getResultObject();

        $answerMap = [];
        foreach ($rawAnswers as $a) {
            $answerMap[(int) $a->question_id] = $a->answer_text;
        }

        // Job-side data for the right panel
        $jobSkills = $db->table('job_skills')
            ->where('job_id', $app->job_id)
            ->get()->getResultArray();

        $jobLangs = model(JobLanguageModel::class)->getByJob((int) $app->job_id);

        // ── Match score (identical logic to JobController::show) ─────────────
        $userSkillNames = array_map(fn($s) => strtolower(trim($s->skill_name)), $skills);
        $userLangNames  = array_map(fn($l) => strtolower(trim($l->name)), $userLangs);

        // Experience years
        $totalExpMonths = 0;
        foreach ($experiences as $exp) {
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
            if (in_array($jsk, $userSkillNames, true)) {
                $skillsMatched++;
            }
        }
        $skillsScore = $skillsTotal > 0 ? round(($skillsMatched / $skillsTotal) * 50) : 25;

        // Experience score (30 pts)
        $minYears = (int) ($app->min_experience_years ?? 0);
        $expScore = $minYears === 0
            ? 30
            : min(30, (int) round(($totalExpYears / $minYears) * 30));

        // Languages score (20 pts)
        $requiredJobLangs = array_filter($jobLangs, fn($l) => !empty($l->is_required));
        $langsTotal       = count($requiredJobLangs);
        $langsMatched     = 0;
        foreach ($requiredJobLangs as $rl) {
            if (in_array(strtolower(trim($rl->language)), $userLangNames, true)) {
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

        return view('applications/show', [
            'title'        => esc($app->first_name) . ' ' . esc($app->last_name) . ' — ' . esc($app->job_title),
            'app'          => $app,
            'profile'      => $profile,
            'skills'       => $skills,
            'experiences'  => $experiences,
            'education'    => $education,
            'userLangs'    => $userLangs,
            'questions'    => $questions,
            'answerMap'    => $answerMap,
            'jobSkills'    => $jobSkills,
            'jobLangs'     => $jobLangs,
            'matchScore'   => $matchScore,
            'matchDetails' => $matchDetails,
            'interview'    => model(InterviewModel::class)->getByApplication($appId),
        ]);
    }

    /**
     * POST applications/(:num)/interview
     * Create or update the scheduled interview for this application.
     */
    public function scheduleInterview(int $appId): RedirectResponse
    {
        $recruiterId = (int) session()->get('user_id');
        $userRole    = session()->get('user_role');

        $db  = \Config\Database::connect();
        $app = $db->table('applications')
            ->select('applications.id, jobs.user_id AS job_recruiter_id')
            ->join('jobs', 'jobs.id = applications.job_id')
            ->where('applications.id', $appId)
            ->get()->getRowObject();

        if (!$app || ((int) $app->job_recruiter_id !== $recruiterId && $userRole !== 'admin')) {
            return redirect()->to('dashboard')->with('error', 'Accès non autorisé.');
        }

        $rules = [
            'interview_type' => 'required|in_list[onsite,remote]',
            'scheduled_at'   => 'required',
            'duration_min'   => 'required|integer|greater_than[0]',
            'location'       => 'permit_empty|max_length[500]',
            'notes'          => 'permit_empty|max_length[2000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('applications/' . $appId))
                             ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $interviewModel = model(InterviewModel::class);
        // datetime-local format: "2026-04-09T14:30" → "2026-04-09 14:30:00"
        $scheduledAt = str_replace('T', ' ', $this->request->getPost('scheduled_at')) . ':00';

        $data = [
            'application_id' => $appId,
            'recruiter_id'   => $recruiterId,
            'type'           => $this->request->getPost('interview_type'),
            'scheduled_at'   => $scheduledAt,
            'duration_min'   => (int) $this->request->getPost('duration_min'),
            'location'       => $this->request->getPost('location') ?: null,
            'notes'          => $this->request->getPost('notes') ?: null,
            'status'         => 'scheduled',
        ];

        $existing = $interviewModel->getByApplication($appId);
        if ($existing) {
            $interviewModel->update($existing->id, $data);
        } else {
            $interviewModel->insert($data);
        }

        // Also mark the application as shortlisted
        model(\App\Models\ApplicationModel::class)->update($appId, ['status' => 'shortlisted']);

        return redirect()->to(base_url('applications/' . $appId))
                         ->with('success', 'Entretien planifié avec succès.');
    }
}
