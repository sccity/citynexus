<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AirflowStatus extends Model
{
    protected $connection = 'ServiceHealth';
    protected $table = 'airflow_status';
    public $timestamps = false;

    protected $fillable = [
        'timestamp',
        'total_dags',
        'running_dags',
        'failed_dags'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'total_dags' => 'integer',
        'running_dags' => 'integer',
        'failed_dags' => 'integer'
    ];

    public function dagStatuses(): HasMany
    {
        return $this->hasMany(AirflowDagStatus::class, 'health_id');
    }
} 