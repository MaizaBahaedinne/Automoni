<?php

namespace App\Models;

use CodeIgniter\Model;

class JobRecruitmentStepModel extends Model
{
    protected $table      = 'job_recruitment_steps';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'job_id', 'step_order', 'step_name', 'description', 'responsible', 'duration_days',
    ];

    public function getByJob(int $jobId): array
    {
        return $this->where('job_id', $jobId)->orderBy('step_order', 'ASC')->findAll();
    }

    public function syncForJob(int $jobId, array $rows): void
    {
        $this->where('job_id', $jobId)->delete();
        $order = 0;
        foreach ($rows as $row) {
            if (empty($row['step_name'])) continue;
            $this->insert([
                'job_id'        => $jobId,
                'step_order'    => $order++,
                'step_name'     => mb_substr(trim($row['step_name']), 0, 200),
                'description'   => trim($row['description'] ?? ''),
                'responsible'   => mb_substr(trim($row['responsible'] ?? ''), 0, 200),
                'duration_days' => !empty($row['duration_days']) ? (int) $row['duration_days'] : null,
            ]);
        }
    }
}
