<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\EditUserRequest;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UsersResources;


class UserController extends Controller
{

    /*=============================================
    OBTENER TODOS LOS USUARIOS
    =============================================*/
    public function getUsers(): \Illuminate\Http\JsonResponse
    {
        $users = UsersResources::collection(
            User::with('roles')->latest()->get()
        );
        return response()->json([
            'success' => true,
            'message' => 'Get Users',
            'response' => 'get_users',
            'total' => $users->count(),
            'data' => $users,
        ], 200);
    }

    /*=============================================
    AGREGAR USUARIO
    =============================================*/
    public function addUser(UserRequest $request): \Illuminate\Http\JsonResponse
    {

        DB::beginTransaction(); //Inicializamos la transacción
        try {
            $user = User::create([
                'name' => ucwords($request['name']),
                'email' => $request['email'],
                'password' => bcrypt('password'),
                'slug' => Str::slug(strtolower($request['name']) . '-' . Str::random(10), '-'),
            ]); // Creamos el usuario
            $user->roles()->attach($request['roleId']); //Asignamos los roles

            DB::commit(); // Si todo sale bien realizamos el commit o transacción a la base de datos
            return response()->json([
                'success' => true,
                'message' => 'Add User',
                'response' => 'add_user',
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
            Log::error('LOG ERROR ADD USER.', $response); // Guardamos el error en el archivo de logs
            DB::rollBack();
            return response()->json($response, 500);
        }
    }

    /*=============================================
    EDITAR USUARIO
    =============================================*/
    public function editUser(EditUserRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction(); //Inicializamos la transacción
        try {
            $user = User::find($id);
            if ($request['name'] !== $user->name) {
                $user->slug = Str::slug($request['name'] . '-' . Str::random(10), '-');
            }
            $user->name = ucwords($request['name']);
            $user->email = $request['email'];
            $user->syncRoles($request['roleId']);
            $user->save();

            DB::commit(); // Si todo sale bien realizamos el commit o transacción a la base de datos
            return response()->json([
                'success' => true,
                'message' => 'Edit User',
                'response' => 'edit_user',
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
            Log::error('LOG ERROR EDIT USER.', $response); // Guardamos el error en el archivo de logs
            DB::rollBack();
            return response()->json($response, 500);
        }
    }

    /*=============================================
         ELIMINAR USUARIO
       =============================================*/
    public function deleteUser($id)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id', $id)->with('roles')->first();

            if ($user->roles[0]->name === 'Customer') {
                $customer = Customer::where('user_id', $user->id);
                $customer->delete();
            }
            $user->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Delete User',
                'response' => 'delete_User',

            ], 200);
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => 'Transaction Error',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ];
            Log::error('LOG ERROR DELETE USER.', $response); // Guardamos el error en el archivo de logs
            DB::rollBack();
            return response()->json($response, 500);
        }
    }
}
