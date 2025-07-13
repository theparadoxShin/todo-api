<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Apply middleware for authentication
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : JsonResponse
    {
        // Fetch tasks for the authenticated user
        // Use the user's tasks relationship to get tasks
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $query = $request->user()->tasks();

        // Filtres optionnels
        if ($request->has('status')) {
            $query->where('is_completed', $request->status === 'completed');
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $tasks = $query->byPriority()
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'stats' => $request->user()->stats,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request) : JsonResponse
    {
        // Check if the authenticated user is authorized to create a task
        $this->authorize('create', Task::class);
        try {

            $task = $request->user()->tasks()->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => $task->load('user')
            ], 201);

        }catch (\Exception $exception){
            return response()->json([
                'success' => false,
                'message' => 'Error in the creation of the tasks : ' . $exception->getMessage(),
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task) : JsonResponse
    {
        // Check if the authenticated user is authorized to view the task
        $this->authorize('view', $task);

        return response()->json([
            'success' => true,
            'data' => $task->load('user')
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task) : JsonResponse
    {
        // Check if the authenticated user is authorized to update the task
        $this->authorize('update', $task);

        try {
            $task->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'data' => $task->load('user')
            ]);

        }catch (\Exception $exception){
            return response()->json([
                'success' => false,
                'message' => 'Error in the update of the tasks : ' . $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tâche supprimée avec succès',
            'stats' => $request->user()->fresh()->stats,
        ]);
    }
}
