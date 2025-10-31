<?php

namespace App\Services;

use Illuminate\Support\Str;

class CvParserService
{
    /**
     * Parse a CV file and attempt to extract structured details.
     *
     * @param  string  $filePath
     * @return array{skills: string|null, experience_years: int|null, education_level: string|null}
     */
    public function parse(string $filePath): array
    {
        $text = $this->extractText($filePath);

        return [
            'skills' => $this->extractSkills($text),
            'experience_years' => $this->extractExperience($text),
            'education_level' => $this->extractEducation($text),
        ];
    }

    /**
     * Attempt to extract raw text from the CV file.
     */
    protected function extractText(string $filePath): string
    {
        if (class_exists(\Smalot\PdfParser\Parser::class)) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
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

        preg_match_all('/\b(PHP|Laravel|JavaScript|SQL|React|Vue|Node\.js|AWS)\b/i', $text, $matches);

        if (empty($matches[0])) {
            return null;
        }

        $skills = collect($matches[0])
            ->map(fn ($skill) => Str::ucfirst(Str::lower($skill)))
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
