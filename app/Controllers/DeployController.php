<?php

namespace App\Controllers;

class DeployController extends BaseController
{
    /**
     * GET /admin/deploy
     */
    public function index(): string
    {
        return view('admin/deploy', ['title' => 'Déploiement']);
    }

    /**
     * POST /admin/deploy/pull
     */
    public function pull(): string
    {
        $projectRoot = realpath(ROOTPATH);

        // Validate that git binary is accessible
        $gitBin = trim((string) shell_exec('which git 2>/dev/null || where git 2>NUL'));
        if (empty($gitBin)) {
            $gitBin = 'git';
        }

        $command = sprintf(
            'cd %s && %s pull 2>&1',
            escapeshellarg($projectRoot),
            escapeshellarg($gitBin)
        );

        $output   = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);

        $outputText = implode("\n", $output);

        log_message('info', '[Deploy] git pull by user ' . session()->get('user_id') . ' — exit ' . $exitCode . "\n" . $outputText);

        return view('admin/deploy', [
            'title'    => 'Déploiement',
            'output'   => $outputText,
            'exitCode' => $exitCode,
            'ran'      => true,
        ]);
    }
}
