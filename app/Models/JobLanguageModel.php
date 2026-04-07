<?php

namespace App\Models;

use CodeIgniter\Model;

class JobLanguageModel extends Model
{
    protected $table      = 'job_languages';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $allowedFields = ['job_id', 'language', 'level_code', 'is_required'];

    public function getByJob(int $jobId): array
    {
        return $this->where('job_id', $jobId)->findAll();
    }

    public function syncForJob(int $jobId, array $rows): void
    {
        $this->where('job_id', $jobId)->delete();
        foreach ($rows as $row) {
            if (empty($row['language'])) continue;
            $this->insert([
                'job_id'      => $jobId,
                'language'    => mb_substr(trim($row['language']), 0, 100),
                'level_code'  => $row['level_code']  ?? 'B2',
                'is_required' => (int) ($row['is_required'] ?? 1),
            ]);
        }
    }
}
