<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Ramsey\Uuid\Uuid;
use Laravel\Sanctum\HasApiTokens;
use App\Contracts\SiteVigilanceUser;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\UserCreatedNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ProtoneMedia\LaravelVerifyNewEmail\MustVerifyNewEmail;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail, SiteVigilanceUser
{
    use HasApiTokens, HasFactory, Notifiable, MustVerifyNewEmail, HasRoles, HasPanelShield, TwoFactorAuthenticatable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'creation_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (User $item) {
            if ($item->type == 'db') {
                $item->password = bcrypt(uniqid());
                $item->creation_token = Uuid::uuid4()->toString();
            }
        });

        static::created(function (User $item) {
            if ($item->type == 'db') {
                $item->notify(new UserCreatedNotification($item));
            }
        });
    }

    public function projectsOwning(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id', 'id');
    }

    public function projectsAffected(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_users', 'user_id', 'project_id')->withPivot(['role']);
    }

    public function favoriteProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_favorites', 'user_id', 'project_id');
    }

    public function ticketsOwned(): HasMany
    {
        return $this->hasMany(Ticket::class, 'owner_id', 'id');
    }

    public function ticketsResponsible(): HasMany
    {
        return $this->hasMany(Ticket::class, 'responsible_id', 'id');
    }

    public function hours(): HasMany
    {
        return $this->hasMany(TicketHour::class, 'user_id', 'id');
    }

    public function totalLoggedInHours(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->hours->sum('value');
            }
        );
    }
}
