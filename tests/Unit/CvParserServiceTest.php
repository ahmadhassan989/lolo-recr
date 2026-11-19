<?php

namespace Tests\Unit;

use App\Services\CvParserService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CvParserServiceTest extends TestCase
{
    protected function parseText(string $text): array
    {
        $path = tempnam(sys_get_temp_dir(), 'cv').'.txt';
        file_put_contents($path, $text);

        try {
            return app(CvParserService::class)->parse($path);
        } finally {
            @unlink($path);
        }
    }

    public function test_keyword_fallback_extracts_details(): void
    {
        config(['services.cv_parser.ai_enabled' => false]);

        $parsed = $this->parseText('Senior developer. Skills: PHP, Laravel, MySQL, Docker. 6 years experience. Bachelor degree.');

        $this->assertSame('PHP, Laravel, MySQL, Docker', $parsed['skills']);
        $this->assertSame(6, $parsed['experience_years']);
        $this->assertSame('Bachelor', $parsed['education_level']);
    }

    public function test_ai_extraction_used_when_enabled(): void
    {
        config([
            'services.cv_parser.ai_enabled' => true,
            'services.ollama.url' => 'http://ollama.test',
        ]);

        Http::fake([
            'ollama.test/*' => Http::response([
                'response' => json_encode([
                    'skills' => ['Go', 'Terraform', 'Kafka'],
                    'experience_years' => 9,
                    'education_level' => 'master',
                ]),
            ]),
        ]);

        $parsed = $this->parseText('Plain CV text with no recognised keywords at all.');

        $this->assertSame('Go, Terraform, Kafka', $parsed['skills']);
        $this->assertSame(9, $parsed['experience_years']);
        $this->assertSame('Master', $parsed['education_level']);
    }

    public function test_ai_failure_falls_back_to_heuristics(): void
    {
        config([
            'services.cv_parser.ai_enabled' => true,
            'services.ollama.url' => 'http://ollama.test',
        ]);

        Http::fake(['ollama.test/*' => Http::response('down', 503)]);

        $parsed = $this->parseText('Worked with React for 3 years.');

        $this->assertSame('React', $parsed['skills']);
        $this->assertSame(3, $parsed['experience_years']);
    }
}
