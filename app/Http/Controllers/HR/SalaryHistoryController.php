<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\SalaryHistory;
use App\Models\HR\Employee;
use Illuminate\Http\Request;

class SalaryHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SalaryHistory::with(['employee', 'approver']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->where('effective_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('effective_date', '<=', $request->date_to);
        }

        $salaries = $query->orderBy('effective_date', 'desc')->paginate(20)->withQueryString();
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.salary.index', compact('salaries', 'employees'));
    }

    public function create(Request $request)
    {
        $employeeId = $request->employee_id;
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.salary.create', compact('employees', 'employeeId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'effective_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['approved_by'] = auth()->id();

        $salaryHistory = SalaryHistory::create($validated);

        // Update employee's current salary
        $employee = Employee::find($validated['employee_id']);
        if ($employee) {
            $employee->update([
                'salary' => $validated['salary'],
                'currency' => $validated['currency'] ?? $employee->currency ?? 'USD',
            ]);
        }

        return redirect()->route('hr.salary.show', $salaryHistory)
            ->with('success', 'Salary history entry created successfully.');
    }

    public function show(SalaryHistory $salary)
    {
        $salary->load(['employee', 'approver']);

        return view('hr.salary.show', compact('salary'));
    }

    public function edit(SalaryHistory $salary)
    {
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.salary.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, SalaryHistory $salary)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'effective_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $salary->update($validated);

        // Update employee's current salary if this is the latest entry
        $latestSalary = SalaryHistory::where('employee_id', $validated['employee_id'])
            ->orderBy('effective_date', 'desc')
            ->first();

        if ($latestSalary && $latestSalary->id == $salary->id) {
            $employee = Employee::find($validated['employee_id']);
            if ($employee) {
                $employee->update([
                    'salary' => $validated['salary'],
                    'currency' => $validated['currency'] ?? $employee->currency ?? 'USD',
                ]);
            }
        }

        return redirect()->route('hr.salary.show', $salary)
            ->with('success', 'Salary history entry updated successfully.');
    }

    public function destroy(SalaryHistory $salary)
    {
        $employeeId = $salary->employee_id;
        $salary->delete();

        // Update employee's current salary to the latest entry
        $latestSalary = SalaryHistory::where('employee_id', $employeeId)
            ->orderBy('effective_date', 'desc')
            ->first();

        $employee = Employee::find($employeeId);
        if ($employee) {
            if ($latestSalary) {
                $employee->update([
                    'salary' => $latestSalary->salary,
                    'currency' => $latestSalary->currency ?? $employee->currency ?? 'USD',
                ]);
            } else {
                $employee->update([
                    'salary' => null,
                ]);
            }
        }

        return redirect()->route('hr.salary.index')
            ->with('success', 'Salary history entry deleted successfully.');
    }
}
