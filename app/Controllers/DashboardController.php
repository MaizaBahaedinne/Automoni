<?php

namespace App\Controllers;

use App\Models\{JobModel, ApplicationModel, ProfileModel, CompanyModel};
use CodeIgniter\Controller;

class DashboardController extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $role   = session()->get('user_role');

        if ($role === 'recruiter') {
            return $this->recruiterDashboard($userId);
        }

        return $this->seekerDashboard($userId);
    }

    private function seekerDashboard(int $userId): string
    {
        $profileModel     = model(ProfileModel::class);
        $applicationModel = model(ApplicationModel::class);
        $jobModel         = model(JobModel::class);

        $profile      = $profileModel->getByUserId($userId);
        $applications = $applicationModel->getApplicationsForUser($userId);
        $recommended  = $jobModel->getRecommended($userId, 6);

        return view('dashboard/seeker', [
            'title'        => 'My Dashboard',
            'profile'      => $profile,
            'applications' => $applications,
            'recommended'  => $recommended,
        ]);
    }

    private function recruiterDashboard(int $userId): string
    {
        $companyModel     = model(CompanyModel::class);
        $jobModel         = model(JobModel::class);
        $applicationModel = model(ApplicationModel::class);

        $company      = $companyModel->getByUserId($userId);
        $jobs         = $company
                            ? model(JobModel::class)->where('company_id', $company->id)->orderBy('created_at', 'DESC')->findAll()
                            : [];
        $applications = $applicationModel->getApplicationsForRecruiter($userId);

        return view('dashboard/recruiter', [
            'title'        => 'Recruiter Dashboard',
            'company'      => $company,
            'jobs'         => $jobs,
            'applications' => $applications,
        ]);
    }
}
