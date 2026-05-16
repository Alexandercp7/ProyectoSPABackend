<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function showByToken(string $token)
    {
        $survey = Survey::where('token', $token)->with('workOrder')->firstOrFail();
        return response()->json(['data' => $survey]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'work_order_id'        => 'required|exists:work_orders,id',
            'cliente'              => 'required|string',
            'satisfaccion_general' => 'required|integer|min:1|max:5',
            'calidad_trabajo'      => 'required|integer|min:1|max:5',
            'trato_recibido'       => 'required|integer|min:1|max:5',
            'recomendacion'        => 'required|boolean',
            'comentarios'          => 'nullable|string',
        ]);

        $survey = Survey::create(array_merge($request->validated(), [
            'token' => Str::uuid(),
            'fecha' => now()->toDateString(),
        ]));

        return response()->json(['data' => $survey], 201);
    }
}
