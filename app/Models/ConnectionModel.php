<?php

namespace App\Models;

use CodeIgniter\Model;

class ConnectionModel extends Model
{
    protected $table         = 'user_connections';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = ['requester_id', 'receiver_id', 'status'];

    // ─── Status ───────────────────────────────────────────────────────────────

    /**
     * Returns 'self' | 'none' | 'pending_sent' | 'pending_received' | 'accepted'
     */
    public function getStatus(int $myId, int $otherId): string
    {
        if ($myId === $otherId) {
            return 'self';
        }

        $row = $this->db->query(
            "SELECT requester_id, status FROM user_connections
             WHERE (requester_id = ? AND receiver_id = ?)
                OR (requester_id = ? AND receiver_id = ?)
             LIMIT 1",
            [$myId, $otherId, $otherId, $myId]
        )->getRowObject();

        if (!$row) {
            return 'none';
        }

        if ($row->status === 'accepted') {
            return 'accepted';
        }

        if ($row->status === 'pending') {
            return (int) $row->requester_id === $myId ? 'pending_sent' : 'pending_received';
        }

        // rejected → allow re-send
        return 'none';
    }

    // ─── Mutations ────────────────────────────────────────────────────────────

    public function sendRequest(int $requesterId, int $receiverId): bool
    {
        $row = $this->db->query(
            "SELECT id, status FROM user_connections
             WHERE (requester_id = ? AND receiver_id = ?)
                OR (requester_id = ? AND receiver_id = ?)
             LIMIT 1",
            [$requesterId, $receiverId, $receiverId, $requesterId]
        )->getRowObject();

        if ($row) {
            if ($row->status === 'rejected') {
                $this->db->table($this->table)->where('id', $row->id)->delete();
            } else {
                return false; // already pending or accepted
            }
        }

        return $this->insert([
            'requester_id' => $requesterId,
            'receiver_id'  => $receiverId,
            'status'       => 'pending',
        ]) !== false;
    }

    public function accept(int $requesterId, int $myId): bool
    {
        $this->db->table($this->table)
            ->where('requester_id', $requesterId)
            ->where('receiver_id', $myId)
            ->where('status', 'pending')
            ->update(['status' => 'accepted', 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->db->affectedRows() > 0;
    }

    public function reject(int $requesterId, int $myId): bool
    {
        $this->db->table($this->table)
            ->where('requester_id', $requesterId)
            ->where('receiver_id', $myId)
            ->where('status', 'pending')
            ->delete();

        return $this->db->affectedRows() > 0;
    }

    public function remove(int $userId, int $otherId): bool
    {
        $this->db->query(
            "DELETE FROM user_connections
             WHERE (requester_id = ? AND receiver_id = ?)
                OR (requester_id = ? AND receiver_id = ?)",
            [$userId, $otherId, $otherId, $userId]
        );

        return $this->db->affectedRows() > 0;
    }

    public function withdraw(int $myId, int $otherId): bool
    {
        $this->db->table($this->table)
            ->where('requester_id', $myId)
            ->where('receiver_id', $otherId)
            ->where('status', 'pending')
            ->delete();

        return $this->db->affectedRows() > 0;
    }

    // ─── Queries ──────────────────────────────────────────────────────────────

    public function getConnections(int $userId, string $keyword = ''): array
    {
        $params = [$userId, $userId, $userId, $userId];

        $like = '';
        $likeClause = '';
        if ($keyword !== '') {
            $like       = '%' . $keyword . '%';
            $likeClause = "AND (u.first_name LIKE ? OR u.last_name LIKE ? OR p.headline LIKE ?)";
            $params     = array_merge($params, [$like, $like, $like]);
        }

        return $this->db->query("
            SELECT u.id, u.first_name, u.last_name, u.avatar,
                   p.headline, p.city, p.country
            FROM user_connections c
            JOIN users u ON (
                (c.requester_id = ? AND u.id = c.receiver_id)
                OR (c.receiver_id = ? AND u.id = c.requester_id)
            )
            LEFT JOIN profiles p ON p.user_id = u.id
            WHERE (c.requester_id = ? OR c.receiver_id = ?)
              AND c.status = 'accepted'
              AND u.deleted_at IS NULL
              $likeClause
            ORDER BY u.first_name, u.last_name
        ", $params)->getResultObject();
    }

    public function getPendingReceived(int $userId): array
    {
        return $this->db->query("
            SELECT u.id, u.first_name, u.last_name, u.avatar,
                   p.headline, p.city, p.country, c.created_at
            FROM user_connections c
            JOIN users u ON u.id = c.requester_id
            LEFT JOIN profiles p ON p.user_id = u.id
            WHERE c.receiver_id = ?
              AND c.status = 'pending'
              AND u.deleted_at IS NULL
            ORDER BY c.created_at DESC
        ", [$userId])->getResultObject();
    }

    public function getSentPending(int $userId): array
    {
        return $this->db->query("
            SELECT u.id, u.first_name, u.last_name, u.avatar,
                   p.headline, p.city, p.country, c.created_at
            FROM user_connections c
            JOIN users u ON u.id = c.receiver_id
            LEFT JOIN profiles p ON p.user_id = u.id
            WHERE c.requester_id = ?
              AND c.status = 'pending'
              AND u.deleted_at IS NULL
            ORDER BY c.created_at DESC
        ", [$userId])->getResultObject();
    }

    public function countAccepted(int $userId): int
    {
        $row = $this->db->query("
            SELECT COUNT(*) AS cnt FROM user_connections
            WHERE (requester_id = ? OR receiver_id = ?) AND status = 'accepted'
        ", [$userId, $userId])->getRowObject();

        return (int) ($row->cnt ?? 0);
    }

    public function getMutualCount(int $userId, int $otherId): int
    {
        $row = $this->db->query("
            SELECT COUNT(*) AS cnt
            FROM (
                SELECT CASE WHEN requester_id = ? THEN receiver_id ELSE requester_id END AS cid
                FROM user_connections
                WHERE (requester_id = ? OR receiver_id = ?) AND status = 'accepted'
            ) AS mine
            WHERE mine.cid IN (
                SELECT CASE WHEN requester_id = ? THEN receiver_id ELSE requester_id END
                FROM user_connections
                WHERE (requester_id = ? OR receiver_id = ?) AND status = 'accepted'
            )
        ", [$userId, $userId, $userId, $otherId, $otherId, $otherId])->getRowObject();

        return (int) ($row->cnt ?? 0);
    }

    /**
     * Search all users (network-wide) with their connection status toward $myId.
     */
    public function searchPeople(int $myId, string $keyword): array
    {
        $like = '%' . $keyword . '%';

        return $this->db->query("
            SELECT u.id, u.first_name, u.last_name, u.avatar,
                   p.headline, p.city, p.country,
                   c.status AS conn_status,
                   c.requester_id AS conn_requester
            FROM users u
            LEFT JOIN profiles p ON p.user_id = u.id
            LEFT JOIN user_connections c ON (
                (c.requester_id = ? AND c.receiver_id = u.id)
                OR (c.requester_id = u.id AND c.receiver_id = ?)
            )
            WHERE u.id != ?
              AND u.deleted_at IS NULL
              AND (u.first_name LIKE ? OR u.last_name LIKE ? OR p.headline LIKE ?)
            ORDER BY u.first_name, u.last_name
            LIMIT 50
        ", [$myId, $myId, $myId, $like, $like, $like])->getResultObject();
    }
}
