<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }
        $users = User::orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function setRole(Request $request, User $user)
    {
        $me = $request->user();
        if (!$me || !$me->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $data = $request->validate(['role' => 'required|string']);

        // roles permitidos
        $allowed = ['user', 'admin', 'client'];
        if (!in_array($data['role'], $allowed, true)) {
            return response()->json(['message' => 'Rol inválido', 'allowed' => $allowed], 422);
        }

        try {
            $user->role = $data['role'];
            $user->save();
        } catch (\Exception $ex) {
            return response()->json(['message' => 'Error al guardar rol', 'error' => $ex->getMessage()], 500);
        }

        return response()->json(['message' => 'Role actualizado', 'user' => $user]);
    }
}
