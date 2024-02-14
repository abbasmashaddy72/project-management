<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketHour extends Model
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'value',
        'comment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'id');
    }

    public function forHumans(): Attribute
    {
        return new Attribute(
            get: function () {
                $seconds = $this->value * 3600;
                return CarbonInterval::seconds($seconds)->cascade()->forHumans();
            }
        );
    }

    public function setHoursAttribute($value): void
    {
        $value = str_replace(',', '.', $value);

        if (str_contains($value, ':')) {
            [$hours, $minutes] = explode(':', $value);
            $this->attributes['value'] = (int) $hours + ($minutes / 60);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    public function scopeThisWeek(Builder $query): void
    {
        $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function scopeLastWeek(Builder $query): void
    {
        $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek()->toDateString(), Carbon::now()->subWeek()->endOfWeek()->toDateString()]);
    }

    public function scopeThisMonth(Builder $query): void
    {
        $query->whereBetween('created_at', [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()]);
    }

    public function scopeThisQuarter(Builder $query): void
    {
        $query->whereBetween('created_at', [Carbon::now()->startOfQuarter()->toDateString(), Carbon::now()->endOfQuarter()->toDateString()]);
    }

    public function scopeLastQuarter(Builder $query): void
    {
        $query->whereBetween('created_at', [Carbon::now()->subQuarter()->startOfQuarter()->toDateString(), Carbon::now()->subQuarter()->endOfQuarter()->toDateString()]);
    }

    public function scopeLastMonth(Builder $query): void
    {
        $query->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth()->toDateString(), Carbon::now()->subMonth()->endOfMonth()->toDateString()]);
    }

    public function scopeThisYear(Builder $query): void
    {
        $query->whereBetween('created_at', [Carbon::now()->startOfYear()->toDateString(), Carbon::now()->endOfYear()->toDateString()]);
    }
}
