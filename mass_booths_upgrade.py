import sys
import re
import os

def upgrade_view_comprehensive(file_path):
    if not os.path.exists(file_path):
        print(f"File {file_path} not found")
        return

    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Layout switch
    content = content.replace("@extends('layouts.app')", "@extends('layouts.admin')")
    content = content.replace("@extends('layouts.adminlte')", "@extends('layouts.admin')")
    
    # Body class push
    if 'ios-dashboard-mode' not in content:
        if "@extends" in content:
            content = content.replace("@extends('layouts.admin')", "@extends('layouts.admin')\n@push('body-class', 'ios-dashboard-mode')")
    
    # CSS Upgrade
    css_push = """
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
    .modern-page-header {
        background: rgba(255, 255, 255, 0.3) !important;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
        border-radius: 24px !important;
        color: #1d1d1f !important;
    }
    .modern-page-header h2 { color: #1d1d1f !important; }
    .modern-stat-card {
        background: rgba(255, 255, 255, 0.45) !important;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
        border-radius: 24px !important;
    }
</style>
@endpush
"""
    if "@push('styles')" not in content:
        if "@section('content')" in content:
            content = content.replace("@section('content')", css_push + "\n@section('content')")
    
    # Replace the "management" specific header background
    content = content.replace("background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%) !important;", 
                             "background: rgba(255, 255, 255, 0.3) !important; backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.5) !important;")
    
    # Table logic
    content = content.replace("table table-hover", "looker-table")
    content = content.replace("table-responsive", "looker-table-container")
    
    # Content wrapper logic (removing specific margin overrides so layouts.admin takes over)
    content = re.sub(r'\.content-wrapper\s*\{[^}]*\}', '', content, flags=re.DOTALL)
    
    # Wrap in looker-dashboard
    if "<div class='looker-dashboard'>" not in content:
        content = content.replace("@section('content')", "@section('content')\n<div class='looker-dashboard'>")
        content = content.replace("@endsection", "</div>\n@endsection")

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Successfully upgraded {file_path}")

files = [
    'resources/views/booths/management.blade.php',
    'resources/views/booths/index.blade.php',
    'resources/views/booths/public-view.blade.php'
]

for f in files:
    upgrade_view_comprehensive(f)
