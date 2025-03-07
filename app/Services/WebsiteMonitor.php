<?php

namespace App\Services;

use App\Models\KubernetesHealth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WebsiteMonitor
{
    public function getWebsiteStatus(): array
    {
        try {
            Log::info('Fetching latest website status from database');
            
            $latestStatus = KubernetesHealth::latest('timestamp')->first();
            
            if (!$latestStatus) {
                Log::warning('No health data found in database');
                return [];
            }

            return $latestStatus->websiteStatuses()
                ->select([
                    'name',
                    'url',
                    'status',
                    'response_time',
                    'last_checked'
                ])
                ->get()
                ->map(function ($website) {
                    return [
                        'name' => $website->name,
                        'status' => $website->status,
                        'latency' => $website->response_time . 'ms',
                        'uptime' => '100%', // We'll calculate this later based on historical data
                        'last_checked' => $website->last_checked->diffForHumans()
                    ];
                })
                ->all();
        } catch (\Exception $e) {
            Log::error('Error fetching website status from database', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
} 