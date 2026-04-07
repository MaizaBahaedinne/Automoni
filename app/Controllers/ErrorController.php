<?php

namespace App\Controllers;

use App\Models\Error404Model;

/**
 * Handles all 404 Not Found responses.
 *
 * Registered via $routes->set404Override() so it runs through the
 * normal CI4 controller pipeline (sessions, services, views all available).
 */
class ErrorController extends BaseController
{
    public function notFound(): string
    {
        $request = $this->request;

        // ── Log the hit ───────────────────────────────────────────────────────
        $userId  = session()->get('logged_in') ? (int) session()->get('user_id') : null;
        $url     = (string) $request->getUri();
        $method  = $request->getMethod();
        $ip      = $request->getIPAddress();
        $ua      = (string) ($request->getUserAgent() ?? '');
        $referer = (string) ($request->getHeaderLine('Referer') ?? '');

        (new Error404Model())->logHit($url, strtoupper($method), $userId, $ip, $ua, $referer);

        // ── Return 404 status + branded view ─────────────────────────────────
        $this->response->setStatusCode(404);

        return view('errors/error_404', [
            'title'       => 'Page introuvable — 404',
            'requestedUrl' => esc($url),
        ]);
    }
}
