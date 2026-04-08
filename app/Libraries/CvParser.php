<?php

namespace App\Libraries;

/**
 * Basic CV text extractor.
 *
 * For production, replace the PDF extraction with a dedicated library such as
 * smalot/pdfparser (composer require smalot/pdfparser) or an external API
 * (e.g. Affinda, Soveren, OpenAI).
 */
class CvParser
{
    /**
     * Parse a CV file and return basic structured data (legacy).
     * For enhanced parsing with confidence scores, use parseDetailed().
     *
     * @param  string $filePath Absolute path to the uploaded file
     * @param  string $mimeType MIME type of the file
     * @return array{email?:string, phone?:string, skills:string[], languages:string[]}
     */
    public function parse(string $filePath, string $mimeType): array
    {
        $detailed = $this->parseDetailed($filePath, $mimeType);

        return [
            'email'     => $detailed['email']['value'] ?? null,
            'phone'     => $detailed['phone']['value'] ?? null,
            'skills'    => $detailed['skills'] ?? [],
            'languages' => $detailed['languages'] ?? [],
        ];
    }

    /**
     * Parse CV with detailed confidence scores and additional fields.
     *
     * @param  string $filePath Absolute path to the uploaded file
     * @param  string $mimeType MIME type of the file
     * @return array Detailed parse result with confidence scores and extended fields
     */
    public function parseDetailed(string $filePath, string $mimeType): array
    {
        try {
            $text = $this->extractText($filePath, $mimeType);
        } catch (\Throwable $e) {
            log_message('warning', 'extractText failed: ' . $e->getMessage());
            $text = '';
        }

        if (empty($text)) {
            return [
                'headline'    => ['value' => '', 'confidence' => 0, 'source' => 'empty'],
                'summary'     => ['value' => '', 'confidence' => 0, 'source' => 'empty'],
                'email'       => ['value' => null, 'confidence' => 0, 'source' => 'empty'],
                'phone'       => ['value' => null, 'confidence' => 0, 'source' => 'empty'],
                'skills'      => [],
                'languages'   => [],
                'experiences' => [],
                'education'   => [],
                'overall_confidence' => 0.0,
            ];
        }

        // Extract each field safely
        $headline    = $this->safExtract('extractHeadline', $text);
        $summary     = $this->safExtract('extractSummary', $text);
        $email       = $this->extractEmail($text);
        $phone       = $this->extractPhone($text);
        $skills      = $this->safExtract('extractSkillsDetailed', $text) ?? [];
        $languages   = $this->safExtract('extractLanguagesDetailed', $text) ?? [];
        $experiences = $this->safExtract('extractExperiences', $text) ?? [];
        $education   = $this->safExtract('extractEducation', $text) ?? [];
        $confidence  = $this->safExtract('calculateOverallConfidence', $text) ?? 0.5;

        return [
            'headline'    => $headline ?? ['value' => '', 'confidence' => 0, 'source' => 'error'],
            'summary'     => $summary ?? ['value' => '', 'confidence' => 0, 'source' => 'error'],
            'email'       => [
                'value'      => $email,
                'confidence' => $email ? 0.95 : 0.0,
                'source'     => 'regex_pattern',
            ],
            'phone'       => [
                'value'      => $phone,
                'confidence' => $phone ? 0.90 : 0.0,
                'source'     => 'regex_pattern',
            ],
            'skills'      => is_array($skills) ? $skills : [],
            'languages'   => is_array($languages) ? $languages : [],
            'experiences' => is_array($experiences) ? $experiences : [],
            'education'   => is_array($education) ? $education : [],
            'overall_confidence' => is_numeric($confidence) ? (float)$confidence : 0.5,
        ];
    }

    /**
     * Safely call an extractor method and catch any errors
     */
    private function safExtract(string $method, string $text)
    {
        try {
            return $this->$method($text);
        } catch (\Throwable $e) {
            log_message('warning', "Extraction method '$method' failed: " . $e->getMessage());
            return null;
        }
    }

    // ─── Text Extraction ────────────────────────────────────────────────

