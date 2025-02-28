<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDueDateNotification;
use Illuminate\Console\Command;

class SendTaskDueDateNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-task-due-date-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * This will send a notification to the user when a task due date is 48 hours away
     */
    public function handle()
    {
        // Get all tasks that are due in 48 hours
        $tasks = Task::where('due_date', now()->addDays(2))->get();

        // Loop through the tasks and send a notification to the user
        foreach ($tasks as $task) {
            $task->user->notify(new TaskDueDateNotification($task));
        }
    }
}
