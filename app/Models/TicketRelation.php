<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'type',
        'relation_id',
        'sort'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    public function relation(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'relation_id', 'id');
    }
}
