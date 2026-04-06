<?php

namespace App\Controllers;

use App\Models\{ProfileModel, SkillModel, ExperienceModel, EducationModel,
    CertificationModel, LanguageModel, ProjectModel, ProjectMemberModel, VolunteeringModel,
    ConnectionModel};
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

        $profile        = $this->profileModel->getByUserId($viewId);
        $skills         = model(SkillModel::class)->getByUserId($viewId);
        $experiences    = model(ExperienceModel::class)->getByUserId($viewId);
        $education      = model(EducationModel::class)->getByUserId($viewId);
        $certifications = model(CertificationModel::class)->getByUserId($viewId);
        $languages      = model(LanguageModel::class)->getByUserId($viewId);
        $projects       = model(ProjectModel::class)->getByUserId($viewId);
        $volunteering   = model(VolunteeringModel::class)->getByUserId($viewId);
        $user           = model(\App\Models\UserModel::class)->find($viewId);

        $connectionModel    = model(ConnectionModel::class);
        $connectionStatus   = $connectionModel->getStatus($this->userId, $viewId);
        $connectionsCount   = $connectionModel->countAccepted($viewId);
        $mutualCount        = ($connectionStatus !== 'self')
                              ? $connectionModel->getMutualCount($this->userId, $viewId)
                              : 0;

        return view('profile/show', compact(
            'profile', 'skills', 'experiences', 'education',
            'certifications', 'languages', 'projects', 'volunteering', 'user',
            'connectionStatus', 'connectionsCount', 'mutualCount'
        ));
    }

    // ─── Edit Basic Info ──────────────────────────────────────────────────

    public function edit(): string
    {
        $profile        = $this->profileModel->getByUserId($this->userId);
        $skills         = model(SkillModel::class)->getByUserId($this->userId);
        $experiences    = model(ExperienceModel::class)->getByUserId($this->userId);
        $education      = model(EducationModel::class)->getByUserId($this->userId);
        $certifications = model(CertificationModel::class)->getByUserId($this->userId);
        $languages      = model(LanguageModel::class)->getByUserId($this->userId);
        $projects       = model(ProjectModel::class)->getByUserId($this->userId);
        $volunteering   = model(VolunteeringModel::class)->getByUserId($this->userId);
        $user           = model(\App\Models\UserModel::class)->find($this->userId);

        return view('profile/edit', compact(
            'profile', 'skills', 'experiences', 'education',
            'certifications', 'languages', 'projects', 'volunteering', 'user'
        ));
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
            $this->request->getPost([
                'title', 'company', 'location', 'contract', 'level', 'department',
                'start_date', 'end_date', 'description', 'manager_name', 'skills_gained',
            ]),
            ['user_id' => $this->userId, 'is_current' => (int) $this->request->getPost('is_current')]
        );

        $managerId = (int) $this->request->getPost('manager_user_id');
        $data['manager_user_id'] = $managerId > 0 ? $managerId : null;

        $orgId = (int) $this->request->getPost('org_id');
        $data['org_id'] = $orgId > 0 ? $orgId : null;

        if (!empty($data['start_date'])) { $data['start_date'] .= '-01'; }
        if (!empty($data['end_date']))   { $data['end_date']   .= '-01'; }

        $expModel->skipValidation(true)->insert($data);
        $this->profileModel->recalculateCompleteness($this->userId);
        return redirect()->to('/profile/edit#experience')->with('success', 'Experience added.');
    }

    public function updateExperience(int $id): RedirectResponse
    {
        $expModel = model(ExperienceModel::class);
        $record   = $expModel->find($id);
        if (!$record || (int) $record->user_id !== $this->userId) {
            return redirect()->to('/profile/edit#experience')->with('error', 'Not found.');
        }

        $rules = $expModel->getValidationRules();
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = array_merge(
            $this->request->getPost([
                'title', 'company', 'location', 'contract', 'level', 'department',
                'start_date', 'end_date', 'description', 'manager_name', 'skills_gained',
            ]),
            ['is_current' => (int) $this->request->getPost('is_current')]
        );

        $managerId = (int) $this->request->getPost('manager_user_id');
        $data['manager_user_id'] = $managerId > 0 ? $managerId : null;

        $orgId = (int) $this->request->getPost('org_id');
        $data['org_id'] = $orgId > 0 ? $orgId : null;

        if (!empty($data['start_date'])) { $data['start_date'] .= '-01'; }
        if (!empty($data['end_date']))   { $data['end_date']   .= '-01'; }

        $expModel->skipValidation(true)->update($id, $data);
        $this->profileModel->recalculateCompleteness($this->userId);
        return redirect()->to('/profile/edit#experience')->with('success', 'Experience updated.');
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

    public function searchUsers(): \CodeIgniter\HTTP\ResponseInterface
    {
        $q = trim($this->request->getGet('q') ?? '');
        if (strlen($q) < 2) {
            return $this->response->setJSON([]);
        }

        $userModel = model(\App\Models\UserModel::class);
        $users = $userModel
            ->select('id, first_name, last_name')
            ->groupStart()
                ->like('first_name', $q)
                ->orLike('last_name', $q)
            ->groupEnd()
            ->limit(10)
            ->findAll();

        return $this->response->setJSON(
            array_map(fn($u) => [
                'id'   => $u->id,
                'name' => trim($u->first_name . ' ' . $u->last_name),
            ], $users)
        );
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
            $this->request->getPost(['degree', 'niveau', 'field', 'institution', 'location', 'start_year', 'end_year', 'description']),
            ['user_id' => $this->userId]
        );
        $orgId = (int) $this->request->getPost('org_id');
        $data['org_id'] = $orgId > 0 ? $orgId : null;
        $eduModel->skipValidation(true)->insert($data);
        $this->profileModel->recalculateCompleteness($this->userId);
        return redirect()->to('/profile/edit#education')->with('success', 'Education added.');
    }

    public function updateEducation(int $id): RedirectResponse
    {
        $eduModel = model(EducationModel::class);
        $record   = $eduModel->find($id);
        if (!$record || (int) $record->user_id !== $this->userId) {
            return redirect()->to('/profile/edit#education')->with('error', 'Not found.');
        }
        $rules = $eduModel->getValidationRules();
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $data = $this->request->getPost(['degree', 'niveau', 'field', 'institution', 'location', 'start_year', 'end_year', 'description']);
        $orgId = (int) $this->request->getPost('org_id');
        $data['org_id'] = $orgId > 0 ? $orgId : null;
        $eduModel->skipValidation(true)->update($id, $data);
        $this->profileModel->recalculateCompleteness($this->userId);
        return redirect()->to('/profile/edit#education')->with('success', 'Formation mise à jour.');
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

    // ─── Certifications CRUD ──────────────────────────────────────────────

    public function addCertification(): RedirectResponse
    {
        $model = model(CertificationModel::class);
        $data  = $this->request->getPost(['name', 'organization', 'issue_date', 'expiry_date', 'credential_url']);
        if (empty(trim($data['name'] ?? ''))) {
            return redirect()->back()->with('error', 'Certification name is required.');
        }
        $data['user_id'] = $this->userId;
        if (!empty($data['issue_date']))  { $data['issue_date']  .= '-01'; }
        if (!empty($data['expiry_date'])) { $data['expiry_date'] .= '-01'; }
        if (!empty($data['credential_url'])) {
            $data['credential_url'] = filter_var($data['credential_url'], FILTER_SANITIZE_URL);
        }
        // Handle logo upload
        $logo = $this->request->getFile('logo_file');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            if (in_array($logo->getMimeType(), $allowed) && $logo->getSize() <= 2 * 1024 * 1024) {
                $dest = WRITEPATH . 'uploads/cert_logos/';
                if (!is_dir($dest)) { mkdir($dest, 0755, true); }
                $fname = 'cert_' . $this->userId . '_' . time() . '.' . $logo->getClientExtension();
                $logo->move($dest, $fname);
                $data['logo_file'] = $fname;
            }
        }
        $model->insert($data);
        return redirect()->to('/profile/edit#certifications')->with('success', 'Certification added.');
    }

    public function updateCertification(int $id): RedirectResponse
    {
        $model  = model(CertificationModel::class);
        $record = $model->find($id);
        if (!$record || (int) $record->user_id !== $this->userId) {
            return redirect()->to('/profile/edit#certifications')->with('error', 'Not found.');
        }
        $data = $this->request->getPost(['name', 'organization', 'issue_date', 'expiry_date', 'credential_url']);
        if (empty(trim($data['name'] ?? ''))) {
            return redirect()->to('/profile/edit#certifications')->with('error', 'Certification name is required.');
        }
        if (!empty($data['issue_date']))  { $data['issue_date']  .= '-01'; }
        if (!empty($data['expiry_date'])) { $data['expiry_date'] .= '-01'; }
        if (!empty($data['credential_url'])) {
            $data['credential_url'] = filter_var($data['credential_url'], FILTER_SANITIZE_URL);
        }
        $model->update($id, $data);
        return redirect()->to('/profile/edit#certifications')->with('success', 'Certification mise à jour.');
    }

    public function deleteCertification(int $id): RedirectResponse
    {
        $model  = model(CertificationModel::class);
        $record = $model->find($id);
        if ($record && (int) $record->user_id === $this->userId) {
            if (!empty($record->logo_file)) {
                @unlink(WRITEPATH . 'uploads/cert_logos/' . $record->logo_file);
            }
            $model->delete($id);
        }
        return redirect()->to('/profile/edit#certifications')->with('success', 'Certification removed.');
    }

    // ─── Languages CRUD ───────────────────────────────────────────────────

    public function addLanguage(): RedirectResponse
    {
        $model = model(LanguageModel::class);
        $data  = $this->request->getPost(['name', 'level']);
        if (empty(trim($data['name'] ?? '')) || empty(trim($data['level'] ?? ''))) {
            return redirect()->back()->with('error', 'Language name and level are required.');
        }
        $data['user_id'] = $this->userId;
        $model->insert($data);
        return redirect()->to('/profile/edit#languages')->with('success', 'Language added.');
    }

    public function updateLanguage(int $id): RedirectResponse
    {
        $model  = model(LanguageModel::class);
        $record = $model->find($id);
        if (!$record || (int) $record->user_id !== $this->userId) {
            return redirect()->to('/profile/edit#languages')->with('error', 'Not found.');
        }
        $data = $this->request->getPost(['name', 'level']);
        if (empty(trim($data['name'] ?? '')) || empty(trim($data['level'] ?? ''))) {
            return redirect()->to('/profile/edit#languages')->with('error', 'Name and level are required.');
        }
        $model->update($id, $data);
        return redirect()->to('/profile/edit#languages')->with('success', 'Langue mise à jour.');
    }

    public function deleteLanguage(int $id): RedirectResponse
    {
        $model  = model(LanguageModel::class);
        $record = $model->find($id);
        if ($record && (int) $record->user_id === $this->userId) {
            $model->delete($id);
        }
        return redirect()->to('/profile/edit#languages')->with('success', 'Language removed.');
    }

    // ─── Projects CRUD ────────────────────────────────────────────────────

    public function addProject(): RedirectResponse
    {
        $model = model(ProjectModel::class);
        $data  = $this->request->getPost(['name', 'start_date', 'end_date', 'is_current', 'description']);
        if (empty(trim($data['name'] ?? ''))) {
            return redirect()->back()->with('error', 'Project name is required.');
        }
        $data['user_id']    = $this->userId;
        $data['is_current'] = !empty($data['is_current']) ? 1 : 0;
        if ($data['is_current']) { $data['end_date'] = null; }
        if (!empty($data['start_date'])) { $data['start_date'] .= '-01'; }
        if (!empty($data['end_date']))   { $data['end_date']   .= '-01'; }
        $projectId = $model->insert($data, true);

        // Sync team members
        $memberIds = $this->request->getPost('member_ids') ?? [];
        if (!empty($memberIds)) {
            model(ProjectMemberModel::class)->syncMembers($projectId, (array) $memberIds);
        }
        return redirect()->to('/profile/edit#projects')->with('success', 'Project added.');
    }

    public function updateProject(int $id): RedirectResponse
    {
        $model  = model(ProjectModel::class);
        $record = $model->find($id);
        if (!$record || (int) $record->user_id !== $this->userId) {
            return redirect()->to('/profile/edit#projects')->with('error', 'Not found.');
        }
        $data = $this->request->getPost(['name', 'start_date', 'end_date', 'is_current', 'description']);
        if (empty(trim($data['name'] ?? ''))) {
            return redirect()->to('/profile/edit#projects')->with('error', 'Project name is required.');
        }
        $data['is_current'] = !empty($data['is_current']) ? 1 : 0;
        if ($data['is_current']) { $data['end_date'] = null; }
        if (!empty($data['start_date'])) { $data['start_date'] .= '-01'; }
        if (!empty($data['end_date']))   { $data['end_date']   .= '-01'; }
        $model->update($id, $data);
        $this->profileModel->recalculateCompleteness($this->userId);
        return redirect()->to('/profile/edit#projects')->with('success', 'Projet mis à jour.');
    }

    public function deleteProject(int $id): RedirectResponse
    {
        $model  = model(ProjectModel::class);
        $record = $model->find($id);
        if ($record && (int) $record->user_id === $this->userId) {
            model(ProjectMemberModel::class)->where('project_id', $id)->delete();
            $model->delete($id);
        }
        return redirect()->to('/profile/edit#projects')->with('success', 'Project removed.');
    }

    // ─── Volunteering CRUD ────────────────────────────────────────────────

    public function addVolunteering(): RedirectResponse
    {
        $model = model(VolunteeringModel::class);
        $data  = $this->request->getPost(['organization', 'position', 'start_date', 'end_date', 'is_current', 'description']);
        if (empty(trim($data['organization'] ?? ''))) {
            return redirect()->back()->with('error', 'Organization name is required.');
        }
        $data['user_id']    = $this->userId;
        $data['is_current'] = !empty($data['is_current']) ? 1 : 0;
        if ($data['is_current']) { $data['end_date'] = null; }
        if (!empty($data['start_date'])) { $data['start_date'] .= '-01'; }
        if (!empty($data['end_date']))   { $data['end_date']   .= '-01'; }
        $model->insert($data);
        return redirect()->to('/profile/edit#volunteering')->with('success', 'Volunteering added.');
    }

    public function updateVolunteering(int $id): RedirectResponse
    {
        $model  = model(VolunteeringModel::class);
        $record = $model->find($id);
        if (!$record || (int) $record->user_id !== $this->userId) {
            return redirect()->to('/profile/edit#volunteering')->with('error', 'Not found.');
        }
        $data = $this->request->getPost(['organization', 'position', 'start_date', 'end_date', 'is_current', 'description']);
        if (empty(trim($data['organization'] ?? ''))) {
            return redirect()->to('/profile/edit#volunteering')->with('error', 'Organization name is required.');
        }
        $data['is_current'] = !empty($data['is_current']) ? 1 : 0;
        if ($data['is_current']) { $data['end_date'] = null; }
        if (!empty($data['start_date'])) { $data['start_date'] .= '-01'; }
        if (!empty($data['end_date']))   { $data['end_date']   .= '-01'; }
        $model->update($id, $data);
        $this->profileModel->recalculateCompleteness($this->userId);
        return redirect()->to('/profile/edit#volunteering')->with('success', 'Bénévolat mis à jour.');
    }

    public function deleteVolunteering(int $id): RedirectResponse
    {
        $model  = model(VolunteeringModel::class);
        $record = $model->find($id);
        if ($record && (int) $record->user_id === $this->userId) {
            $model->delete($id);
        }
        return redirect()->to('/profile/edit#volunteering')->with('success', 'Volunteering removed.');
    }
}
