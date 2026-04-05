<?php

namespace App\Controllers;

use App\Models\{ProfileModel, SkillModel, ExperienceModel, EducationModel};
use App\Libraries\CvParser;
use CodeIgniter\HTTP\RedirectResponse;

class ProfileController extends BaseController
{
    private int $userId;
    private ProfileModel $profileModel;

    public function __construct()
    {
        $this->userId       = (int) session()->get('user_id');
        $this->profileModel = model(ProfileModel::class);
    }

    // ─── View Profile ─────────────────────────────────────────────────────

    public function show(int $userId = 0): string
    {
        $viewId = $userId ?: $this->userId;

        $profile     = $this->profileModel->getByUserId($viewId);
        $skills      = model(SkillModel::class)->getByUserId($viewId);
        $experiences = model(ExperienceModel::class)->getByUserId($viewId);
        $education   = model(EducationModel::class)->getByUserId($viewId);
        $user        = model(\App\Models\UserModel::class)->find($viewId);

        return view('profile/show', compact('profile', 'skills', 'experiences', 'education', 'user'));
    }

    // ─── Edit Basic Info ──────────────────────────────────────────────────

    public function edit(): string
    {
        $profile     = $this->profileModel->getByUserId($this->userId);
        $skills      = model(SkillModel::class)->getByUserId($this->userId);
        $experiences = model(ExperienceModel::class)->getByUserId($this->userId);
        $education   = model(EducationModel::class)->getByUserId($this->userId);

        return view('profile/edit', compact('profile', 'skills', 'experiences', 'education'));
    }

    public function update(): RedirectResponse
    {
        $rules = [
            'headline'   => 'permit_empty|max_length[255]',
            'phone'      => 'permit_empty|max_length[30]',
            'city'       => 'permit_empty|max_length[100]',
            'country'    => 'permit_empty|max_length[100]',
            'linkedin'   => 'permit_empty|valid_url_strict|max_length[255]',
            'github'     => 'permit_empty|valid_url_strict|max_length[255]',
            'portfolio'  => 'permit_empty|valid_url_strict|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost([
            'headline', 'summary', 'phone', 'phone_code', 'city', 'country',
            'position', 'department',
            'linkedin', 'github', 'portfolio',
            'desired_salary', 'desired_contract', 'desired_location', 'availability',
        ]);

        // Sanitise URLs
        foreach (['linkedin', 'github', 'portfolio'] as $field) {
            if (!empty($data[$field])) {
                $data[$field] = filter_var($data[$field], FILTER_SANITIZE_URL);
            }
        }

        $profile = $this->profileModel->getByUserId($this->userId);
        if ($profile) {
            $this->profileModel->update($profile->id, $data);
        } else {
            $data['user_id'] = $this->userId;
            $this->profileModel->insert($data);
        }

        // Sync skills
        $rawSkills = $this->request->getPost('skills');
        if ($rawSkills) {
            $skills = array_map('trim', explode(',', strip_tags($rawSkills)));
            $structured = array_map(fn($s) => ['name' => $s, 'level' => 'intermediate'], array_filter($skills));
            model(SkillModel::class)->syncSkills($this->userId, $structured);
        }

        $this->profileModel->recalculateCompleteness($this->userId);

