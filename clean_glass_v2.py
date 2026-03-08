import os
import re

files = [
    'resources/views/booths/index.blade.php', 
    'resources/views/booths/management.blade.php', 
    'resources/views/booths/my-booths.blade.php', 
    'resources/views/booths/show.blade.php'
]

def clean_inline_glass_v2(path):
    if not os.path.exists(path): return
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Aggressively remove any inline CSS related to glass-card
    content = re.sub(r'\.glass-card\s*(\:hover)?\s*\{[^\}]+\}', '', content)
    
    # Remove looker-dashboard inline style if it exists
    content = re.sub(r'\.looker-dashboard\s*\{[^\}]+\}', '', content)

    # Ensure v1.1 of the glamor CSS is linked to avoid caching
    content = content.replace('css/booths-premium-glamor.css?v=1.0', 'css/booths-premium-glamor.css?v=1.1')
    if 'booths-premium-glamor.css' not in content:
        glamor_link = '<link rel="stylesheet" href="{{ asset(\'css/booths-premium-glamor.css\') }}?v=1.1">'
        content = content.replace("@push('styles')", f"@push('styles')\\n{glamor_link}")

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Aggressively cleaned and upgraded {path}")

for f in files:
    clean_inline_glass_v2(f)
