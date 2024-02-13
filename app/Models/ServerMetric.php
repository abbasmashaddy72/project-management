<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Repositories\SiteRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServerMetric extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'cpu_load',
        'memory_usage',
        'disk_usage',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(SiteRepository::resolveModelClass());
    }

    public function diskUsage(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $diskUsage = json_decode($value, true);
                $freeSpace = $diskUsage['freeSpace'] ?? null;
                $totalSpace = $diskUsage['totalSpace'] ?? null;
                $percentage = 0;

                if ($totalSpace && $freeSpace) {
                    $percentage = number_format(($totalSpace - $freeSpace) / $totalSpace * 100, 2);
                }
                $percentage = floatval($percentage);

                return [
                    'freeSpace' => $freeSpace,
                    'totalSpace' => $totalSpace,
                    'percentage' => $percentage,
                ];
            }
        );
    }
}
