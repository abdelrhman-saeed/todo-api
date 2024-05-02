<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $taksTitle = 1;
        return [
            'title' => "Task Title No." . $taksTitle++,

            'description' => 'dummy description',

            'due_date' => now()->addDays(rand(1,9)),
            
            'status' => 'pending',

            'assignee' => rand(1,10),

            'parent_task_id' => null,

            'user_id' => rand(11,20)
        ];

    }
}
