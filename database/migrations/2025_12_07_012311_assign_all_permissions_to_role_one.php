<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear rol "Super Admin" si no existe
        $role = Role::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'super_admin',
                'guard_name' => 'web',
            ]
        );

        // Obtener todos los permisos existentes
        $permissions = Permission::all();

        // Asignar todos los permisos al rol
        if ($permissions->isNotEmpty()) {
            $role->syncPermissions($permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover todos los permisos del rol con id 1
        $role = Role::find(1);

        if ($role) {
            $role->syncPermissions([]);
        }
    }
};
