<?php

namespace App\Models;

use CodeIgniter\Model;

class JobPrescreeningModel extends Model
{
    protected $table      = 'job_prescreening_questions';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'job_id', 'sort_order', 'question_text',
        'question_type', 'expected_answer', 'is_eliminatory',
    ];

    public function getByJob(int $jobId): array
    {
        return $this->where('job_id', $jobId)->orderBy('sort_order', 'ASC')->findAll();
    }

    public function syncForJob(int $jobId, array $rows): void
    {
        $this->where('job_id', $jobId)->delete();
        $order = 0;
        foreach ($rows as $row) {
            if (empty($row['question_text'])) continue;
            $this->insert([
                'job_id'          => $jobId,
                'sort_order'      => $order++,
                'question_text'   => trim($row['question_text']),
                'question_type'   => in_array($row['question_type'] ?? '', ['yes_no','text','number'])
                                        ? $row['question_type'] : 'yes_no',
                'expected_answer' => mb_substr(trim($row['expected_answer'] ?? ''), 0, 500),
                'is_eliminatory'  => (int) ($row['is_eliminatory'] ?? 0),
            ]);
        }
    }
}
