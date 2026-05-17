<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get()->map(fn($user) => [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->getRoleNames()->first() ?? '',
        ]);

        return response()->json(['data' => $users]);
    }

    public function roles()
    {
        $roles = Role::orderBy('name')->pluck('name');
        return response()->json(['data' => $roles]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'data' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $request->role,
            ],
        ], 201);
    }

    public function destroy(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }
}
