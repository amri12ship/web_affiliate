<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * The email of the legacy default admin account.
     */
    public const DEFAULT_EMAIL = 'admin@affiliate.test';

    /**
     * Seed the development admin account.
     */
    public function run(): void
    {
        $email = config('admin.email');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('admin.name'),
                'password' => Hash::make(config('admin.password')),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        // Remove the insecure default account once a custom admin email is configured.
        if ($email !== self::DEFAULT_EMAIL) {
            User::where('email', self::DEFAULT_EMAIL)->delete();
        }
    }
}
