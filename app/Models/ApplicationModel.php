<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table         = 'applications';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'job_id', 'user_id', 'cover_letter', 'cv_file',
        'status', 'recruiter_note', 'applied_at', 'updated_at',
    ];

    protected $beforeInsert = ['setAppliedAt'];
    protected $beforeUpdate = ['setUpdatedAt'];

    protected function setAppliedAt(array $data): array
    {
        $data['data']['applied_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function setUpdatedAt(array $data): array
    {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function hasApplied(int $userId, int $jobId): bool
    {
        return $this->where('user_id', $userId)->where('job_id', $jobId)->countAllResults() > 0;
    }

    public function getApplicationsForRecruiter(int $recruiterId): array
    {
        return $this->select('applications.*, jobs.title as job_title, users.first_name, users.last_name, users.email')
                    ->join('jobs', 'jobs.id = applications.job_id')
                    ->join('users', 'users.id = applications.user_id')
                    ->where('jobs.user_id', $recruiterId)
                    ->orderBy('applications.applied_at', 'DESC')
                    ->findAll();
    }

    public function getApplicationsForUser(int $userId): array
    {
        return $this->select('applications.*, jobs.title as job_title, companies.name as company_name')
                    ->join('jobs', 'jobs.id = applications.job_id')
                    ->join('companies', 'companies.id = jobs.company_id')
                    ->where('applications.user_id', $userId)
                    ->orderBy('applications.applied_at', 'DESC')
                    ->findAll();
    }
}
