<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KubernetesHealth extends Model
{
    protected $connection = 'ServiceHealth';
    protected $table = 'kubernetes_health';
    public $timestamps = false;

    protected $fillable = [
        'timestamp',
        'healthy_deployments',
        'warning_deployments',
        'error_deployments',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function deploymentStatuses(): HasMany
    {
        return $this->hasMany(KubernetesDeploymentStatus::class, 'health_id');
    }

    public function websiteStatuses(): HasMany
    {
        return $this->hasMany(WebsiteStatus::class, 'health_id');
    }
} 