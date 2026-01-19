<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        // 1. Pastikan SEMUA Role ada
        $roles = ['super_admin', 'admin', 'user'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $superAdminUsername = 'user';

        // 2. Cari atau Buat Super Admin
        $user = User::firstOrCreate(
            ['username' => $superAdminUsername],
            [
                'name' => 'user',
                'telegram_username' => 'user',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'status' => 'approved',
            ]
        );

        // 3. Sync Role untuk Super Admin
        if (!$user->hasRole('user')) {
            $user->assignRole('user');
            $this->command->info("✅ Role 'user' berhasil di-assign ke user '$superAdminUsername'.");
        }

        // 4. FIX: Sync role untuk semua user lain yang mungkin kehilangan role
        $allUsers = User::all();
        foreach ($allUsers as $u) {
            if (!empty($u->role) && in_array($u->role, $roles)) {
                if (!$u->hasRole($u->role)) {
                    $u->assignRole($u->role);
                    $this->command->info("✅ Fixed: Role '{$u->role}' assigned to user '{$u->username}'");
                }
            }
        }
    }
}