        return redirect()->to('/profile')->with('success', 'Profile updated successfully.');
    }

    // ─── CV Upload ────────────────────────────────────────────────────────

    public function uploadCv(): RedirectResponse
    {
        $file = $this->request->getFile('cv_file');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'Please select a valid CV file.');
        }

        $allowedTypes = ['application/pdf', 'application/msword',
                         'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file->getMimeType(), $allowedTypes, true)) {
            return redirect()->back()->with('error', 'Only PDF and Word documents are allowed.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return redirect()->back()->with('error', 'File size must not exceed 5 MB.');
        }

        $newName = 'cv_' . $this->userId . '_' . time() . '.' . $file->getClientExtension();
        $destination = WRITEPATH . 'uploads/cv/';
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        $file->move($destination, $newName);

        // Delete old CV
        $profile = $this->profileModel->getByUserId($this->userId);
        if ($profile && $profile->cv_file) {
            $oldPath = WRITEPATH . 'uploads/cv/' . $profile->cv_file;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $profileData = [
            'cv_file'          => $newName,
            'cv_original_name' => esc($file->getClientName()),
        ];

        if ($profile) {
            $this->profileModel->update($profile->id, $profileData);
        } else {
            $profileData['user_id'] = $this->userId;
            $this->profileModel->insert($profileData);
        }

        // Try to parse CV
        try {
            $parser  = new CvParser();
            $parsed  = $parser->parse($destination . $newName, $file->getMimeType());

            if (!empty($parsed['skills'])) {
                $structured = array_map(fn($s) => ['name' => $s, 'level' => 'intermediate'], $parsed['skills']);
                model(SkillModel::class)->syncSkills($this->userId, $structured);
            }

            if (!empty($parsed['email']) || !empty($parsed['phone'])) {
                $updateData = [];
                if (!empty($parsed['phone'])) {
                    $updateData['phone'] = substr(preg_replace('/[^0-9+\s\-()]/', '', $parsed['phone']), 0, 30);
                }
                $updatedProfile = $this->profileModel->getByUserId($this->userId);
                if ($updatedProfile && !empty($updateData)) {
                    $this->profileModel->update($updatedProfile->id, $updateData);
                }
            }

            session()->setFlashdata('cv_parsed', $parsed);
        } catch (\Throwable $e) {
            log_message('error', 'CV parse failed: ' . $e->getMessage());
        }

        $this->profileModel->recalculateCompleteness($this->userId);
        return redirect()->to('/profile/edit')->with('success', 'CV uploaded' . (session()->getFlashdata('cv_parsed') ? ' and parsed' : '') . ' successfully.');
    }

    // ─── Download CV ─────────────────────────────────────────────────────

    public function downloadCv(int $userId = 0): void
    {
        $viewId  = $userId ?: $this->userId;
        $profile = $this->profileModel->getByUserId($viewId);

        if (!$profile || !$profile->cv_file) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('CV not found.');
        }

        // Only owner or recruiter can download
        $myRole = session()->get('user_role');
        if ($viewId !== $this->userId && $myRole !== 'recruiter' && $myRole !== 'admin') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied.');
        }

        $path = WRITEPATH . 'uploads/cv/' . $profile->cv_file;
        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File not found.');
        }

        $response = service('response');
        $response->setHeader('Content-Type', mime_content_type($path))
                 ->setHeader('Content-Disposition', 'attachment; filename="' . esc($profile->cv_original_name ?? $profile->cv_file) . '"')
                 ->setBody(file_get_contents($path))
                 ->send();
        exit;
    }

    // ─── Experience CRUD ─────────────────────────────────────────────────

    public function addExperience(): RedirectResponse
    {
        $expModel = model(ExperienceModel::class);
        $rules    = $expModel->getValidationRules();
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = array_merge(
            $this->request->getPost(['job_title', 'company', 'location', 'contract', 'start_date', 'end_date', 'description']),
            ['user_id' => $this->userId, 'is_current' => (int) $this->request->getPost('is_current')]
        );
        // map form field job_title → DB column title
        $data['title'] = $data['job_title'] ?? null;
        unset($data['job_title']);
        $expModel->insert($data);
        $this->profileModel->recalculateCompleteness($this->userId);
        return redirect()->to('/profile/edit#experience')->with('success', 'Experience added.');
    }

    public function deleteExperience(int $id): RedirectResponse
    {
        $expModel = model(ExperienceModel::class);
        $record   = $expModel->find($id);
        if ($record && (int) $record->user_id === $this->userId) {
            $expModel->delete($id);
            $this->profileModel->recalculateCompleteness($this->userId);
        }
        return redirect()->to('/profile/edit#experience')->with('success', 'Experience removed.');
    }

    // ─── Education CRUD ───────────────────────────────────────────────────

    public function addEducation(): RedirectResponse
    {
        $eduModel = model(EducationModel::class);
        $rules    = $eduModel->getValidationRules();
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = array_merge(
            $this->request->getPost(['degree', 'field', 'institution', 'location', 'start_year', 'end_year', 'description']),
            ['user_id' => $this->userId, 'is_current' => (int) $this->request->getPost('is_current')]
        );
        $eduModel->insert($data);
        $this->profileModel->recalculateCompleteness($this->userId);
        return redirect()->to('/profile/edit#education')->with('success', 'Education added.');
    }

    public function deleteEducation(int $id): RedirectResponse
    {
        $eduModel = model(EducationModel::class);
        $record   = $eduModel->find($id);
        if ($record && (int) $record->user_id === $this->userId) {
            $eduModel->delete($id);
            $this->profileModel->recalculateCompleteness($this->userId);
        }
        return redirect()->to('/profile/edit#education')->with('success', 'Education removed.');
    }
}
