<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuthenticateApi;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller implements HasMiddleware
{

    public static function middleware() {
        return [ new Middleware( AuthenticateApi::class ) ];
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            if (auth()->user()->role == 'user' && $request->has('assignee')) {
                return response('forbidden', 403);
            }

            $tasks = Task::where(
                        $request->merge(['parent_task_id' => null])->all() )->with('dependencies');

            return auth()->user()->role == 'manager'
                    ? $tasks->get()
                    : $tasks->where('assignee', auth()->user()->id)->get();
        }
        catch(QueryException $e) {
            return response('the query has unknown database columns', 403) ;
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        auth()->user()->tasks()->create($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        Gate::authorize('view', $task);

        // return $task->toArray();
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->fill($request->validated())->save();
        
        Task::whereIn('id', $request->dependencies)
                ->update(['parent_task_id' => $task->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        
        $task->delete();
    }
}
