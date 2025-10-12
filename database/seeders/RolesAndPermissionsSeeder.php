<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 🎧 Define permissions
        $permissions = [
            'view podcasts', 'create podcasts', 'edit podcasts', 'delete podcasts',
            'view episodes', 'create episodes', 'edit episodes', 'delete episodes',
            'view releases', 'create releases', 'edit releases', 'delete releases',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 👑 Define roles
        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $uploader = Role::firstOrCreate(['name' => 'uploader']);
        $editor  = Role::firstOrCreate(['name' => 'editor']);
        $viewer  = Role::firstOrCreate(['name' => 'viewer']);

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        $uploader->givePermissionTo(['create podcasts', 'create episodes', 'create releases']);
        $editor->givePermissionTo(['edit podcasts', 'edit episodes', 'edit releases']);
        $viewer->givePermissionTo(['view podcasts', 'view episodes', 'view releases']);
    }
}
