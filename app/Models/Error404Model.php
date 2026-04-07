<?php

namespace App\Models;

use CodeIgniter\Model;

class Error404Model extends Model
{
    protected $table      = 'error_404_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $allowedFields = [
        'url', 'method', 'user_id', 'ip', 'user_agent', 'referer',
    ];

    protected $useTimestamps      = false; // uses plain created_at DEFAULT CURRENT_TIMESTAMP
    protected $useSoftDeletes     = false;
    protected $createdField       = 'created_at';

    /**
     * Log a 404 hit. Never throws — silently fails so it never
     * breaks the page rendering.
     */
    public function logHit(string $url, string $method, ?int $userId, string $ip, string $ua, string $referer): void
    {
        try {
            $this->db->query(
                'INSERT INTO error_404_logs (url, method, user_id, ip, user_agent, referer)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [
                    mb_substr($url,     0, 2048),
                    mb_substr($method,  0, 10),
                    $userId,
                    mb_substr($ip,      0, 45),
                    mb_substr($ua,      0, 512),
                    mb_substr($referer, 0, 2048),
                ]
            );
        } catch (\Throwable $e) {
            log_message('error', '404Logger: ' . $e->getMessage());
        }
    }

    /**
     * Count rows — optionally filtered by date range and URL fragment.
     */
    public function countFiltered(?string $from, ?string $to, ?string $search): int
    {
        return (int) $this->buildQuery($from, $to, $search)->countAllResults(false);
    }

    /**
     * Paginated results for the admin table.
     *
     * @return list<object>
     */
    public function getFiltered(?string $from, ?string $to, ?string $search, int $limit, int $offset): array
    {
        return $this->buildQuery($from, $to, $search)
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultObject();
    }

    /**
     * Top N most-hit 404 URLs (all time or filtered).
     *
     * @return list<object{url:string, hits:int}>
     */
    public function topUrls(int $n = 20): array
    {
        return $this->db->query(
            'SELECT url, COUNT(*) AS hits
               FROM error_404_logs
           GROUP BY url
           ORDER BY hits DESC
              LIMIT ?',
            [$n]
        )->getResultObject();
    }

    /**
     * Daily counts for the last N days (for a sparkline / chart).
     *
     * @return list<object{day:string, hits:int}>
     */
    public function dailyCounts(int $days = 30): array
    {
        return $this->db->query(
            'SELECT DATE(created_at) AS day, COUNT(*) AS hits
               FROM error_404_logs
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
           GROUP BY day
           ORDER BY day ASC',
            [$days]
        )->getResultObject();
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function buildQuery(?string $from, ?string $to, ?string $search): \CodeIgniter\Database\BaseBuilder
    {
        $builder = $this->db->table('error_404_logs');

        if ($from) {
            $builder->where('created_at >=', $from . ' 00:00:00');
        }
        if ($to) {
            $builder->where('created_at <=', $to . ' 23:59:59');
        }
        if ($search) {
            $builder->like('url', $search);
        }

        return $builder;
    }
}
