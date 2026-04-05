<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $table         = 'profiles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id', 'headline', 'position', 'department', 'summary',
        'phone', 'phone_code', 'city', 'country',
        'linkedin', 'github', 'portfolio',
        'cv_file', 'cv_original_name', 'desired_salary',
        'desired_contract', 'desired_location', 'availability', 'completeness',
        'avatar',
    ];

    public function getByUserId(int $userId): ?object
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Calculate and update profile completeness percentage.
     */
    public function recalculateCompleteness(int $userId): int
    {
        $profile = $this->getByUserId($userId);
        if (!$profile) {
            return 0;
        }

        $fields = [
            'headline', 'summary', 'phone', 'city',
            'country', 'cv_file', 'desired_contract',
        ];
        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($profile->$field)) {
                $filled++;
            }
        }

        // Check skills, experience, education
        $skillModel = model(SkillModel::class);
        $expModel   = model(ExperienceModel::class);
        $eduModel   = model(EducationModel::class);

        if ($skillModel->where('user_id', $userId)->countAllResults() > 0) {
            $filled++;
        }
        if ($expModel->where('user_id', $userId)->countAllResults() > 0) {
            $filled++;
        }
        if ($eduModel->where('user_id', $userId)->countAllResults() > 0) {
            $filled++;
        }

        $total = count($fields) + 3;
        $completeness = (int) round(($filled / $total) * 100);

        $this->update($profile->id, ['completeness' => $completeness]);
        return $completeness;
    }
}
