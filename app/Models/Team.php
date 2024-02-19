<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            $team->slug = Str::slug($team->name);
        });
    }

    // Model Relations
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    // Configuration Setup
    public function contractTypes(): HasMany
    {
        return $this->hasMany(ContractType::class);
    }

    public function projectStatuses(): HasMany
    {
        return $this->hasMany(ProjectStatus::class);
    }

    public function ticketStatuses(): HasMany
    {
        return $this->hasMany(TicketStatus::class);
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function ticketPriorities(): HasMany
    {
        return $this->hasMany(TicketPriority::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function invoiceStatuses(): HasMany
    {
        return $this->hasMany(InvoiceStatus::class);
    }

    // User Management
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
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

    // Site Vigilance
    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }
}
