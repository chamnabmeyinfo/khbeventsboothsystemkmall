import sys
import re
import os

def upgrade_view(file_path):
    if not os.path.exists(file_path):
        return

    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Change layout extension
    content = content.replace("@extends('layouts.adminlte')", "@extends('layouts.admin')")
    
    # Check if already upgraded
    if 'ios-dashboard-mode' in content:
        return

    push_styles = """
@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=2.6">
<style>
    .looker-dashboard { padding: 0 !important; }
    .glass-card {
        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(40px) saturate(180%);
        -webkit-backdrop-filter: blur(40px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 24px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        margin-bottom: 24px;
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }
    .glass-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.55);
        box-shadow: 0 15px 45px rgba(31, 38, 135, 0.2);
    }
    .kpi-card-looker {
        margin-bottom: 24px;
    }
</style>
@endpush

@push('body-class', 'ios-dashboard-mode')
"""

    if "@push('styles')" in content:
        # Replace the existing style block or just prepend
        content = re.sub(r"@push\('styles'\).*?@endpush", push_styles, content, flags=re.DOTALL)
    else:
        # Prepend after extends
        if "@extends" in content:
            lines = content.split('\n')
            new_lines = []
            for line in lines:
                new_lines.append(line)
                if '@extends' in line:
                    new_lines.append(push_styles)
            content = '\n'.join(new_lines)
        else:
            content = push_styles + content

    # Replace standard cards with glass-cards and looker components
    content = content.replace('card kpi-card primary', 'kpi-card-looker')
    content = content.replace('card kpi-card success', 'kpi-card-looker')
    content = content.replace('card kpi-card warning', 'kpi-card-looker')
    content = content.replace('card kpi-card info', 'kpi-card-looker')
    content = content.replace('class="card mb-4"', 'class="glass-card mb-4"')
    content = content.replace('class="card"', 'class="glass-card"')
    content = content.replace('class="card-header"', 'class="p-4 border-bottom"')
    content = content.replace('class="card-body"', 'class="p-4"')
    content = content.replace('class="card-footer"', 'class="p-3 border-top bg-transparent"')
    content = content.replace('class="card-title"', 'class="h5 fw-bold mb-0 text-dark"')
    
    # Update KPI labels
    content = content.replace('kpi-label', 'kpi-title')
    content = content.replace('kpi-value', 'kpi-value-looker')

    # Wrap the whole content in a .looker-dashboard container for proper spacing
    if "@section('content')" in content:
        content = content.replace("@section('content')", "@section('content')\n<div class='looker-dashboard'>")
        content = content.replace("@endsection", "</div>\n@endsection")

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Successfully upgraded {file_path}")

# Run for more views
views = [
    'resources/views/hr/dashboard.blade.php',
    'resources/views/finance/dashboard.blade.php',
    'resources/views/clients/show.blade.php',
    'resources/views/clients/edit.blade.php',
    'resources/views/booths/show.blade.php',
    'resources/views/booths/edit.blade.php',
    'resources/views/books/show.blade.php',
    'resources/views/books/edit.blade.php'
]

for v in views:
    upgrade_view(v)
