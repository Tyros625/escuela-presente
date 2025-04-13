<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class TenantsSeeder extends Seeder
{
    public function run()
    {
        $client = new ClientRepository;
        $client->createPasswordGrantClient(null, 'Default password grant client', 'http://your.redirect.path');
        $client->createPersonalAccessClient(null, 'Default personal access client', 'http://your.redirect.path');

        $this->call(RolesSeeder::class);
        $this->call(DefaultTenantsSeeder::class);
    }
}
