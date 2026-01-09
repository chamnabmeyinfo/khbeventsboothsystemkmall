# Git Push Helper Script
# This script reads GitHub credentials from .env and pushes to GitHub

param(
    [string]$Message = "Update from local",
    [string]$Branch = "main"
)

# Load .env file
$envFile = Join-Path $PSScriptRoot ".env"
if (-not (Test-Path $envFile)) {
    Write-Host "Error: .env file not found!" -ForegroundColor Red
    Write-Host "Please copy .env.example to .env and fill in your credentials." -ForegroundColor Yellow
    exit 1
}

# Parse .env file
$envVars = @{}
Get-Content $envFile | ForEach-Object {
    if ($_ -match '^\s*([^#][^=]*?)\s*=\s*(.*)$') {
        $key = $matches[1].Trim()
        $value = $matches[2].Trim()
        $envVars[$key] = $value
    }
}

# Get GitHub credentials
$githubUsername = $envVars['GITHUB_USERNAME']
$githubToken = $envVars['GITHUB_TOKEN']
$useSSH = $envVars['GITHUB_USE_SSH'] -eq 'true'

# Validate credentials
if ([string]::IsNullOrWhiteSpace($githubUsername)) {
    Write-Host "Error: GITHUB_USERNAME not set in .env file!" -ForegroundColor Red
    exit 1
}

if (-not $useSSH -and [string]::IsNullOrWhiteSpace($githubToken)) {
    Write-Host "Error: GITHUB_TOKEN not set in .env file!" -ForegroundColor Red
    Write-Host "Generate a token at: https://github.com/settings/tokens" -ForegroundColor Yellow
    exit 1
}

# Get repository URL
$repoUrl = $envVars['GITHUB_REPO_URL']
if ([string]::IsNullOrWhiteSpace($repoUrl)) {
    # Try to get from git remote
    $remoteUrl = git remote get-url origin 2>$null
    if ($remoteUrl) {
        $repoUrl = $remoteUrl
    } else {
        Write-Host "Error: Could not determine repository URL!" -ForegroundColor Red
        exit 1
    }
}

# Configure remote URL with credentials if using HTTPS
if (-not $useSSH) {
    # Update remote URL to include credentials
    $urlWithAuth = $repoUrl -replace 'https://', "https://${githubUsername}:${githubToken}@"
    git remote set-url origin $urlWithAuth
    Write-Host "Configured git remote with credentials from .env" -ForegroundColor Green
}

# Change to project directory
Set-Location $PSScriptRoot

# Check git status
Write-Host "`nChecking git status..." -ForegroundColor Cyan
$status = git status --porcelain
if ([string]::IsNullOrWhiteSpace($status)) {
    Write-Host "No changes to commit." -ForegroundColor Yellow
    exit 0
}

# Show status
git status

# Add all changes
Write-Host "`nStaging all changes..." -ForegroundColor Cyan
git add -A

# Commit changes
Write-Host "Committing changes with message: $Message" -ForegroundColor Cyan
git commit -m $Message

# Push to GitHub
Write-Host "`nPushing to GitHub (branch: $Branch)..." -ForegroundColor Cyan
git push origin $Branch

if ($LASTEXITCODE -eq 0) {
    Write-Host "`nSuccessfully pushed to GitHub!" -ForegroundColor Green
} else {
    Write-Host "`nError: Failed to push to GitHub!" -ForegroundColor Red
    exit 1
}
