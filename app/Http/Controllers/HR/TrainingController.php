<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\EmployeeTraining;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeTraining::with(['employee', 'creator']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $trainings = $query->orderBy('start_date', 'desc')->paginate(20)->withQueryString();
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.training.index', compact('trainings', 'employees'));
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.training.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'training_name' => 'required|string|max:255',
            'training_provider' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:scheduled,in-progress,completed,cancelled',
            'certificate_number' => 'nullable|string|max:100',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Handle certificate file upload
        if ($request->hasFile('certificate_file')) {
            $file = $request->file('certificate_file');
            $path = $file->store('training/certificates', 'public');
            $validated['certificate_file'] = $path;
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        $validated['created_by'] = auth()->id();

        $training = EmployeeTraining::create($validated);

        return redirect()->route('hr.training.show', $training)
            ->with('success', 'Training record created successfully.');
    }

    public function show(EmployeeTraining $training)
    {
        $training->load(['employee', 'creator']);

        return view('hr.training.show', compact('training'));
    }

    public function edit(EmployeeTraining $training)
    {
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.training.edit', compact('training', 'employees'));
    }

    public function update(Request $request, EmployeeTraining $training)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'training_name' => 'required|string|max:255',
            'training_provider' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:scheduled,in-progress,completed,cancelled',
            'certificate_number' => 'nullable|string|max:100',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Handle certificate file upload
        if ($request->hasFile('certificate_file')) {
            // Delete old file if exists
            if ($training->certificate_file) {
                Storage::disk('public')->delete($training->certificate_file);
            }

            $file = $request->file('certificate_file');
            $path = $file->store('training/certificates', 'public');
            $validated['certificate_file'] = $path;
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        $training->update($validated);

        return redirect()->route('hr.training.show', $training)
            ->with('success', 'Training record updated successfully.');
    }

    public function destroy(EmployeeTraining $training)
    {
        // Delete certificate file if exists
        if ($training->certificate_file) {
            Storage::disk('public')->delete($training->certificate_file);
        }

        $training->delete();

        return redirect()->route('hr.training.index')
            ->with('success', 'Training record deleted successfully.');
    }
}
