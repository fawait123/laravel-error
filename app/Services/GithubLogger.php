<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubLogger
{
    protected string $repo;
    protected string $token;

    public function __construct()
    {
        $this->repo = env('GITHUB_REPO');
        $this->token = env('GITHUB_TOKEN');
    }

    public function logError(
        string $logLevel,
        string $message,
        string $stackTrace,
        array $extraData = [],
        array $labels = ['bug']
    ): bool {
        $formattedBody = "**Log Level:** `$logLevel`\n\n" .
            "**Message:**\n" .
            "> $message\n\n" .
            "**Stack Trace:**\n" .
            "```php\n$stackTrace\n```\n\n" .
            "**Extra Data:**\n" .
            "```json\n" . $this->formatJson($extraData) . "\n```";

        $response = Http::withToken($this->token)->post("https://api.github.com/repos/{$this->repo}/issues", [
            'title' => "{$logLevel}: {$message}",
            'body' => $formattedBody,
            'labels' => $labels,
        ]);

        if ($response->failed()) {
            Log::error("Failed to send GitHub issue: " . $response->body());
            return false;
        }

        Log::info("GitHub issue created: " . $response->json('html_url'));
        return true;
    }

    private function formatJson(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
