<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates
     */
    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active' ? 1 : 0);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $templates = $query->orderBy('category')->orderBy('name')->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_templates' => EmailTemplate::count(),
            'active_templates' => EmailTemplate::where('is_active', true)->count(),
            'categories_count' => EmailTemplate::distinct()->count('category'),
        ];

        // Get unique categories
        $categories = EmailTemplate::distinct()->pluck('category')->filter()->sort()->values();

        return view('email-templates.index', compact('templates', 'stats', 'categories'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        $categories = EmailTemplate::distinct()->pluck('category')->filter()->sort()->values();

        return view('email-templates.create', compact('categories'));
    }

    /**
     * Store a newly created template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:email_templates,slug',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'category' => 'nullable|string|max:255',
            'variables' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['variables'] = $this->parseVariables($validated['variables'] ?? '');

        EmailTemplate::create($validated);

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    /**
     * Display the specified template
     */
    public function show(EmailTemplate $emailTemplate)
    {
        return view('email-templates.show', compact('emailTemplate'));
    }

    /**
     * Show the form for editing the specified template
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        $categories = EmailTemplate::distinct()->pluck('category')->filter()->sort()->values();

        return view('email-templates.edit', compact('emailTemplate', 'categories'));
    }

    /**
     * Update the specified template
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:email_templates,slug,'.$emailTemplate->id,
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'category' => 'nullable|string|max:255',
            'variables' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['variables'] = $this->parseVariables($validated['variables'] ?? '');
        $validated['is_active'] = $request->has('is_active');

        $emailTemplate->update($validated);

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    /**
     * Remove the specified template
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }

    /**
     * Preview template with sample data
     */
    public function preview(EmailTemplate $emailTemplate)
    {
        $sampleData = $this->getSampleData($emailTemplate->category);
        $rendered = $emailTemplate->render($sampleData);

        return view('email-templates.preview', compact('emailTemplate', 'rendered', 'sampleData'));
    }

    /**
     * Send test email
     */
    public function sendTest(EmailTemplate $emailTemplate, Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $sampleData = $this->getSampleData($emailTemplate->category);
        $rendered = $emailTemplate->render($sampleData);

        try {
            // Note: Configure mail settings in .env for this to work
            Mail::raw($rendered['body'], function ($message) use ($rendered, $request) {
                $message->to($request->email)
                    ->subject($rendered['subject']);
            });

            return back()->with('success', 'Test email sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: '.$e->getMessage());
        }
    }

    /**
     * Parse variables from string to array
     */
    private function parseVariables($variablesString)
    {
        if (empty($variablesString)) {
            return null;
        }

        $variables = [];
        $lines = explode("\n", $variablesString);
        foreach ($lines as $line) {
            $line = trim($line);
            if (! empty($line)) {
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $variables[trim($parts[0])] = trim($parts[1]);
                }
            }
        }

        return ! empty($variables) ? $variables : null;
    }

    /**
     * Get sample data for preview
     */
    private function getSampleData($category)
    {
        $default = [
            'client_name' => 'John Doe',
            'client_company' => 'ABC Company',
            'booth_number' => 'A-101',
            'booking_id' => '12345',
            'amount' => '$1,000.00',
            'date' => date('Y-m-d'),
            'user_name' => auth()->user()->username ?? 'Admin',
        ];

        $categoryData = [
            'booking' => array_merge($default, [
                'booking_date' => date('Y-m-d'),
                'booth_count' => '3',
            ]),
            'payment' => array_merge($default, [
                'payment_amount' => '$1,000.00',
                'payment_method' => 'Bank Transfer',
                'invoice_number' => 'INV-001',
            ]),
            'notification' => array_merge($default, [
                'notification_type' => 'Booking Confirmation',
                'message' => 'Your booking has been confirmed.',
            ]),
        ];

        return $categoryData[$category] ?? $default;
    }
}
