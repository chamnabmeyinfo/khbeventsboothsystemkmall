import os
import re

files = [
    'resources/views/booths/index.blade.php', 
    'resources/views/booths/management.blade.php', 
    'resources/views/booths/my-booths.blade.php', 
    'resources/views/booths/show.blade.php'
]

def apply_glamor(path):
    if not os.path.exists(path): return
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 1. Inject the new CSS
    if 'booths-premium-glamor.css' not in content:
        glamor_css = '<link rel="stylesheet" href="{{ asset(\'css/booths-premium-glamor.css\') }}?v=1.0">'
        content = content.replace("@push('styles')", f"@push('styles')\\n{glamor_css}")
    
    # 2. Upgrade Buttons to Glass Style
    content = content.replace('btn-primary', 'btn-glass-primary')
    content = content.replace('btn-success', 'btn-glass-primary') # Standardize success to primary blue for clean look
    content = content.replace('btn-secondary', 'btn-glass-secondary')
    content = content.replace('btn-outline-secondary', 'btn-glass-secondary')
    content = content.replace('btn-info', 'btn-glass-secondary')

    # 3. Upgrade Form Inputs
    content = content.replace('form-control', 'glass-input')
    content = content.replace('form-select', 'glass-input')

    # 4. Clean up the "looker-dashboard" if it's messy
    content = content.replace('<div class="container-fluid mt-2 mb-2">', '<div class="container-fluid">')

    # 5. Global Table Lift
    if 'looker-table' in content:
        content = content.replace('background: rgba(255,255,255,0.2)', 'background: rgba(255,255,255,0.4)')

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Applied Glamor to {path}")

for f in files:
    apply_glamor(f)
