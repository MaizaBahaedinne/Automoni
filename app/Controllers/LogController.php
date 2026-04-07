<?php

namespace App\Controllers;

class LogController extends BaseController
{
    private const LEVELS = ['CRITICAL', 'ERROR', 'WARNING', 'INFO', 'DEBUG'];

    /**
     * GET /admin/logs
     * GET /admin/logs?date=2026-04-07&level=ERROR
     */
    public function index(): string
    {
        $requestedDate  = $this->request->getVar('date')  ?? '';
        $requestedLevel = strtoupper((string)($this->request->getVar('level') ?? ''));

        // Validate date — must match YYYY-MM-DD or empty
        if ($requestedDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $requestedDate)) {
            $requestedDate = '';
        }

        $date = $requestedDate !== '' ? $requestedDate : date('Y-m-d');

        // List all available log files (last 30 days)
        $availableDates = $this->getAvailableDates();

        // Read and parse entries
        $entries   = $this->parseLogFile($date);
        $filtered  = $this->filterEntries($entries, $requestedLevel);

        // Count per level
        $counts = array_fill_keys(self::LEVELS, 0);
        foreach ($entries as $entry) {
            if (isset($counts[$entry['level']])) {
                $counts[$entry['level']]++;
            }
        }

        return view('admin/logs', [
            'title'          => 'Journal d\'erreurs',
            'date'           => $date,
            'availableDates' => $availableDates,
            'entries'        => $filtered,
            'totalEntries'   => count($entries),
            'counts'         => $counts,
            'activeLevel'    => $requestedLevel,
            'levels'         => self::LEVELS,
        ]);
    }

    /**
     * Return sorted list of dates for which a log file exists (newest first, max 30).
     */
    private function getAvailableDates(): array
    {
        $logDir = WRITEPATH . 'logs/';
        $dates  = [];

        if (!is_dir($logDir)) {
            return $dates;
        }

        foreach (glob($logDir . 'log-????-??-??.log') ?: [] as $file) {
            $name = basename($file, '.log');
            $d    = substr($name, 4); // strip "log-"
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
                $dates[] = $d;
            }
        }

        rsort($dates);
        return array_slice($dates, 0, 30);
    }

    /**
     * Read and parse a single log file into structured entries.
     * Each entry is: [ 'level'=>..., 'datetime'=>..., 'message'=>..., 'trace'=>... ]
     */
    private function parseLogFile(string $date): array
    {
        $logFile = WRITEPATH . 'logs/log-' . $date . '.log';

        if (!is_file($logFile)) {
            return [];
        }

        $content = file_get_contents($logFile);
        if ($content === false) {
            return [];
        }

        $entries = [];
        // Each CI4 log entry starts with: LEVEL - YYYY-MM-DD HH:MM:SS -->
        $pattern = '/^(' . implode('|', self::LEVELS) . ')\s+-\s+(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})\s+-->/m';
        $parts   = preg_split($pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        // $parts[0] = text before first entry (usually empty)
        // Then groups of 3: [level, datetime, body]
        $i = 1;
        while ($i + 2 < count($parts)) {
            $level    = $parts[$i];
            $datetime = $parts[$i + 1];
            $body     = trim($parts[$i + 2]);

            // Split body into main message + optional stack trace
            $newline = strpos($body, "\n");
            $message = $newline !== false ? trim(substr($body, 0, $newline)) : $body;
            $trace   = $newline !== false ? trim(substr($body, $newline + 1))   : '';

            $entries[] = [
                'level'    => $level,
                'datetime' => $datetime,
                'message'  => $message,
                'trace'    => $trace,
            ];

            $i += 3;
        }

        return array_reverse($entries); // newest first
    }

    /**
     * Filter entries by level (empty = all).
     */
    private function filterEntries(array $entries, string $level): array
    {
        if ($level === '' || !in_array($level, self::LEVELS, true)) {
            return $entries;
        }
        return array_values(array_filter($entries, fn($e) => $e['level'] === $level));
    }
}
