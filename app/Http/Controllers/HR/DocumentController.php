<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\EmployeeDocument;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeDocument::with(['employee', 'uploader']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('expiring_soon')) {
            $query->whereNotNull('expiry_date')
                ->where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>=', now());
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        // Get unique document types
        $documentTypes = EmployeeDocument::distinct()->pluck('document_type')->sort()->values();

        return view('hr.documents.index', compact('documents', 'employees', 'documentTypes'));
    }

    public function create(Request $request)
    {
        $employeeId = $request->employee_id;
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.documents.create', compact('employees', 'employeeId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'document_type' => 'required|string|max:100',
            'document_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
            'expiry_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Handle file upload
        $file = $request->file('file');
        $path = $file->store('employee-documents', 'public');

        $validated['file_path'] = $path;
        $validated['file_size'] = $file->getSize();
        $validated['mime_type'] = $file->getMimeType();
        $validated['uploaded_by'] = auth()->id();

        unset($validated['file']);

        $document = EmployeeDocument::create($validated);

        return redirect()->route('hr.documents.show', $document)
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(EmployeeDocument $document)
    {
        $document->load(['employee', 'uploader']);

        return view('hr.documents.show', compact('document'));
    }

    public function download(EmployeeDocument $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->document_name);
    }

    public function edit(EmployeeDocument $document)
    {
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.documents.edit', compact('document', 'employees'));
    }

    public function update(Request $request, EmployeeDocument $document)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'document_type' => 'required|string|max:100',
            'document_name' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
            'expiry_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Handle file upload if new file provided
        if ($request->hasFile('file')) {
            // Delete old file
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('employee-documents', 'public');
            $validated['file_path'] = $path;
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        unset($validated['file']);

        $document->update($validated);

        return redirect()->route('hr.documents.show', $document)
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(EmployeeDocument $document)
    {
        // Delete file
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('hr.documents.index')
            ->with('success', 'Document deleted successfully.');
    }
}
