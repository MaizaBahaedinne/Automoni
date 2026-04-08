<?php

namespace App\Controllers;

use App\Models\{ProfileModel, SkillModel, ExperienceModel, EducationModel,
    LanguageModel, CertificationModel};
use App\Services\CvParsingClient;
use App\Libraries\CvParser;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CV Integration Controller
 * Handles CV upload, parsing, preview, and profile auto-fill
 */
class CvIntegrationController extends BaseController
{
    private int $userId;
    private ProfileModel $profileModel;
    private CvParsingClient $parsingClient;

    public function __construct()
    {
        $this->userId = (int) session()->get('user_id');
        $this->profileModel = model(ProfileModel::class);
        $this->parsingClient = new CvParsingClient();
    }

    /**
     * GET /profile/cv-integrate
     * Show CV integration page
     */
    public function showIntegrationPage(): string
    {
        $profile = $this->profileModel->getByUserId($this->userId);

        return view('profile/cv_integrate', [
            'title' => 'CV Integration',
            'profile' => $profile,
        ]);
    }

    /**
     * POST /profile/cv-parse
     * Parse uploaded CV file via Python service
     * Returns JSON response
     */
    public function parseCv(): ResponseInterface
    {
        // Only AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'AJAX only',
            ]);
        }

        try {
            // Get uploaded file
            $file = $this->request->getFile('cv_file');

            if (!$file || !$file->isValid()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'No file provided or file is invalid',
                ]);
            }

            // Validate file
            $this->validateCvFile($file);

            // Save temporarily
            $tempPath = $this->savePlainUpload($file);

            // Try Python service — fall back to PHP parser if unavailable
            $usedFallback = false;
            try {
                $parseResult = $this->parsingClient->parseCv($tempPath);
                $data = $parseResult['data'] ?? [];
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                if (
                    strpos($msg, 'unavailable') !== false ||
                    strpos($msg, 'disabled') !== false ||
                    strpos($msg, 'Connection refused') !== false ||
                    strpos($msg, 'Failed to connect') !== false ||
                    strpos($msg, 'timed out') !== false
                ) {
                    log_message('warning', 'Python CV service unavailable, using PHP fallback parser');
                    $usedFallback = true;
                    $data = $this->fallbackParseCv($tempPath, $file->getMimeType());
                } else {
                    throw $e;
                }
            }

            // Store in session (temporary)
            session()->set('cv_parse_result', ['data' => $data]);

            log_message('info', "CV parsed for user {$this->userId}" . ($usedFallback ? ' (PHP fallback)' : ''));

            return $this->response->setJSON([
                'success' => true,
                'message' => $usedFallback
                    ? 'CV analysé (mode basique — service IA indisponible)'
                    : 'CV parsed successfully',
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            log_message('error', "CV parsing failed: {$e->getMessage()}");

            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * POST /profile/cv-save
     * Save parsed CV data to profile
     * User must approve each field before saving
     */
    public function saveProfileFromCv(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'AJAX only',
            ]);
        }

        try {
            // Get data from request
            $data = $this->request->getJSON(true);

            if (!$data) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'No data provided',
                ]);
            }

            // ── Profile Info ───────────────────────────────────────────────
            $profileData = [];

            if (!empty($data['profile']['headline'])) {
                $profileData['headline'] = esc($data['profile']['headline']);
            }

            if (!empty($data['profile']['summary'])) {
                $profileData['summary'] = esc($data['profile']['summary']);
            }

            if (!empty($data['profile']['email'])) {
                $userModel = model(\App\Models\UserModel::class);
                $user = $userModel->find($this->userId);
                if ($user && $user->email !== $data['profile']['email']) {
                    $userModel->update($this->userId, ['email' => esc($data['profile']['email'])]);
                }
            }

            if (!empty($data['profile']['phone'])) {
                $profileData['phone'] = esc($data['profile']['phone']);
            }

            if (!empty($data['profile']['city'])) {
                $profileData['city'] = esc($data['profile']['city']);
            }

            if (!empty($data['profile']['country'])) {
                $profileData['country'] = esc($data['profile']['country']);
            }

            // Save profile info
            if (!empty($profileData)) {
                $profile = $this->profileModel->getByUserId($this->userId);
                if ($profile) {
                    $this->profileModel->update($profile->id, $profileData);
                } else {
                    $profileData['user_id'] = $this->userId;
                    $this->profileModel->insert($profileData);
                }
            }

            // ── Skills ─────────────────────────────────────────────────────
            if (!empty($data['skills']) && is_array($data['skills'])) {
                $skillsData = array_map(function($skill) {
                    return [
                        'name' => esc($skill['name'] ?? ''),
                        'level' => esc($skill['level'] ?? 'intermediate'),
                    ];
                }, $data['skills']);

                model(SkillModel::class)->syncSkills($this->userId, $skillsData);
            }

            // ── Languages ──────────────────────────────────────────────────
            if (!empty($data['languages']) && is_array($data['languages'])) {
                $langModel = model(LanguageModel::class);
                // Clear existing
                $langModel->deleteByUserId($this->userId);
                // Add new
                foreach ($data['languages'] as $lang) {
                    $langModel->insert([
                        'user_id' => $this->userId,
                        'name' => esc($lang['name'] ?? ''),
                        'level' => esc($lang['level'] ?? 'intermediate'),
                    ]);
                }
            }

            // ── Experiences ────────────────────────────────────────────────
            if (!empty($data['experiences']) && is_array($data['experiences'])) {
                $expModel = model(ExperienceModel::class);
                foreach ($data['experiences'] as $exp) {
                    $expModel->insert([
                        'user_id' => $this->userId,
                        'title' => esc($exp['title'] ?? ''),
                        'company' => esc($exp['organization'] ?? ''),
                        'location' => esc($exp['location'] ?? ''),
                        'start_date' => $this->formatDate($exp['start_year'] ?? null),
                        'end_date' => $this->formatDate($exp['end_year'] ?? null),
                        'is_current' => (int)($exp['is_current'] ?? false),
                        'description' => esc($exp['description'] ?? ''),
                    ]);
                }
            }

            // ── Education ──────────────────────────────────────────────────
            if (!empty($data['education']) && is_array($data['education'])) {
                $eduModel = model(EducationModel::class);
                foreach ($data['education'] as $edu) {
                    $eduModel->insert([
                        'user_id' => $this->userId,
                        'degree' => esc($edu['degree'] ?? ''),
                        'field' => esc($edu['field'] ?? ''),
                        'institution' => esc($edu['institution'] ?? ''),
                        'start_year' => (int)($edu['start_year'] ?? 0) ?: null,
                        'end_year' => (int)($edu['end_year'] ?? 0) ?: null,
                        'year_graduated' => (int)($edu['year_graduated'] ?? 0) ?: null,
                    ]);
                }
            }

            // ── Certifications ────────────────────────────────────────────
            if (!empty($data['certifications']) && is_array($data['certifications'])) {
                $certModel = model(CertificationModel::class);
                foreach ($data['certifications'] as $cert) {
                    $certModel->insert([
                        'user_id' => $this->userId,
                        'name' => esc($cert['name'] ?? ''),
                        'organization' => esc($cert['organization'] ?? ''),
                        'issue_date' => esc($cert['issue_date'] ?? ''),
                        'credential_url' => esc($cert['credential_url'] ?? ''),
                    ]);
                }
            }

            // Recalculate completeness
            $profile = $this->profileModel->getByUserId($this->userId);
            if ($profile) {
                $this->profileModel->recalculateCompleteness($profile->id);
            }

            // Clear session
            session()->remove('cv_parse_result');

            log_message('info', "Profile updated from CV for user {$this->userId}");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Profile updated successfully',
                'redirect' => '/profile',
            ]);

        } catch (\Exception $e) {
            log_message('error', "Save CV profile failed: {$e->getMessage()}");
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to save profile',
            ]);
        }
    }

    /**
     * Validate CV file before upload
     */
    private function validateCvFile($file): void
    {
        $config = config('CvParsing');

        // Check extension
        $ext = strtolower(pathinfo($file->getClientName(), PATHINFO_EXTENSION));
        if (!in_array($ext, $config->allowedExtensions)) {
            throw new \Exception(
                'Invalid file type. Allowed: ' . implode(', ', $config->allowedExtensions)
            );
        }

        // Check size
        $sizeMB = $file->getSize() / (1024 * 1024);
        if ($sizeMB > $config->maxFileSizeMB) {
            throw new \Exception(
                "File too large. Max: {$config->maxFileSizeMB}MB, Got: {$sizeMB}MB"
            );
        }
    }

    /**
     * Save uploaded file temporarily
     */
    private function savePlainUpload($file): string
    {
        $newName = 'cv_' . $this->userId . '_' . time() . '.' . $file->getClientExtension();
        $destination = WRITEPATH . 'uploads/cv/';

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        // Move file
        $file->move($destination, $newName);

        return $destination . $newName;
    }

    /**
     * Format year to date string (YYYY-01-01 format)
     */
    private function formatDate(?int $year): ?string
    {
        if (!$year || $year < 1900 || $year > date('Y')) {
            return null;
        }
        return "{$year}-01-01";
    }

    /**
     * Fallback CV parser using the PHP CvParser library.
     * Normalises output to match the Python service response shape so
     * the view JS can consume it identically.
     */
    private function fallbackParseCv(string $tempPath, string $mimeType): array
    {
        $parser = new CvParser();
        $raw    = $parser->parseDetailed($tempPath, $mimeType);

        return [
            'profile' => [
                'first_name'  => '',
                'last_name'   => '',
                'name'        => '',
                'headline'    => $raw['headline']['value'] ?? '',
                'email'       => $raw['email']['value'] ?? null,
                'phone'       => $raw['phone']['value'] ?? null,
                'city'        => null,
                'country'     => null,
                'summary'     => $raw['summary']['value'] ?? '',
                'confidences' => [
                    'first_name' => ['score' => 0.0],
                    'last_name'  => ['score' => 0.0],
                    'headline'   => ['score' => (float) ($raw['headline']['confidence'] ?? 0)],
                    'email'      => ['score' => (float) ($raw['email']['confidence'] ?? 0)],
                    'phone'      => ['score' => (float) ($raw['phone']['confidence'] ?? 0)],
                    'summary'    => ['score' => (float) ($raw['summary']['confidence'] ?? 0)],
                ],
            ],
            'skills'         => $raw['skills'] ?? [],
            'languages'      => $raw['languages'] ?? [],
            'experiences'    => $raw['experiences'] ?? [],
            'education'      => $raw['education'] ?? [],
            'certifications' => [],
        ];
    }
}
