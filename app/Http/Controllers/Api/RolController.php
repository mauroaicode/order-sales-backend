<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolController extends Controller
{
    public function getRoles(): \Illuminate\Http\JsonResponse
    {
        $roles = Role::all();
        return response()->json([
            'success' => true,
            'message' => 'Get Roles',
            'response' => 'get_roles',
            'total' => $roles->count(),
            'data' => $roles,
        ], 200);
    }
}
