import os
import re

files = [
    'resources/views/booths/index.blade.php', 
    'resources/views/booths/management.blade.php', 
    'resources/views/booths/my-booths.blade.php', 
    'resources/views/booths/show.blade.php'
]

def clean_inline_glass(path):
    if not os.path.exists(path): return
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Remove the .glass-card { ... } block from inline styles
    # Usually it looks like this:
    # .glass-card {
    #     background: ... !important;
    #     ...
    # }
    content = re.sub(r'\.glass-card\s*\{[^\}]+\}', '', content)
    
    # Also remove any redundant dashboard-looker overrides if they differ from our new premium glamor
    content = re.sub(r'\.looker-dashboard\s*\{[^\}]+\}', '', content)

    # Ensure the link to premium glamor is at the top of styles
    if 'booths-premium-glamor.css' not in content:
        glamor_link = '<link rel="stylesheet" href="{{ asset(\'css/booths-premium-glamor.css\') }}?v=1.1">'
        content = content.replace("@push('styles')", f"@push('styles')\\n{glamor_link}")

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Cleaned and upgraded {path}")

for f in files:
    clean_inline_glass(f)
