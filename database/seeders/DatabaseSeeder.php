<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /*=============================================
             CREAMOS LOS ROLES
         =============================================*/
        $administrator = Role::create(['name' => 'Administrador']);
        $customer = Role::create(['name' => 'Cliente']);
        $employee = Role::create(['name' => 'Empleado']);

        /*=============================================
           CREAMOS UN USUARIO ADMINISTRADOR
       =============================================*/
        User::factory()->count(1)->create([
            'name' => 'Mauricio Gutierrez',
            'email' => 'admin@admin.com',
            'slug' => Str::slug(strtolower('Mauricio') . '-' . Str::random(10), '-'),
        ])->each(function (User $user) use ($administrator) {
            $user->roles()->attach($administrator['id']); // Asignamos el rol administrador al usuario
        });

        /*=============================================
           CREAMOS DIEZ CLIENTES
       =============================================*/
        User::factory()->count(10)->create([
        ])->each(function (User $u) use ($customer) {
            Customer::factory()->count(1)->create([
                'user_id' => $u->id
            ]);
            $u->roles()->attach($customer['id']); // Asignamos el rol cliente al usuario
        });

        /*=============================================
           CREAMOS 2 TRABAJADORES
       =============================================*/
        User::factory()->count(2)->create([
        ])->each(function (User $u) use ($employee) {
            $u->roles()->attach($employee['id']); // Asignamos el rol trabajador al usuario
        });

        /*=============================================
           CREAMOS 6 PRODUCTOS
       =============================================*/
        Product::factory()->count(1)->create([
            'product_name' => 'Producto 1',
            'picture' => '/assets/images/producto-1.jpg',
            'price' => 25000,
            'product_slug' => Str::slug(strtolower('Producto 1') . '-' . Str::random(10), '-')
        ]);
        Product::factory()->count(1)->create([
            'product_name' => 'Producto 2',
            'picture' => '/assets/images/producto-2.jpg',
            'price' => 45000,
            'product_slug' => Str::slug(strtolower('Producto 2') . '-' . Str::random(10), '-')
        ]);
        Product::factory()->count(1)->create([
            'product_name' => 'Producto 3',
            'picture' => '/assets/images/producto-3.jpg',
            'price' => 80000,
            'product_slug' => Str::slug(strtolower('Producto 3') . '-' . Str::random(10), '-')
        ]);
        Product::factory()->count(1)->create([
            'product_name' => 'Producto 4',
            'picture' => '/assets/images/producto-4.jpg',
            'price' => 55000,
            'product_slug' => Str::slug(strtolower('Producto 4') . '-' . Str::random(10), '-')
        ]);
        Product::factory()->count(1)->create([
            'product_name' => 'Producto 5',
            'picture' => '/assets/images/producto-5.jpg',
            'price' => 50000,
            'product_slug' => Str::slug(strtolower('Producto 5') . '-' . Str::random(10), '-')
        ]);
        Product::factory()->count(1)->create([
            'product_name' => 'Producto 6',
            'picture' => '/assets/images/producto-6.jpg',
            'price' => 75000,
            'product_slug' => Str::slug(strtolower('Producto 6') . '-' . Str::random(10), '-')
        ]);
    }
}
