<?php

namespace App\Models;

use CodeIgniter\Model;

class JobCertificationModel extends Model
{
    protected $table      = 'job_certifications';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $allowedFields = ['job_id', 'certification_name', 'is_required', 'delay_months'];

    public function getByJob(int $jobId): array
    {
        return $this->where('job_id', $jobId)->findAll();
    }

    public function syncForJob(int $jobId, array $rows): void
    {
        $this->where('job_id', $jobId)->delete();
        foreach ($rows as $row) {
            if (empty($row['certification_name'])) continue;
            $this->insert([
                'job_id'             => $jobId,
                'certification_name' => mb_substr(trim($row['certification_name']), 0, 500),
                'is_required'        => (int) ($row['is_required'] ?? 0),
                'delay_months'       => !empty($row['delay_months']) ? (int) $row['delay_months'] : null,
            ]);
        }
    }
}
