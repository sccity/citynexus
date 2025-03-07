<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KubernetesPodStatus extends Model
{
    protected $table = 'kubernetes_pod_status';
    public $timestamps = false;

    protected $fillable = [
        'health_id',
        'pod_name',
        'namespace',
        'status',
        'start_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function health(): BelongsTo
    {
        return $this->belongsTo(KubernetesHealth::class, 'health_id');
    }
} 