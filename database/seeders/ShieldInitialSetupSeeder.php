<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ShieldInitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdminEmail = env('SUPER_ADMIN_EMAIL');

        if (! $superAdminEmail) {
            $this->command?->warn('SUPER_ADMIN_EMAIL no está definido. Se omite ShieldInitialSetupSeeder.');

            return;
        }

        $superAdminRoleName = (string) config('filament-shield.super_admin.name', 'super_admin');

        $role = Role::firstOrCreate([
            'name' => $superAdminRoleName,
            'guard_name' => 'web',
        ]);

        $permissionCount = Permission::query()->count();

        if ($permissionCount > 0) {
            $role->syncPermissions(Permission::all());
        } else {
            $this->command?->warn('No hay permisos aún. Ejecuta primero la generación de permisos de Filament Shield y vuelve a correr el seeder.');
        }

        $user = User::query()->where('email', $superAdminEmail)->first();

        if (! $user) {
            $password = env('SUPER_ADMIN_PASSWORD');

            if (! $password) {
                $this->command?->warn('No existe el usuario super admin y SUPER_ADMIN_PASSWORD está vacío. No se creará el usuario.');

                return;
            }

            $user = User::query()->create([
                'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'email' => $superAdminEmail,
                'password' => Hash::make($password),
                'status' => UserStatus::ACTIVE,
            ]);

            $this->command?->info("Usuario creado: {$superAdminEmail}");
        }

        $user->syncRoles([$role]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info("Rol '{$superAdminRoleName}' asignado a {$superAdminEmail}");
    }
}
