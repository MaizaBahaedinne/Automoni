<?php

namespace App\Models;

use CodeIgniter\Model;

class InterviewModel extends Model
{
    protected $table         = 'interviews';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'application_id', 'recruiter_id', 'type',
        'scheduled_at', 'duration_min', 'location', 'notes', 'status',
    ];

    public function getByApplication(int $applicationId): ?object
    {
        return $this->where('application_id', $applicationId)
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }
}
