<?php

namespace App\Controllers;

use App\Models\JobModel;
use App\Models\ProfileModel;
use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Models\PostModel;
use App\Models\PostReactionModel;
use CodeIgniter\Controller;

class HomeController extends BaseController
{
    public function index(): string
    {
        $userId = (int) (session()->get('user_id') ?? 0);

        $latestJobs = model(JobModel::class)
            ->select('jobs.*, companies.name as company_name, companies.logo as company_logo, companies.slug as company_slug')
            ->join('companies', 'companies.id = jobs.company_id')
            ->where('jobs.status', 'active')
            ->orderBy('jobs.created_at', 'DESC')
            ->limit($userId ? 9 : 6)
            ->findAll();

        $myProfile    = null;
        $myUser       = null;
        $topCompanies = [];
        $posts        = [];
        $myReactions  = [];

        if ($userId) {
            $myProfile = model(ProfileModel::class)->getByUserId($userId);
            $myUser    = model(UserModel::class)->find($userId);
            $topCompanies = model(CompanyModel::class)
                ->select('companies.*, COUNT(jobs.id) as job_count')
                ->join('jobs', 'jobs.company_id = companies.id AND jobs.status = "active"', 'left')
                ->groupBy('companies.id')
                ->orderBy('job_count', 'DESC')
                ->limit(5)
                ->findAll();

            $posts = model(PostModel::class)->getFeed(20);

            if (!empty($posts)) {
                $postIds = array_map(fn($p) => $p->id, $posts);
                $rawReactions = model(PostReactionModel::class)
                    ->where('user_id', $userId)
                    ->whereIn('post_id', $postIds)
                    ->findAll();
                foreach ($rawReactions as $r) {
                    $myReactions[$r->post_id] = $r->reaction_type;
                }
            }
        }

        return view('home/index', [
            'title'        => 'Persomy – Your Professional Network',
            'latestJobs'   => $latestJobs,
            'myProfile'    => $myProfile,
            'myUser'       => $myUser,
            'topCompanies' => $topCompanies,
            'posts'        => $posts,
            'myReactions'  => $myReactions,
        ]);
    }

    public function coaching(): string
    {
        return view('home/coaching', ['title' => 'Career Coaching Tips']);
    }
}
