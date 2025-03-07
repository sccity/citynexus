<?php

namespace App\Services;

use App\Models\KubernetesHealth;
use App\Models\KubernetesDeploymentStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class KubernetesMonitor
{
    public function getClusterStatus(): array
    {
        try {
            Log::info('Fetching latest Kubernetes cluster status from database');
            
            $latestStatus = KubernetesHealth::latest('timestamp')->first();
            
            if (!$latestStatus) {
                Log::warning('No Kubernetes health data found in database');
                return [
                    'healthy' => 0,
                    'warning' => 0,
                    'error' => 0,
                    'total' => 0,
                    'timestamp' => null,
                    'deployments' => []
                ];
            }

            $deployments = $latestStatus->deploymentStatuses()
                ->select([
                    'deployment_name',
                    'namespace',
                    'status',
                    'replicas',
                    'available_replicas',
                    'unavailable_replicas',
                    'updated_replicas'
                ])
                ->get()
                ->map(function ($deployment) {
                    return [
                        'name' => $deployment->deployment_name,
                        'namespace' => $deployment->namespace,
                        'status' => $deployment->status,
                        'replicas' => [
                            'total' => $deployment->replicas,
                            'available' => $deployment->available_replicas,
                            'unavailable' => $deployment->unavailable_replicas,
                            'updated' => $deployment->updated_replicas,
                        ]
                    ];
                });

            return [
                'healthy' => $latestStatus->healthy_deployments,
                'warning' => $latestStatus->warning_deployments,
                'error' => $latestStatus->error_deployments,
                'total' => $latestStatus->healthy_deployments + $latestStatus->warning_deployments + $latestStatus->error_deployments,
                'timestamp' => $latestStatus->timestamp->toIso8601String(),
                'deployments' => $deployments
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching Kubernetes status from database', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    public function getDeploymentStatus(string $namespace, string $deploymentName): ?array
    {
        try {
            Log::info('Fetching deployment status from database', [
                'namespace' => $namespace,
                'deployment' => $deploymentName
            ]);
            
            $latestStatus = KubernetesHealth::latest('timestamp')->first();
            
            if (!$latestStatus) {
                Log::warning('No Kubernetes health data found in database');
                return null;
            }

            $deploymentStatus = $latestStatus->deploymentStatuses()
                ->where('namespace', $namespace)
                ->where('deployment_name', $deploymentName)
                ->first();

            if (!$deploymentStatus) {
                Log::warning('Deployment not found in latest status', [
                    'namespace' => $namespace,
                    'deployment' => $deploymentName
                ]);
                return null;
            }

            return [
                'name' => $deploymentStatus->deployment_name,
                'namespace' => $deploymentStatus->namespace,
                'status' => $deploymentStatus->status,
                'replicas' => [
                    'total' => $deploymentStatus->replicas,
                    'available' => $deploymentStatus->available_replicas,
                    'unavailable' => $deploymentStatus->unavailable_replicas,
                    'updated' => $deploymentStatus->updated_replicas,
                ],
                'timestamp' => $latestStatus->timestamp->toIso8601String()
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching deployment status from database', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'namespace' => $namespace,
                'deployment' => $deploymentName
            ]);
            
            throw $e;
        }
    }

    public function getNamespaceStatus(string $namespace): array
    {
        try {
            Log::info('Fetching namespace status from database', [
                'namespace' => $namespace
            ]);
            
            $latestStatus = KubernetesHealth::latest('timestamp')->first();
            
            if (!$latestStatus) {
                Log::warning('No Kubernetes health data found in database');
                return [
                    'healthy' => 0,
                    'warning' => 0,
                    'error' => 0,
                    'total' => 0,
                    'timestamp' => null,
                    'deployments' => []
                ];
            }

            $deployments = $latestStatus->deploymentStatuses()
                ->where('namespace', $namespace)
                ->get();

            $stats = [
                'healthy' => 0,
                'warning' => 0,
                'error' => 0
            ];

            $deploymentDetails = $deployments->map(function ($deployment) use (&$stats) {
                $stats[$deployment->status]++;
                return [
                    'name' => $deployment->deployment_name,
                    'status' => $deployment->status,
                    'replicas' => [
                        'total' => $deployment->replicas,
                        'available' => $deployment->available_replicas,
                        'unavailable' => $deployment->unavailable_replicas,
                        'updated' => $deployment->updated_replicas,
                    ]
                ];
            });

            return [
                'healthy' => $stats['healthy'],
                'warning' => $stats['warning'],
                'error' => $stats['error'],
                'total' => $deployments->count(),
                'timestamp' => $latestStatus->timestamp->toIso8601String(),
                'deployments' => $deploymentDetails
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching namespace status from database', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'namespace' => $namespace
            ]);
            
            throw $e;
        }
    }
} 