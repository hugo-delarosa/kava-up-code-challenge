<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a base user
        $user = User::factory()->create([
            'email' => 'baseuser@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create 2-3 projects for the base user
        Project::factory()->count(rand(2, 3))->create(['user_id' => $user->id])->each(function ($project) use ($user) {
            // Create 10-25 tasks for each project
            Task::factory()->count(rand(10, 25))->create([
                'project_id' => $project->id,
                'user_id' => $user->id, // Ensure user_id is set
                'status' => function () {
                    // Randomly assign different statuses
                    $statuses = ['to_do', 'in_progress', 'done'];
                    return $statuses[array_rand($statuses)];
                },
                'due_date' => function () {
                    // Randomly set some tasks to be due in 48 hours
                    return rand(0, 1) ? Carbon::now()->addDays(rand(0,2)) : Carbon::now()->addDays(rand(3, 10));
                },
            ]);
        });
    }
}
