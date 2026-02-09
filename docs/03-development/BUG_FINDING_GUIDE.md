# Bug Finding Guide - Agent Mode vs Debug Mode

**Created:** February 10, 2026  
**Project:** KHB Events Booth Booking System  
**Framework:** Laravel 10.x

---

## Quick Answer

### For Bug Finding: **Use Agent Mode** ✅

**Why Agent Mode?**
- ✅ Can analyze multiple files simultaneously
- ✅ Can understand code relationships across files
- ✅ Can identify patterns and systemic issues
- ✅ Can suggest fixes and even implement them
- ✅ Better for comprehensive code review

**When to Use Debug Mode:**
- 🔍 When you have a **specific error** to investigate
- 🔍 When debugging a **known issue** step-by-step
- 🔍 When you need **detailed execution flow** analysis
- 🔍 When testing **specific scenarios**

---

## Model Selection for Bug Finding

### 🏆 **Best Choice: Claude Sonnet 4.5 or GPT-4**

**Why These Models?**
- ✅ Excellent code analysis capabilities
- ✅ Strong understanding of Laravel/PHP
- ✅ Can identify logical errors, type issues, security vulnerabilities
- ✅ Good at understanding context across files
- ✅ Can suggest comprehensive fixes

### Model Comparison for Bug Finding

| Model | Best For | Strengths | Limitations |
|-------|----------|-----------|-------------|
| **Claude Sonnet 4.5** | 🏆 **Best Overall** | Excellent code analysis, understands context, suggests fixes | May be slower for very large codebases |
| **GPT-4** | 🏆 **Best Alternative** | Strong reasoning, good at finding edge cases | May miss some Laravel-specific patterns |
| **GPT-4 Turbo** | Fast Analysis | Quick responses, good coverage | May be less thorough than Sonnet 4.5 |
| **Claude Opus** | Deep Analysis | Most thorough analysis | Slower, more expensive |
| **GPT-3.5** | Quick Checks | Fast, cost-effective | Less thorough, may miss complex bugs |

---

## Recommended Workflow

### Option 1: Comprehensive Bug Finding (Recommended)

**Mode:** Agent Mode  
**Model:** Claude Sonnet 4.5 or GPT-4  
**Approach:** Systematic code review

**Steps:**
1. **Ask:** "Find all bugs in [specific file/class/module]"
2. **Or:** "Review [BoothController.php] for bugs, security issues, and potential errors"
3. **Or:** "Analyze the entire codebase for common bugs and issues"

**Example Prompts:**
```
"Find all bugs in BoothController.php. Check for:
- Type errors
- Null pointer exceptions
- SQL injection vulnerabilities
- Logic errors
- Race conditions
- Missing error handling"
```

```
"Review all service classes for bugs. Focus on:
- Transaction handling
- Error handling
- Type safety
- Edge cases"
```

### Option 2: Specific Error Investigation

**Mode:** Debug Mode  
**Model:** Claude Sonnet 4.5 or GPT-4  
**Approach:** Step-by-step debugging

**When to Use:**
- You have a specific error message
- A feature is not working as expected
- You need to trace execution flow

**Example Prompts:**
```
"Debug why bookings are not being created. 
Error: [paste error message]
File: BookController.php line 150"
```

---

## Best Practices for Bug Finding

### 1. **Start with Static Analysis Tools** (Recommended First Step)

**Why:** Automated tools catch obvious issues quickly

**Tools:**
```bash
# Install PHPStan (we discussed this earlier)
composer require --dev phpstan/phpstan phpstan/extension-installer larastan/larastan

# Run analysis
vendor/bin/phpstan analyse
```

**Benefits:**
- Catches type errors
- Finds undefined variables
- Identifies unused code
- Fast and automated

### 2. **Then Use AI for Complex Analysis**

**Why:** AI can find logical errors, security issues, and architectural problems

**Best Approach:**
1. Run PHPStan first → Fix obvious issues
2. Use Agent Mode → Find complex bugs
3. Review suggestions → Implement fixes

---

## Agent Mode Workflow for Bug Finding

