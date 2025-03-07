<?php

namespace App\Services\ExternalApi;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class FinanceApiService
{
    protected $baseUrl;
    protected $apiToken;

    public function __construct()
    {
        // Make sure we remove the api/finance part from the URL if it's already there
        $baseUrl = rtrim(config('services.finance_api.base_url'), '/');
        
        // Extract the base part of the URL without any path components
        $parsedUrl = parse_url($baseUrl);
        $baseWithoutPath = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        if (isset($parsedUrl['port'])) {
            $baseWithoutPath .= ':' . $parsedUrl['port'];
        }
        
        // If the baseUrl includes /api/finance, use only the baseWithoutPath
        if (strpos($baseUrl, '/api/finance') !== false) {
            $this->baseUrl = $baseWithoutPath;
            Log::info('Found /api/finance in base URL, using base without path', [
                'original' => $baseUrl,
                'modified' => $this->baseUrl
            ]);
        } else {
            $this->baseUrl = $baseUrl;
        }
        
        $this->apiToken = config('services.finance_api.token');
        
        // Log the configuration for debugging
        Log::info('Finance API Service Initialized', [
            'baseUrl' => $this->baseUrl,
            'tokenExists' => !empty($this->apiToken)
        ]);
    }

    /**
     * Get data from the Finance API
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     */
    public function getData(string $endpoint, array $params = []): array
    {
        try {
            // Ensure endpoint doesn't start with a slash
            $endpoint = ltrim($endpoint, '/');
            
            // Always make sure we're using /api/finance/ as the path
            $path = 'api/finance';
            
            // Combine the path and endpoint properly
            $fullPath = $path . '/' . $endpoint;
            
            // Make sure there are no duplicate slashes
            $fullPath = ltrim($fullPath, '/');
            
            // Add the token to the parameters
            $params['token'] = $this->apiToken;
            
            $fullUrl = "{$this->baseUrl}/{$fullPath}";
            
            // Log the request for debugging
            Log::info('Making Finance API Request', [
                'url' => $fullUrl,
                'baseUrl' => $this->baseUrl,
                'path' => $path,
                'endpoint' => $endpoint,
                'fullPath' => $fullPath,
                'params' => array_merge($params, ['token' => '***REDACTED***'])
            ]);
            
            $response = Http::get($fullUrl, $params);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Finance API Request Failed', [
                'url' => isset($fullUrl) ? $fullUrl : "URL not constructed yet",
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Post data to the Finance API
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    public function postData(string $endpoint, array $data): array
    {
        try {
            // Ensure endpoint doesn't start with a slash
            $endpoint = ltrim($endpoint, '/');
            
            // Always make sure we're using /api/finance/ as the path
            $path = 'api/finance';
            
            // Combine the path and endpoint properly
            $fullPath = $path . '/' . $endpoint;
            
            // Make sure there are no duplicate slashes
            $fullPath = ltrim($fullPath, '/');
            
            // For POST requests, you might need to append the token differently
            // depending on the API's requirements
            $url = "{$this->baseUrl}/{$fullPath}?token={$this->apiToken}";
            
            // Log the request for debugging
            Log::info('Making Finance API POST Request', [
                'url' => str_replace($this->apiToken, '***REDACTED***', $url),
                'baseUrl' => $this->baseUrl,
                'path' => $path,
                'endpoint' => $endpoint,
                'fullPath' => $fullPath,
            ]);
            
            $response = Http::post($url, $data);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Finance API POST Request Failed', [
                'url' => isset($url) ? str_replace($this->apiToken, '***REDACTED***', $url) : "URL not constructed yet",
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle the API response
     *
     * @param Response $response
     * @return array
     * @throws \Exception
     */
    protected function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return $response->json();
        }

        // Log the error
        Log::error('Finance API Error:', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        // Throw an exception with appropriate message
        throw new \Exception('Finance API Error: ' . $response->status() . ' - ' . $response->body());
    }
}
