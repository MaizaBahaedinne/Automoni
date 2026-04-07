<?php

namespace App\Services;

use App\Libraries\CvParser;
use App\Models\{ProfileModel, SkillModel, ExperienceModel, EducationModel, LanguageModel};
use CodeIgniter\Cache\CacheInterface;

class CvPreviewService
{
    private CacheInterface $cache;
    private ProfileModel $profileModel;
    private SkillModel $skillModel;
    private ExperienceModel $experienceModel;
    private EducationModel $educationModel;
    private LanguageModel $languageModel;
    private CvParser $cvParser;
    private int $userId;
    private int $cacheTtl = 3600; // 1 hour

    public function __construct()
    {
        $this->cache = service('cache');
        $this->profileModel = model(ProfileModel::class);
        $this->skillModel = model(SkillModel::class);
        $this->experienceModel = model(ExperienceModel::class);
        $this->educationModel = model(EducationModel::class);
        $this->languageModel = model(LanguageModel::class);
        $this->cvParser = new CvParser();
        $this->userId = (int) (session()->get('user_id') ?? 0);
    }

    /**
     * Parse CV file and return a preview of extracted data.
     * Does NOT save anything to the database.
     *
     * @param  string $filePath Absolute path to CV file
     * @param  string $mimeType MIME type of file
     * @return array Preview data with profile, experiences, education, skills
     * @throws \RuntimeException If parsing fails
     */
    public function parseAndPreview(string $filePath, string $mimeType): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException('CV file not found: ' . $filePath);
        }

        // Parse with detailed extraction
        $parsed = $this->cvParser->parseDetailed($filePath, $mimeType);

        // Map parsed data to database entities
        $preview = [
            'profile'     => $this->mapProfileData($parsed),
            'experiences' => $this->mapExperiencesData($parsed['experiences'] ?? []),
            'education'   => $this->mapEducationData($parsed['education'] ?? []),
            'skills'      => $this->mapSkillsData($parsed['skills'] ?? []),
            'languages'   => $this->mapLanguagesData($parsed['languages'] ?? []),
            'metadata'    => [
                'parsed_at'            => date('Y-m-d H:i:s'),
                'overall_confidence'   => $parsed['overall_confidence'] ?? 0.0,
                'file_path'            => $filePath,
                'mime_type'            => $mimeType,
            ],
        ];

        // Store in cache for 1 hour
        $this->storePreviewInCache($preview);

        return $preview;
    }

    /**
     * Get stored preview from cache.
     */
    public function getPreviewFromCache(): ?array
    {
        $cacheKey = $this->getCacheKey();
        $preview = $this->cache->get($cacheKey);
        
        return $preview ? (array) json_decode(json_encode($preview), true) : null;
    }

    /**
     * Store preview data in cache.
     */
    private function storePreviewInCache(array $preview): void
    {
        $cacheKey = $this->getCacheKey();
        $this->cache->save($cacheKey, $preview, $this->cacheTtl);
    }

    /**
     * Clear cached preview.
     */
    public function clearPreviewFromCache(): void
    {
        $cacheKey = $this->getCacheKey();
        $this->cache->delete($cacheKey);
    }

    /**
     * Apply cached preview to database (PERSIST).
     * This is the moment when data is actually saved.
     *
     * @param  array $edits User-provided edits to the preview
     * @return bool Success status
     */
    public function applyPreview(array $edits = []): bool
    {
        $preview = $this->getPreviewFromCache();
        if (!$preview) {
            throw new \RuntimeException('No preview found in cache. Please parse CV first.');
        }

        // Merge user edits with preview
        $profileData = array_merge($preview['profile'] ?? [], $edits['profile'] ?? []);
        $experiencesData = array_merge($preview['experiences'] ?? [], $edits['experiences'] ?? []);
        $educationData = array_merge($preview['education'] ?? [], $edits['education'] ?? []);
        $skillsData = array_merge($preview['skills'] ?? [], $edits['skills'] ?? []);
        $languagesData = array_merge($preview['languages'] ?? [], $edits['languages'] ?? []);

        try {
            // Start transaction
            $db = \Config\Database::connect();
            $db->transBegin();

            // 1. Update profile
            $profile = $this->profileModel->getByUserId($this->userId);
            if ($profile) {
                $this->profileModel->update($profile->id, $profileData);
            } else {
                $profileData['user_id'] = $this->userId;
                $this->profileModel->insert($profileData);
            }

            // 2. Save experiences
            if (!empty($experiencesData)) {
                // Clear old experiences if needed (optional: preserve old ones instead)
                $this->experienceModel->where('user_id', $this->userId)->delete();
                foreach ($experiencesData as $exp) {
                    $this->experienceModel->insert([
                        'user_id'    => $this->userId,
                        'title'      => $exp['title'] ?? '',
                        'organization_id' => null, // Or lookup based on name
                        'start_year' => $exp['start_year'] ?? null,
                        'end_year'   => $exp['end_year'] ?? null,
                        'description' => $exp['description'] ?? '',
                    ]);
                }
            }

            // 3. Save education
            if (!empty($educationData)) {
                $this->educationModel->where('user_id', $this->userId)->delete();
                foreach ($educationData as $edu) {
                    $this->educationModel->insert([
                        'user_id'    => $this->userId,
                        'institution' => $edu['institution'] ?? '',
                        'degree'     => $edu['degree'] ?? '',
                        'field'      => $edu['field'] ?? '',
                        'year'       => $edu['year'] ?? null,
                    ]);
                }
            }

            // 4. Sync skills
            if (!empty($skillsData)) {
                $skillStructured = array_map(fn($s) => [
                    'name'  => $s['name'] ?? '',
                    'level' => $s['level'] ?? 'intermediate',
                ], $skillsData);
                $this->skillModel->syncSkills($this->userId, $skillStructured);
            }

            // 5. Sync languages
            if (!empty($languagesData)) {
                $this->languageModel->where('user_id', $this->userId)->delete();
                foreach ($languagesData as $lang) {
                    $this->languageModel->insert([
                        'user_id'   => $this->userId,
                        'language'  => $lang['name'] ?? '',
                        'level'     => $lang['level'] ?? 'intermediate',
                    ]);
                }
            }

            // 6. Update profile completeness
            $profile = $this->profileModel->getByUserId($this->userId);
            if ($profile) {
                $this->profileModel->recalculateCompleteness($profile->id);
            }

            // Commit transaction
            $db->transCommit();

            // Clear cache after successful apply
            $this->clearPreviewFromCache();

            return true;
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'CV preview apply failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Map parsed CV profile data to Profile model fields.
     */
    private function mapProfileData(array $parsed): array
    {
        return [
            'headline' => $parsed['headline']['value'] ?? null,
            'summary'  => $parsed['summary']['value'] ?? null,
            'phone'    => $this->cleanPhone($parsed['phone']['value'] ?? null),
            'email'    => $parsed['email']['value'] ?? null, // If we want to store it
        ];
    }

    /**
     * Map parsed experiences to Experience model fields.
     */
    private function mapExperiencesData(array $experiences): array
    {
        return array_map(function ($exp) {
            return [
                'title'       => $exp['title'] ?? '',
                'organization' => $exp['organization'] ?? '',
                'start_year'  => $exp['start_year'] ?? null,
                'end_year'    => $exp['end_year'] ?? null,
                'description' => $exp['description'] ?? '',
                'confidence'  => $exp['confidence'] ?? 0.0,
            ];
        }, $experiences);
    }

    /**
     * Map parsed education to Education model fields.
     */
    private function mapEducationData(array $education): array
    {
        return array_map(function ($edu) {
            return [
                'institution'  => $edu['institution'] ?? '',
                'degree'       => $edu['degree'] ?? '',
                'field'        => $edu['field'] ?? '',
                'year'         => $edu['year'] ?? null,
                'confidence'   => $edu['confidence'] ?? 0.0,
            ];
        }, $education);
    }

    /**
     * Map parsed skills for display/editing.
     */
    private function mapSkillsData(array $skills): array
    {
        return array_map(function ($skill) {
            return [
                'name'       => $skill['name'] ?? '',
                'level'      => $skill['level'] ?? 'intermediate',
                'confidence' => $skill['confidence'] ?? 0.0,
            ];
        }, $skills);
    }

    /**
     * Map parsed languages for display/editing.
     */
    private function mapLanguagesData(array $languages): array
    {
        return array_map(function ($lang) {
            return [
                'name'       => $lang['name'] ?? '',
                'level'      => $lang['level'] ?? 'intermediate',
                'confidence' => $lang['confidence'] ?? 0.0,
            ];
        }, $languages);
    }

    /**
     * Clean phone number for storage.
     */
    private function cleanPhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }
        return substr(preg_replace('/[^0-9+\s\-()]/', '', $phone), 0, 30);
    }

    /**
     * Get cache key for this user's CV preview.
     */
    private function getCacheKey(): string
    {
        return "cv_preview_{$this->userId}";
    }

    /**
     * Set user ID (for testing or batch operations).
     */
    public function setUserId(int $userId): self
    {
        $this->userId = max(0, $userId);
        return $this;
    }
}
