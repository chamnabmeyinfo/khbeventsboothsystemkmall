# HR Module Implementation Guide

## Overview

This document describes the comprehensive HR (Human Resources) Management System that has been created for managing all aspects of employee management in the company.

## Features

### 1. Employee Management
- Complete employee profiles with personal and professional information
- Link employees to user accounts
- Track employment history, contracts, and termination
- Manage employee status (active, inactive, terminated, on-leave, suspended)
- Employee code generation (EMP + Year + Sequential Number)

### 2. Department Management
- Create and manage organizational departments
- Hierarchical department structure (parent-child relationships)
- Assign department managers
- Track department budgets
- View all employees in each department

### 3. Position Management
- Define job positions within departments
- Set salary ranges (min/max)
- Define job requirements and descriptions
- Track employees in each position

### 4. Attendance Tracking
- Record daily attendance (check-in, check-out)
- Track break duration
- Calculate total hours worked
- Support multiple statuses (present, absent, late, half-day, on-leave, holiday)
- Track overtime hours
- Approval workflow for attendance records

### 5. Leave Management
- Multiple leave types (Annual, Sick, Personal, Maternity, Paternity, Unpaid, Emergency)
- Leave request workflow (pending, approved, rejected, cancelled)
- Leave balance tracking per employee per year
- Carry-forward support for eligible leave types
- Automatic balance deduction on approval
- Overlap detection to prevent conflicting leave requests

### 6. Performance Reviews
- Create performance reviews for employees
- Set review periods (start and end dates)
- Overall rating system (out of 5.00)
- Multiple review criteria with individual ratings
- Employee and reviewer comments
- Review status tracking (draft, submitted, acknowledged, completed)

### 7. Employee Documents
- Upload and manage employee documents
- Track document types (contracts, certificates, IDs, etc.)
- Expiry date tracking
- Automatic expiry notifications
- Document categorization

### 8. Training Management
- Record employee training programs
- Track training providers
- Training status (scheduled, in-progress, completed, cancelled)
- Certificate management
- Cost tracking

### 9. Salary History
- Complete salary change history
- Track effective dates
- Record reasons for salary changes
- Approval workflow
- Currency support

## Database Structure

### Main Tables
1. **departments** - Organizational departments
2. **positions** - Job positions
3. **employees** - Employee master data
4. **attendance** - Daily attendance records
5. **leave_types** - Types of leave (Annual, Sick, etc.)
6. **leave_requests** - Leave applications
7. **leave_balances** - Leave balance per employee per year
8. **performance_reviews** - Performance evaluation records
9. **performance_review_criteria** - Individual criteria ratings
10. **employee_documents** - Document storage
11. **employee_training** - Training records
12. **salary_history** - Salary change history

## Permissions

The HR module includes comprehensive permissions:

- `hr.employees.view` - View employees
- `hr.employees.create` - Create employees
- `hr.employees.edit` - Edit employees
- `hr.employees.delete` - Delete employees
- `hr.departments.manage` - Manage departments
- `hr.positions.manage` - Manage positions
- `hr.attendance.view` - View attendance
- `hr.attendance.manage` - Manage attendance
- `hr.leaves.view` - View leave requests
- `hr.leaves.manage` - Manage leave requests
- `hr.leaves.approve` - Approve/reject leave requests
- `hr.performance.view` - View performance reviews
- `hr.performance.manage` - Manage performance reviews
- `hr.documents.manage` - Manage documents
- `hr.training.manage` - Manage training
- `hr.salary.manage` - Manage salary history

## Role Assignments

### HR Manager
- Has access to ALL HR module features
- Can manage employees, departments, positions
- Can approve attendance and leave requests
- Can create performance reviews
- Can manage all HR documents and training

### HR Staff
- Can view employees, attendance, and leave requests
- Limited management capabilities
- Cannot approve requests

## Setup Instructions

1. **Run the SQL file**:
   ```sql
   -- Import HR_MODULE_TABLES.sql into your database
   -- This will create all necessary tables and default leave types
   ```

2. **Update Permissions**:
   ```sql
   -- Run the updated ROLES_AND_PERMISSIONS_SETUP.sql
   -- This adds HR permissions and assigns them to HR roles
   ```

3. **Routes are already configured** in `routes/web.php`:
   - All HR routes are under `/hr` prefix
   - Protected with appropriate permission middleware

## Usage Examples

### Creating an Employee
1. Navigate to HR > Employees > Create
2. Fill in employee information
3. Select department and position
4. Set hire date and employment type
5. System automatically:
   - Generates employee code
   - Creates leave balances for current year
   - Creates initial salary history entry

### Recording Attendance
1. Navigate to HR > Attendance > Create
2. Select employee and date
3. Enter check-in and check-out times
4. System automatically calculates total hours
5. Approve attendance if needed

### Managing Leave Requests
1. Employee or HR creates leave request
2. System checks leave balance
3. System checks for overlapping requests
4. Manager/HR approves or rejects
5. On approval, leave balance is automatically deducted

### Performance Reviews
1. Create performance review for employee
2. Set review period
3. Add review criteria with ratings
4. Add comments from reviewer and employee
5. Calculate overall rating
6. Submit and acknowledge review

## Next Steps

1. **Create Views**: The views need to be created in `resources/views/hr/`
   - `employees/index.blade.php` - Employee listing
   - `employees/create.blade.php` - Create employee form
   - `employees/show.blade.php` - Employee profile
   - `employees/edit.blade.php` - Edit employee form
   - Similar views for departments, positions, attendance, leaves, etc.

2. **Add Navigation**: Add HR menu items to the main navigation

3. **Create Dashboard**: Create HR dashboard with statistics and quick actions

4. **Add Reports**: Create HR reports (attendance summary, leave balance, etc.)

5. **Add Notifications**: Set up notifications for:
   - Leave request approvals
   - Document expiry warnings
   - Performance review due dates

## File Structure

```
app/
├── Http/Controllers/HR/
│   ├── EmployeeController.php
│   ├── DepartmentController.php
│   ├── PositionController.php
│   ├── AttendanceController.php
│   └── LeaveController.php
├── Models/HR/
│   ├── Employee.php
│   ├── Department.php
│   ├── Position.php
│   ├── Attendance.php
│   ├── LeaveType.php
│   ├── LeaveRequest.php
│   ├── LeaveBalance.php
│   ├── PerformanceReview.php
│   ├── PerformanceReviewCriterion.php
│   ├── EmployeeDocument.php
│   ├── EmployeeTraining.php
│   └── SalaryHistory.php

database/
└── HR_MODULE_TABLES.sql

resources/views/hr/
├── employees/
├── departments/
├── positions/
├── attendance/
└── leaves/
```

## Notes

- All foreign key constraints are properly set up
- Employee codes are auto-generated if not provided
- Leave balances are automatically initialized when employee is created
- Salary history is automatically tracked on salary changes
- The system supports multiple currencies
- All dates are properly validated
- Overlap detection prevents conflicting leave requests
