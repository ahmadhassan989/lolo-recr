<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CvParserService
{
    /**
     * Skills recognised by the keyword fallback when AI parsing is off.
     */
    protected const KNOWN_SKILLS = [
        'PHP', 'Laravel', 'Symfony', 'JavaScript', 'TypeScript', 'SQL', 'MySQL',
        'PostgreSQL', 'MongoDB', 'Redis', 'React', 'Vue', 'Angular', 'Node.js',
        'Python', 'Django', 'Java', 'Spring', 'C#', '.NET', 'Kotlin', 'Swift',
        'Flutter', 'Docker', 'Kubernetes', 'Git', 'AWS', 'Azure', 'Linux',
        'REST', 'GraphQL', 'Figma', 'Photoshop', 'Excel', 'SEO',
    ];

    /**
     * Parse a CV file and attempt to extract structured details.
     * Uses the configured LLM when CV_PARSER_AI is on, falling back
     * to keyword heuristics on any failure. Never throws.
     *
     * @return array{skills: string|null, experience_years: int|null, education_level: string|null}
     */
    public function parse(string $filePath): array
    {
        $text = $this->extractText($filePath);

        $ai = config('services.cv_parser.ai_enabled') ? $this->aiExtract($text) : [];

        return [
            'skills' => $ai['skills'] ?? $this->extractSkills($text),
            'experience_years' => $ai['experience_years'] ?? $this->extractExperience($text),
            'education_level' => $ai['education_level'] ?? $this->extractEducation($text),
        ];
    }

    /**
     * Ask the local LLM for structured CV data. Returns a partial
     * result array; empty when AI is unreachable or output is bad.
     *
     * @return array{skills?: string|null, experience_years?: int|null, education_level?: string|null}
     */
    protected function aiExtract(string $text): array
    {
        if (trim($text) === '') {
            return [];
        }

        $prompt = 'Extract structured data from this CV text. Reply with JSON only: '
            .'{"skills": [skill names], "experience_years": integer|null, '
            .'"education_level": "PhD"|"Master"|"Bachelor"|"Diploma"|null}. '
            .'Experience is total professional years. '
            ."CV TEXT:\n---\n".Str::limit($text, 8000, '')."\n---";

        try {
            $response = Http::timeout(90)->post(rtrim(config('services.ollama.url'), '/').'/api/generate', [
                'model' => config('services.ollama.model'),
                'prompt' => $prompt,
                'stream' => false,
                'format' => 'json',
                'options' => ['num_predict' => 400, 'temperature' => 0],
            ]);

            if (! $response->successful()) {
                return [];
            }

            $data = json_decode(trim((string) $response->json('response', '')), true);
            if (! is_array($data)) {
                return [];
            }
        } catch (\Throwable $e) {
            return [];
        }

        $result = [];

        if (! empty($data['skills']) && is_array($data['skills'])) {
            $skills = collect($data['skills'])->filter(fn ($s) => is_string($s) && trim($s) !== '')
                ->map(fn ($s) => trim($s))->unique()->take(30)->implode(', ');
            $result['skills'] = $skills !== '' ? $skills : null;
        }

        if (isset($data['experience_years']) && is_numeric($data['experience_years'])) {
            $years = (int) $data['experience_years'];
            $result['experience_years'] = $years >= 0 && $years <= 60 ? $years : null;
        }

        if (! empty($data['education_level']) && is_string($data['education_level'])) {
            $level = Str::ucfirst(Str::lower(trim($data['education_level'])));
            if (in_array($level, ['PhD', 'Master', 'Bachelor', 'Diploma'], true)) {
                $result['education_level'] = $level;
            }
        }

        return $result;
    }

    /**
     * Attempt to extract raw text from the CV file.
     */
    protected function extractText(string $filePath): string
    {
        if (class_exists(\Smalot\PdfParser\Parser::class)) {
            try {
                $parser = new \Smalot\PdfParser\Parser;
                $pdf = $parser->parseFile($filePath);

                return $pdf->getText() ?? '';
            } catch (\Throwable $e) {
                // fall through to simple extraction
            }
        }

        return @file_get_contents($filePath) ?: '';
    }

    /**
     * Extract a comma separated list of recognised skills.
     */
    protected function extractSkills(string $text): ?string
    {
        if ($text === '') {
            return null;
        }

        $edgeCases = ['.NET', 'C#'];
        $wordSkills = array_diff(self::KNOWN_SKILLS, $edgeCases);
        $quote = fn (array $list) => implode('|', array_map(fn ($s) => preg_quote($s, '/'), $list));

        $pattern = '/\b('.$quote($wordSkills).')\b|('.$quote($edgeCases).')(?!\w)/i';

        preg_match_all($pattern, $text, $matches);

        $found = array_filter(array_merge($matches[1] ?? [], $matches[2] ?? []), fn ($s) => $s !== '');

        if (empty($found)) {
            return null;
        }

        $canonical = collect(self::KNOWN_SKILLS)->mapWithKeys(fn ($s) => [mb_strtolower($s) => $s]);

        $skills = collect($found)
            ->map(fn ($skill) => $canonical->get(mb_strtolower($skill), Str::ucfirst(Str::lower($skill))))
            ->unique()
            ->values()
            ->implode(', ');

        return $skills ?: null;
    }

    /**
     * Extract years of experience.
     */
    protected function extractExperience(string $text): ?int
    {
        if ($text === '') {
            return null;
        }

        if (preg_match('/(\d+)\+?\s*(years|yrs)/i', $text, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Extract highest education level.
     */
    protected function extractEducation(string $text): ?string
    {
        if ($text === '') {
            return null;
        }

        $text = Str::lower($text);

        if (str_contains($text, 'phd') || str_contains($text, 'doctorate')) {
            return 'PhD';
        }

        if (str_contains($text, 'master')) {
            return 'Master';
        }

        if (str_contains($text, 'bachelor')) {
            return 'Bachelor';
        }

        if (str_contains($text, 'diploma')) {
            return 'Diploma';
        }

        return null;
    }
}
