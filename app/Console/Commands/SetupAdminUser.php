<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SetupAdminUser extends Command
{
    protected $signature = 'admin:setup {email=seturry@gmail.com} {password=123456}';

    protected $description = 'Setup admin user with is_admin flag';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info('Starting admin user setup...');
        $this->newLine();

        // Check database connection
        try {
            $this->info('Checking database connection...');
            \DB::connection()->getPdo();
            $this->info('✓ Database connection successful.');
            $this->newLine();
        } catch (\Exception $e) {
            $this->error('✗ Database connection failed!');
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            $this->line('Please ensure:');
            $this->line('1. MySQL server is running');
            $this->line('2. Database credentials in .env are correct');
            $this->line('3. Database exists');
            return Command::FAILURE;
        }

        // Check if is_admin column exists
        $this->info('Checking if is_admin column exists...');
        if (!Schema::hasColumn('users', 'is_admin')) {
            $this->warn('is_admin column not found. Please run migration first:');
            $this->line('  php artisan migrate');
            $this->newLine();
            $this->info('Or the migration will be run automatically...');
            
            try {
                Schema::table('users', function ($table) {
                    $table->boolean('is_admin')->default(false)->after('password');
                });
                $this->info('✓ is_admin column added successfully.');
            } catch (\Exception $e) {
                $this->error('✗ Failed to add is_admin column: ' . $e->getMessage());
                return Command::FAILURE;
            }
        } else {
            $this->info('✓ is_admin column already exists.');
        }
        $this->newLine();

        // Create or update admin user
        $user = User::where('email', $email)->first();

        if ($user) {
            $this->info("User found. Updating...");
            $user->password = $password;
            $user->is_admin = true;
            $user->save();
            $this->info('✓ User updated successfully.');
        } else {
            $this->info('Creating new user...');
            try {
                User::create([
                    'name' => 'Admin',
                    'email' => $email,
                    'password' => $password,
                    'is_admin' => true,
                ]);
                $this->info('✓ New user created successfully.');
            } catch (\Exception $e) {
                $this->error('✗ Failed to create user: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        $this->newLine();
        $this->line('========================================');
        $this->info('Setup completed!');
        $this->line('========================================');
        $this->line("Email: {$email}");
        $this->line("Password: {$password}");
        $this->line('========================================');

        return Command::SUCCESS;
    }
}
