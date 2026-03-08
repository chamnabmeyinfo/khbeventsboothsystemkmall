import os
import re

files = [
    'resources/views/booths/index.blade.php', 
    'resources/views/booths/management.blade.php', 
    'resources/views/booths/my-booths.blade.php', 
    'resources/views/booths/show.blade.php',
    'resources/views/booths/public-view.blade.php'
]

def polish_file(path):
    if not os.path.exists(path): return
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 1. Fix recursive class names
    content = content.replace('text-dark-gray-gray', 'text-dark-gray')
    
    # 2. Fix double wrappers
    content = re.sub(r"<div class='looker-dashboard'>\s*<div class='looker-dashboard'>", "<div class='looker-dashboard'>", content)
    
    # 3. Ensure layouts.admin is used (except public view)
    if 'public-view.blade.php' not in path:
        content = content.replace("@extends('layouts.app')", "@extends('layouts.admin')")
        content = content.replace("@extends('layouts.adminlte')", "@extends('layouts.admin')")
    
    # 4. Handle Public View specially
    if 'public-view.blade.php' in path:
        content = content.replace('background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);', 
                                  'background: rgba(255, 255, 255, 0.45); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.5);')
        content = content.replace('color: white;', 'color: #1d1d1f;')
        
        if 'ios-dashboard-mode' not in content:
             content = content.replace('<body', '<body class="ios-dashboard-mode"')
             if '</style>' in content:
                 content = content.replace('</style>', """
        body.ios-dashboard-mode {
            background-color: #ffb8d2 !important;
            background-image: 
                radial-gradient(at 0% 0%, #4facfe 0px, transparent 50%),
                radial-gradient(at 100% 0%, #00f2fe 0px, transparent 50%),
                radial-gradient(at 100% 100%, #f093fb 0px, transparent 50%),
                radial-gradient(at 0% 100%, #f5576c 0px, transparent 50%),
                radial-gradient(at 50% 50%, #5ee7df 0px, transparent 50%) !important;
            background-attachment: fixed !important;
            background-size: 200% 200% !important;
            animation: gradientMove 15s ease infinite !important;
        }
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>""")

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Polished {path}")

for f in files:
    polish_file(f)
