import sys
import os

def final_booths_fix():
    # 1. Update floor-plan-designer.css to support Glass Mode
    fpd_path = 'public/css/floor-plan-designer.css'
    if os.path.exists(fpd_path):
        with open(fpd_path, 'r', encoding='utf-8') as f:
            fpd_css = f.read()

        glass_fpd = """
/* ── Glass Mode Override ─────────────────────────────────── */
body.ios-dashboard-mode .floorplan-designer {
    background: rgba(255, 255, 255, 0.45) !important;
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.5) !important;
    border-radius: 24px !important;
    box-shadow: 0 12px 48px rgba(31, 38, 135, 0.15) !important;
    color: #1d1d1f !important;
    overflow: hidden;
}

body.ios-dashboard-mode .designer-toolbar,
body.ios-dashboard-mode .controls-panel,
body.ios-dashboard-mode .canvas-sidebar {
    background: rgba(255, 255, 255, 0.5) !important;
    backdrop-filter: blur(20px) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    border-radius: 16px !important;
    color: #1d1d1f !important;
}

body.ios-dashboard-mode .canvas-area {
    background: rgba(255, 255, 255, 0.3) !important;
    border-radius: 12px !important;
}

body.ios-dashboard-mode .tool-btn {
    color: #1d1d1f !important;
}

body.ios-dashboard-mode .tool-btn:hover {
    background: rgba(255, 255, 255, 0.6) !important;
}

body.ios-dashboard-mode .tool-btn.active {
    background: #6366f1 !important;
    color: white !important;
}

body.ios-dashboard-mode .canvas-loading-overlay {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(10px);
}
"""
        if 'Glass Mode Override' not in fpd_css:
            fpd_css += glass_fpd
            with open(fpd_path, 'w', encoding='utf-8') as f:
                f.write(fpd_css)

    # 2. Update booths files to use the combined layout and correct pushing
    files = ['resources/views/booths/index.blade.php', 'resources/views/booths/my-booths.blade.php']
    for v in files:
        if os.path.exists(v):
            with open(v, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Switch to layouts.admin for the animated background & sidebar support
            content = content.replace("@extends('layouts.app')", "@extends('layouts.admin')")
            content = content.replace("@extends('layouts.adminlte')", "@extends('layouts.admin')")
            
            # Ensure body class is pushed
            if 'ios-dashboard-mode' not in content:
                content = content.replace("@extends('layouts.admin')", "@extends('layouts.admin')\n@push('body-class', 'ios-dashboard-mode')")
            
            # Ensure CSS is versioned
            content = content.replace("floor-plan-designer.css')", "floor-plan-designer.css') }}?v=2.6")
            
            with open(v, 'w', encoding='utf-8') as f:
                f.write(content)

final_booths_fix()
