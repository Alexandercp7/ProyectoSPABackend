<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return EmployeeResource::collection(Employee::with('user')->get());
    }

    public function store(StoreEmployeeRequest $request)
    {
        $employee = Employee::create($request->validated());
        return response()->json(['data' => new EmployeeResource($employee)], 201);
    }

    public function show(int $id)
    {
        return response()->json(['data' => new EmployeeResource(Employee::findOrFail($id))]);
    }

    public function update(Request $request, int $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->only(['nombre','apellido','telefono','role','foto_url','activo','user_id']));
        return response()->json(['data' => new EmployeeResource($employee)]);
    }

    public function destroy(int $id)
    {
        Employee::findOrFail($id)->delete();
        return response()->json(['message' => 'Empleado eliminado.']);
    }
}
