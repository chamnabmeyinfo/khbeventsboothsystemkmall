import os
import re

def upgrade_file(file_path):
    if not os.path.exists(file_path):
        print(f"File {file_path} not found")
        return

    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Update Layout
    content = content.replace("@extends('layouts.app')", "@extends('layouts.admin')")
    content = content.replace("@extends('layouts.adminlte')", "@extends('layouts.admin')")
    
    # 2. Add glass mode push and extra styles
    # We do a targeted injection after the title section
    if "@push('body-class'" not in content:
        injection = """
@push('body-class', 'ios-dashboard-mode')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=2.6">
<style>
    .looker-dashboard { padding: 0 !important; max-width: 100% !important; background: transparent !important; }
    .glass-card {
        background: rgba(255, 255, 255, 0.45) !important;
        backdrop-filter: blur(40px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(40px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
        border-radius: 24px !important;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15) !important;
        margin-bottom: 24px !important;
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1) !important;
        overflow: hidden !important;
    }
    .glass-card:hover {
        transform: translateY(-5px) !important;
        background: rgba(255, 255, 255, 0.55) !important;
        box-shadow: 0 15px 45px rgba(31, 38, 135, 0.2) !important;
    }
    .text-dark-gray { color: #1d1d1f !important; }
    
    /* Designer specific fixes */
    .designer-toolbar {
        background: rgba(255, 255, 255, 0.5) !important;
        backdrop-filter: blur(24px) !important;
        border-radius: 16px !important;
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
        color: #1d1d1f !important;
    }
    .designer-toolbar button { color: #1d1d1f !important; border-radius: 8px !important; }
    .designer-toolbar button.active { background: #6366f1 !important; color: white !important; }
    
    .controls-panel {
        background: rgba(255, 255, 255, 0.5) !important;
        backdrop-filter: blur(24px) !important;
        border-radius: 20px !important;
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
    }
</style>
@endpush
"""
        # Find where to inject - after the title or breadcrumb section
        if "@section('breadcrumb')" in content:
            content = content.replace("@section('breadcrumb')", "@section('breadcrumb')" + injection)
        elif "@section('title')" in content:
            # We need to find the end of the title section
            content = re.sub(r"(@section\('title'.*?\))", r"\1" + injection, content, flags=re.DOTALL)
        else:
            # Fallback to after extends
            content = content.replace("@extends('layouts.admin')", "@extends('layouts.admin')" + injection)

    # 3. Content Wrapper - Avoid double wrapping if already done
    if "<div class='looker-dashboard'>" not in content:
        content = content.replace("@section('content')", "@section('content')\n<div class='looker-dashboard'>")
        # Find the last @endsection and replace it
        parts = content.rsplit("@endsection", 1)
        if len(parts) == 2:
            content = parts[0] + "</div>\n@endsection" + parts[1]
    
    # 4. Global replacements
    # Using regex to match class="card" or class="card mb-3" etc.
    content = re.sub(r'class=(\"|\')card(\s+.*?)?(\"|\')', r'class=\1glass-card\2\1', content)
    
    # Update status labels to dark text for glass contrast
    content = content.replace('text-dark', 'text-dark-gray')
    content = content.replace('text-primary', 'text-dark-gray')
    
    # Tables
    content = content.replace('table-responsive', 'looker-table-container')
    content = re.sub(r'class=(\"|\')table(\s+.*?)?(\"|\')', r'class=\1looker-table\2\1', content)

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Successfully upgraded {file_path}")

# Run for both files
upgrade_file('resources/views/booths/index.blade.php')
upgrade_file('resources/views/booths/management.blade.php')
upgrade_file('resources/views/booths/my-booths.blade.php')
upgrade_file('resources/views/booths/show.blade.php')
