<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_activity","view_any_activity","create_activity","update_activity","restore_activity","restore_any_activity","replicate_activity","reorder_activity","delete_activity","delete_any_activity","force_delete_activity","force_delete_any_activity","view_exception::log","view_any_exception::log","create_exception::log","update_exception::log","restore_exception::log","restore_any_exception::log","replicate_exception::log","reorder_exception::log","delete_exception::log","delete_any_exception::log","force_delete_exception::log","force_delete_any_exception::log","view_media","view_any_media","create_media","update_media","restore_media","restore_any_media","replicate_media","reorder_media","delete_media","delete_any_media","force_delete_media","force_delete_any_media","view_project","view_any_project","create_project","update_project","restore_project","restore_any_project","replicate_project","reorder_project","delete_project","delete_any_project","force_delete_project","force_delete_any_project","view_project::status","view_any_project::status","create_project::status","update_project::status","restore_project::status","restore_any_project::status","replicate_project::status","reorder_project::status","delete_project::status","delete_any_project::status","force_delete_project::status","force_delete_any_project::status","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_site","view_any_site","create_site","update_site","restore_site","restore_any_site","replicate_site","reorder_site","delete_site","delete_any_site","force_delete_site","force_delete_any_site","view_ticket","view_any_ticket","create_ticket","update_ticket","restore_ticket","restore_any_ticket","replicate_ticket","reorder_ticket","delete_ticket","delete_any_ticket","force_delete_ticket","force_delete_any_ticket","view_ticket::priority","view_any_ticket::priority","create_ticket::priority","update_ticket::priority","restore_ticket::priority","restore_any_ticket::priority","replicate_ticket::priority","reorder_ticket::priority","delete_ticket::priority","delete_any_ticket::priority","force_delete_ticket::priority","force_delete_any_ticket::priority","view_ticket::status","view_any_ticket::status","create_ticket::status","update_ticket::status","restore_ticket::status","restore_any_ticket::status","replicate_ticket::status","reorder_ticket::status","delete_ticket::status","delete_any_ticket::status","force_delete_ticket::status","force_delete_any_ticket::status","view_ticket::type","view_any_ticket::type","create_ticket::type","update_ticket::type","restore_ticket::type","restore_any_ticket::type","replicate_ticket::type","reorder_ticket::type","delete_ticket::type","delete_any_ticket::type","force_delete_ticket::type","force_delete_any_ticket::type","view_timesheet","view_any_timesheet","create_timesheet","update_timesheet","restore_timesheet","restore_any_timesheet","replicate_timesheet","reorder_timesheet","delete_timesheet","delete_any_timesheet","force_delete_timesheet","force_delete_any_timesheet","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","page_Board","page_HealthCheckResults","page_Kanban","page_RoadMap","page_Scrum","page_MyProfilePage","page_Logs","widget_SiteStatsWidget","widget_FavoriteProjects","widget_TicketsByType","widget_OverlookWidget","widget_TicketsByPriority","widget_TicketTimeLogged","widget_UserTimeLogged","widget_LatestTickets","widget_LatestProjects","widget_LatestComments","widget_LatestActivities"]},{"name":"panel_user","guard_name":"web","permissions":[]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}