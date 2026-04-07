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
        $text = $this->extractText($filePath, $mimeType);

        return [
            'headline'    => $this->extractHeadline($text),
            'summary'     => $this->extractSummary($text),
            'email'       => $this->extractEmail($text),
            'phone'       => $this->extractPhone($text),
            'skills'      => $this->extractSkillsDetailed($text),
            'languages'   => $this->extractLanguagesDetailed($text),
            'experiences' => $this->extractExperiences($text),
            'education'   => $this->extractEducation($text),
            'overall_confidence' => $this->calculateOverallConfidence($text),
        ];
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
     * Extract experiences: company + title + years (basic pattern matching).
     */
    private function extractExperiences(string $text): array
    {
        $experiences = [];
        
        // Look for sections like "EXPERIENCE" or "WORK HISTORY"
        if (!preg_match('/(?:experience|work\s+history|career|employment)/i', $text)) {
            return [];
        }

        // Split by years: 2020-2022, 2020-Present, etc.
        $pattern = '/(\d{4})\s*[-–]\s*(?:(\d{4})|present|current)/i';
        if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $idx => $match) {
                // Extract context around the date
                $offset = $match[1];
                $contextStart = max(0, $offset - 200);
                $contextEnd = min(strlen($text), $offset + 300);
                $context = substr($text, $contextStart, $contextEnd - $contextStart);

                // Try to extract company and title from context
                $lines = array_filter(array_map('trim', explode("\n", $context)));
                $company = '';
                $title = '';

                foreach ($lines as $line) {
                    if (strlen($line) > 5 && strlen($line) < 100) {
                        if (empty($title)) {
                            $title = $line;
                        } elseif (empty($company)) {
                            $company = $line;
                        }
                    }
                }

                if (!empty($title)) {
                    $experiences[] = [
                        'title'       => substr($title, 0, 255),
                        'organization' => substr($company, 0, 255),
                        'start_year'  => (int) $matches[1][$idx],
                        'end_year'    => stripos($matches[0][$idx], 'present') !== false ? null : (int) $matches[2][$idx],
                        'confidence'  => 0.75,
                        'source'      => 'date_pattern',
                    ];
                }
            }
        }

        return array_slice($experiences, 0, 20); // Limit to 20
    }

    /**
     * Extract education: degree, university, year.
     */
    private function extractEducation(string $text): array
    {
        $education = [];
        
        // Keywords for education section
        if (!preg_match('/(?:education|degree|university|studies)/i', $text)) {
            return [];
        }

        // Common degree patterns
        $degreePatterns = [
            'Bachelor|Bachelor\'s|B\.S\.|B\.A\.' => 'Bachelor',
            'Master|Master\'s|M\.S\.|M\.A\.' => 'Master',
            'PhD|Doctorate|Ph\.D\.' => 'Doctorate',
            'Diploma|Certificate' => 'Certificate',
        ];

        $foundDegrees = [];
        foreach ($degreePatterns as $pattern => $degreeType) {
            if (preg_match_all('/' . $pattern . '/i', $text, $matches)) {
                $foundDegrees[] = [
                    'degree'     => $degreeType,
                    'confidence' => 0.90,
                ];
            }
        }

        // University patterns
        $universities = [
            'Paris', 'Sorbonne', 'Stanford', 'MIT', 'Cambridge', 'Oxford',
            'Harvard', 'Yale', 'Princeton', 'Columbia', 'University',
        ];

        foreach ($universities as $uni) {
            if (stripos($text, $uni) !== false) {
                // Find the closest degree to this university
                if (!empty($foundDegrees)) {
                    $education[] = [
                        'degree'       => $foundDegrees[0]['degree'] ?? 'Degree',
                        'institution'  => $uni,
                        'field'        => '',
                        'year'         => null,
                        'confidence'   => 0.75,
                        'source'       => 'pattern_match',
                    ];
                    break;
                }
            }
        }

        return $education;
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
