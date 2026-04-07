<?php

namespace App\Helpers;

use App\Libraries\CvParser;
use App\Services\CvPreviewService;

/**
 * CV Analysis Helper
 * 
 * Centralizes all CV analysis, parsing, preview generation, and intelligent
 * profile pre-filling functionality.
 * 
 * Usage:
 *   $analysis = new CvAnalysisHelper();
 *   $preview = $analysis->analyzeUserCv($userId);
 */
class CvAnalysisHelper
{
    private CvParser $parser;
    private CvPreviewService $previewService;

    public function __construct()
    {
        $this->parser = new CvParser();
        $this->previewService = new CvPreviewService();
    }

    /**
     * Analyze user's CV and return preview
     * 
     * Flow:
     * 1. Get user's CV file path
     * 2. Parse with detailed extraction
     * 3. Store in cache
     * 4. Return preview data
     * 
     * @param int $userId User ID
     * @return array Preview data or error array
     * 
     * @example
     *   $result = CvAnalysisHelper::analyze($userId);
     *   if ($result['success']) {
     *       echo "CV analyzed: " . $result['data']['profile']['headline']['value'];
     *   } else {
     *       echo "Error: " . $result['message'];
     *   }
     */
    public function analyzeUserCv(int $userId): array
    {
        try {
            // Get user's CV file
            $profile = model('ProfileModel')->getByUserId($userId);
            
            if (!$profile || !$profile->cv_file) {
                return [
                    'success' => false,
                    'message' => 'No CV found for this user',
                    'code' => 'NO_CV_FOUND',
                ];
            }

            $cvPath = WRITEPATH . 'uploads/cv/' . $profile->cv_file;
            
            if (!file_exists($cvPath)) {
                return [
                    'success' => false,
                    'message' => 'CV file not found on disk',
                    'code' => 'CV_FILE_MISSING',
                ];
            }

            // Parse and generate preview
            $this->previewService->setUserId($userId);
            $preview = $this->previewService->parseAndPreview($cvPath, mime_content_type($cvPath));

            return [
                'success' => true,
                'message' => 'CV analyzed successfully',
                'data' => $preview,
                'code' => 'ANALYSIS_SUCCESS',
            ];
        } catch (\Throwable $e) {
            log_message('error', 'CV analysis failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to analyze CV: ' . $e->getMessage(),
                'code' => 'ANALYSIS_ERROR',
                'exception' => $e,
            ];
        }
    }

    /**
     * Get cached preview for user
     * 
     * @param int $userId User ID
     * @return array|null Preview data or null if expired/not found
     */
    public function getPreview(int $userId): ?array
    {
        $this->previewService->setUserId($userId);
        return $this->previewService->getPreviewFromCache();
    }

    /**
     * Apply preview to user's profile (PERSIST to DB)
     * 
     * Flow:
     * 1. Get cached preview
     * 2. Merge user edits
     * 3. Start transaction
     * 4. Save to: profiles, experiences, education, skills, languages
     * 5. Rollback if error
     * 
     * @param int $userId User ID
     * @param array $edits User-provided edits to merge with preview
     * @return array Success/error result
     * 
     * @example
     *   $edits = [
     *       'profile' => ['headline' => 'Modified headline'],
     *       'skills' => [...]
     *   ];
     *   $result = CvAnalysisHelper::applyPreview($userId, $edits);
     */
    public function applyPreview(int $userId, array $edits = []): array
    {
        try {
            $this->previewService->setUserId($userId);
            $this->previewService->applyPreview($edits);

            // Recalculate completeness
            $profile = model('ProfileModel')->getByUserId($userId);
            if ($profile) {
                model('ProfileModel')->recalculateCompleteness($profile->id);
            }

            return [
                'success' => true,
                'message' => 'Profile updated successfully from CV',
                'code' => 'APPLY_SUCCESS',
            ];
        } catch (\Throwable $e) {
            log_message('error', 'CV preview apply failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to apply preview: ' . $e->getMessage(),
                'code' => 'APPLY_ERROR',
                'exception' => $e,
            ];
        }
    }

