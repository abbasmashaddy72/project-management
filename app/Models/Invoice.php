<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, HasTenantScope, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'team_id',
        'project_id',
        'invoice_number',
        'total',
        'subtotal',
        'vat',
        'currency',
        'start_date',
        'end_date',
        'issued_on',
        'due_on',
        'paid_on',
        'cancelled_on',
        'reminded_on',
        'status_id',
        'summary',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'issued_on' => 'date',
        'due_on' => 'date',
        'paid_on' => 'date',
        'cancelled_on' => 'date',
        'reminded_on' => 'date',
    ];

    /**
     * Generate the next invoice number for the current team.
     *
     * @return string
     */
    public static function generateInvoiceNumber()
    {
        // Get the active team name
        $teamName = \Filament\Facades\Filament::getTenant()->name;

        // Extract the first letters of each word in the team name
        $prefix = implode('', array_map(fn ($word) => strtoupper(substr($word, 0, 1)), explode(' ', $teamName)));

        // If the prefix has fewer than 3 characters, add 'X' to make it a 3-character prefix
        $prefix = str_pad($prefix, 3, 'X');

        // Get the current date and format it as YYMMDD
        $datePart = now()->format('ymd');

        // Find the count of invoices created on the current date
        $count = self::whereDate('created_at', now()->toDateString())
            ->where('team_id', \Filament\Facades\Filament::getTenant()->id)
            ->count() + 1;

        // Format the invoice number with prefix, date, and count
        $invoiceNumber = $prefix . $datePart . str_pad($count, 3, '0', STR_PAD_LEFT);

        return $invoiceNumber;
    }

    protected static function booted()
    {
        static::creating(function ($invoice) {
            // Set the user_id when creating a new invoice
            $invoice->user_id = auth()->id();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(InvoiceStatus::class, 'status_id', 'id')->withTrashed();
    }
}
