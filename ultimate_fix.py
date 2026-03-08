import os
import re

files = [
    'resources/views/booths/index.blade.php', 
    'resources/views/booths/management.blade.php', 
    'resources/views/booths/my-booths.blade.php', 
    'resources/views/booths/show.blade.php'
]

def ultimate_fix(path):
    if not os.path.exists(path): return
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 1. Ensure the PREMIUM CSS is linked at the top of the HEAD section
    if 'booths-premium-glamor.css' not in content:
        link = '<link rel="stylesheet" href="{{ asset(\'css/booths-premium-glamor.css\') }}?v=1.2">'
        content = content.replace("@push('styles')", f"@push('styles')\\n{link}")
    else:
        content = content.replace('css/booths-premium-glamor.css?v=1.0', 'css/booths-premium-glamor.css?v=1.2')
        content = content.replace('css/booths-premium-glamor.css?v=1.1', 'css/booths-premium-glamor.css?v=1.2')

    # 2. Fix text colors (User wants Dark Gray #1d1d1f for text on glass)
    # The script previously changed it to text-dark-gray, which is our custom class
    content = content.replace('text-white', 'text-dark-gray')
    content = content.replace('text-dark', 'text-dark-gray')
    
    # 3. Clean up any remaining blocky card-header backgrounds
    content = content.replace('card-header', 'p-4 border-bottom bg-transparent')
    content = content.replace('card-body', 'p-4')

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Ultimately fixed {path}")

for f in files:
    ultimate_fix(f)
