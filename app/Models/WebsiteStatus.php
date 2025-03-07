<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteStatus extends Model
{
    protected $connection = 'ServiceHealth';
    protected $table = 'website_status';
    public $timestamps = false;

    protected $fillable = [
        'health_id',
        'url',
        'name',
        'status',
        'response_time',
        'last_checked',
    ];

    protected $casts = [
        'last_checked' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function health(): BelongsTo
    {
        return $this->belongsTo(KubernetesHealth::class, 'health_id');
    }
} 