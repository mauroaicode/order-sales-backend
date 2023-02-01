<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\RolController;
use \App\Http\Controllers\Api\UserController;
use \App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['guest:api']], function () {
    Route::post('login', [LoginController::class, 'login']);
});

Route::group(['middleware' => ['auth:api']], function () {
    /*Cerrar sesión*/
    Route::post('logout', [LoginController::class, 'logout']);
    /*Obtener el usuario autenticado*/
    Route::get('user', [LoginController::class, 'user']);


    /*=============================================
    RUTAS PARA EL ADMINISTRADOR
    =============================================*/
    Route::group(['middleware' => ['admin.permissions']], function () {

        /*=============================================
         RUTAS USUARIOS
        =============================================*/
        /*Obtener la lista de usuarios*/
        Route::get('get-users', [UserController::class, 'getUsers'])->name('get.users');
        /*Agregar Usuario*/
        Route::post('add-user', [UserController::class, 'addUser'])->name('add.user');
        /*Editar Usuario*/
        Route::post('edit-user/{id}', [UserController::class, 'editUser'])->name('edit.user');
        /*Eliminar Usuario*/
        Route::post('delete-user/{id}', [UserController::class, 'deleteUser'])->name('delete.user');


        /*=============================================
        RUTAS ROLES
        =============================================*/
        /*Obtener la lista de roles*/
        Route::get('get-roles', [RolController::class, 'getRoles'])->name('get.roles');
    });

});
