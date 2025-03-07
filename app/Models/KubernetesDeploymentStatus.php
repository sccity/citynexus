<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KubernetesDeploymentStatus extends Model
{
    protected $connection = 'ServiceHealth';
    protected $table = 'kubernetes_deployment_status';
    public $timestamps = false;

    protected $fillable = [
        'health_id',
        'deployment_name',
        'namespace',
        'status',
        'replicas',
        'available_replicas',
        'unavailable_replicas',
        'updated_replicas',
    ];

    protected $casts = [
        'replicas' => 'integer',
        'available_replicas' => 'integer',
        'unavailable_replicas' => 'integer',
        'updated_replicas' => 'integer',
        'created_at' => 'datetime',
    ];

    public function health(): BelongsTo
    {
        return $this->belongsTo(KubernetesHealth::class, 'health_id');
    }
} 