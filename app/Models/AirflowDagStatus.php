<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AirflowDagStatus extends Model
{
    protected $connection = 'ServiceHealth';
    protected $table = 'airflow_dag_status';
    public $timestamps = false;

    protected $fillable = [
        'health_id',
        'dag_id',
        'is_active',
        'last_run_state',
        'last_run_start_date',
        'last_run_end_date'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run_start_date' => 'datetime',
        'last_run_end_date' => 'datetime'
    ];

    public function airflowStatus(): BelongsTo
    {
        return $this->belongsTo(AirflowStatus::class, 'health_id');
    }
} 