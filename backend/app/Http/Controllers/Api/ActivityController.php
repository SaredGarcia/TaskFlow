<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // These counters describe the complete collection, not only the current page.
        $statistics = Activity::query()
            ->selectRaw(
                'COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed',
                ['pending', 'in_progress', 'completed']
            )
            ->first();

        $query = Activity::query()->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $activities = $query->paginate(12)->withQueryString();

        return response()->json([
            'data' => $activities->items(),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
                'stats' => [
                    'total' => (int) $statistics->total,
                    'pending' => (int) $statistics->pending,
                    'in_progress' => (int) $statistics->in_progress,
                    'completed' => (int) $statistics->completed,
                ],
            ],
        ]);
    }

    public function store(StoreActivityRequest $request): JsonResponse
    {
        $activity = Activity::create($request->validated());

        return response()->json([
            'message' => 'Actividad creada correctamente.',
            'data' => $activity,
        ], 201);
    }

    public function show(Activity $activity): JsonResponse
    {
        return response()->json(['data' => $activity]);
    }

    public function update(UpdateActivityRequest $request, Activity $activity): JsonResponse
    {
        $activity->update($request->validated());

        return response()->json([
            'message' => 'Actividad actualizada correctamente.',
            'data' => $activity->fresh(),
        ]);
    }

    public function destroy(Activity $activity): JsonResponse
    {
        $activity->delete();

        return response()->json([
            'message' => 'Actividad eliminada correctamente.',
        ]);
    }
}
