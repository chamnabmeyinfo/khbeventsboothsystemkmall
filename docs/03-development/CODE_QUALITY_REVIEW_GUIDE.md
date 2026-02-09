# Code Quality Review Guide

**Generated:** February 10, 2026  
**Project:** KHB Events Booth Booking System  
**Framework:** Laravel 10.x

---

## Overview

This guide provides recommendations for code quality review tools and approaches for your Laravel project. After completing Priority 1 refactoring, it's time to establish automated code quality checks.

---

## Recommended Tools for Laravel Projects

### 🏆 **Best Choice: PHPStan + Laravel Pint**

**Why PHPStan?**
- ✅ Industry standard for PHP static analysis
- ✅ Excellent Laravel support
- ✅ Catches bugs before runtime
- ✅ Free and open-source
- ✅ Integrates with CI/CD
- ✅ Can be configured incrementally (levels 0-9)

**Why Laravel Pint?**
- ✅ Already installed in your project (`laravel/pint`)
- ✅ Laravel's official code formatter
- ✅ Based on PHP-CS-Fixer
- ✅ Zero configuration needed
- ✅ PSR-12 compliant

---

## Tool Comparison

| Tool | Type | Best For | Cost | Setup Complexity |
|------|------|----------|------|------------------|
| **PHPStan** | Static Analysis | Finding bugs, type errors | Free | Medium |
| **Psalm** | Static Analysis | Type checking, security | Free | Medium |
| **Laravel Pint** | Code Formatter | Code style, PSR-12 | Free | Easy (already installed) |
| **PHP CS Fixer** | Code Formatter | Code style | Free | Medium |
| **SonarQube** | Full Platform | Enterprise code quality | Free/Paid | High |
| **CodeClimate** | Full Platform | GitHub integration | Free/Paid | Medium |
| **PHPUnit** | Testing | Unit/Feature tests | Free | Easy (already installed) |

---

## Recommended Setup: PHPStan + Laravel Pint

### Step 1: Install PHPStan

```bash
composer require --dev phpstan/phpstan phpstan/extension-installer
composer require --dev larastan/larastan
```

### Step 2: Create PHPStan Configuration

Create `phpstan.neon` in project root:

```neon
includes:
    - ./vendor/larastan/larastan/extension.neon

parameters:
    paths:
        - app
        - config
        - database
        - routes

    # Rule level (0-9, start with 5 for Laravel projects)
    level: 5

    ignoreErrors:
        - '#PHPDoc tag @var#'

    excludePaths:
        - ./*/*/FileToIgnore.php

    checkMissingIterableValueType: false
```

### Step 3: Configure Laravel Pint

Create `pint.json` in project root (optional, uses defaults):

```json
{
    "preset": "laravel",
    "rules": {
        "array_syntax": {
            "syntax": "short"
        },
        "binary_operator_spaces": {
            "default": "single_space"
        }
    }
}
```

### Step 4: Add Composer Scripts

Add to `composer.json`:

```json
"scripts": {
    "analyse": "vendor/bin/phpstan analyse",
    "format": "vendor/bin/pint",
    "test": "phpunit",
    "quality": [
        "@format",
        "@analyse",
        "@test"
    ]
}
```

### Step 5: Run Quality Checks

```bash
# Format code
composer format

# Analyze code
composer analyse

# Run all quality checks
composer quality
```

---

## Alternative: Psalm (More Strict)

If you want stricter type checking:

```bash
composer require --dev vimeo/psalm psalm/plugin-laravel
```

Create `psalm.xml`:

```xml
<?xml version="1.0"?>
<psalm
    errorLevel="5"
    resolveFromConfigFile="true"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns="https://getpsalm.org/schema/config"
    xsi:schemaLocation="https://getpsalm.org/schema/config vendor/vimeo/psalm/config.xsd"
>
    <projectFiles>
        <directory name="app" />
        <directory name="config" />
        <directory name="database" />
        <directory name="routes" />
    </projectFiles>

    <plugins>
        <pluginClass class="Psalm\LaravelPlugin\Plugin"/>
    </plugins>
</psalm>
```

---

## Enterprise Solution: SonarQube

**Best for:** Large teams, enterprise projects, comprehensive reporting

### Setup Steps:

1. **Install SonarQube Server** (Docker recommended)
```bash
docker run -d --name sonarqube -p 9000:9000 sonarqube:lts-community
```

2. **Install SonarScanner**
```bash
composer require --dev sonar-scanner
```

3. **Create `sonar-project.properties`**
```properties
sonar.projectKey=khbevents-booth-system
sonar.sources=app,config,database,routes
sonar.php.tests.reportPath=reports/phpunit.xml
sonar.exclusions=**/vendor/**,**/node_modules/**
```

4. **Run Analysis**
```bash
sonar-scanner
```

