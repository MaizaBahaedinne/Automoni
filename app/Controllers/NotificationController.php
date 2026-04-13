<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\HTTP\RedirectResponse;

class NotificationController extends BaseController
{
    /**
     * GET notifications
     * Full list for the logged-in user; marks all as read on visit.
     */
    public function index(): string
    {
        $userId = (int) session()->get('user_id');
        $model  = model(NotificationModel::class);

        $model->markAllRead($userId);
        $notifications = $model->getRecent($userId, 50);

        return view('notifications/index', [
            'title'         => 'Notifications',
            'notifications' => $notifications,
        ]);
    }

    /**
     * POST notifications/(:num)/read
     * Mark a single notification as read (AJAX or form).
     */
    public function markRead(int $id): RedirectResponse
    {
        $userId = (int) session()->get('user_id');
        $notif  = model(NotificationModel::class);
        $n      = $notif->find($id);

        if ($n && (int) $n->user_id === $userId) {
            $notif->markRead($id, $userId);
            if (!empty($n->link)) {
                return redirect()->to($n->link);
            }
        }

        return redirect()->to(base_url('notifications'));
    }
}
