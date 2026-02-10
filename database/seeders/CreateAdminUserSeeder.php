<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CreateAdminUserSeeder extends Seeder
{
    public function run()
    {
        $email = 'seturry@gmail.com';
        $password = '123456';

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'password' => $password,
                'is_admin' => true,
            ]);
            $this->command->info("User updated: {$email}");
        } else {
            User::create([
                'name' => 'Admin',
                'email' => $email,
                'password' => $password,
                'is_admin' => true,
            ]);
            $this->command->info("New user created: {$email}");
        }

        $this->command->info("Password: {$password}");
    }
}
