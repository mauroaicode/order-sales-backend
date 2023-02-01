<?php

namespace Database\Seeders;

use App\Models\Customer;
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
        $administrator = Role::create(['name' => 'Admin']);
        $customer = Role::create(['name' => 'Customer']);
        $employee = Role::create(['name' => 'Employee']);

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

    }
}
