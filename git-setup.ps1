# Git Setup Script
# This script configures git to use credentials from .env file

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
$repoUrl = $envVars['GITHUB_REPO_URL']
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

# Change to project directory
Set-Location $PSScriptRoot

# Configure git remote
if ($useSSH) {
    # Use SSH
    if ([string]::IsNullOrWhiteSpace($repoUrl)) {
        $repoUrl = git remote get-url origin 2>$null
    }
    if ($repoUrl) {
        $sshUrl = $repoUrl -replace 'https://github.com/', 'git@github.com:' -replace '\.git$', '.git'
        git remote set-url origin $sshUrl
        Write-Host "Configured git remote to use SSH: $sshUrl" -ForegroundColor Green
    }
} else {
    # Use HTTPS with credentials
    if ([string]::IsNullOrWhiteSpace($repoUrl)) {
        $repoUrl = git remote get-url origin 2>$null
    }
    if ($repoUrl) {
        # Remove existing credentials from URL if present
        $cleanUrl = $repoUrl -replace 'https://[^@]+@', 'https://'
        $urlWithAuth = $cleanUrl -replace 'https://', "https://${githubUsername}:${githubToken}@"
        git remote set-url origin $urlWithAuth
        Write-Host "Configured git remote with credentials from .env" -ForegroundColor Green
        Write-Host "Remote URL configured (credentials hidden for security)" -ForegroundColor Gray
    }
}

# Configure git user (optional, if not already set)
$gitUser = git config user.name
$gitEmail = git config user.email

if ([string]::IsNullOrWhiteSpace($gitUser)) {
    Write-Host "`nGit user.name is not set. Would you like to set it to $githubUsername? (Y/N)" -ForegroundColor Yellow
    $response = Read-Host
    if ($response -eq 'Y' -or $response -eq 'y') {
        git config user.name $githubUsername
        Write-Host "Set git user.name to $githubUsername" -ForegroundColor Green
    }
}

if ([string]::IsNullOrWhiteSpace($gitEmail)) {
    Write-Host "`nGit user.email is not set. Please enter your email:" -ForegroundColor Yellow
    $email = Read-Host
    if (-not [string]::IsNullOrWhiteSpace($email)) {
        git config user.email $email
        Write-Host "Set git user.email to $email" -ForegroundColor Green
    }
}

Write-Host "`nGit setup complete!" -ForegroundColor Green
Write-Host "You can now use: .\git-push.ps1 -Message 'Your commit message'" -ForegroundColor Cyan
