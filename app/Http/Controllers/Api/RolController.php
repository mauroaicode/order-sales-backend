<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Requests\RoleRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\RolesResources;

use App\Http\Requests\EditRoleRequest;

class RolController extends Controller
{
    /*=============================================
    OBTENER TODOS LOS ROLES
    =============================================*/
    public function getRoles(): \Illuminate\Http\JsonResponse
    {
        $roles = RolesResources::collection( Role::all());
        if (count($roles) === 0) return response()->json(['No hay roles registrados.'], 401);
        return response()->json([
            'success' => true,
            'message' => 'Get Roles',
            'response' => 'get_roles',
            'total' => $roles->count(),
            'data' => $roles,
        ], 200);
    }
    /*=============================================
    AGREGAR ROL
    =============================================*/
    public function addRole(RoleRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction(); //Inicializamos la transacción
        try {
            $user = Role::create([
                'name' => ucwords($request['name']),
            ]); // Creamos el rol

            DB::commit(); // Si todo sale bien realizamos el commit o transacción a la base de datos
            return response()->json([
                'success' => true,
                'message' => 'Add Role',
                'response' => 'add_role',
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            /* Si sale un error realizamos un rollback a la base de datos, mostramos el error en el en archivo Log*/
            $response = [
                'success' => false,
                'message' => 'Transaction Error',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ];
            Log::error('LOG ERROR ADD ROLE.', $response); // Guardamos el error en el archivo de logs
            DB::rollBack();
            return response()->json($response, 500);
        }
    }
    /*=============================================
    EDITAR ROL
    =============================================*/
    public function editRole(EditRoleRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction(); //Inicializamos la transacción
        try {
            $role = Role::find($id);
            /* Validamos que existe el rol en la base de datos*/
            if(!$role) return response()->json(['El rol que quieres editar no existe.'], 404);
            $role->name = ucwords($request['name']);
            $role->save();

            DB::commit(); // Si todo sale bien realizamos el commit o transacción a la base de datos
            return response()->json([
                'success' => true,
                'message' => 'Edit Role',
                'response' => 'edit_role',
                'data' => $role
            ], 200);
        } catch (\Throwable $th) {
            /* Si sale un error realizamos un rollback a la base de datos, mostramos el error en el en archivo Log*/
            $response = [
                'success' => false,
                'message' => 'Transaction Error',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ];
            Log::error('LOG ERROR EDIT ROLE.', $response); // Guardamos el error en el archivo de logs
            DB::rollBack();
            return response()->json($response, 500);
        }
    }
    /*=============================================
        ELIMINAR ROL
      =============================================*/
    public function deleteRole($id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $role = Role::find($id);
            /* Validamos que existe el rol en la base de datos*/
            if(!$role) return response()->json(['El rol que quieres eliminar no existe.'], 404);
            $users = User::role($role)->get();
            /* Validamos que el rol no tenga usuarios asociados */
            if(count($users) > 0) return response()->json(['No puedes eliminar este rol porque tiene usuarios asociados.'], 400);
            /* Si existe el rol, lo eliminamos de la base de datos*/
            $role->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Delete Role',
                'response' => 'delete_role',

            ], 200);
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => 'Transaction Error',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ];
            Log::error('LOG ERROR DELETE ROLE.', $response); // Guardamos el error en el archivo de logs
            DB::rollBack();
            return response()->json($response, 500);
        }
    }
}
