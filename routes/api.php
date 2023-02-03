<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\RolController;
use \App\Http\Controllers\Api\UserController;
use \App\Http\Controllers\Api\OrderController;
use \App\Http\Controllers\Auth\LoginController;
use \App\Http\Controllers\Api\ProductController;

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
        /*Agregar Rol*/
        Route::post('add-role', [RolController::class, 'addRole'])->name('add.role');
        /*Editar Rol*/
        Route::post('edit-role/{id}', [RolController::class, 'editRole'])->name('edit.role');
        /*Eliminar Rol*/
        Route::post('delete-role/{id}', [RolController::class, 'deleteRole'])->name('delete.role');
    });

    /*=============================================
       RUTAS PARA LOS PRODUCTOS
    =============================================*/
    /*Obtener los productos*/
    Route::get('get-products', [ProductController::class, 'getProducts'])->name('get.products');
    /*Agregar Producto*/
    Route::post('add-product', [ProductController::class, 'addProduct'])->middleware('product.permissions')->name('add.product');
    /*Editar Producto*/
    Route::post('edit-product/{id}', [ProductController::class, 'editProduct'])->middleware('product.permissions')->name('edit.product');
    /*Eliminar Producto*/
    Route::post('delete-product/{id}', [ProductController::class, 'deleteProduct'])->middleware('product.permissions')->name('deleter.product');
    /*=============================================
       RUTAS PARA LAS ORDENES DE COMPRA
    =============================================*/
    /* Crear Orden de Compra*/
    Route::post('create-order', [OrderController::class, 'createOrder'])->name('create.order');
    /* Obtener todas las ordenes de compra*/
    Route::get('get-orders', [OrderController::class, 'getOrders'])->middleware('order.permissions')->name('get.orders.customer');
    /* Obtener las Ordene de Compra por Cliente*/
    Route::get('get-orders/customer/{id}', [OrderController::class, 'getOrdersCustomer'])->name('get.orders.customer');

});
/*Subir imagen del producto*/
Route::post('upload-picture/{id}', [ProductController::class, 'uploadPicture'])->name('api.upload.picture');