    /**
     * Clear cached preview for user
     * 
     * @param int $userId User ID
     * @return bool Success status
     */
    public function clearPreview(int $userId): bool
    {
        try {
            $this->previewService->setUserId($userId);
            $this->previewService->clearPreviewFromCache();
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Clear preview failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract specific field from CV (without full preview)
     * 
     * Lightweight parsing for extracting single fields:
     * - email
     * - phone
     * - skills
     * - languages
     * 
     * @param int $userId User ID
     * @param string $field Field to extract (email|phone|skills|languages|headline|summary)
     * @return mixed|null Extracted value or null
     * 
     * @example
     *   $email = CvAnalysisHelper::extractField($userId, 'email');
     *   $skills = CvAnalysisHelper::extractField($userId, 'skills');
     */
    public function extractField(int $userId, string $field)
    {
        try {
            $profile = model('ProfileModel')->getByUserId($userId);
            
            if (!$profile || !$profile->cv_file) {
                return null;
            }

            $cvPath = WRITEPATH . 'uploads/cv/' . $profile->cv_file;
            
            if (!file_exists($cvPath)) {
                return null;
            }

            // Parse CV
            $parsed = $this->parser->parseDetailed($cvPath, mime_content_type($cvPath));

            // Return requested field
            if ($field === 'email') {
                return $parsed['email']['value'] ?? null;
            } elseif ($field === 'phone') {
                return $parsed['phone']['value'] ?? null;
            } elseif ($field === 'skills') {
                return array_map(fn($s) => $s['name'], $parsed['skills'] ?? []);
            } elseif ($field === 'languages') {
                return array_map(fn($l) => $l['name'], $parsed['languages'] ?? []);
            } elseif ($field === 'headline') {
                return $parsed['headline']['value'] ?? null;
            } elseif ($field === 'summary') {
                return $parsed['summary']['value'] ?? null;
            }

            return null;
        } catch (\Throwable $e) {
            log_message('error', 'Extract field failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get confidence score for a field from cached preview
     * 
     * @param int $userId User ID
     * @param string $section Section (profile|experiences|education|skills|languages)
     * @param string $field Field name (e.g., headline, title, name)
     * @return float|null Confidence score (0-1) or null
     * 
     * @example
     *   $confidence = $helper->getFieldConfidence($userId, 'profile', 'headline');
     *   echo "Headline confidence: " . ($confidence * 100) . "%";
     */
    public function getFieldConfidence(int $userId, string $section, string $field): ?float
    {
        $preview = $this->getPreview($userId);
        
        if (!$preview) {
            return null;
        }

        return $preview[$section][$field]['confidence'] ?? $preview['metadata']['overall_confidence'] ?? null;
    }

    /**
     * Get overall parsing confidence for user's CV
     * 
     * @param int $userId User ID
     * @return float|null Overall confidence (0-1) or null
     * 
     * @example
     *   $confidence = $helper->getOverallConfidence($userId);
     *   if ($confidence > 0.85) {
     *       echo "High confidence extraction!";
     *   }
     */
    public function getOverallConfidence(int $userId): ?float
    {
        $preview = $this->getPreview($userId);
        return $preview['metadata']['overall_confidence'] ?? null;
    }

    /**
     * Check if CV has been recently analyzed (cache exists)
     * 
     * @param int $userId User ID
     * @return bool True if valid preview exists in cache
     */
    public function hasRecentAnalysis(int $userId): bool
    {
        return $this->getPreview($userId) !== null;
    }

    /**
     * Compare CV data with current profile data
     * 
     * Shows what fields are present in CV vs already in profile:
     * - Fields only in CV
     * - Fields that differ
     * - Fields already in profile
     * 
     * @param int $userId User ID
     * @return array Comparison data
     * 
     * @example
     *   $diff = $helper->compareWithProfile($userId);
     *   echo "Missing fields: " . implode(', ', $diff['missing']);
     */
    public function compareWithProfile(int $userId): array
    {
        try {
            $profile = model('ProfileModel')->getByUserId($userId);
            $preview = $this->getPreview($userId);

            if (!$profile || !$preview) {
                return [
                    'missing' => [],
                    'differs' => [],
                    'complete' => [],
                ];
            }

            $missing = [];
            $differs = [];
            $complete = [];

            // Profile fields
            if (!empty($preview['profile']['headline']['value']) && empty($profile->headline)) {
                $missing[] = 'headline';
            } elseif (!empty($preview['profile']['headline']['value']) && $profile->headline !== $preview['profile']['headline']['value']) {
                $differs[] = 'headline';
            } else {
                $complete[] = 'headline';
            }

            // Skills count
            $cvSkillsCount = count($preview['skills'] ?? []);
            $currentSkills = model('SkillModel')->getByUserId($userId);
            if ($cvSkillsCount > count($currentSkills ?? [])) {
                $missing[] = 'skills';
            } elseif ($cvSkillsCount > 0) {
                $complete[] = 'skills';
            }

            // Experiences
            $cvExpCount = count($preview['experiences'] ?? []);
            $currentExp = model('ExperienceModel')->getByUserId($userId);
            if ($cvExpCount > count($currentExp ?? [])) {
                $missing[] = 'experiences';
            } elseif ($cvExpCount > 0) {
                $complete[] = 'experiences';
            }

            // Education
            $cvEduCount = count($preview['education'] ?? []);
            $currentEdu = model('EducationModel')->getByUserId($userId);
            if ($cvEduCount > count($currentEdu ?? [])) {
                $missing[] = 'education';
            } elseif ($cvEduCount > 0) {
                $complete[] = 'education';
            }

            return [
                'missing' => array_unique($missing),
                'differs' => array_unique($differs),
                'complete' => array_unique($complete),
                'profile_completeness' => $profile->completeness ?? 0,
                'potential_completeness' => $this->estimateCompleteness($userId, $preview),
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Compare profile failed: ' . $e->getMessage());
            return ['missing' => [], 'differs' => [], 'complete' => []];
        }
    }

    /**
     * Estimate what profile completeness would be after applying preview
     * 
     * @param int $userId User ID
     * @param array $preview Preview data
     * @return int Estimated completeness percentage (0-100)
     */
    private function estimateCompleteness(int $userId, array $preview): int
    {
        $score = 0;

        // Profile sections
        if (!empty($preview['profile']['headline']['value'])) $score += 10;
        if (!empty($preview['profile']['summary']['value'])) $score += 10;
        if (!empty($preview['profile']['phone']['value'])) $score += 10;

        // Data sections
        if (!empty($preview['skills'])) $score += 20;
        if (!empty($preview['experiences'])) $score += 20;
        if (!empty($preview['education'])) $score += 20;
        if (!empty($preview['languages'])) $score += 10;

        return min(100, $score);
    }

    /**
     * Get extraction quality report for user
     * 
     * Returns detailed info about extraction quality:
     * - Which fields have high/medium/low confidence
     * - Warnings or issues
     * - Recommendations
     * 
     * @param int $userId User ID
     * @return array Quality report
     */
    public function getQualityReport(int $userId): array
    {
        $preview = $this->getPreview($userId);
        
        if (!$preview) {
            return [
                'overall_confidence' => 0,
                'quality_level' => 'unknown',
                'high_confidence_fields' => [],
                'medium_confidence_fields' => [],
                'low_confidence_fields' => [],
                'warnings' => ['No preview available'],
                'recommendations' => ['Please analyze CV first'],
            ];
        }

        $high = [];
        $medium = [];
        $low = [];
        $warnings = [];
        $recommendations = [];

        // Analyze profile fields
        foreach (['headline', 'summary', 'phone', 'email'] as $field) {
            if (!empty($preview['profile'][$field])) {
                $confidence = $preview['profile'][$field]['confidence'] ?? 0;
                
                if ($confidence >= 0.90) {
                    $high[] = $field;
                } elseif ($confidence >= 0.75) {
                    $medium[] = $field;
                } else {
                    $low[] = $field;
                }
            }
        }

        // Check for issues
        if (empty($preview['experiences']) && empty($preview['education'])) {
            $warnings[] = 'No experience or education data found';
            $recommendations[] = 'CV may not contain structured experience section';
        }

        if (count($preview['skills'] ?? []) < 3) {
            $warnings[] = 'Few skills extracted (< 3)';
            $recommendations[] = 'Add more technical skills to CV';
        }

        $overall = $preview['metadata']['overall_confidence'] ?? 0.5;

        return [
            'overall_confidence' => $overall,
            'quality_level' => $overall >= 0.85 ? 'excellent' : ($overall >= 0.70 ? 'good' : 'fair'),
            'high_confidence_fields' => $high,
            'medium_confidence_fields' => $medium,
            'low_confidence_fields' => $low,
            'warnings' => $warnings,
            'recommendations' => $recommendations,
            'data_extracted' => [
                'profile_fields' => count(array_filter($preview['profile'])),
                'skills' => count($preview['skills'] ?? []),
                'experiences' => count($preview['experiences'] ?? []),
                'education' => count($preview['education'] ?? []),
                'languages' => count($preview['languages'] ?? []),
            ],
        ];
    }

    /**
     * Log CV analysis event for analytics
     * 
     * @param int $userId User ID
     * @param string $action Action (analyze|preview|apply|clear)
     * @param array $metadata Additional metadata
     * @return void
     */
    public function logAnalysisEvent(int $userId, string $action, array $metadata = []): void
    {
        try {
            $event = [
                'user_id' => $userId,
                'action' => $action,
                'timestamp' => date('Y-m-d H:i:s'),
                'metadata' => $metadata,
            ];

            log_message('info', 'CV Analysis Event: ' . json_encode($event));
        } catch (\Throwable $e) {
            log_message('error', 'Log analysis event failed: ' . $e->getMessage());
        }
    }
}
