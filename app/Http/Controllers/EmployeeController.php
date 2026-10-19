<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmployeeController extends Controller
{
    public function index()
    {
        $distributorId = auth()->id(); // Logged-in shop ID

        $thisMonthStart = Carbon::now()->startOfMonth();
        $thisMonthEnd = Carbon::now()->endOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Fetch only employees belonging to this specific shop with shop-isolated performance metrics
        $employees = Employee::where('distributor_id', $distributorId)
            ->withCount(['sales as total_sales_count' => function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            }])
            ->withSum(['sales as total_sales_amount' => function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            }], 'total_amount')
            ->withSum(['sales as this_month_sales' => function ($query) use ($thisMonthStart, $thisMonthEnd, $distributorId) {
                $query->where('distributor_id', $distributorId)
                      ->whereBetween('invoice_date', [$thisMonthStart, $thisMonthEnd]);
            }], 'total_amount')
            ->withSum(['sales as last_month_sales' => function ($query) use ($lastMonthStart, $lastMonthEnd, $distributorId) {
                $query->where('distributor_id', $distributorId)
                      ->whereBetween('invoice_date', [$lastMonthStart, $lastMonthEnd]);
            }], 'total_amount')
            ->get();

        return view('distributor.employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email',
            'phone' => 'required|string|unique:employees,phone',
            'incentive_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $validated['distributor_id'] = auth()->id();

        Employee::create($validated);

        return redirect()->back()->with('success', 'Employee registered successfully!');
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email,' . $employee->employee_id . ',employee_id',
            'phone' => 'required|string|unique:employees,phone,' . $employee->employee_id . ',employee_id',
            'incentive_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $employee->update($validated);

        return redirect()->back()->with('success', 'Employee details modified successfully!');
    }

    public function showSales(Request $request, Employee $employee)
    {
        $distributorId = auth()->id();

        // SECURITY CHECK: Prevent Shop A from executing cross-tenant lookups on Shop B's employees
        if ($employee->distributor_id !== $distributorId) {
            abort(403, 'Unauthorized access to employee profile.');
        }

        // Constrain the sales ledger queries entirely to this active logged-in shop
        $query = $employee->sales()->where('distributor_id', $distributorId);

        // Date Range Filtering Logic
        if ($request->has('view_all')) {
            // No date conditions applied
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
        } else {
            // Default fall-back configuration: Show current month only
            $query->whereBetween('invoice_date', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString()
            ]);
        }

        // Clone the filtered target subset to calculate summary cards totals before paginating records
        $totalsQuery = clone $query;
        $totalSalesAmount = $totalsQuery->sum('total_amount');
        $totalIncentiveAmount = ($totalSalesAmount * $employee->incentive_percentage) / 100;

        // Fetch paginated results and append url parameters safely for cross-page navigation links
        $sales = $query->orderBy('invoice_date', 'desc')->paginate(15)->withQueryString();

        return view('distributor.employees.sales', compact(
            'employee',
            'sales',
            'totalSalesAmount',
            'totalIncentiveAmount'
        ));
    }
}
