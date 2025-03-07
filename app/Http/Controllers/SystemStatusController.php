<?php

namespace App\Http\Controllers;

use App\Services\KubernetesMonitor;
use App\Services\AirflowMonitor;
use App\Services\WebsiteMonitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\Request;

class SystemStatusController extends Controller
{
    private KubernetesMonitor $k8s;
    private AirflowMonitor $airflow;
    private WebsiteMonitor $website;

    public function __construct(
        KubernetesMonitor $k8s,
        AirflowMonitor $airflow,
        WebsiteMonitor $website
    ) {
        $this->k8s = $k8s;
        $this->airflow = $airflow;
        $this->website = $website;
    }

    public function getStatus(Request $request): JsonResponse
    {
        Log::info('System status request received', [
            'method' => $request->method(),
            'path' => $request->path(),
            'headers' => $request->headers->all(),
            'user' => $request->user()?->id
        ]);

        try {
            $k8sStatus = $this->k8s->getClusterStatus();
            $airflowStatus = $this->airflow->getDagStatus();
            $websiteStatus = $this->website->getWebsiteStatus();

            Log::info('System status retrieved successfully', [
                'kubernetes_deployments' => count($k8sStatus['deployments'] ?? []),
                'airflow_dags' => count($airflowStatus['dags'] ?? []),
                'websites' => count($websiteStatus)
            ]);

            return response()->json([
                'kubernetes' => $k8sStatus,
                'airflow' => $airflowStatus,
                'websites' => $websiteStatus,
                'timestamp' => now()->toIso8601String()
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve system status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to retrieve system status',
                'details' => $e->getMessage(),
                'kubernetes' => [
                    'healthy' => 0,
                    'warning' => 0,
                    'error' => 0,
                    'total' => 0,
                    'deployments' => []
                ],
                'airflow' => [
                    'total' => 0,
                    'running' => 0,
                    'failed' => 0,
                    'dags' => []
                ],
                'websites' => [],
                'timestamp' => now()->toIso8601String()
            ], 500);
        }
    }
} 