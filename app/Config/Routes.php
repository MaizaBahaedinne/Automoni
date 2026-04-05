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
    $routes->post('profile/experience/delete/(:num)', 'ProfileController::deleteExperience/$1');
    $routes->post('profile/education/add',            'ProfileController::addEducation');
    $routes->post('profile/education/delete/(:num)',  'ProfileController::deleteEducation/$1');

    // Job alerts (job seekers only)
    $routes->get ('alerts',               'AlertController::index',  ['filter' => 'role:job_seeker']);
    $routes->post('alerts/store',         'AlertController::store',  ['filter' => 'role:job_seeker']);
    $routes->post('alerts/toggle/(:num)', 'AlertController::toggle', ['filter' => 'role:job_seeker']);
    $routes->post('alerts/delete/(:num)', 'AlertController::delete', ['filter' => 'role:job_seeker']);

    // Apply to job (job seekers only)
    $routes->post('jobs/(:num)/apply', 'JobController::apply/$1', ['filter' => 'role:job_seeker']);
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
