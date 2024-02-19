<?php

namespace App\Providers;

use App\Models\Site;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Activity;
use App\Models\TicketHour;
use App\Models\TicketType;
use App\Models\CustomMedia;
use App\Models\ContractType;
use App\Models\TicketStatus;
use App\Policies\RolePolicy;
use App\Policies\SitePolicy;
use App\Policies\UserPolicy;
use App\Models\InvoiceStatus;
use App\Models\ProjectStatus;
use App\Policies\MediaPolicy;
use App\Models\TicketPriority;
use App\Policies\TicketPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\ProjectPolicy;
use App\Policies\ActivityPolicy;
use App\Models\ExceptionLogGroup;
use Awcodes\Curator\Models\Media;
use App\Policies\PermissionPolicy;
use App\Policies\TicketHourPolicy;
use App\Policies\TicketTypePolicy;
use Spatie\Permission\Models\Role;
use App\Policies\CustomMediaPolicy;
use App\Policies\ContractTypePolicy;
use App\Policies\TicketStatusPolicy;
use App\Policies\InvoiceStatusPolicy;
use App\Policies\ProjectStatusPolicy;
use App\Policies\TicketPriorityPolicy;
use Spatie\Permission\Models\Permission;
use App\Policies\ExceptionLogGroupPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Activity::class => ActivityPolicy::class,
        ContractType::class => ContractTypePolicy::class,
        CustomMedia::class => CustomMediaPolicy::class,
        ExceptionLogGroup::class => ExceptionLogGroupPolicy::class,
        Invoice::class => InvoicePolicy::class,
        InvoiceStatus::class => InvoiceStatusPolicy::class,
        Media::class => MediaPolicy::class,
        Permission::class => PermissionPolicy::class,
        Project::class => ProjectPolicy::class,
        ProjectStatus::class => ProjectStatusPolicy::class,
        Role::class => RolePolicy::class,
        Site::class => SitePolicy::class,
        TicketHour::class => TicketHourPolicy::class,
        Ticket::class => TicketPolicy::class,
        TicketPriority::class => TicketPriorityPolicy::class,
        TicketStatus::class => TicketStatusPolicy::class,
        TicketType::class => TicketTypePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
