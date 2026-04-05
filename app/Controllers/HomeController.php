<?php

namespace App\Controllers;

use App\Models\JobModel;
use CodeIgniter\Controller;

class HomeController extends BaseController
{
    public function index(): string
    {
        $latestJobs = model(JobModel::class)
            ->select('jobs.*, companies.name as company_name, companies.logo as company_logo')
            ->join('companies', 'companies.id = jobs.company_id')
            ->where('jobs.status', 'active')
            ->orderBy('jobs.created_at', 'DESC')
            ->limit(6)
            ->findAll();

        return view('home/index', [
            'title'      => 'Persomy – Find Your Next Career',
            'latestJobs' => $latestJobs,
        ]);
    }

    public function coaching(): string
    {
        return view('home/coaching', ['title' => 'Career Coaching Tips']);
    }
}
