<?php

namespace App\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
    protected $table          = 'jobs';
    protected $primaryKey     = 'id';
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'company_id', 'user_id', 'title', 'slug', 'description',
        'requirements', 'benefits', 'contract_type', 'location', 'remote',
        'salary_min', 'salary_max', 'salary_currency', 'experience_level',
        'status', 'views', 'expires_at',
        // Enhanced fields
        'internal_ref', 'department', 'num_positions', 'hierarchical_level',
        'direct_manager', 'min_experience_years', 'education_level', 'education_field',
        'mission_context', 'salary_period', 'salary_public', 'salary_variable',
        'salary_bonus_pct', 'recruitment_notes', 'internal_only', 'visibility_level',
    ];

    protected $validationRules = [
        'title'         => 'required|min_length[5]|max_length[255]',
        'description'   => 'required|min_length[50]',
        'contract_type' => 'required|in_list[CDI,CDD,Freelance,Internship,PartTime]',
    ];

    protected $beforeInsert = ['generateSlug'];

    protected function generateSlug(array $data): array
    {
        if (isset($data['data']['title'])) {
            $base  = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $data['data']['title']));
            $slug  = $base . '-' . time();
            $data['data']['slug'] = $slug;
        }
        return $data;
    }

    /**
     * Paginated job search with filters.
     */
    public function search(array $filters = [], int $perPage = 15): array
    {
        $builder = $this->select('jobs.*, companies.name as company_name, companies.logo as company_logo')
                        ->join('companies', 'companies.id = jobs.company_id')
                        ->where('jobs.status', 'active')
                        ->orderBy('jobs.created_at', 'DESC');

        if (!empty($filters['keyword'])) {
            $kw = esc($filters['keyword']);
            $builder->groupStart()
                    ->like('jobs.title', $kw)
                    ->orLike('jobs.description', $kw)
                    ->groupEnd();
        }
        if (!empty($filters['location'])) {
            $builder->like('jobs.location', esc($filters['location']));
        }
        if (!empty($filters['contract_type'])) {
            $builder->where('jobs.contract_type', $filters['contract_type']);
        }
        if (!empty($filters['remote'])) {
            $builder->where('jobs.remote', $filters['remote']);
        }
        if (!empty($filters['experience_level'])) {
            $builder->where('jobs.experience_level', $filters['experience_level']);
        }

        return $builder->paginate($perPage);
    }

    /**
     * Get jobs recommended for a user based on their skills.
     */
    public function getRecommended(int $userId, int $limit = 6): array
    {
        $skills = model(SkillModel::class)->getByUserId($userId);
        if (empty($skills)) {
            return $this->select('jobs.*, companies.name as company_name, companies.logo as company_logo')
                        ->join('companies', 'companies.id = jobs.company_id')
                        ->where('jobs.status', 'active')
                        ->orderBy('jobs.created_at', 'DESC')
                        ->limit($limit)
                        ->findAll();
        }

        $skillNames = array_column((array) $skills, 'skill_name');

        return $this->select('jobs.*, companies.name as company_name, companies.logo as company_logo')
                    ->join('companies', 'companies.id = jobs.company_id')
                    ->join('job_skills', 'job_skills.job_id = jobs.id')
                    ->whereIn('job_skills.skill_name', $skillNames)
                    ->where('jobs.status', 'active')
                    ->groupBy('jobs.id')
                    ->orderBy('jobs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function incrementViews(int $id): void
    {
        $this->set('views', 'views + 1', false)->where('id', $id)->update();
    }
}