### Step 1: Comprehensive Review

**Prompt Example:**
```
"Review the entire BoothController for bugs. Check for:
1. Type errors and null pointer exceptions
2. SQL injection vulnerabilities
3. Missing validation
4. Race conditions in concurrent operations
5. Error handling gaps
6. Logic errors
7. Security vulnerabilities
8. Performance issues

Provide a detailed report with:
- Bug location (file, line)
- Bug type (critical/high/medium/low)
- Description
- Suggested fix
- Code example"
```

### Step 2: Focused Module Review

**Prompt Example:**
```
"Find bugs in all service classes (BoothService, BookingService, etc.).
Focus on:
- Transaction handling (missing rollbacks)
- Error handling (uncaught exceptions)
- Type safety (missing type hints)
- Edge cases (null checks, empty arrays)
- Business logic errors"
```

### Step 3: Security Review

**Prompt Example:**
```
"Security audit: Find all security vulnerabilities in the codebase.
Check for:
- SQL injection
- XSS vulnerabilities
- CSRF protection
- Authentication/authorization bypass
- File upload vulnerabilities
- Sensitive data exposure
- Insecure direct object references"
```

---

## Debug Mode Workflow for Specific Issues

### When You Have an Error

**Mode:** Debug Mode  
**Model:** Claude Sonnet 4.5

**Prompt Example:**
```
"Debug this error:
[Error message]

Context:
- File: BookController.php
- Method: createBooking()
- Line: 245
- Request data: [paste relevant data]

Help me understand:
1. What caused the error?
2. Why did it happen?
3. How to fix it?
4. How to prevent it?"
```

---

## Recommended Model Settings

### For Comprehensive Bug Finding

**Mode:** Agent Mode  
**Model:** Claude Sonnet 4.5  
**Temperature:** 0.1-0.3 (lower = more focused)  
**Max Tokens:** Higher (for detailed analysis)

**Why:**
- Sonnet 4.5 has excellent code understanding
- Lower temperature = more consistent, focused analysis
- Higher tokens = more detailed reports

### For Quick Bug Checks

**Mode:** Agent Mode  
**Model:** GPT-4 Turbo  
**Temperature:** 0.2  
**Max Tokens:** Medium

**Why:**
- Faster responses
- Good for quick scans
- Cost-effective

---

## Types of Bugs to Look For

### 1. **Type Errors** (High Priority)
- Missing type hints
- Type mismatches
- Null pointer exceptions
- Undefined variables

### 2. **Logic Errors** (High Priority)
- Incorrect conditions
- Off-by-one errors
- Missing edge cases
- Wrong calculations

### 3. **Security Vulnerabilities** (Critical Priority)
- SQL injection
- XSS vulnerabilities
- CSRF missing
- Authentication bypass
- File upload issues

### 4. **Race Conditions** (Medium Priority)
- Missing locks in concurrent operations
- Transaction issues
- Booking conflicts

### 5. **Error Handling** (Medium Priority)
- Uncaught exceptions
- Missing try-catch blocks
- Poor error messages
- Missing rollbacks

### 6. **Performance Issues** (Low Priority)
- N+1 queries
- Missing indexes
- Inefficient loops
- Memory leaks

---

## Example: Finding Bugs in Your Refactored Code

### Comprehensive Review Prompt

```
"Review all refactored services and controllers for bugs.
Focus on:
1. Transaction handling - are all DB operations wrapped?
2. Error handling - are exceptions caught properly?
3. Type safety - are all parameters/returns typed?
4. Null checks - are null values handled?
5. Edge cases - empty arrays, missing data
6. Race conditions - concurrent booking operations
7. Security - input validation, authorization checks

Files to review:
- app/Services/BoothService.php
- app/Services/BookingService.php
- app/Services/BookService.php
- app/Services/ClientService.php
- app/Http/Controllers/BoothController.php
- app/Http/Controllers/BookController.php
- app/Http/Controllers/ClientController.php

Provide a detailed bug report with:
- Bug ID
- File and line number
- Severity (Critical/High/Medium/Low)
- Description
- Impact
- Suggested fix
- Code example"
```

