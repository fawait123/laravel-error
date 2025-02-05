<?php

use App\Jobs\GithubErrorJob;
use App\Services\GitHubLogger;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Exception $th) {
            // Single channel
            GithubErrorJob::dispatch("BUG", "{$th->getMessage()} \n \n \n \n", $th->getTraceAsString());
        });
    })->create();
