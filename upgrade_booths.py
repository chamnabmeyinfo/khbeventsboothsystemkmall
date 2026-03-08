import sys
import re
import os

def upgrade_booths_view(file_path):
    if not os.path.exists(file_path):
        print(f"File {file_path} not found")
        return

    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Change layout extension to admin for full sidebar/header support if needed
    # (Checking if it should be layouts.app or layouts.admin)
    # The dashboard uses layouts.app or layouts.admin. 
    # Let's stick with the current extends but ensure styles are pushed.

    # 1. Update the inline glass styles to match our new premium standard
    # Looking for: style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.18); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);"
    pattern = r'style="background:\s*rgba\(255,\s*255,\s*255,\s*0\.95\);\s*backdrop-filter:\s*blur\(10px\);\s*border-radius:\s*12px;\s*border:\s*1px\s*solid\s*rgba\(255,\s*255,\s*255,\s*0\.18\);\s*box-shadow:\s*0\s*8px\s*32px\s*0\s*rgba\(31,\s*38,\s*135,\s*0\.37\);"'
    content = re.sub(pattern, 'class="glass-card mb-4"', content)

    # 2. Fix other card types
    content = content.replace('class="card mb-3"', 'class="glass-card mb-3"')
    content = content.replace('class="card-body"', 'class="p-4"')
    content = content.replace('class="card-header"', 'class="p-4 border-bottom"')
    
    # 3. Canvas and Toolbar upgrades
    content = content.replace('class="controls-panel"', 'class="controls-panel glass-card p-4"')
    content = content.replace('class="canvas-toolbar"', 'class="canvas-toolbar glass-card p-2 mb-3"')
    content = content.replace('id="canvasWrapper" class="canvas-wrapper"', 'id="canvasWrapper" class="canvas-wrapper glass-card"')
    
    # 4. Ensure body class is present
    if 'ios-dashboard-mode' not in content:
        if "@extends" in content:
            lines = content.split('\n')
            new_lines = []
            for line in lines:
                new_lines.append(line)
                if '@extends' in line:
                    new_lines.append("@push('body-class', 'ios-dashboard-mode')")
                    new_lines.append("@push('styles')")
                    new_lines.append("<link rel=\"stylesheet\" href=\"{{ asset('css/dashboard-looker.css') }}?v=2.6\">")
                    new_lines.append("@endpush")
            content = '\n'.join(new_lines)

    # 5. Fix titles
    content = content.replace('class="card-title"', 'class="h5 fw-bold mb-0 text-dark"')

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Successfully upgraded {file_path}")

upgrade_booths_view('resources/views/booths/index.blade.php')
upgrade_booths_view('resources/views/booths/my-booths.blade.php')
