<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table         = 'applications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'job_id', 'user_id', 'cover_letter', 'cv_file',
        'status', 'recruiter_note', 'rejection_reason', 'applied_at', 'updated_at',
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
        // Returns applications for:
        //  1. Jobs posted directly by this recruiter
        //  2. Jobs posted by any member of organizations managed by this recruiter
        // Also resolves the organization name via a correlated sub-select.
        $sql = "
            SELECT DISTINCT
                a.*,
                j.title AS job_title,
                CONCAT(u.first_name, ' ', u.last_name) AS candidate_name,
                u.first_name, u.last_name, u.email, u.avatar,
                (
                    SELECT org.name
                    FROM organization_members om_job
                    JOIN organization_members om_me
                         ON om_me.organization_id = om_job.organization_id
                        AND om_me.user_id = ?
                    JOIN organizations org ON org.id = om_job.organization_id
                    WHERE om_job.user_id = j.user_id
                    ORDER BY org.name
                    LIMIT 1
                ) AS org_name,
                (
                    SELECT org2.id
                    FROM organization_members om_job2
                    JOIN organization_members om_me2
                         ON om_me2.organization_id = om_job2.organization_id
                        AND om_me2.user_id = ?
                    JOIN organizations org2 ON org2.id = om_job2.organization_id
                    WHERE om_job2.user_id = j.user_id
                    ORDER BY org2.name
                    LIMIT 1
                ) AS org_id
            FROM applications a
            JOIN jobs j ON j.id = a.job_id
            JOIN users u ON u.id = a.user_id AND u.deleted_at IS NULL
            WHERE
                j.user_id = ?
                OR j.user_id IN (
                    SELECT om_j.user_id
                    FROM organization_members om_me
                    JOIN organization_members om_j
                         ON om_j.organization_id = om_me.organization_id
                    WHERE om_me.user_id = ?
                      AND om_me.role IN ('owner', 'manager')
                )
            ORDER BY a.applied_at DESC
        ";

        $query = $this->db->query($sql, [
            $recruiterId, $recruiterId,   // correlated sub-selects
            $recruiterId, $recruiterId,   // WHERE clause
        ]);

        return $query ? $query->getResultObject() : [];
    }

    public function getApplicationsForUser(int $userId): array
    {
        return $this->select('applications.*, jobs.title AS job_title, jobs.slug, jobs.expires_at, companies.name as company_name')
                    ->join('jobs', 'jobs.id = applications.job_id')
                    ->join('companies', 'companies.id = jobs.company_id')
                    ->where('applications.user_id', $userId)
                    ->orderBy('applications.applied_at', 'DESC')
                    ->findAll();
    }

    /**
     * All applications for the admin dashboard — with optional filters.
     */
    public function getAllForAdmin(?string $status, ?string $search, ?string $from, ?string $to, int $perPage = 40): array
    {
        $builder = $this->select(
                'applications.*,
                 jobs.title          AS job_title,
                 jobs.id             AS job_id_ref,
                 companies.name      AS company_name,
                 users.first_name, users.last_name, users.email'
            )
            ->join('jobs',      'jobs.id      = applications.job_id')
            ->join('companies', 'companies.id = jobs.company_id')
            ->join('users',     'users.id     = applications.user_id');

        if ($status && $status !== 'all') {
            $builder->where('applications.status', $status);
        }
        if ($search) {
            $builder->groupStart()
                    ->like('jobs.title', $search)
                    ->orLike('users.first_name', $search)
                    ->orLike('users.last_name', $search)
                    ->orLike('users.email', $search)
                    ->orLike('companies.name', $search)
                    ->groupEnd();
        }
        if ($from) {
            $builder->where('applications.applied_at >=', $from . ' 00:00:00');
        }
        if ($to) {
            $builder->where('applications.applied_at <=', $to . ' 23:59:59');
        }

        return $builder->orderBy('applications.applied_at', 'DESC')
                       ->paginate($perPage, 'admin_apps');
    }

    /**
     * Status counts for the admin stats strip.
     *
     * @return array<string,int>
     */
    public function statusCounts(): array
    {
        $rows = $this->db->query(
            'SELECT status, COUNT(*) AS n FROM applications GROUP BY status'
        )->getResultObject();

        $counts = ['total' => 0, 'pending' => 0, 'reviewing' => 0, 'accepted' => 0, 'rejected' => 0];
        foreach ($rows as $r) {
            $counts[$r->status] = (int) $r->n;
            $counts['total']    += (int) $r->n;
        }
        return $counts;
    }
}
