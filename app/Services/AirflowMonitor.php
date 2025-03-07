<?php

namespace App\Services;

use App\Models\AirflowStatus;
use App\Models\AirflowDagStatus;
use Illuminate\Support\Facades\Log;
use Exception;

class AirflowMonitor
{
    public function getDagStatus()
    {
        try {
            Log::info('Fetching latest Airflow status from database');
            
            $latestStatus = AirflowStatus::latest('id')->first();
            
            if (!$latestStatus) {
                Log::warning('No Airflow status found in database');
                return [
                    'total' => 0,
                    'running' => 0,
                    'failed' => 0,
                    'dags' => [],
                    'error_message' => 'No Airflow status data available'
                ];
            }

            $dagStatuses = AirflowDagStatus::where('health_id', $latestStatus->id)->get();
            
            $status = [
                'total' => $latestStatus->total_dags,
                'running' => $latestStatus->running_dags,
                'failed' => $latestStatus->failed_dags,
                'dags' => []
            ];

            foreach ($dagStatuses as $dag) {
                $status['dags'][] = [
                    'name' => $dag->dag_id,
                    'status' => $this->mapDagStatus($dag->last_run_state),
                    'lastRun' => $this->formatLastRunTime($dag->last_run_start_date)
                ];
            }

            return $status;
        } catch (Exception $e) {
            Log::error('Failed to fetch Airflow status from database: ' . $e->getMessage());
            return [
                'total' => 0,
                'running' => 0,
                'failed' => 0,
                'dags' => [],
                'error_message' => $e->getMessage()
            ];
        }
    }

    private function mapDagStatus(?string $state): string
    {
        if (!$state) {
            return 'success';
        }

        $state = strtolower($state);
        if ($state === 'running') {
            return 'running';
        }
        if (in_array($state, ['failed', 'failed_upstream'])) {
            return 'failed';
        }
        return 'success';
    }

    private function formatLastRunTime(?string $startDate): string
    {
        if (!$startDate) {
            return 'Never';
        }

        $start = strtotime($startDate);
        $now = time();
        $diff = $now - $start;

        if ($diff < 60) {
            return 'Just now';
        }

        if ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "{$minutes}m ago";
        }

        if ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "{$hours}h ago";
        }

        $days = floor($diff / 86400);
        return "{$days}d ago";
    }
} 