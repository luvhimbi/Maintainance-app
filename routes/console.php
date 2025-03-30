<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IssueController;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::call(function () {
    $queuedTasks = Task::whereNull('assignee_id')->get();
    Log::info('Processing queued tasks: ' . $queuedTasks->count() . ' tasks found.');
    foreach ($queuedTasks as $task) {
        app()->make(IssueController::class)->assignOrQueueTask($task);
    }
})->everyFifteenMinutes();