    private function extractText(string $path, string $mime): string
    {
        if ($mime === 'application/pdf') {
            return $this->extractFromPdf($path);
        }

        // DOCX: it's a ZIP — parse the word/document.xml part
        if (in_array($mime, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ], true)) {
            return $this->extractFromDocx($path);
        }

        return '';
    }

    /**
     * Naive PDF text extraction using pdfparser if available, otherwise
     * falls back to reading raw bytes and stripping binary chars.
     */
    private function extractFromPdf(string $path): string
    {
        // If smalot/pdfparser is installed, use it
        if (class_exists('\Smalot\PdfParser\Parser')) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf    = $parser->parseFile($path);
                return $pdf->getText();
            } catch (\Throwable $e) {
                log_message('notice', 'pdfparser failed: ' . $e->getMessage());
            }
        }

        // Basic fallback: read raw and strip binary
        $raw  = file_get_contents($path);
        $text = preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', ' ', $raw);
        return $text ?: '';
    }

    private function extractFromDocx(string $path): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return '';
        }
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            return '';
        }

        // Strip XML tags and decode entities
        $text = strip_tags(str_replace('</w:p>', "\n", $xml));
        return html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    // ─── Field Extractors ───────────────────────────────────────────────

    private function extractEmail(string $text): ?string
    {
        if (preg_match('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $text, $m)) {
            return strtolower($m[0]);
        }
        return null;
    }

    private function extractPhone(string $text): ?string
    {
        // Matches common international formats: +33 6 12 34 56 78, 06.12.34.56.78, etc.
        if (preg_match('/(?:\+?\d[\d\s\.\-\(\)]{7,}\d)/', $text, $m)) {
            return trim($m[0]);
        }
        return null;
    }

    /**
     * Extract skills by matching against a common skill keyword list.
     * In production, use an NLP service for better accuracy.
     */
    private function extractSkills(string $text): array
    {
        $knownSkills = [
            // Programming languages
            'PHP', 'Python', 'JavaScript', 'TypeScript', 'Java', 'C#', 'C++', 'Go', 'Rust', 'Swift', 'Kotlin',
            // Frameworks
            'Laravel', 'CodeIgniter', 'Symfony', 'Django', 'Flask', 'Spring', 'React', 'Vue', 'Angular', 'Next.js',
            'Node.js', 'Express', 'Ruby on Rails', 'ASP.NET',
            // Databases
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'Oracle', 'SQL Server',
            // Cloud / DevOps
            'AWS', 'Azure', 'GCP', 'Docker', 'Kubernetes', 'CI/CD', 'Terraform', 'Git', 'Linux',
            // Data / AI
            'TensorFlow', 'PyTorch', 'Pandas', 'NumPy', 'Machine Learning', 'Deep Learning', 'NLP',
            // Design
            'Figma', 'Photoshop', 'Illustrator', 'UX', 'UI',
            // Other
            'HTML', 'CSS', 'SASS', 'REST', 'GraphQL', 'Agile', 'Scrum', 'Jira',
        ];

        $found = [];
        foreach ($knownSkills as $skill) {
            if (stripos($text, $skill) !== false) {
                $found[] = $skill;
            }
        }

        return array_unique($found);
    }

    private function extractLanguages(string $text): array
    {
        $langs  = ['French', 'English', 'Spanish', 'German', 'Arabic', 'Portuguese', 'Italian', 'Chinese', 'Japanese'];
        $found  = [];
        foreach ($langs as $lang) {
            if (stripos($text, $lang) !== false) {
                $found[] = $lang;
            }
        }
        return $found;
    }

    // ─── Extended Field Extractors (with confidence) ─────────────────────

    /**
     * Extract headline/title from CV (first line or title section).
     * Returns array with value and confidence score.
     */
    private function extractHeadline(string $text): array
    {
        $lines = array_filter(array_map('trim', explode("\n", $text)), 'strlen');
        $headline = reset($lines) ?? '';
        
        // Confidence: high if looks like a job title (3-50 chars, no emails/URLs)
        $confidence = 0.0;
        if (!empty($headline) && strlen($headline) <= 100 && !filter_var($headline, FILTER_VALIDATE_EMAIL)) {
            $confidence = stripos($headline, 'developer') !== false || 
                         stripos($headline, 'manager') !== false ||
                         stripos($headline, 'engineer') !== false ? 0.95 : 0.70;
        }

        return [
            'value'      => strlen($headline) <= 255 ? $headline : substr($headline, 0, 255),
            'confidence' => $confidence,
            'source'     => 'first_line',
        ];
    }

    /**
     * Extract professional summary (usually second section).
     */
    private function extractSummary(string $text): array
    {
        $lines = explode("\n", $text);
        $summary = '';
        $inSummary = false;

        foreach ($lines as $i => $line) {
            $line = trim($line);
            
            // Start summary detection
            if (!$inSummary && (stripos($line, 'summary') !== false || 
                                stripos($line, 'about') !== false ||
                                stripos($line, 'profile') !== false)) {
                $inSummary = true;
                continue;
            }

            // Collect summary until section break
            if ($inSummary) {
                if (strlen($line) > 0 && (stripos($line, 'experience') !== false ||
                                          stripos($line, 'education') !== false ||
                                          stripos($line, 'skills') !== false)) {
                    break;
                }
                if (strlen($line) > 5) {
                    $summary .= ($summary ? "\n" : '') . $line;
                }
            }
        }

        $confidence = strlen($summary) > 20 ? 0.85 : 0.0;
        return [
            'value'      => substr($summary, 0, 10000),
            'confidence' => $confidence,
            'source'     => 'cv_section',
        ];
    }

    /**
     * Extract skills with confidence scores (detailed version).
     */
    private function extractSkillsDetailed(string $text): array
    {
        $knownSkills = $this->getKnownSkillsList();
        $found = [];
        
        foreach ($knownSkills as $skill) {
            // Use word boundaries for better matching
            $pattern = '/\b' . preg_quote($skill, '/') . '\b/i';
            if (preg_match($pattern, $text)) {
                $found[] = [
                    'name'       => $skill,
                    'level'      => 'intermediate',
                    'confidence' => 0.92,
                    'source'     => 'keyword_match',
                ];
            }
        }

        return $found;
    }

    /**
     * Extract languages with confidence (detailed version).
     */
    private function extractLanguagesDetailed(string $text): array
    {
        $langMap = [
            'French'     => ['français', 'french', 'native', 'fluent'],
            'English'    => ['english', 'anglais', 'native', 'fluent'],
            'Spanish'    => ['spanish', 'español', 'castellano'],
            'German'     => ['german', 'deutsch'],
            'Arabic'     => ['arabic', 'arabe'],
            'Portuguese' => ['portuguese', 'portugais'],
            'Italian'    => ['italian', 'italiano'],
            'Chinese'    => ['chinese', 'mandarin', 'cantonese'],
            'Japanese'   => ['japanese', 'nihongo'],
        ];

        $found = [];
        foreach ($langMap as $lang => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($text, $keyword) !== false) {
                    $found[] = [
                        'name'       => $lang,
                        'level'      => 'intermediate',
                        'confidence' => 0.88,
                        'source'     => 'keyword_match',
                    ];
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * Extract experiences: supports formats like:
     *   "Job Title – Company (04/2022 – Présent)"
     *   "Job Title – Company (2019 – 2022)"
     */
    private function extractExperiences(string $text): array
    {
        // Isolate the experience section
        $section = $this->extractSection($text, [
            'experience professionnelle', 'expériences professionnelles', 'expériences',
            'work experience', 'professional experience', 'employment', 'career',
        ]);

        if (empty($section)) {
            return [];
        }

        $experiences = [];
        // Pattern: anything – anything (MM/YYYY – MM/YYYY|Présent|Present|Actuel)
        $dateGroup = '(?:\\d{1,2}\\/)?\\d{4}';
        $endGroup  = '(?:\\d{1,2}\\/)?\\d{4}|[Pp]r[eé]sent|[Aa]ctuel(?:lement)?|[Cc]urrent|[Aa]ujourd\'hui';
        $pattern   = '/^(.+?)\\s*[–—-]\\s*(.+?)\\s*\\(' . '(' . $dateGroup . ')' . '\\s*[–—-]\\s*' . '(' . $endGroup . ')' . '\\)/mu';

        if (!preg_match_all($pattern, $section, $m, PREG_SET_ORDER)) {
            return [];
        }

        // Collect description lines between entries
        $entryPositions = [];
        preg_match_all($pattern, $section, $allM, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $sectionLines = explode("\n", $section);

        foreach ($m as $idx => $match) {
            $title       = trim($match[1]);
            $company     = trim($match[2]);
            $startRaw    = trim($match[3]);
            $endRaw      = trim($match[4]);

            // Parse start/end years
            $startYear = (int) preg_replace('/.*?(\d{4})/', '$1', $startRaw);
            $isCurrent = preg_match('/present|actuel|current|aujourd/i', $endRaw);
            $endYear   = $isCurrent ? null : (int) preg_replace('/.*?(\d{4})/', '$1', $endRaw);

            // Collect bullet description lines after this entry header
            $descLines = [];
            $inEntry   = false;
            foreach ($sectionLines as $line) {
                $trimmed = trim($line);
                if (stripos($trimmed, $title) !== false && stripos($trimmed, $company) !== false) {
                    $inEntry = true;
                    continue;
                }
                if ($inEntry) {
                    if (empty($trimmed)) continue;
                    // Stop at next entry (line that matches another entry pattern)
                    if (preg_match('/\\((?:\\d{1,2}\\/)?\\d{4}\\s*[–—-]/u', $trimmed)) break;
                    $descLines[] = ltrim($trimmed, '•·✓-– ');
                }
            }

            $experiences[] = [
                'title'        => substr($title, 0, 255),
                'organization' => substr($company, 0, 255),
                'start_year'   => $startYear ?: null,
                'end_year'     => $endYear,
                'is_current'   => (int) $isCurrent,
                'description'  => substr(implode(' ', array_slice($descLines, 0, 8)), 0, 1000),
                'confidence'   => 0.80,
                'source'       => 'section_parse',
            ];
        }

        return array_slice($experiences, 0, 20);
    }

    /**
     * Extract education entries. Supports formats like:
     *   "Diplôme d'Ingénieur Informatique – ESPRIT (2017 – 2020)"
     *   "Master Computer Science – Paris Saclay (2018)"
     */
    private function extractEducation(string $text): array
    {
        $section = $this->extractSection($text, [
            'formation', 'formations', 'études', 'education', 'academic',
            'diplômes', 'qualifications', 'scolarité',
        ]);

        if (empty($section)) {
            return [];
        }

        $education = [];
        $yearGroup = '(?:\\d{1,2}\\/)?\\d{4}';

        // Pattern 1: Degree – Institution (YYYY – YYYY) or (YYYY)
        $p1 = '/^[•·\\-\\s]*(.+?)\\s*[–—-]\\s*(.+?)\\s*\\((' . $yearGroup . ')(?:\\s*[–—-]\\s*(' . $yearGroup . '))?\\)/mu';
        if (preg_match_all($p1, $section, $m, PREG_SET_ORDER)) {
            foreach ($m as $match) {
                $degree  = trim($match[1]);
                $school  = trim($match[2]);
                $year1   = (int) preg_replace('/.*?(\d{4})/', '$1', $match[3]);
                $year2   = isset($match[4]) && $match[4] ? (int) preg_replace('/.*?(\d{4})/', '$1', $match[4]) : null;
                $gradYear = $year2 ?: $year1;

                $education[] = [
                    'degree'        => substr($degree, 0, 255),
                    'institution'   => substr($school, 0, 255),
                    'field'         => '',
                    'start_year'    => $year2 ? $year1 : null,
                    'end_year'      => $gradYear,
                    'year_graduated'=> $gradYear,
                    'confidence'    => 0.82,
                    'source'        => 'section_parse',
                ];
            }
            return array_slice($education, 0, 10);
        }

        // Pattern 2: fallback — lines with a degree keyword and a 4-digit year
        $degreeRe = '/Dipl[oô]me|Licence|Ing[eé]nieur|Bachelor|Master|MBA|BTS|DUT|PhD|Doctorat|Certificat/i';
        foreach (explode("\n", $section) as $line) {
            $line = ltrim(trim($line), '•·- ');
            if (preg_match($degreeRe, $line) && preg_match('/(\d{4})/', $line, $ym)) {
                $education[] = [
                    'degree'        => substr($line, 0, 255),
                    'institution'   => '',
                    'field'         => '',
                    'year_graduated'=> (int) $ym[1],
                    'confidence'    => 0.65,
                    'source'        => 'keyword_match',
                ];
            }
        }

        return array_slice($education, 0, 10);
    }

    /**
     * Isolate a named section from CV text by detecting its header keyword.
     * Returns the text between the matching header and the next section header.
     */
    private function extractSection(string $text, array $headers): string
    {
        // All known section starts (to detect where a section ends)
        $allHeaders = [
            'experience', 'expérience', 'formation', 'education', 'compétences',
            'skills', 'langues', 'languages', 'certifi', 'projets', 'projects',
            'profil', 'summary', 'contact', 'références', 'references',
        ];

        $lines   = explode("\n", $text);
        $result  = [];
        $inside  = false;

        foreach ($lines as $line) {
            $lower = mb_strtolower(trim($line));

            // Check if this line is a header we're looking for
            if (!$inside) {
                foreach ($headers as $h) {
                    if (strpos($lower, $h) !== false && mb_strlen($lower) < 60) {
                        $inside = true;
                        break;
                    }
                }
                continue;
            }

            // Check if we've reached the next section
            foreach ($allHeaders as $h) {
                $isTarget = false;
                foreach ($headers as $th) {
                    if (strpos($h, $th) !== false || strpos($th, $h) !== false) {
                        $isTarget = true;
                        break;
                    }
                }
                if (!$isTarget && strpos($lower, $h) !== false && mb_strlen($lower) < 60) {
                    return implode("\n", $result);
                }
            }

            $result[] = $line;
        }

        return implode("\n", $result);
    }

    /**
     * Calculate overall confidence score for the entire parsing.
     */
    private function calculateOverallConfidence(string $text): float
    {
        $confidences = [];

        if (strlen($text) > 100) $confidences[] = 0.9;
        if (preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $text)) $confidences[] = 0.95;
        if (preg_match('/(?:\+?\d[\d\s\.\-\(\)]{7,}\d)/', $text)) $confidences[] = 0.95;
        if (preg_match('/\d{4}\s*[-–]\s*(?:\d{4}|present)/i', $text)) $confidences[] = 0.88;
        if (preg_match('/(?:experience|education|skills)/i', $text)) $confidences[] = 0.85;

        return count($confidences) > 0 ? array_sum($confidences) / count($confidences) : 0.5;
    }

    /**
     * Get the hardcoded list of known skills.
     * Can be extended or moved to config/database in the future.
     */
    private function getKnownSkillsList(): array
    {
        return [
            // Programming languages
            'PHP', 'Python', 'JavaScript', 'TypeScript', 'Java', 'C#', 'C++', 'Go', 'Rust', 'Swift', 'Kotlin',
            // Frameworks
            'Laravel', 'CodeIgniter', 'Symfony', 'Django', 'Flask', 'Spring', 'React', 'Vue', 'Angular', 'Next.js',
            'Node.js', 'Express', 'Ruby on Rails', 'ASP.NET',
            // Databases
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'Oracle', 'SQL Server',
            // Cloud / DevOps
            'AWS', 'Azure', 'GCP', 'Docker', 'Kubernetes', 'CI/CD', 'Terraform', 'Git', 'Linux',
            // Data / AI
            'TensorFlow', 'PyTorch', 'Pandas', 'NumPy', 'Machine Learning', 'Deep Learning', 'NLP',
            // Design
            'Figma', 'Photoshop', 'Illustrator', 'UX', 'UI',
            // Other
            'HTML', 'CSS', 'SASS', 'REST', 'GraphQL', 'Agile', 'Scrum', 'Jira',
        ];
    }
}
