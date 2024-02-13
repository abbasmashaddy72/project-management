<?php

namespace App\Models;

use App\Notifications\TicketCommented;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content'
    ];


    public static function boot()
    {
        parent::boot();

        static::created(function (TicketComment $item) {
            foreach ($item->ticket->watchers as $user) {
                $user->notify(new TicketCommented($item));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }
}