---

## Comparison: Agent Mode vs Debug Mode

| Feature | Agent Mode | Debug Mode |
|---------|------------|------------|
| **Best For** | Comprehensive bug finding | Specific error debugging |
| **Scope** | Multiple files, entire codebase | Single issue, specific flow |
| **Analysis** | Pattern recognition, systemic issues | Step-by-step execution |
| **Fixes** | Can suggest and implement | Focuses on understanding |
| **Speed** | Faster for broad analysis | Slower, more detailed |
| **Use Case** | "Find all bugs" | "Why is this failing?" |

---

## Recommended Approach for Your Project

### Phase 1: Automated Analysis (Do This First)

```bash
# Install PHPStan
composer require --dev phpstan/phpstan phpstan/extension-installer larastan/larastan

# Create phpstan.neon config
# (See CODE_QUALITY_REVIEW_GUIDE.md)

# Run analysis
vendor/bin/phpstan analyse --level=5
```

**Result:** Catches ~60-70% of bugs automatically

### Phase 2: AI-Powered Review (Then Do This)

**Mode:** Agent Mode  
**Model:** Claude Sonnet 4.5

**Prompt:**
```
"After running PHPStan, review the codebase for bugs that static analysis might miss:
- Logic errors
- Business rule violations
- Security vulnerabilities
- Race conditions
- Edge cases
- Architectural issues

Focus on recently refactored code first."
```

**Result:** Finds complex bugs, logical errors, security issues

### Phase 3: Specific Debugging (As Needed)

**Mode:** Debug Mode  
**Model:** Claude Sonnet 4.5

**When:** When you encounter specific errors in production or testing

---

## My Recommendation for Your Project

### **Use Agent Mode + Claude Sonnet 4.5**

**Why:**
1. ✅ Best code analysis capabilities
2. ✅ Can review multiple files
3. ✅ Understands Laravel patterns
4. ✅ Can suggest comprehensive fixes
5. ✅ Good at finding security issues

### **Workflow:**

1. **First:** Run PHPStan (automated)
   ```bash
   composer require --dev phpstan/phpstan larastan/larastan
   vendor/bin/phpstan analyse
   ```

2. **Then:** Use Agent Mode for comprehensive review
   ```
   "Find all bugs in the refactored services and controllers.
   Check for type errors, logic errors, security issues, 
   race conditions, and missing error handling."
   ```

3. **Finally:** Use Debug Mode for specific issues
   ```
   "Debug this specific error: [error message]"
   ```

---

## Quick Start Commands

### For Comprehensive Bug Finding

**In Cursor:**
1. Switch to **Agent Mode**
2. Select **Claude Sonnet 4.5** model
3. Use prompt:
   ```
   "Find all bugs in [file/module]. Check for type errors, 
   logic errors, security vulnerabilities, race conditions, 
   and missing error handling. Provide detailed report."
   ```

### For Specific Error Debugging

**In Cursor:**
1. Switch to **Debug Mode**
2. Select **Claude Sonnet 4.5** model
3. Use prompt:
   ```
   "Debug this error: [error message]
   File: [file path]
   Line: [line number]
   Help me understand and fix it."
   ```

---

## Summary

| Task | Mode | Model | Why |
|------|------|-------|-----|
| **Find All Bugs** | Agent Mode | Claude Sonnet 4.5 | Comprehensive analysis |
| **Debug Specific Error** | Debug Mode | Claude Sonnet 4.5 | Detailed investigation |
| **Quick Bug Scan** | Agent Mode | GPT-4 Turbo | Fast results |
| **Security Audit** | Agent Mode | Claude Sonnet 4.5 | Best security analysis |
| **Logic Error Finding** | Agent Mode | Claude Sonnet 4.5 | Strong reasoning |

---

**Recommendation:** Start with **Agent Mode + Claude Sonnet 4.5** for comprehensive bug finding, then use **Debug Mode** for specific issues.

---

**Last Updated:** February 10, 2026  
**Status:** Ready to Use
