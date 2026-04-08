<?php

namespace App\Models;

use CodeIgniter\Model;

class JobAlertModel extends Model
{
    protected $table         = 'job_alerts';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id', 'keywords', 'location', 'contract_type',
        'frequency', 'is_active', 'last_sent',
    ];

    public function getActiveAlertsForJob(object $job): array
    {
        $alerts = $this->where('is_active', 1)->findAll();
        $matched = [];

        foreach ($alerts as $alert) {
            $match = false;

            if (!empty($alert->keywords)) {
                $keywords = explode(',', strtolower($alert->keywords));
                $jobText  = strtolower($job->title . ' ' . $job->description);
                foreach ($keywords as $kw) {
                    if (str_contains($jobText, trim($kw))) {
                        $match = true;
                        break;
                    }
                }
            } else {
                $match = true;
            }

            if ($match && !empty($alert->contract_type) && $alert->contract_type !== $job->contract_type) {
                $match = false;
            }

            if ($match && !empty($alert->location)) {
                if (!str_contains(strtolower($job->location ?? ''), strtolower($alert->location))) {
                    $match = false;
                }
            }

            if ($match) {
                $matched[] = $alert;
            }
        }

        return $matched;
    }
}