**Pros:**
- Comprehensive dashboard
- Historical tracking
- Security vulnerability detection
- Code coverage integration
- Team collaboration features

**Cons:**
- Requires server setup
- More complex configuration
- Resource intensive

---

## GitHub Integration: CodeClimate

**Best for:** GitHub projects, automated PR reviews

### Setup:

1. **Sign up at codeclimate.com**
2. **Connect GitHub repository**
3. **Add `.codeclimate.yml`**:

```yaml
version: "2"
plugins:
  phpmd:
    enabled: true
  duplication:
    enabled: true
    config:
      languages:
        - php
exclude_paths:
  - "vendor/"
  - "node_modules/"
  - "tests/"
```

**Pros:**
- GitHub integration
- Automated PR comments
- Free for open source
- Easy setup

**Cons:**
- Limited free tier
- Less control than SonarQube

---

## AI-Powered Code Review

### Option 1: Use Current AI Assistant (Composer)

**Pros:**
- ✅ Already available
- ✅ Context-aware
- ✅ Can review specific files/methods
- ✅ Provides explanations
- ✅ Can suggest fixes

**Best for:**
- Reviewing specific code sections
- Understanding complex logic
- Getting explanations
- Quick quality checks

**How to use:**
- Ask: "Review this code for quality issues"
- Ask: "Check this method for bugs"
- Ask: "Analyze code quality of [file/class]"

### Option 2: GitHub Copilot / Cursor AI

**Pros:**
- Real-time suggestions
- Context-aware
- Integrated in IDE

**Cons:**
- Requires subscription
- May not catch all issues

---

## Recommended Approach for Your Project

### Phase 1: Quick Setup (Recommended Now)

1. **Install PHPStan + Larastan**
   ```bash
   composer require --dev phpstan/phpstan phpstan/extension-installer larastan/larastan
   ```

2. **Create `phpstan.neon`** (use config above)

3. **Run initial analysis**
   ```bash
   vendor/bin/phpstan analyse
   ```

4. **Fix critical issues first** (Level 5)

5. **Gradually increase level** (6 → 7 → 8 → 9)

### Phase 2: Code Formatting

1. **Run Laravel Pint** (already installed)
   ```bash
   vendor/bin/pint
   ```

2. **Format entire codebase**
   ```bash
   vendor/bin/pint --test  # Dry run
   vendor/bin/pint          # Apply changes
   ```

### Phase 3: CI/CD Integration

Add to GitHub Actions (`.github/workflows/quality.yml`):

```yaml
name: Code Quality

on: [push, pull_request]

jobs:
  quality:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install Dependencies
        run: composer install
      - name: Run Pint
        run: vendor/bin/pint --test
      - name: Run PHPStan
        run: vendor/bin/phpstan analyse
      - name: Run Tests
        run: vendor/bin/phpunit
```

---

## Code Quality Metrics to Track

### 1. **Static Analysis Metrics**
- Type errors found
- Unused variables
- Dead code
- Security vulnerabilities
- Code complexity

### 2. **Code Style Metrics**
- PSR-12 compliance
- Consistent formatting
- Naming conventions
- Documentation coverage

### 3. **Architecture Metrics**
- Cyclomatic complexity
- Class coupling
- Method length
- Class size
- Dependency depth

### 4. **Test Coverage**
- Line coverage
- Branch coverage
- Function coverage
- Class coverage

---

## Quick Start Commands

```bash
# Install PHPStan
composer require --dev phpstan/phpstan phpstan/extension-installer larastan/larastan

# Create PHPStan config
# (Copy config from above)

# Run analysis
vendor/bin/phpstan analyse

# Format code
vendor/bin/pint

# Run tests
vendor/bin/phpunit

# All-in-one quality check
composer quality  # (after adding scripts)
```

---

## My Recommendation for Your Project

### **Start with PHPStan + Laravel Pint**

**Why:**
1. ✅ PHPStan is the industry standard
2. ✅ Laravel Pint is already installed
3. ✅ Free and open-source
4. ✅ Easy to integrate
5. ✅ Can start at level 5 (moderate strictness)
6. ✅ Catches real bugs
7. ✅ Great Laravel support via Larastan

**Next Steps:**
1. Install PHPStan + Larastan
2. Create `phpstan.neon` config
3. Run initial analysis
4. Fix critical issues
5. Gradually increase strictness level
6. Add to CI/CD pipeline

---

## Need Help?

I can help you:
1. ✅ Set up PHPStan configuration
2. ✅ Review specific code files
3. ✅ Create quality check scripts
4. ✅ Set up CI/CD integration
5. ✅ Review code quality issues

Just ask: "Set up PHPStan for code quality review" or "Review code quality of [specific file/class]"

---

**Last Updated:** February 10, 2026  
**Status:** Ready for Implementation
