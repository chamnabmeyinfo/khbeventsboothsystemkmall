{{-- Single responsive clear form: day / month / year (Bootstrap breakpoints). --}}
@php
    $d = now()->format('Y-m-d');
    $m = now()->format('Y-m');
    $y = (int) now()->format('Y');
@endphp

<div class="canvas-panel lp-canvas-panel--warning mb-3">
    <div class="panel-header">
        <h2 class="panel-title"><i class="fas fa-trash-alt" aria-hidden="true"></i> Clear analytics data</h2>
    </div>
    <p class="text-muted small mb-3">
        Removes <strong>tracking events</strong> (views, CTA, language, thank-you, etc.) whose time falls in the selected <strong>calendar day, month, or year</strong> (app timezone).
        <strong>Marketing leads</strong> are not deleted; links to deleted tracking rows may show empty.
    </p>
    <form id="{{ $formId }}" method="post" action="{{ $actionUrl }}">
        @csrf
        @if($showPageSelector && $landingPageOptions && $landingPageOptions->count() > 0)
            <div class="form-group">
                <label for="{{ $formId }}_lpid">Landing page</label>
                <select name="landing_page_id" id="{{ $formId }}_lpid" class="form-control">
                    <option value="">All landing pages</option>
                    @foreach($landingPageOptions as $opt)
                        <option value="{{ $opt->id }}">{{ $opt->name }} ({{ $opt->slug }})</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="form-group">
            <label for="{{ $formId }}_scope">Clear by</label>
            <select name="scope" id="{{ $formId }}_scope" class="form-control">
                <option value="day">Single day</option>
                <option value="month">Whole month</option>
                <option value="year">Whole year</option>
            </select>
        </div>
        <div class="form-group lp-clear-pane" data-pane="day">
            <label for="{{ $formId }}_day">Day</label>
            <input type="date" name="day" id="{{ $formId }}_day" class="form-control" value="{{ $d }}">
        </div>
        <div class="form-group lp-clear-pane" data-pane="month" style="display:none;">
            <label for="{{ $formId }}_month">Month</label>
            <input type="month" name="month" id="{{ $formId }}_month" class="form-control" value="{{ $m }}">
        </div>
        <div class="form-group lp-clear-pane" data-pane="year" style="display:none;">
            <label for="{{ $formId }}_year">Year</label>
            <input type="number" name="year" id="{{ $formId }}_year" class="form-control" value="{{ $y }}" min="2000" max="2100">
        </div>
        <div class="form-group form-check">
            <input type="checkbox" name="confirm" value="1" id="{{ $formId }}_confirm" class="form-check-input" required>
            <label class="form-check-label" for="{{ $formId }}_confirm">I understand this will permanently delete matching tracking events.</label>
        </div>
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-eraser mr-1" aria-hidden="true"></i> Clear data
        </button>
    </form>
</div>
<script>
(function() {
    var form = document.getElementById(@json($formId));
    if (!form) return;
    var scope = form.querySelector('select[name="scope"]');
    if (!scope) return;
    function sync() {
        var v = scope.value;
        form.querySelectorAll('.lp-clear-pane').forEach(function(el) {
            el.style.display = el.getAttribute('data-pane') === v ? 'block' : 'none';
        });
    }
    scope.addEventListener('change', sync);
    sync();
})();
</script>
