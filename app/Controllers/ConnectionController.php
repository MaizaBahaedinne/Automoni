<?php

namespace App\Controllers;

use App\Models\ConnectionModel;
use CodeIgniter\HTTP\ResponseInterface;

class ConnectionController extends BaseController
{
    private int $userId;
    private ConnectionModel $connectionModel;

    public function __construct()
    {
        $this->userId          = (int) session()->get('user_id');
        $this->connectionModel = model(ConnectionModel::class);
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    /** GET /connections */
    public function index(): string
    {
        $connections      = $this->connectionModel->getConnections($this->userId);
        $pendingReceived  = $this->connectionModel->getPendingReceived($this->userId);
        $sentPending      = $this->connectionModel->getSentPending($this->userId);
        $connectionsCount = count($connections);

        return view('connections/index', compact(
            'connections', 'pendingReceived', 'sentPending', 'connectionsCount'
        ));
    }

    /** GET /connections/search?q= */
    public function search(): string
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $results = $keyword !== '' ? $this->connectionModel->searchPeople($this->userId, $keyword) : [];

        return view('connections/search', compact('results', 'keyword'));
    }

    // ─── AJAX actions (return JSON) ───────────────────────────────────────────

    /** POST /connections/send/{id} */
    public function send(int $toId): ResponseInterface
    {
        if ($toId === $this->userId) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid']);
        }

        $ok = $this->connectionModel->sendRequest($this->userId, $toId);

        return $this->response->setJSON([
            'success'    => $ok,
            'message'    => $ok ? 'Invitation envoyée' : 'Déjà envoyé',
            'new_status' => $ok ? 'pending_sent' : null,
        ]);
    }

    /** POST /connections/accept/{id} */
    public function accept(int $fromId): ResponseInterface
    {
        $ok = $this->connectionModel->accept($fromId, $this->userId);

        return $this->response->setJSON([
            'success'    => $ok,
            'message'    => $ok ? 'Connexion acceptée' : 'Erreur',
            'new_status' => $ok ? 'accepted' : null,
        ]);
    }

    /** POST /connections/reject/{id} */
    public function reject(int $fromId): ResponseInterface
    {
        $ok = $this->connectionModel->reject($fromId, $this->userId);

        return $this->response->setJSON([
            'success'    => $ok,
            'message'    => $ok ? 'Invitation refusée' : 'Erreur',
        ]);
    }

    /** POST /connections/remove/{id} */
    public function remove(int $userId): ResponseInterface
    {
        $ok = $this->connectionModel->remove($this->userId, $userId);

        return $this->response->setJSON([
            'success'    => $ok,
            'message'    => $ok ? 'Connexion supprimée' : 'Erreur',
            'new_status' => $ok ? 'none' : null,
        ]);
    }

    /** POST /connections/withdraw/{id} */
    public function withdraw(int $toId): ResponseInterface
    {
        $ok = $this->connectionModel->withdraw($this->userId, $toId);

        return $this->response->setJSON([
            'success'    => $ok,
            'message'    => $ok ? 'Invitation annulée' : 'Erreur',
            'new_status' => $ok ? 'none' : null,
        ]);
    }
}
