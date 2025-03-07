<?php

namespace App\Http\Controllers;

use App\Services\ExternalApi\FinanceApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class FinanceApiController extends Controller
{
    protected $financeApiService;

    public function __construct(FinanceApiService $financeApiService)
    {
        $this->financeApiService = $financeApiService;
        Log::info('FinanceApiController initialized');
    }

    /**
     * Display budget information
     */
    public function budget(Request $request)
    {
        Log::info('Budget method called in FinanceApiController', [
            'request_path' => $request->path(),
            'request_url' => $request->url(),
            'request_full_url' => $request->fullUrl(),
            'request_method' => $request->method(),
        ]);
        
        try {
            Log::info('About to call getData with endpoint "budget"');
            $budgetData = $this->financeApiService->getData('budget');
            Log::info('Successfully retrieved budget data', ['data_count' => is_array($budgetData) ? count($budgetData) : 'not an array']);
            return Inertia::render('Finance/Budget', ['budgetData' => $budgetData]);
        } catch (\Exception $e) {
            Log::error('Error in budget method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Inertia::render('Finance/Error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Display employee information
     */
    public function employees(Request $request)
    {
        try {
            $employeeData = $this->financeApiService->getData('employee');
            return Inertia::render('Finance/Employees', ['employeeData' => $employeeData]);
        } catch (\Exception $e) {
            return Inertia::render('Finance/Error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Display expense details
     */
    public function expenseDetails()
    {
        Log::info('FinanceApiController: expenseDetails method called');
        
        try {
            $data = $this->financeApiService->getData('expense');
            Log::info('FinanceApiController: Successfully retrieved expense data');
            return Inertia::render('Finance/ExpenseDetails', ['data' => $data]);
        } catch (\Exception $e) {
            Log::error('FinanceApiController: Error in expenseDetails method: ' . $e->getMessage());
            return Inertia::render('Finance/Error', [
                'error' => 'Unable to retrieve expense details: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display past due information
     */
    public function pastDue(Request $request)
    {
        try {
            $pastDueData = $this->financeApiService->getData('pastdue');
            return Inertia::render('Finance/PastDue', ['pastDueData' => $pastDueData]);
        } catch (\Exception $e) {
            return Inertia::render('Finance/Error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Display revenue details
     */
    public function revenueDetails(Request $request)
    {
        try {
            $revenueData = $this->financeApiService->getData('revenue');
            return Inertia::render('Finance/RevenueDetails', ['revenueData' => $revenueData]);
        } catch (\Exception $e) {
            return Inertia::render('Finance/Error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the finance dashboard showing summary information from all endpoints
     */
    public function dashboard(Request $request)
    {
        // Initialize data arrays with empty defaults
        $budgetData = [];
        $employeeData = [];
        $expenseData = [];
        $pastDueData = [];
        $revenueData = [];
        $errors = [];

        // Try to get budget data
        try {
            $budgetData = $this->financeApiService->getData('budget');
        } catch (\Exception $e) {
            Log::warning('Error fetching budget data', ['error' => $e->getMessage()]);
            $errors['budget'] = $e->getMessage();
        }

        // Try to get employee data
        try {
            $employeeData = $this->financeApiService->getData('employee');
        } catch (\Exception $e) {
            Log::warning('Error fetching employee data', ['error' => $e->getMessage()]);
            $errors['employee'] = $e->getMessage();
        }

        // Try to get expense data
        try {
            $expenseData = $this->financeApiService->getData('expense');
        } catch (\Exception $e) {
            Log::warning('Error fetching expense data', ['error' => $e->getMessage()]);
            $errors['expense'] = $e->getMessage();
        }

        // Try to get past due data
        try {
            $pastDueData = $this->financeApiService->getData('pastdue');
        } catch (\Exception $e) {
            Log::warning('Error fetching past due data', ['error' => $e->getMessage()]);
            $errors['pastdue'] = $e->getMessage();
        }

        // Try to get revenue data
        try {
            $revenueData = $this->financeApiService->getData('revenue');
        } catch (\Exception $e) {
            Log::warning('Error fetching revenue data', ['error' => $e->getMessage()]);
            $errors['revenue'] = $e->getMessage();
        }

        // Render the dashboard with whatever data we were able to collect
        return Inertia::render('Finance/Dashboard', [
            'budgetData' => $budgetData,
            'employeeData' => $employeeData,
            'expenseData' => $expenseData,
            'pastDueData' => $pastDueData, 
            'revenueData' => $revenueData,
            'errors' => $errors
        ]);
    }
} 