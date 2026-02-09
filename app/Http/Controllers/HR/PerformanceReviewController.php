<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\HR\PerformanceReview;
use App\Models\HR\PerformanceReviewCriterion;
use Illuminate\Http\Request;

class PerformanceReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = PerformanceReview::with(['employee', 'reviewer']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('reviewer_id')) {
            $query->where('reviewer_id', $request->reviewer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('year')) {
            $query->whereYear('review_date', $request->year);
        }

        $reviews = $query->orderBy('review_date', 'desc')->paginate(20)->withQueryString();
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.performance.index', compact('reviews', 'employees'));
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();
        $reviewers = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.performance.create', compact('employees', 'reviewers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'reviewer_id' => 'required|exists:employees,id',
            'review_period_start' => 'required|date',
            'review_period_end' => 'required|date|after_or_equal:review_period_start',
            'review_date' => 'required|date',
            'overall_rating' => 'nullable|numeric|min:0|max:5',
            'goals_achieved' => 'nullable|string',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'comments' => 'nullable|string',
            'employee_comments' => 'nullable|string',
            'status' => 'required|in:draft,submitted,acknowledged,completed',
            'criteria' => 'nullable|array',
            'criteria.*.criterion_name' => 'required|string|max:255',
            'criteria.*.rating' => 'nullable|numeric|min:0|max:5',
            'criteria.*.comments' => 'nullable|string',
            'criteria.*.weight' => 'nullable|numeric|min:0|max:10',
        ]);

        $criteria = $validated['criteria'] ?? [];
        unset($validated['criteria']);

        $review = PerformanceReview::create($validated);

        // Create criteria
        foreach ($criteria as $criterion) {
            $review->criteria()->create($criterion);
        }

        // Calculate overall rating if criteria exist
        if ($review->criteria()->count() > 0) {
            $weightedSum = $review->criteria()->sum(function ($c) {
                return ($c->rating ?? 0) * ($c->weight ?? 1);
            });
            $totalWeight = $review->criteria()->sum('weight') ?: 1;
            $review->overall_rating = round($weightedSum / $totalWeight, 2);
            $review->save();
        }

        return redirect()->route('hr.performance.show', $review)
            ->with('success', 'Performance review created successfully.');
    }

    public function show(PerformanceReview $performanceReview)
    {
        $performanceReview->load(['employee', 'reviewer', 'criteria']);

        return view('hr.performance.show', compact('performanceReview'));
    }

    public function edit(PerformanceReview $performanceReview)
    {
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();
        $reviewers = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();
        $performanceReview->load('criteria');

        return view('hr.performance.edit', compact('performanceReview', 'employees', 'reviewers'));
    }

    public function update(Request $request, PerformanceReview $performanceReview)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'reviewer_id' => 'required|exists:employees,id',
            'review_period_start' => 'required|date',
            'review_period_end' => 'required|date|after_or_equal:review_period_start',
            'review_date' => 'required|date',
            'overall_rating' => 'nullable|numeric|min:0|max:5',
            'goals_achieved' => 'nullable|string',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'comments' => 'nullable|string',
            'employee_comments' => 'nullable|string',
            'status' => 'required|in:draft,submitted,acknowledged,completed',
            'criteria' => 'nullable|array',
            'criteria.*.id' => 'nullable|exists:performance_review_criteria,id',
            'criteria.*.criterion_name' => 'required|string|max:255',
            'criteria.*.rating' => 'nullable|numeric|min:0|max:5',
            'criteria.*.comments' => 'nullable|string',
            'criteria.*.weight' => 'nullable|numeric|min:0|max:10',
        ]);

        $criteria = $validated['criteria'] ?? [];
        unset($validated['criteria']);

        $performanceReview->update($validated);

        // Update or create criteria
        $existingIds = [];
        foreach ($criteria as $criterionData) {
            if (isset($criterionData['id'])) {
                $criterion = PerformanceReviewCriterion::find($criterionData['id']);
                if ($criterion && $criterion->review_id == $performanceReview->id) {
                    $criterion->update($criterionData);
                    $existingIds[] = $criterion->id;
                }
            } else {
                $criterion = $performanceReview->criteria()->create($criterionData);
                $existingIds[] = $criterion->id;
            }
        }

        // Delete removed criteria
        $performanceReview->criteria()->whereNotIn('id', $existingIds)->delete();

        // Recalculate overall rating
        if ($performanceReview->criteria()->count() > 0) {
            $weightedSum = $performanceReview->criteria()->sum(function ($c) {
                return ($c->rating ?? 0) * ($c->weight ?? 1);
            });
            $totalWeight = $performanceReview->criteria()->sum('weight') ?: 1;
            $performanceReview->overall_rating = round($weightedSum / $totalWeight, 2);
            $performanceReview->save();
        }

        return redirect()->route('hr.performance.show', $performanceReview)
            ->with('success', 'Performance review updated successfully.');
    }

    public function destroy(PerformanceReview $performanceReview)
    {
        $performanceReview->delete();

        return redirect()->route('hr.performance.index')
            ->with('success', 'Performance review deleted successfully.');
    }
}
