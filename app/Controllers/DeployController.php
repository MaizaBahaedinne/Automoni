<?php

namespace App\Controllers;

class DeployController extends BaseController
{
    /**
     * GET /admin/deploy
     */
    public function index(): string
    {
        return view('admin/deploy', [
            'title'   => 'Déploiement',
            'logTail' => $this->readLogTail(),
        ]);
    }

    /**
     * POST /admin/deploy/pull
     */
    public function pull(): string
    {
        $projectRoot = realpath(ROOTPATH);

        $gitBin = trim((string) shell_exec('which git 2>/dev/null'));
        if (empty($gitBin)) {
            $gitBin = 'git';
        }
        $phpBin = trim((string) shell_exec('which php 2>/dev/null'));
        if (empty($phpBin)) {
            $phpBin = 'php';
        }

        // Step 1: git pull
        $pullCmd = sprintf(
            'cd %s && %s -c safe.directory=%s pull 2>&1',
            escapeshellarg($projectRoot),
            escapeshellarg($gitBin),
            escapeshellarg($projectRoot)
        );
        $pullLines = [];
        $pullExit  = 0;
        exec($pullCmd, $pullLines, $pullExit);
        $pullOutput = implode("\n", $pullLines);

        // Step 2: php spark migrate (only when pull succeeded)
        $migrateOutput = '';
        $migrateExit   = null;
        if ($pullExit === 0) {
            $migrateCmd = sprintf(
                'cd %s && %s spark migrate --no-interaction 2>&1',
                escapeshellarg($projectRoot),
                escapeshellarg($phpBin)
            );
            $migrateLines = [];
            exec($migrateCmd, $migrateLines, $migrateExit);
            $migrateOutput = implode("\n", $migrateLines);
        }

        $permissionError = str_contains($pullOutput, 'Permission denied');

        log_message('info', sprintf(
            '[Deploy] git pull by user %s — exit %d%s',
            session()->get('user_id'),
            $pullExit,
            "\n" . $pullOutput . ($migrateOutput ? "\n[migrate]\n" . $migrateOutput : '')
        ));

        return view('admin/deploy', [
            'title'           => 'Déploiement',
            'pullOutput'      => $pullOutput,
            'pullExit'        => $pullExit,
            'migrateOutput'   => $migrateOutput,
            'migrateExit'     => $migrateExit,
            'ran'             => true,
            'permissionError' => $permissionError,
            'projectRoot'     => $projectRoot,
            'logTail'         => $this->readLogTail(),
        ]);
    }

    /**
     * Read the last 60 lines of today's CI4 log file.
     */
    private function readLogTail(): string
    {
        $logFile = WRITEPATH . 'logs/log-' . date('Y-m-d') . '.log';
        if (!is_file($logFile)) {
            return '';
        }
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return '';
        }
        return implode("\n", array_slice($lines, -60));
    }
}
