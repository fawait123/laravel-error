<?php

namespace App\Jobs;

use App\Services\GitHubLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GithubErrorJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $logLevel,
        public string $message,
        public string $stackTrace
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $logger = new GitHubLogger();
        $logger->logError($this->logLevel, $this->message, $this->stackTrace);
    }
}
