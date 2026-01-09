# Organize Code - Move kmallxmas-laravel to Root
# This script moves all files from kmallxmas-laravel/ to root folder

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "Organizing Code for GitHub Push" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

$rootPath = $PSScriptRoot
$sourcePath = Join-Path $rootPath "kmallxmas-laravel"

# Check if source exists
if (-not (Test-Path $sourcePath)) {
    Write-Host "Error: kmallxmas-laravel folder not found!" -ForegroundColor Red
    exit 1
}

Write-Host "Source: $sourcePath" -ForegroundColor Gray
Write-Host "Destination: $rootPath" -ForegroundColor Gray
Write-Host ""

# Files/folders to preserve in root (won't be overwritten)
$preserveItems = @(
    ".git",
    ".cursor",
    ".env",
    ".gitignore",
    "git-push.ps1",
    "git-setup.ps1",
    "GITHUB_SETUP.md",
    "README-GITHUB.md",
    "MOVE-TO-ROOT.md",
    "move-to-root.sh",
    "ROOT-DEPLOYMENT.md",
    "organize-code.ps1"
)

# Items that should be replaced (newer version in kmallxmas-laravel)
$replaceItems = @(
    "app",
    "config",
    "database",
    "routes",
    "resources",
    "composer.json",
    "README.md"
)

Write-Host "Step 1: Backing up conflicting items..." -ForegroundColor Yellow

# Backup old files that will be replaced
$backupDir = Join-Path $rootPath "backup_old_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
if (Test-Path $backupDir) {
    Remove-Item $backupDir -Recurse -Force
}
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null

foreach ($item in $replaceItems) {
    $itemPath = Join-Path $rootPath $item
    if (Test-Path $itemPath) {
        Write-Host "  Backing up: $item" -ForegroundColor Gray
        Copy-Item -Path $itemPath -Destination $backupDir -Recurse -Force
    }
}

Write-Host ""
Write-Host "Step 2: Moving files from kmallxmas-laravel to root..." -ForegroundColor Yellow

# Get all items from source
$sourceItems = Get-ChildItem -Path $sourcePath -Force

$movedCount = 0
$skippedCount = 0

foreach ($item in $sourceItems) {
    $itemName = $item.Name
    $sourceItemPath = $item.FullName
    $destItemPath = Join-Path $rootPath $itemName
    
    # Skip if in preserve list
    if ($preserveItems -contains $itemName) {
        Write-Host "  Skipping (preserved): $itemName" -ForegroundColor Gray
        $skippedCount++
        continue
    }
    
    # If destination exists and is in replace list, remove it first
    if (Test-Path $destItemPath) {
        if ($replaceItems -contains $itemName) {
            Write-Host "  Replacing: $itemName" -ForegroundColor Yellow
            Remove-Item -Path $destItemPath -Recurse -Force
        } else {
            Write-Host "  Skipping (exists): $itemName" -ForegroundColor Gray
            $skippedCount++
            continue
        }
    }
    
    # Move the item
    Write-Host "  Moving: $itemName" -ForegroundColor Green
    Move-Item -Path $sourceItemPath -Destination $destItemPath -Force
    $movedCount++
}

Write-Host ""
Write-Host "Step 3: Cleaning up..." -ForegroundColor Yellow

# Remove empty kmallxmas-laravel folder
if (Test-Path $sourcePath) {
    $remainingItems = Get-ChildItem -Path $sourcePath -Force
    if ($remainingItems.Count -eq 0) {
        Remove-Item -Path $sourcePath -Force
        Write-Host "  Removed empty kmallxmas-laravel folder" -ForegroundColor Green
    } else {
        Write-Host "  Warning: kmallxmas-laravel folder still contains items:" -ForegroundColor Yellow
        foreach ($item in $remainingItems) {
            Write-Host "    - $($item.Name)" -ForegroundColor Gray
        }
    }
}

Write-Host ""
Write-Host "Step 4: Setting permissions..." -ForegroundColor Yellow

# Set storage permissions
$storagePath = Join-Path $rootPath "storage"
if (Test-Path $storagePath) {
    Get-ChildItem -Path $storagePath -Recurse | ForEach-Object {
        $_.Attributes = $_.Attributes -bor [System.IO.FileAttributes]::Normal
    }
    Write-Host "  Set storage permissions" -ForegroundColor Green
}

$bootstrapCachePath = Join-Path $rootPath "bootstrap\cache"
if (Test-Path $bootstrapCachePath) {
    Get-ChildItem -Path $bootstrapCachePath -Recurse | ForEach-Object {
        $_.Attributes = $_.Attributes -bor [System.IO.FileAttributes]::Normal
    }
    Write-Host "  Set bootstrap/cache permissions" -ForegroundColor Green
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "Organization Complete!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Summary:" -ForegroundColor Cyan
Write-Host "  Moved: $movedCount items" -ForegroundColor Green
Write-Host "  Skipped: $skippedCount items" -ForegroundColor Yellow
Write-Host "  Backup: $backupDir" -ForegroundColor Gray
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "  1. Review the changes" -ForegroundColor White
Write-Host "  2. Test the application locally" -ForegroundColor White
Write-Host "  3. Commit and push to GitHub" -ForegroundColor White
Write-Host ""
Write-Host "Backup location: $backupDir" -ForegroundColor Gray
Write-Host "  (You can delete this after verifying everything works)" -ForegroundColor Gray
Write-Host ""
