<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $perms = [
            'work-orders.view','work-orders.create','work-orders.edit','work-orders.delete',
            'clients.view','clients.create','clients.edit','clients.delete',
            'inventory.view','inventory.create','inventory.edit',
            'finance.view','finance.create','finance.edit',
            'employees.view','employees.manage',
            'settings.manage',
        ];
        foreach ($perms as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $admin   = Role::firstOrCreate(['name' => 'admin',         'guard_name' => 'web']);
        $tecnico = Role::firstOrCreate(['name' => 'tecnico',       'guard_name' => 'web']);
        $recep   = Role::firstOrCreate(['name' => 'recepcionista', 'guard_name' => 'web']);
        $gerente = Role::firstOrCreate(['name' => 'gerente',       'guard_name' => 'web']);

        $admin->syncPermissions($perms);
        $tecnico->syncPermissions(['work-orders.view','work-orders.edit','inventory.view']);
        $recep->syncPermissions(['work-orders.view','work-orders.create','clients.view','clients.create']);
        $gerente->syncPermissions(['work-orders.view','clients.view','finance.view','employees.view']);

        $users = [
            ['email' => 'admin@spa.com',         'name' => 'Admin',         'role' => 'admin'],
            ['email' => 'tecnico@spa.com',        'name' => 'Técnico Demo',  'role' => 'tecnico'],
            ['email' => 'recepcion@spa.com',      'name' => 'Recepción Demo','role' => 'recepcionista'],
            ['email' => 'gerente@spa.com',        'name' => 'Gerente Demo',  'role' => 'gerente'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => bcrypt('password')]
            );
            if (!$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
            $this->command->info("{$data['role']}: {$data['email']} / password");
        }
    }
}
