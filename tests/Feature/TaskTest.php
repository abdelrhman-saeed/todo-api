<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

it('results all tasks for manager', function () {

    $this->seed();

    $this->actingAs(User::where('role', 'manager')->first())
            ->get('/api/tasks')
            ->assertJsonCount(Task::count())
            ->assertOk();

});

it('results all filtered tasks for a manager', function () {

    $user       = User::factory(['role' => 'user'])->create();
    $manager    = User::factory(['role' => 'manager'])->create();

    $task       = Task::factory( ['assignee' => $user->id, 'user_id' => $manager->id] )->create();

    $this->actingAs($manager)
            ->get('api/tasks', $task->toArray())
            ->assertOk();

});

it('responds with 403 when quering a task with non existing task columns', function () {

    $this->actingAs(User::factory(['role' => 'manager'])->create())
            ->get('/api/tasks?taskColumnThatDoesntExist=dummyValue')
            ->assertForbidden();
});


it("responds with 404 for not found task", function () {

    $this->actingAs(User::factory(['role' => 'manager'])->create())
            ->get('api/task', ['title' => 'a non existent task'])
            ->assertNotFound();

});

/**
 * the user shouldn't request a task with an assignee param because the api
 * return him the results that are assigned to him by default
 */
it('responds with 403 when user request a task with assignee param', function () {

    // that's for the /api/tasks route
    $this->actingAs($user = User::factory(['role' => 'user'])->create())
            ->get('api/tasks?assignee=1')
            ->assertForbidden();

    // check if the user can request a task that is not assigned to him from
    // the api/tasks/{task} route

    $task = Task::factory([
        'assignee'  => User::factory(['role' => 'user'])->create(),
        'user_id'   => User::factory(['role' => 'manager'])->create()
    ])->create();

     // that task is not assigned to this user so it should respond with 403 status code
    $this->actingAs($user)->get('api/tasks/'. $task->id)->assertForbidden();

});


it ('responds with 403 when a user stores a task', function () {

    $this->actingAs(User::factory(['role' => 'user'])
            ->create())->post('api/tasks')->assertForbidden();

});

it ('responds with 200 when a manager stores a task', function () {
      
    $task = Task::factory([
            'assignee'  => User::factory(['role' => 'user'])->create(),
            'user_id'   => ( $manager = User::factory(['role' => 'manager'])->create() )->id
    ])
    ->make()
    ->toArray();

    $this->actingAs($manager)->post('api/tasks', $task)->assertOk();
});


it('responds with a 200 when manager request any a task by id', function () {

    $task = Task::factory([
        'assignee'  => User::factory(['role' => 'user'])->create(),
        'user_id'   => ( $manager = User::factory(['role' => 'manager'])->create() )->id
    ])
    ->create();

    $this->actingAs($manager)->get('api/tasks/'. $task->id)->assertOk();

});

it('responds with a 200 when user request a task assignned to him', function () {

    $task = Task::factory([
        'assignee'  => $user = User::factory(['role' => 'user'])->create(),
        'user_id'   => User::factory(['role' => 'manager'])->create()->id
    ])
    ->create();

    $this->actingAs($user)->get('api/tasks/'. $task->id)->assertOk();

});

it('responds with a 404 when user request a task that is not assignned to him', function () {

    $task = Task::factory([
        'assignee'  => User::factory(['role' => 'user'])->create(),
        'user_id'   => User::factory(['role' => 'manager'])->create()->id
    ])
    ->create();

    $this->actingAs( User::factory(['role' => 'user'])->create() )
            ->get('api/tasks/'. $task->id)
            ->assertForbidden();

});


it('responds with 200 when a manager update a task', function () {

    $tasks = Task::factory([
        'assignee'  => User::factory(['role' => 'user'])->create()->id,
        'user_id'   => ( $manager = User::factory(['role' => 'manager'])->create() )->id
    ])
    ->count(2)
    ->create();

    $this->actingAs($manager)
            ->put('api/tasks/'. $tasks[0]->id, [
                    'title' => 'updated title',
                    'description' => 'updated description',
                    'due_date' => '1998/9/24',
                    'status' => 'canceled',
                    'dependencies' => [$tasks[1]->id]
                ])->assertOk();

});


it(<<<TESTCASE
        it responds with 403 when a user or a manager update a task status
        while its dependencies are not completed'
    TESTCASE, function () {

        $tasks = Task::factory([
            'assignee'  => ( $user = User::factory(['role' => 'user'])->create() )->id,
            'user_id'   => ( $manager = User::factory(['role' => 'manager'])->create() )->id
        ])
        ->count(3)
        ->create();

        // $task[0] will be the parent task and all thier status are "pending"

        $this->actingAs($manager)->put('api/tasks/'. $tasks[0]->id, [
            'dependencies' => [$tasks[1]->id, $tasks[2]->id]
        ])
        ->assertOk();

        $this->actingAs($manager)
                ->put('api/tasks/'. $tasks[0]->id, ['status' => 'completed'])
                ->assertForbidden();

        $this->actingAs($user)
                ->put('api/tasks/'. $tasks[0]->id, ['status' => 'completed'])
                ->assertForbidden();

});

it('responds 403 when a user update a task that is not assigned to him', function () {

    $task = Task::factory([
        'assignee'  => User::factory(['role' => 'user'])->create()->id,
        'user_id'   => User::factory(['role' => 'manager'])->create()->id
    ])
    ->create();

    // it doesn't matter what is status is getting updating
    // while the normal user is the authenticated one
    $this->actingAs(User::factory(['role' => 'user'])->create())
            ->put('api/tasks/'. $task->id, ['status' => 'canceled'])
            ->assertForbidden();

});


it('responds 200 when a manager deletes a taks', function () {

    $task = Task::factory([
        'assignee'  => User::factory(['role' => 'user'])->create()->id,
        'user_id'   => ( $manager = User::factory(['role' => 'manager'])->create() )->id
    ])
    ->create();

    $this->actingAs($manager)->delete('api/tasks/'. $task->id)->assertOk();

});

it('responds 403 when a user deletes a taks', function () {

    $task = Task::factory([
        'assignee'  => ( $user = User::factory(['role' => 'user'])->create() )->id,
        'user_id'   => User::factory(['role' => 'manager'])->create()->id
    ])
    ->create();

    $this->actingAs($user)->delete('api/tasks/'. $task->id)->assertForbidden();

});