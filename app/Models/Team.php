<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function getCurrentTenantLabel(): string
    {
        return 'Active team';
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    // Model Relations
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user', 'team_id', 'user_id')->using(Member::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Configuration Setup
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function projectStatuses(): HasMany
    {
        return $this->hasMany(ProjectStatus::class);
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function ticketPriorities(): HasMany
    {
        return $this->hasMany(TicketPriority::class);
    }

    public function ticketStatuses(): HasMany
    {
        return $this->hasMany(TicketStatus::class);
    }

    public function contractTypes(): HasMany
    {
        return $this->hasMany(ContractType::class);
    }

    public function invoiceStatuses(): HasMany
    {
        return $this->hasMany(InvoiceStatus::class);
    }

    // User Management
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user', 'team_id', 'user_id')->using(Member::class);
    }

    // Project Management
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
