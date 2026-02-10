<?php

/**
 * Admin User Setup Script
 * 
 * This script performs the following:
 * 1. Adds is_admin column to users table if it doesn't exist
 * 2. Creates or updates seturry@gmail.com user
 * 
 * Usage:
 * php setup_admin.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

try {
    echo "Starting admin user setup...\n\n";

    // Check database connection first
    echo "Checking database connection...\n";
    try {
        DB::connection()->getPdo();
        echo "✓ Database connection successful.\n\n";
    } catch (\Exception $e) {
        echo "✗ Database connection failed!\n";
        echo "Error: " . $e->getMessage() . "\n";
        echo "\nPlease ensure:\n";
        echo "1. MySQL server is running\n";
        echo "2. Database credentials in .env are correct\n";
        echo "3. Database 'school' exists\n\n";
        exit(1);
    }

    // 1. Check if is_admin column exists, add if not
    echo "Checking if is_admin column exists...\n";
    if (!Schema::hasColumn('users', 'is_admin')) {
        echo "Adding is_admin column to users table...\n";
        Schema::table('users', function ($table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });
        echo "✓ is_admin column added successfully.\n\n";
    } else {
        echo "✓ is_admin column already exists.\n\n";
    }

    // 2. Create or update admin user
    $email = 'seturry@gmail.com';
    $password = '123456';
    
    $user = User::where('email', $email)->first();
    
    if ($user) {
        echo "User found. Updating...\n";
        $user->password = $password;
        $user->is_admin = true;
        $user->save();
        echo "✓ User updated successfully.\n\n";
    } else {
        echo "Creating new user...\n";
        User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => $password,
            'is_admin' => true,
        ]);
        echo "✓ New user created successfully.\n\n";
    }

    echo "========================================\n";
    echo "Setup completed!\n";
    echo "========================================\n";
    echo "Email: {$email}\n";
    echo "Password: {$password}\n";
    echo "========================================\n";
    
} catch (\Illuminate\Database\QueryException $e) {
    echo "\n✗ Database error occurred!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "1. MySQL server is running\n";
    echo "2. Database connection settings in .env file\n";
    echo "3. Database 'school' exists\n";
    exit(1);
} catch (\Exception $e) {
    echo "\n✗ Error occurred: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), 'Connection') !== false || strpos($e->getMessage(), 'refused') !== false) {
        echo "\nThis appears to be a database connection issue.\n";
        echo "Please ensure MySQL server is running.\n";
    }
    exit(1);
}
