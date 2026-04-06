<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ─── Language Switch ────────────────────────────────────────────────────────
$routes->get('lang/(:segment)', 'LangController::switch/$1');

// ─── LinkedIn OAuth (callback must be public — no session yet when LinkedIn redirects) ──
$routes->get('linkedin/login',    'LinkedInController::loginConnect');
$routes->get('linkedin/callback', 'LinkedInController::callback');

// ─── Public ──────────────────────────────────────────────────────────────────
$routes->get('/',          'HomeController::index');
$routes->get('coaching',   'HomeController::coaching');

// ─── Auth ─────────────────────────────────────────────────────────────────────
$routes->get ('login',    'AuthController::loginForm');
$routes->post('login',    'AuthController::login');
$routes->get ('register', 'AuthController::registerForm');
$routes->post('register', 'AuthController::register');
$routes->get ('logout',   'AuthController::logout');

// ─── Jobs (public read) ───────────────────────────────────────────────────────
$routes->get('jobs',           'JobController::index');
$routes->get('jobs/(:segment)', 'JobController::show/$1');

// ─── Company (public) ─────────────────────────────────────────────────────────
$routes->get('companies/(:segment)', 'CompanyController::show/$1');

// ─── Organizations (public read) ──────────────────────────────────────────────
$routes->get('organizations',               'OrganizationController::index');
$routes->get('organizations/(:segment)',    'OrganizationController::show/$1');
$routes->get('organizations/(:segment)/hierarchy', 'OrganizationController::hierarchy/$1');

// ─── Protected — any role ─────────────────────────────────────────────────────
$routes->group('', ['filter' => 'auth'], static function ($routes) {

    $routes->get ('dashboard', 'DashboardController::index');

    // LinkedIn connect (authenticated — starts the OAuth flow)
    $routes->get ('linkedin/connect',         'LinkedInController::connect');
    $routes->post('linkedin/import/confirm',  'LinkedInController::confirmImport');
    $routes->get ('linkedin/import/cancel',   'LinkedInController::cancelImport');

    // Profile
    $routes->get ('profile',                    'ProfileController::show');
    $routes->get ('profile/view/(:num)',         'ProfileController::show/$1');
    $routes->get ('profile/edit',               'ProfileController::edit');
    $routes->post('profile/update',             'ProfileController::update');
    $routes->post('profile/cv/upload',          'ProfileController::uploadCv');
    $routes->get ('profile/cv/download/(:num)', 'ProfileController::downloadCv/$1');
    $routes->get ('profile/cv/download',        'ProfileController::downloadCv');

    // Experiences & Education (via AJAX/form posts)
    $routes->post('profile/experience/add',           'ProfileController::addExperience');
    $routes->post('profile/experience/update/(:num)', 'ProfileController::updateExperience/$1');
    $routes->post('profile/experience/delete/(:num)', 'ProfileController::deleteExperience/$1');
    $routes->get ('profile/users/search',             'ProfileController::searchUsers');
    $routes->post('profile/education/add',            'ProfileController::addEducation');
    $routes->post('profile/education/delete/(:num)',  'ProfileController::deleteEducation/$1');

    // Certifications
    $routes->post('profile/certification/add',           'ProfileController::addCertification');
    $routes->post('profile/certification/delete/(:num)', 'ProfileController::deleteCertification/$1');

    // Languages
    $routes->post('profile/language/add',           'ProfileController::addLanguage');
    $routes->post('profile/language/delete/(:num)', 'ProfileController::deleteLanguage/$1');

    // Projects
    $routes->post('profile/project/add',           'ProfileController::addProject');
    $routes->post('profile/project/delete/(:num)', 'ProfileController::deleteProject/$1');

    // Volunteering
    $routes->post('profile/volunteering/add',           'ProfileController::addVolunteering');
    $routes->post('profile/volunteering/delete/(:num)', 'ProfileController::deleteVolunteering/$1');

    // Job alerts (job seekers only)
    $routes->get ('alerts',               'AlertController::index',  ['filter' => 'role:job_seeker']);
    $routes->post('alerts/store',         'AlertController::store',  ['filter' => 'role:job_seeker']);
    $routes->post('alerts/toggle/(:num)', 'AlertController::toggle', ['filter' => 'role:job_seeker']);
    $routes->post('alerts/delete/(:num)', 'AlertController::delete', ['filter' => 'role:job_seeker']);

    // Apply to job (job seekers only)
    $routes->post('jobs/(:num)/apply', 'JobController::apply/$1', ['filter' => 'role:job_seeker']);

    // Posts (social feed)
    $routes->post('posts/store',                 'PostController::store');
    $routes->post('posts/(:num)/delete',         'PostController::destroy/$1');
    $routes->post('posts/(:num)/react',          'PostController::react/$1');
    $routes->post('posts/(:num)/comment',        'PostController::addComment/$1');
    $routes->get ('posts/(:num)/comments',       'PostController::comments/$1');

    // ─── Organizations (authenticated users) ──────────────────────────────────
    $routes->get ('organizations/create',           'OrganizationController::create');
    $routes->post('organizations',                  'OrganizationController::store');
    $routes->get ('organizations/(:num)/edit',      'OrganizationController::edit/$1');
    $routes->post('organizations/(:num)',           'OrganizationController::update/$1');
    $routes->post('organizations/(:num)/update',    'OrganizationController::update/$1');
    $routes->delete('organizations/(:num)',         'OrganizationController::delete/$1');

    // ─── Organization Members (owner/manager only, handled in controller)  ────
    $routes->get ('organizations/(:num)/members',              'OrganizationMemberController::index/$1');
    $routes->post('organizations/(:num)/members',              'OrganizationMemberController::add/$1');
    $routes->post('organizations/(:num)/members/(:num)/role',  'OrganizationMemberController::updateRole/$1/$2');
    $routes->delete('organizations/(:num)/members/(:num)',     'OrganizationMemberController::remove/$1/$2');
});

// ─── Protected — recruiters only ──────────────────────────────────────────────
$routes->group('', ['filter' => 'role:recruiter,admin'], static function ($routes) {

    // Company
    $routes->get ('company/create', 'CompanyController::create');
    $routes->post('company/store',  'CompanyController::store');
    $routes->get ('company/edit',   'CompanyController::edit');
    $routes->post('company/update', 'CompanyController::update');

    // Jobs CRUD
    $routes->get ('jobs/create',         'JobController::create');
    $routes->post('jobs/store',          'JobController::store');
    $routes->get ('jobs/edit/(:num)',     'JobController::edit/$1');
    $routes->post('jobs/update/(:num)',   'JobController::update/$1');
    $routes->post('jobs/delete/(:num)',   'JobController::delete/$1');

    // Application management
    $routes->post('applications/(:num)/status', 'JobController::updateApplicationStatus/$1');
});
