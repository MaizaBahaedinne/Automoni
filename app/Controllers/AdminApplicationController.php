<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Admin: global view of all job applications.
 * Routes: GET  /admin/applications
 *         POST /admin/applications/(:num)/status
 */
class AdminApplicationController extends BaseController
{
    private const PER_PAGE = 40;

    public function index(): string
    {
        $model  = new ApplicationModel();

        $status = $this->request->getGet('status') ?? 'all';
        $search = trim($this->request->getGet('search') ?? '');
        $from   = $this->request->getGet('from')   ?? '';
        $to     = $this->request->getGet('to')     ?? '';

        // Sanitise dates
        $from = preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) ? $from : null;
        $to   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)   ? $to   : null;

        $apps   = $model->getAllForAdmin(
            $status !== 'all' ? $status : null,
            $search !== '' ? $search : null,
            $from,
            $to,
            self::PER_PAGE
        );

        return view('admin/applications', [
            'title'   => 'Gestion des candidatures',
            'apps'    => $apps,
            'pager'   => $model->pager,
            'counts'  => $model->statusCounts(),
            'status'  => $status,
            'search'  => $search,
            'from'    => $from ?? '',
            'to'      => $to   ?? '',
        ]);
    }

    /**
     * POST /admin/applications/purge
     * Deletes all applications from the database (test/dev use only).
     * Requires an extra confirmation token to prevent accidental clicks.
     */
    public function purge(): RedirectResponse
    {
        $token = $this->request->getPost('confirm_token');
        if ($token !== 'PURGE_CONFIRMED') {
            return redirect()->back()->with('error', 'Token de confirmation invalide.');
        }

        $db = \Config\Database::connect();
        $db->table('applications')->truncate();

        log_message('warning', '[AdminApplicationController::purge] Toutes les candidatures ont été supprimées par user_id=' . session()->get('user_id'));

        return redirect()->to(base_url('admin/applications'))->with('success', 'Toutes les candidatures ont été purgées.');
    }

    /**
     * POST /admin/applications/(:num)/status
     * Updates the status of a single application (AJAX or form).
     */
    public function updateStatus(int $id): RedirectResponse
    {
        $allowed = ['pending', 'reviewing', 'accepted', 'rejected'];
        $new     = $this->request->getPost('status');

        if (!in_array($new, $allowed, true)) {
            return redirect()->back()->with('error', 'Statut invalide.');
        }

        $model = new ApplicationModel();
        $app   = $model->find($id);

        if (!$app) {
            return redirect()->to(base_url('admin/applications'))->with('error', 'Candidature introuvable.');
        }

        $model->update($id, [
            'status'         => $new,
            'recruiter_note' => $this->request->getPost('recruiter_note') ?? $app->recruiter_note,
        ]);

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }
}
