<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Role;
use App\Models\Team;
use App\Models\Activity;
use Filament\Forms\Form;
use App\Models\TicketType;
use App\Models\ContractType;
use App\Models\TicketStatus;
use App\Models\InvoiceStatus;
use App\Models\ProjectStatus;
use App\Models\TicketPriority;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use BezhanSalleh\FilamentShield\Support\Utils;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register team';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->unique()->required(),
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $team = Team::create($data);
        $team->members()->attach(auth()->user());

        $this->initializeTeamData($team->id);

        return $team;
    }

    protected function initializeTeamData($teamId)
    {
        $superAdminRole = $this->rolesWithPermissions($teamId);
        $this->initializeData($teamId, TicketType::class, 'ticketType');
        $this->initializeData($teamId, TicketPriority::class, 'ticketPriority');
        $this->initializeData($teamId, TicketStatus::class, 'ticketStatus');
        $this->initializeData($teamId, Activity::class, 'activity');
        $this->initializeData($teamId, ProjectStatus::class, 'projectStatus');
        $this->initializeData($teamId, ContractType::class, 'contractType');
        $this->initializeData($teamId, InvoiceStatus::class, 'invoiceStatus');

        setPermissionsTeamId($teamId);

        if ($superAdminRole) {
            auth()->user()->assignRole($superAdminRole);
        }
    }

    protected function initializeData($teamId, $modelClass, $method)
    {
        $data = $this->{$method . 'Data'}();

        foreach ($data as $item) {
            $item['team_id'] = $teamId;
            $modelClass::firstOrCreate($item);
        }
    }

    protected function rolesWithPermissions($teamId): ?Role
    {
        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_activity","view_any_activity","create_activity","update_activity","restore_activity","restore_any_activity","replicate_activity","reorder_activity","delete_activity","delete_any_activity","force_delete_activity","force_delete_any_activity","view_contract::type","view_any_contract::type","create_contract::type","update_contract::type","restore_contract::type","restore_any_contract::type","replicate_contract::type","reorder_contract::type","delete_contract::type","delete_any_contract::type","force_delete_contract::type","force_delete_any_contract::type","view_exception::log","view_any_exception::log","create_exception::log","update_exception::log","restore_exception::log","restore_any_exception::log","replicate_exception::log","reorder_exception::log","delete_exception::log","delete_any_exception::log","force_delete_exception::log","force_delete_any_exception::log","view_invoice","view_any_invoice","create_invoice","update_invoice","restore_invoice","restore_any_invoice","replicate_invoice","reorder_invoice","delete_invoice","delete_any_invoice","force_delete_invoice","force_delete_any_invoice","view_invoice::status","view_any_invoice::status","create_invoice::status","update_invoice::status","restore_invoice::status","restore_any_invoice::status","replicate_invoice::status","reorder_invoice::status","delete_invoice::status","delete_any_invoice::status","force_delete_invoice::status","force_delete_any_invoice::status","view_media","view_any_media","create_media","update_media","restore_media","restore_any_media","replicate_media","reorder_media","delete_media","delete_any_media","force_delete_media","force_delete_any_media","view_project","view_any_project","create_project","update_project","restore_project","restore_any_project","replicate_project","reorder_project","delete_project","delete_any_project","force_delete_project","force_delete_any_project","view_project::status","view_any_project::status","create_project::status","update_project::status","restore_project::status","restore_any_project::status","replicate_project::status","reorder_project::status","delete_project::status","delete_any_project::status","force_delete_project::status","force_delete_any_project::status","view_shield::role","view_any_shield::role","create_shield::role","update_shield::role","delete_shield::role","delete_any_shield::role","view_site","view_any_site","create_site","update_site","restore_site","restore_any_site","replicate_site","reorder_site","delete_site","delete_any_site","force_delete_site","force_delete_any_site","view_ticket","view_any_ticket","create_ticket","update_ticket","restore_ticket","restore_any_ticket","replicate_ticket","reorder_ticket","delete_ticket","delete_any_ticket","force_delete_ticket","force_delete_any_ticket","view_ticket::priority","view_any_ticket::priority","create_ticket::priority","update_ticket::priority","restore_ticket::priority","restore_any_ticket::priority","replicate_ticket::priority","reorder_ticket::priority","delete_ticket::priority","delete_any_ticket::priority","force_delete_ticket::priority","force_delete_any_ticket::priority","view_ticket::status","view_any_ticket::status","create_ticket::status","update_ticket::status","restore_ticket::status","restore_any_ticket::status","replicate_ticket::status","reorder_ticket::status","delete_ticket::status","delete_any_ticket::status","force_delete_ticket::status","force_delete_any_ticket::status","view_ticket::type","view_any_ticket::type","create_ticket::type","update_ticket::type","restore_ticket::type","restore_any_ticket::type","replicate_ticket::type","reorder_ticket::type","delete_ticket::type","delete_any_ticket::type","force_delete_ticket::type","force_delete_any_ticket::type","view_timesheet","view_any_timesheet","create_timesheet","update_timesheet","restore_timesheet","restore_any_timesheet","replicate_timesheet","reorder_timesheet","delete_timesheet","delete_any_timesheet","force_delete_timesheet","force_delete_any_timesheet","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","page_Board","page_HealthCheckResults","page_Kanban","page_RoadMap","page_Scrum","page_ServerMonitoring","page_TeamSettings","page_MyProfilePage","page_Logs","widget_OverallWidget"]}]';
        if (!blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'team_id' => $teamId,
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (!blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                    return $role;
                }
            }
        }
    }

    protected function ticketTypeData()
    {
        return [
            [
                'name' => 'Task',
                'icon' => 'heroicon-o-document-check',
                'color' => '#1D4ED8',
                'is_default' => true
            ],
            [
                'name' => 'Evolution',
                'icon' => 'heroicon-o-arrow-trending-up',
                'color' => '#15803D',
                'is_default' => false
            ],
            [
                'name' => 'Bug',
                'icon' => 'heroicon-o-bug-ant',
                'color' => '#DC2626',
                'is_default' => false
            ],
            [
                'name' => 'Feature Request',
                'icon' => 'heroicon-o-light-bulb',
                'color' => '#FFD700',
                'is_default' => false
            ],
            [
                'name' => 'Enhancement',
                'icon' => 'heroicon-o-star',
                'color' => '#F59E0B',
                'is_default' => false
            ],
            [
                'name' => 'Support Request',
                'icon' => 'heroicon-o-lifebuoy',
                'color' => '#059669',
                'is_default' => false
            ],
            [
                'name' => 'Documentation',
                'icon' => 'heroicon-o-document-text',
                'color' => '#4B5563',
                'is_default' => false
            ],
            [
                'name' => 'Design Task',
                'icon' => 'heroicon-o-pencil-square',
                'color' => '#9333EA',
                'is_default' => false
            ],
        ];
    }

    protected function ticketPriorityData()
    {
        return [
            ['name' => 'Low', 'color' => '#16A34A', 'is_default' => false], // Green
            ['name' => 'Normal', 'color' => '#2563EB', 'is_default' => true], // Blue
            ['name' => 'High', 'color' => '#DC2626', 'is_default' => false], // Red
            ['name' => 'Urgent', 'color' => '#FFA500', 'is_default' => false], // Orange
        ];
    }

    protected function ticketStatusData()
    {
        return [
            [
                'name' => 'Todo',
                'color' => '#6E6E6E', // Gray
                'is_default' => true,
                'order' => 1
            ],
            [
                'name' => 'In Progress',
                'color' => '#FF7F00', // Orange
                'is_default' => false,
                'order' => 2
            ],
            [
                'name' => 'Done',
                'color' => '#008000', // Green
                'is_default' => false,
                'order' => 3
            ],
            [
                'name' => 'Archived',
                'color' => '#FF0000', // Red
                'is_default' => false,
                'order' => 4
            ],
            [
                'name' => 'Blocked',
                'color' => '#FFD700', // Gold
                'is_default' => false,
                'order' => 5
            ],
            [
                'name' => 'Review',
                'color' => '#4169E1', // Royal Blue
                'is_default' => false,
                'order' => 6
            ],
        ];
    }

    protected function activityData()
    {
        return [
            ['name' => 'Programming', 'description' => 'Coding, debugging, and software development.'],
            ['name' => 'Testing', 'description' => 'Executing test cases and ensuring software quality.'],
            ['name' => 'Learning', 'description' => 'Continuous education and skill development.'],
            ['name' => 'Research', 'description' => 'Gathering information to inform project decisions.'],
            ['name' => 'Migration', 'description' => 'Transferring data and applications to new environments.'],
            ['name' => 'Upgrade', 'description' => 'Improving existing systems with the latest technologies.'],
            ['name' => 'Backups', 'description' => 'Creating and managing data backups to prevent loss.'],
            ['name' => 'Enhancements', 'description' => 'Implementing additional features or improvements.'],
            ['name' => 'Patching', 'description' => 'Applying updates to address vulnerabilities or improve performance.'],
            ['name' => 'Security', 'description' => 'Implementing measures to protect systems and data.'],
            ['name' => 'Documentation', 'description' => 'Creating records and manuals detailing project processes.'],
            ['name' => 'Knowledge Transfer', 'description' => 'Sharing expertise and insights with team members.'],
            ['name' => 'User Feedback', 'description' => 'Collecting and incorporating user suggestions for system improvement.'],
            ['name' => 'Customization', 'description' => 'Adapting systems to meet specific user requirements.'],
            ['name' => 'Legal Guidance', 'description' => 'Seeking legal advice to ensure compliance and mitigate risks.'],
            ['name' => 'Other', 'description' => 'Miscellaneous activities not covered by specified categories.'],
        ];
    }

    protected function projectStatusData()
    {
        return [
            ['name' => 'Created', 'color' => '#3498db', 'is_default' => true], // Blue (Initiation)
            ['name' => 'In Progress', 'color' => '#2ecc71', 'is_default' => false], // Green (Active)
            ['name' => 'Archived', 'color' => '#f39c12', 'is_default' => false], // Orange (Not Active)
            ['name' => 'Finished', 'color' => '#27ae60', 'is_default' => false], // Green (Positive)
            ['name' => 'On Hold', 'color' => '#95a5a6', 'is_default' => false], // Grey (Paused)
            ['name' => 'Cancelled', 'color' => '#e74c3c', 'is_default' => false], // Red (Negative)
        ];
    }

    protected function contractTypeData()
    {
        return [
            [
                'name' => 'Hourly',
                'description' => 'Developer gets paid for hours worked, suitable for flexible project scopes.',
                'is_default' => true
            ],
            [
                'name' => 'Fixed',
                'description' => 'Developer agrees on a set price for the entire project with clear requirements.',
                'is_default' => false
            ],
            [
                'name' => 'Retainer',
                'description' => 'Client pays a regular fee for reserved developer hours in an ongoing relationship',
                'is_default' => false
            ],
            [
                'name' => 'Equity-based',
                'description' => 'Developer receives ownership shares in exchange for services, common in startups',
                'is_default' => false
            ],
            [
                'name' => 'Build-Operate-Transfer (BOT)',
                'description' => 'Developer builds and operates a project, transferring ownership to the client later',
                'is_default' => false
            ],
            [
                'name' => 'Cost-Plus',
                'description' => 'Client reimburses developer for costs plus an additional fee, good for transparency',
                'is_default' => false
            ],
            [
                'name' => 'Milestone-based',
                'description' => 'Payments tied to achieving project milestones, useful for larger projects',
                'is_default' => false
            ],
            [
                'name' => 'Subcontracting or Outsourcing',
                'description' => 'Developer outsources part of the project to another party with specialized skills',
                'is_default' => false
            ],
            [
                'name' => 'Non-Disclosure Agreement (NDA)',
                'description' => 'Legal contract ensuring confidentiality of project details',
                'is_default' => false
            ],
            [
                'name' => 'Joint Venture Agreement',
                'description' => 'Developers and clients form a partnership to share responsibilities and risks',
                'is_default' => false
            ],
        ];
    }

    protected function invoiceStatusData()
    {
        return [
            [
                'name' => 'Draft',
                'description' => 'Invoice is in draft status and not yet finalized.',
                'is_default' => true
            ],
            [
                'name' => 'Sent',
                'description' => 'Invoice has been sent to the client but not yet paid.',
                'is_default' => false
            ],
            [
                'name' => 'Paid',
                'description' => 'Invoice has been paid by the client.',
                'is_default' => false
            ],
            [
                'name' => 'Overdue',
                'description' => 'Invoice is past the due date and payment is overdue.',
                'is_default' => false
            ],
            [
                'name' => 'Void',
                'description' => 'Invoice has been voided and is no longer valid.',
                'is_default' => false
            ],
            [
                'name' => 'Pending Approval',
                'description' => 'Invoice is pending approval before being sent.',
                'is_default' => false
            ],
            [
                'name' => 'Processing',
                'description' => 'Payment for the invoice is currently being processed.',
                'is_default' => false
            ],
            [
                'name' => 'Refunded',
                'description' => 'Payment for the invoice has been refunded.',
                'is_default' => false
            ],
            [
                'name' => 'Partially Paid',
                'description' => 'Only a partial payment has been received for the invoice.',
                'is_default' => false
            ],
            [
                'name' => 'Disputed',
                'description' => 'Invoice payment is under dispute.',
                'is_default' => false
            ],
        ];
    }
}
