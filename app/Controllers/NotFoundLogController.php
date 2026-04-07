<?php

namespace App\Controllers;

use App\Models\Error404Model;

class NotFoundLogController extends BaseController
{
    private const PER_PAGE = 50;

    public function index(): string
    {
        $model = new Error404Model();

        $from   = $this->request->getGet('from')   ?? '';
        $to     = $this->request->getGet('to')     ?? '';
        $search = $this->request->getGet('search') ?? '';
        $page   = max(1, (int) ($this->request->getGet('page') ?? 1));

        // Sanitise date inputs
        $from   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)   ? $from   : null;
        $to     = preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)     ? $to     : null;
        $search = $search !== '' ? trim($search) : null;

        $total  = $model->countFiltered($from, $to, $search);
        $offset = ($page - 1) * self::PER_PAGE;
        $rows   = $model->getFiltered($from, $to, $search, self::PER_PAGE, $offset);
        $pages  = (int) ceil($total / self::PER_PAGE);

        $topUrls     = $model->topUrls(15);
        $dailyCounts = $model->dailyCounts(30);

        return view('admin/not_found_logs', [
            'title'       => 'Rapport 404',
            'rows'        => $rows,
            'total'       => $total,
            'page'        => $page,
            'pages'       => $pages,
            'perPage'     => self::PER_PAGE,
            'from'        => $from ?? '',
            'to'          => $to   ?? '',
            'search'      => $search ?? '',
            'topUrls'     => $topUrls,
            'dailyCounts' => $dailyCounts,
        ]);
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        (new Error404Model())->where('id', $id)->delete();
        return redirect()->to(base_url('admin/404-logs'))->with('success', 'Entrée supprimée.');
    }

    public function clear(): \CodeIgniter\HTTP\RedirectResponse
    {
        $this->db->query('TRUNCATE TABLE error_404_logs');
        return redirect()->to(base_url('admin/404-logs'))->with('success', 'Tous les logs 404 ont été effacés.');
    }
}
