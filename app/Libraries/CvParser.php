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
     * Parse a CV file and return structured data.
     *
     * @param  string $filePath Absolute path to the uploaded file
     * @param  string $mimeType MIME type of the file
     * @return array{name?:string, email?:string, phone?:string, skills:string[], languages:string[]}
     */
    public function parse(string $filePath, string $mimeType): array
    {
        $text = $this->extractText($filePath, $mimeType);

        return [
            'email'     => $this->extractEmail($text),
            'phone'     => $this->extractPhone($text),
            'skills'    => $this->extractSkills($text),
            'languages' => $this->extractLanguages($text),
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
}
