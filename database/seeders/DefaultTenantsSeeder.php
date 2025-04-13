<?php

namespace Database\Seeders;

use App\Models\Tenants\AccountConfiguration;
use App\Models\Tenants\GeneralConfiguration;
use App\Models\Tenants\Role;
use App\Models\Tenants\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultTenantsSeeder extends Seeder
{
    public function run()
    {
        // $this->createDefaultUsers();
        // $this->createMoreData();
        $this->schoolData();
    }

    protected function createDefaultUsers()
    {
        /* if (!User::where('email', 'super@laravel.io')->exists()) {
            $admin = User::create([
                'email' => 'super@laravel.io',
                'name' => 'Super Admin',
                'active' => true,
                'password' => '123456',
            ]);

            $admin->syncRoles([Role::ROLE_SUPER_ADMIN]);
        } */

        $admin = User::find(1);
        $admin->syncRoles([Role::ROLE_SUPER_ADMIN]);

        if (! User::where('email', 'admin@laravel.io')->exists()) {
            $user = User::create([
                'email' => 'admin@laravel.io',
                'name' => 'Admin',
                'active' => true,
                'password' => '123456',
            ]);

            $user->syncRoles([Role::ROLE_ADMIN]);
        }
    }

    protected function createMoreData()
    {
        User::factory(8)->create()->each(
            function ($user) {
                $user->assignRole(Role::ROLE_USER);
            }
        );

        GeneralConfiguration::create([
            'name' => 'Nombre Empresa',
            'address' => 'Dirección Base',
            'email' => 'hola@hola.com',
            'phone' => '987654321',
        ]);

        AccountConfiguration::create([
            'country' => 'PE',
            'timezone' => 'America/Lima',
            'city' => 'Piura',
            'language' => 'es',
            'user_id' => 1,
        ]);
    }

    protected function schoolData()
    {
        $incidents = [
            'EL ALUMNO(A) NO TRAJO MATERIAL',
            'EL ALUMNO(A) NO TRABAJA EN CLASE',
            'EL ALUMNO(A) NO HACE TAREAS',
            'EL ALUMNO(A) LLEGA TARDE',
            'EL ALUMNO(A) NO CUMPLE CON LOS MATERIALES',
            'EL ALUMNO(A) DISCUTE EN EL SALON DE CLASES',
            'EL ALUMNO(A) NO OBEDECE A LAS INSTRUCCIONES DEL PROFESOR',
            'EL ALUMNO(A) SALIO DEL SALON SIN AUTORIZACION',
            'EL ALUMNO(A) NO TRAE BATA DE LABORATORIO',
            'EL ALUMNO(A) NO TRAE EL UNIFORME COMPLETO',
            'EL ALUMNO(A) UTILIZA PALABRAS ANTISONANTES EN EL AULA',
            'EL ALUMNO(A) GOLPEA A SUS COMPAÑEROS',
            'EL ALUMNO(A) COME EN CLASE',
            'EL ALUMNO(A) NO RESPETA AL PERSONAL DOCENTE',
        ];

        foreach ($incidents as $value) {
            DB::table('incidents')->insert(['description' => $value]);
        }

        $specialties = [
            'SUB. ACADEMICA',
            'DPTO. DE ORIENTACION',
            'ESPAÑOL',
            'MATEMATICAS',
            'HISTORIA',
            'GEOGRAFIA',
            'BIOLOGIA',
            'FISICA',
            'INGLES',
            'FORMACION CIVICA',
            'MUSICA',
            'TEATRO',
            'ARTES VISUALES',
        ];

        foreach ($specialties as $value) {
            DB::table('specialties')->insert(['description' => $value]);
        }

        $teachers = [
            'VICTOR MANUEL GARCIA ORDAZ',
            'BENITEZ ORTIZ JUANA',
            'ISLAS JIMENEZ MAGALI',
            'MEJIA GARCIA NAYELY SUGEY',
            'MONTERO REYES ELIZABETH',
            'BUENDIA GONZALEZ IVAN HECTOR',
            'GUTIERREZ NORMAN JOSE ANTONIO',
            'RODRIGUEZ JIMENEZ LEONARDO',
            'SERRANO CASTRO VIVIANA',
            'BENITEZ POLANCO ADRIANA GEORGINA',
            'SANCHEZ ALMARAZ IRENE',
            'CIPRIANO REYES BESALEL DAFNE',
            'TREJO CASTAÑEDA ALEJANDRA BRISEIDA',
            'VAZQUEZ RAMIREZ GUADALUPE',
            'ESPINOSA ROMERO MARIA MARTHA',
            'GODINEZ REYES ZAFIRO BIBIANA',
            'GONZALEZ MUÑOZ LUISA SILVIA',
            'PEREZ MAZA FRANCISCO',
            'MARQUEZ GARCIA PERLA SUSANA',
            'MARTINEZ CANO JOCELYN',
            'GARCIA LUGO MARGARITA',
            'GUZMAN RODRIGUEZ MARINA',
            'VILLARREAL LINARES JUDITH',
            'DE LA LONGA CABRERA LUZ AUDELLI',
            'WALLES TORRES SOLEDAD IRMA',
            'JIMENEZ HERRERA IRMA ',
            'ROJAS JACINTO LETICIA',
            'SOTO MUÑOZ VANESSA SASHENKA',
            'ESCALONA MENDEZ MARIA ANTONIETA',
        ];

        foreach ($teachers as $value) {
            DB::table('teachers')->insert([
                'name' => $value,
                'specialty_id' => rand(1, 13),
            ]);
        }
    }
}
