<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::with(['asignadoA','comments.user'])
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->when($request->asignado_a_id, fn($q) => $q->where('asignado_a_id', $request->asignado_a_id))
            ->orderByDesc('created_at')
            ->paginate(30);
        return ActivityResource::collection($activities);
    }

    public function store(StoreActivityRequest $request)
    {
        $activity = Activity::create(array_merge($request->validated(), ['created_by' => $request->user()->id]));
        return response()->json(['data' => new ActivityResource($activity->load('asignadoA'))], 201);
    }

    public function show(int $id)
    {
        $activity = Activity::with(['asignadoA','comments.user'])->findOrFail($id);
        return response()->json(['data' => new ActivityResource($activity)]);
    }

    public function update(UpdateActivityRequest $request, int $id)
    {
        $activity = Activity::findOrFail($id);
        $activity->update($request->validated());
        return response()->json(['data' => new ActivityResource($activity)]);
    }

    public function destroy(int $id)
    {
        Activity::findOrFail($id)->delete();
        return response()->json(['message' => 'Actividad eliminada.']);
    }

    public function addComment(Request $request, int $id)
    {
        $request->validate(['texto' => 'required|string']);
        $activity = Activity::findOrFail($id);
        $comment  = $activity->comments()->create([
            'usuario_id' => $request->user()->id,
            'texto'      => $request->texto,
        ]);
        return response()->json(['data' => $comment->load('user')], 201);
    }
}
