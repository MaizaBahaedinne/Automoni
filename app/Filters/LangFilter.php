<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Reads the locale from the session and sets it on every request.
 * Supports: en | fr | ar
 */
class LangFilter implements FilterInterface
{
    private const SUPPORTED = ['en', 'fr', 'ar'];

    public function before(RequestInterface $request, $arguments = null)
    {
        $locale = session()->get('locale') ?? 'en';

        if (!in_array($locale, self::SUPPORTED, true)) {
            $locale = 'en';
        }

        service('language')->setLocale($locale);
        // Make it accessible inside views without extra calls
        app_timezone(); // touches the service, keeps CI happy
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
