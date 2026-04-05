<?php

namespace App\Controllers;

use App\Models\JobAlertModel;
use CodeIgniter\HTTP\RedirectResponse;

class AlertController extends BaseController
{
    private JobAlertModel $alertModel;
    private int $userId;

    public function __construct()
    {
        $this->alertModel = model(JobAlertModel::class);
        $this->userId     = (int) session()->get('user_id');
    }

    public function index(): string
    {
        $alerts = $this->alertModel->where('user_id', $this->userId)->findAll();
        return view('alerts/index', ['title' => 'Job Alerts', 'alerts' => $alerts]);
    }

    public function store(): RedirectResponse
    {
        $rules = [
            'keywords'      => 'permit_empty|max_length[255]',
            'location'      => 'permit_empty|max_length[150]',
            'contract_type' => 'permit_empty|in_list[CDI,CDD,Freelance,Internship,PartTime]',
            'frequency'     => 'required|in_list[instant,daily,weekly]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->alertModel->insert([
            'user_id'       => $this->userId,
            'keywords'      => strip_tags($this->request->getPost('keywords')),
            'location'      => strip_tags($this->request->getPost('location')),
            'contract_type' => $this->request->getPost('contract_type'),
            'frequency'     => $this->request->getPost('frequency'),
        ]);

        return redirect()->to('/alerts')->with('success', 'Alert created successfully.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $alert = $this->ownerAlert($id);
        $this->alertModel->update($id, ['is_active' => (int) !$alert->is_active]);
        return redirect()->to('/alerts')->with('success', 'Alert ' . ($alert->is_active ? 'paused' : 'resumed') . '.');
    }

    public function delete(int $id): RedirectResponse
    {
        $this->ownerAlert($id);
        $this->alertModel->delete($id);
        return redirect()->to('/alerts')->with('success', 'Alert deleted.');
    }

    private function ownerAlert(int $id): object
    {
        $alert = $this->alertModel->find($id);
        if (!$alert || (int) $alert->user_id !== $this->userId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $alert;
    }
}
