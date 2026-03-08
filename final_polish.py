import os
import re

files = [
    'resources/views/booths/index.blade.php', 
    'resources/views/booths/management.blade.php', 
    'resources/views/booths/my-booths.blade.php', 
    'resources/views/booths/show.blade.php'
]

def final_polish(path):
    if not os.path.exists(path): return
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 1. Fix the newline escape characters (\n)
    content = content.replace('\\n', '\n')
    
    # 2. Add soft shadows via global utility if missing
    if 'soft-glass-shadow' not in content:
        shadow_style = """
<style>
    .glass-card, .looker-table, .designer-toolbar, .controls-panel {
        box-shadow: 0 10px 40px rgba(0,0,0,0.08), 0 1px 1px rgba(255,255,255,0.3) !important;
    }
    .btn-glass-primary {
        box-shadow: 0 4px 14px 0 rgba(0, 118, 255, 0.39) !important;
    }
</style>
"""
        content = content.replace("</head>", f"{shadow_style}\n</head>") if "</head>" in content else content + shadow_style

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)

for f in files:
    final_polish(f)
print("Final Polish Complete.")
