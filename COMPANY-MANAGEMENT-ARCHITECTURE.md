# 🏢 KHB EVENTS - Company Management System Architecture

**Vision:** Transform from event/booth booking system → Full company operating platform **while keeping Booth + Floor Plan + Booking as the central engine**

---

## ⚠️ CORE PRIORITY - NON-NEGOTIABLE

### 🎯 **BOOTH + FLOOR PLAN + BOOKING = PRIMARY ENGINE**

**This is the central, untouchable core of KHB EVENTS. All company-level features must be designed around this engine, never weakening or complicating it.**

#### Core Product Focus

**The main value of KHB EVENTS:**

1. **Visually managing floor plans** - Interactive, drag-and-drop floor plan editor
2. **Managing booths on those floor plans** - Real-time booth status, positioning, styling
3. **Booking / selling those booths efficiently** - Quick booking from floor plan view, status tracking

**All other company modules (Sales, Marketing, Finance, HR, etc.) are supporting layers that enhance and extend this core engine, NOT replace it.**

#### Protected Hierarchy (Untouchable Core)

**Keep this hierarchy exactly as shown - it's the foundation:**

```
Company
  └── Department: Operations
      └── Event / Project
          └── Floor Plans (multiple per event)
              └── Booths (belong to floor plan)
                  └── Bookings (central transaction)
                      ├── Client / Customer
                      ├── Booth(s)
                      ├── Event / Project
                      └── Payments / Invoices
```

**Key Principles:**

- ✅ Floor Plan Management = **First-class feature** in Operations/Events area
- ✅ UI must make it **very easy and fast** to:
  - Open an event
  - See all its floor plans
  - See booth availability status (visual, real-time)
  - Create or manage bookings **directly from floor plan view**
- ✅ Booking (book) stays the **central linking entity** between Client, Booth(s), Event, Payments, and Invoices

#### Booking Mechanism as Central Transaction

**All workflows must connect back to Bookings:**

- **Sales Pipeline:** Lead → Opportunity → Quote → **Won → Booking created** → Invoice
- **Walk-in / Internal:** Direct **Booking** → Invoice → Payment
- **Finance:** Invoices and expenses tied back to **Bookings and Events**
- **Marketing:** Campaign success measured in **bookings generated** for specific events/floor plans

**Never bypass the Booking entity. Never make booking feel like a secondary feature.**

#### Visual Architecture: Protected Core vs Supporting Layers

```
┌─────────────────────────────────────────────────────────────────┐
│                    COMPANY MANAGEMENT PLATFORM                   │
│                  (Supporting Layers - Enhance)                   │
├─────────────────────────────────────────────────────────────────┤
│ Sales │ Marketing │ Finance │ HR │ Admin │ Operations │ Reports │
│       │           │         │    │       │            │         │
│  ┌────────────────────────────────────────────────────┐         │
│  │  ALL MODULES CONNECT BACK TO BOOKING CORE  ────────┼──────┐  │
│  └────────────────────────────────────────────────────┘      │  │
│                                                               │  │
│  ┌──────────────────────────────────────────────────────────┐ │  │
│  │  ╔══════════════════════════════════════════════════╗   │ │  │
│  │  ║         PROTECTED CORE - UNTOUCHABLE             ║   │ │  │
│  │  ╠══════════════════════════════════════════════════╣   │ │  │
│  │  ║  Event / Project                                 ║   │ │  │
│  │  ║    └─ Floor Plans (Visual Editor)                ║   │ │  │
│  │  ║         └─ Booths (Status: Available/Reserved/  ║   │ │  │
│  │  ║            Confirmed/Paid)                       ║   │ │  │
│  │  ║            └─ BOOKING ⭐ CENTRAL TRANSACTION     ║   │ │  │
│  │  ║               ├─ Client                          ║   │ │  │
│  │  ║               ├─ Booths (JSON array)             ║   │ │  │
│  │  ║               ├─ Event                           ║   │ │  │
│  │  ║               └─ Payments / Invoices             ║   │ │  │
│  │  ║                                                  ║   │ │  │
│  │  ║  CORE WORKFLOWS (MUST STAY FAST & SIMPLE):      ║   │ │  │
│  │  ║  1. Open Event → See Floor Plans (1 click)      ║   │ │  │
│  │  ║  2. Open Floor Plan → See Booths (visual)       ║   │ │  │
│  │  ║  3. Select Booths → Create Booking (2 clicks)   ║   │ │  │
│  │  ║  4. Booking Created → Auto-generate Invoice     ║   │ │  │
│  │  ║  5. Payment Received → Link to Booking          ║   │ │  │
│  │  ╚══════════════════════════════════════════════════╝   │ │  │
│  └──────────────────────────────────────────────────────────┘ │  │
│                                                                │  │
│  Supporting Layers (ALL ENHANCE, NEVER REPLACE):              │  │
│  • Sales: Lead → Opportunity → Quote → BOOKING                │  │
│  • Marketing: Campaign → Lead → Opportunity → BOOKING         │  │
│  • Finance: BOOKING → Invoice → Payment                       │  │
│  • Operations: Tasks support floor plan setup → BOOKING ready │  │
│  • HR: Staff assignments support booking creation             │  │
└────────────────────────────────────────────────────────────────┘
```

**Key Principle:**

- **Core (Inner Box):** Protected, fast, simple, visual
- **Supporting Layers (Outer Layer):** Enhance core workflows, never replace them
- **All Arrows Point TO Booking:** Every department's workflow ends in or supports booking creation

---

## 1. CURRENT SYSTEM UNDERSTANDING

### Current Architecture (Event-Based)

**Main Entity:** **Booking** (Book model)

- Central entity that connects clients, booths, and users
- Bookings are created when clients reserve booths
- Payments are linked to bookings

**Entity Relationships:**

```
Event (optional, separate admin system)
  └── Not directly linked to main booking flow

Booking (Book)
  ├── Client (clientid)
  ├── User/Sales Staff (userid)
  └── Booths (boothid - JSON array)
      └── Booth Status (Available → Reserved → Confirmed → Paid)

Payment
  ├── Booking (booking_id)
  ├── Client (client_id)
  └── User (user_id)

Booth
  ├── Client (client_id) - when booked
  ├── User (userid) - who booked it
  ├── Booking (bookid) - which booking
  └── Category/Asset/BoothType
```

**Current Focus:**

- Event/booth-centric operations
- Single floor plan (all booths in one view)
- Booking-driven workflow
- Payment tracking per booking
- User-based permissions (Admin/Sale)

**Limitations:**

- No company-level structure
- No department separation
- No project/event costing
- No sales pipeline
- No marketing campaigns
- Limited financial tracking (only payments, no expenses/invoices)
- No multi-floor plan support
- No company-wide reporting

---

## 2. COMPANY-BASED ARCHITECTURE REDESIGN

### Core High-Level Entities

```
Company (KHB EVENTS)
  │
  ├── Departments
  │   ├── Sales
  │   ├── Marketing
  │   ├── Finance & Accounting
  │   ├── Operations (Events/Projects)
  │   ├── HR / Staff Management
  │   └── Admin / Management
  │
  ├── Staff / Users
  │   ├── Department Assignment
  │   ├── Roles & Permissions (department-aware)
  │   └── Performance Tracking
  │
  ├── Events / Projects
  │   ├── Event Details (dates, location, type)
  │   ├── Floor Plans (multiple per event)
  │   │   └── Booths (belong to floor plan)
  │   ├── Budget & Costing
  │   ├── Tasks & Milestones
  │   └── Resource Allocation
  │
  ├── Clients / Partners / Vendors
  │   ├── Client Type (Customer, Vendor, Partner)
  │   ├── Sales Pipeline (for customers)
  │   └── Vendor Management (for suppliers)
  │
  ├── Financial Records
  │   ├── Invoices (to clients)
  │   ├── Payments (from clients)
  │   ├── Expenses (to vendors, staff, materials)
  │   ├── Budgets (per event/project)
  │   └── P&L Reports (company, department, event)
  │
  └── Assets & Resources
      ├── Booths (physical assets)
      ├── Equipment
      ├── Venues
      └── Inventory
```

### How Events & Floor Plans Fit (Core-First Design)

**Protected Hierarchy - DO NOT BREAK THIS CHAIN:**

```
Company
  └── Department: Operations
      └── Event/Project
          ├── Floor Plans (multiple) ⭐ FIRST-CLASS FEATURE
          │   └── Booths (visual management)
          │       └── Bookings ⭐ CENTRAL TRANSACTION
          │           ├── Client
          │           ├── Booths
          │           ├── Event
          │           └── Payments/Invoices
          ├── Budget & Costing (supports floor plan/booking)
          ├── Tasks & Timeline (supports event delivery)
          ├── Invoices (generated from bookings)
          └── Expenses (track event costs)
```

**Key Changes (While Protecting Core):**

- Events become **Projects** under Operations department
- **Floor Plans belong to Events/Projects** (not global) - Each event has its own floor plans
- **Booths belong to Floor Plans** (not global) - Clear visual hierarchy
- **Bookings can span multiple floor plans** within same event (flexible)
- **All financials linked to events/projects** for costing, but always trace back to bookings
- **Floor Plan Management remains prominently accessible** - Quick access from event view, not buried in menus

---

## 3. DEPARTMENT-LEVEL FEATURES & MODULES

### 📊 SALES DEPARTMENT

**Key Entities:**

- **Lead** (name, company, email, phone, source, status, assigned_to, created_at)
- **Opportunity** (lead_id, event_id, stage, value, probability, expected_close_date)
- **Quote/Proposal** (opportunity_id, client_id, items, total, status, valid_until)
- **Sales Pipeline** (stages: Lead → Qualified → Proposal → Negotiation → Won/Lost)

**Key Actions:**

- Create/manage leads
- Convert lead to opportunity
- Create quotes/proposals
- Track pipeline stages
- Link opportunities to events
- Convert won opportunity to booking

**KPIs:**

- Total leads
- Conversion rate (Lead → Opportunity → Booking)
- Pipeline value
- Average deal size
- Sales by staff member
- Win/loss ratio

**Integration:**

- Opportunities → **Bookings** (when won) - Pipeline ends in booking creation
- Quotes → **Bookings** (when approved) - Quote approval triggers booking
- Quotes → Invoices (when booking confirmed)
- Client management (existing Client model)

**How This Supports the Booth-Floor Plan-Booking Core:**

- **Sales Pipeline ends in Booking:** When opportunity is won, create booking for specific booths on a floor plan
- **Quote includes Booth Selection:** Quotes can specify which booths/floor plan the client is interested in
- **Sales KPIs centered on bookings:** "Booths sold per event/floor plan", "Conversion rate to bookings", "Revenue per floor plan"
- **Sales Dashboard shows:** Active events with floor plans, booth availability by event, booking trends
- **Never bypass booking:** All sales activities (leads, opportunities, quotes) must flow through to booking creation

---

### 📢 MARKETING DEPARTMENT

**Key Entities:**

- **Campaign** (name, type, event_id, start_date, end_date, budget, status)
- **Channel** (campaign_id, type: email/social/ads, name, cost, performance_metrics)
- **Marketing Asset** (campaign_id, type, file, url, description)

**Key Actions:**

- Create campaigns (linked to events or company-wide)
- Track channels (email, social media, ads)
- Monitor performance (reach, engagement, conversions)
- Manage marketing assets
- Schedule email campaigns (use existing Email Templates)

**KPIs:**

- Campaign ROI
- Cost per lead
- Channel performance
- Email open/click rates
- Social engagement
- Event attendance from campaigns

**Integration:**

- Campaigns → Events (promote specific events and their floor plans)
- Email Templates → Campaigns (reuse templates)
- Notifications → Campaigns (automated marketing)
- Campaigns → **Bookings** (measure success by bookings generated)

**How This Supports the Booth-Floor Plan-Booking Core:**

- **Campaign Success = Bookings Generated:** Campaign performance measured by number of bookings created for target events/floor plans
- **Campaigns Promote Floor Plans:** Marketing materials showcase available booths and floor plans visually
- **Event-Specific Campaigns:** Campaigns linked to specific events show floor plan availability and booth options
- **Lead Attribution:** Track which campaigns generated leads that converted to bookings
- **Marketing Dashboard shows:** Bookings per campaign, cost per booking, which events/floor plans are most promoted

---

### 💰 FINANCE & ACCOUNTING DEPARTMENT

**Key Entities:**

- **Invoice** (client_id, event_id, invoice_number, items, subtotal, tax, total, due_date, status)
- **Payment** (existing, link to invoice_id)
- **Expense** (vendor_id, event_id, category, amount, date, receipt, approved_by)
- **Budget** (event_id, category, planned_amount, actual_amount, variance)
- **Account** (chart of accounts: Revenue, Expenses, Assets, Liabilities)

**Key Actions:**

- Create invoices (from bookings or manual)
- Record payments (existing functionality)
- Track expenses (vendors, materials, staff costs)
- Create budgets per event/project
- Generate P&L reports
- Track accounts receivable/payable

**KPIs:**

- Total revenue (company, department, event)
- Total expenses (company, department, event)
- Profit margin (gross, net)
- Budget vs Actual
- Outstanding invoices
- Cash flow

**Integration:**

- **Bookings → Invoices** (auto-generate from booking) ⭐ PRIMARY FLOW
- Payments → Invoices (link payment to invoice)
- Payments → **Bookings** (existing relationship, maintain)
- Expenses → Events (cost tracking)
- Budgets → Events (planning)

**How This Supports the Booth-Floor Plan-Booking Core:**

- **Booking is the source of revenue:** Invoices generated from bookings (not standalone)
- **Financial KPIs per floor plan:** Revenue per floor plan, profit per event, cost per sold booth
- **Booking-based invoicing:** When booking is confirmed, auto-generate invoice with booth details
- **Payment tracking per booking:** All payments linked back to specific bookings (and their booths)
- **Expense tracking per event:** Track event costs separately, but always link to bookings for profit calculation
- **Budget vs Actual:** Compare planned event budget vs actual revenue from bookings
- **Financial Dashboard shows:** Bookings revenue by event/floor plan, outstanding invoices from bookings, profit margins

---

### 🎯 PROJECT COSTING / EVENT OPERATIONS

**Key Entities:**

- **Project Budget** (event_id, category: setup/marketing/staff/logistics/venue/booths, planned, actual)
- **Task** (event_id, name, assigned_to, due_date, status, priority)
- **Milestone** (event_id, name, target_date, status)
- **Resource Allocation** (event_id, resource_type: staff/vendor/booth/equipment, assigned_to, dates)

**Key Actions:**

- Create project budgets
- Track actual costs vs budget
- Create tasks and assign to staff
- Set milestones
- Allocate resources (staff, vendors, booths)
- Monitor project progress

**KPIs:**

- Budget variance (planned vs actual)
- Project completion %
- Task completion rate
- Resource utilization
- Cost per event
- Profitability per event

**Integration:**

- Budgets → Expenses (track actuals)
- Tasks → Staff (assignments)
- Tasks → **Events/Floor Plans** (tasks related to booth setup, floor plan preparation)
- Resources → **Booths** (allocation: which booths assigned to which resources)
- **Floor Plans → Events** (visual planning - core feature)
- Budgets → **Bookings** (track revenue from bookings vs planned budget)

**How This Supports the Booth-Floor Plan-Booking Core:**

- **Budgets clearly reference bookings:** Project budget tracks planned booth sales vs actual bookings revenue
- **Tasks reference floor plans:** Tasks like "Setup Floor Plan A", "Prepare Booth Inventory", "Confirm Booth Layout"
- **Resource allocation includes booths:** Track which staff/vendors are responsible for which booths/areas on floor plan
- **Budget categories include booth-related costs:** Setup costs, booth materials, floor plan design, booth installation
- **Task completion affects booking flow:** Tasks like "Floor plan approved" unlock booking creation for that floor plan
- **Operations Dashboard shows:** Floor plan completion status, booth setup tasks, booking readiness per event/floor plan

---

### 👥 HR / STAFF & CONTRACTORS

**Key Entities:**

- **Staff Profile** (extends User, department_id, position, hire_date, salary, availability)
- **Contractor** (name, company, type, rate, contact, availability)
- **Department** (name, manager_id, budget, status)
- **Performance Review** (staff_id, period, ratings, notes)
- **Time Tracking** (staff_id, event_id, hours, date, task)

**Key Actions:**

- Manage staff profiles
- Assign staff to departments
- Track availability
- Assign staff to events/projects
- Manage contractors
- Track time/hours
- Performance reviews

**KPIs:**

- Staff count per department
- Staff utilization rate
- Average hours per event
- Staff cost per event
- Contractor costs
- Department headcount

**Integration:**

- Staff → **Events/Floor Plans** (assignments: which events/floor plans staff responsible for)
- Staff → Tasks (work allocation)
- Staff → Expenses (salary costs)
- Staff → **Bookings** (track which staff created which bookings)
- Users → Departments (existing User model)

**How This Supports the Booth-Floor Plan-Booking Core:**

- **Staff assignments show floor plan responsibility:** Which staff member handles which floor plan area (e.g., "Zone A - Ground Floor")
- **Booking attribution to staff:** Track which sales staff created which bookings (performance tracking)
- **Event/floor plan teams:** Assign staff teams to specific events or floor plans for coordination
- **Staff availability affects booking capacity:** If key staff unavailable, may affect ability to process bookings for specific events
- **Performance reviews include booking metrics:** Track staff performance by bookings created, revenue generated per event/floor plan
- **HR Dashboard shows:** Staff assignments per event/floor plan, booking performance by staff, team availability

---

### ⚙️ ADMIN / MANAGEMENT

**Key Entities:**

- **Company Settings** (name, logo, address, tax_id, fiscal_year)
- **Department Settings** (budgets, permissions, workflows)
- **System Settings** (existing Settings model)
- **Audit Logs** (existing ActivityLog, enhanced)
- **Reports** (company-wide, department, event-level)

**Key Actions:**

- Company-wide dashboard
- Department management
- System configuration
- User/role/permission management (existing)
- Generate comprehensive reports
- View audit trails

**KPIs:**

- Company revenue
- Company profit
- Department performance
- Event portfolio performance
- Staff productivity
- Client satisfaction

**Integration:**

- All modules (central oversight)
- Reports → All departments
- Reports → **Bookings/Floor Plans/Events** (core reporting)
- Permissions → All modules
- Permissions → **Floor Plan/Booking access** (critical permissions)

**How This Supports the Booth-Floor Plan-Booking Core:**

- **Company dashboard prioritizes booking KPIs:** Total booths available/sold/reserved, revenue per event/floor plan, booking pace
- **Department oversight includes floor plan status:** View all events and their floor plan completion/booking status
- **Admin controls booking permissions:** Who can create bookings, who can edit floor plans, who can view which events
- **Company-wide reports center on bookings:** All financial, sales, and operations reports trace back to bookings
- **System settings control booking workflow:** Booking approval process, floor plan editing permissions, booth status rules
- **Admin Dashboard shows:** Company booking overview, floor plan utilization across all events, cross-event booking trends

---

## 4. UNIFIED NAVIGATION & UX

### Main Navigation Structure

```
🏠 Company Overview (Dashboard)
   ├── Company KPIs
   ├── Department Performance
   ├── Recent Activities
   └── Quick Actions

📈 Sales
   ├── Leads
   ├── Opportunities (Pipeline)
   ├── Quotes & Proposals
   ├── Clients (CRM)
   └── Sales Reports

📢 Marketing
   ├── Campaigns
   ├── Channels
   ├── Assets
   └── Marketing Reports

💰 Finance
   ├── Invoices
   ├── Payments
   ├── Expenses
   ├── Budgets
   ├── Accounts
   └── Financial Reports

🎯 Operations / Events ⭐ CORE MODULE
   ├── Events / Projects
   │   ├── Event List
   │   ├── Create Event
   │   └── Event Details
   │       ├── Overview
   │       ├── 🎨 Floor Plan Management ⭐ FIRST-CLASS (Quick Access Button)
   │       │   ├── Floor Plan List
   │       │   ├── Create Floor Plan
   │       │   └── Floor Plan Editor (Drag-and-Drop)
   │       │       ├── Booth Management (Visual)
   │       │       ├── Real-time Availability Status
   │       │       └── Quick Booking (from floor plan)
   │       ├── 📋 Bookings ⭐ CENTRAL TRANSACTION (Quick Access Button)
   │       │   ├── Booking List
   │       │   ├── Create Booking (from floor plan or direct)
   │       │   └── Booking Details
   │       ├── Budget & Costing
   │       ├── Tasks & Milestones
   │       └── Resources
   ├── Floor Plans (Global View - All Events)
   ├── Bookings (Global View - All Events)
   └── Vendors & Partners

👥 HR / Staff
   ├── Staff Management
   ├── Departments
   ├── Contractors
   ├── Time Tracking
   └── Performance

⚙️ Settings & Admin
   ├── Company Settings
   ├── Users & Roles
   ├── Permissions
   ├── System Settings
   └── Activity Logs
```

### Navigation Patterns (Floor Plan & Booking Priority)

**Breadcrumb Example (Core Path):**

```
Company Overview → Operations → Events → "K Mall Xmas 2026" → Floor Plans → "Ground Floor" → Booths → Create Booking
```

**Quick Access - Always Visible:**

- **🔥 Quick Access Panel (Top Bar):**
  - "Open Floor Plans" (current event) - Direct link to floor plan management
  - "View Booth Map" (current event) - Real-time booking status
  - "Create Booking" (from floor plan or direct) - Fast booking creation
- **Recent Events in Sidebar** (with booking status indicators)
- **Quick Create Buttons:** Event, Floor Plan, **Booking** (prominently placed)
- **Department Switcher** (if user has access to multiple)

**UX Priority: Floor Plan & Booking Workflows**

**Always Available Shortcuts:**

1. **"Open Floor Plans for Current Event"** - One-click access from any page
2. **"View Booth Map with Real-Time Booking Status"** - Visual dashboard
3. **"Create New Booking from Floor Plan"** - Direct booking from visual selection

**Dashboard Quick Actions (Always Visible):**

- Jump to active events with floor plans
- View booth availability at a glance
- Create booking in 2 clicks

**Consistency:**

- All detail pages: Breadcrumb + Action buttons (Floor Plan/Booking actions always visible)
- All list pages: Filters + Search + **"Create Booking"** button (prominent)
- All forms: Validation errors + Success messages
- **Event detail pages:** Floor Plan and Booking tabs are **first tabs** (not buried)

**Visual Hierarchy:**

- Floor Plan Management = **Large, prominent button** in event detail
- Booking Creation = **Primary action button** (green/prominent)
- Booth Status = **Color-coded visual indicators** (available/reserved/confirmed/paid)

---

## 5. DATA MODEL EVOLUTION PLAN

### New Tables Needed

#### Company & Department Structure

```sql
companies
  - id, name, logo, address, tax_id, fiscal_year_start, status, created_at, updated_at

departments
  - id, company_id, name, manager_id, budget, status, created_at, updated_at

user_departments (pivot)
  - user_id, department_id, role_in_dept, joined_at
```

#### Sales Module

```sql
leads
  - id, name, company, email, phone, source, status, assigned_to, department_id, notes, created_at

opportunities
  - id, lead_id, event_id, name, stage, value, probability, expected_close_date, assigned_to, status

quotes
  - id, opportunity_id, client_id, quote_number, items (JSON), subtotal, tax, total, valid_until, status

sales_pipeline_stages
  - id, name, order, color, department_id
```

#### Marketing Module

```sql
campaigns
  - id, name, type, event_id, department_id, start_date, end_date, budget, status, created_by

campaign_channels
  - id, campaign_id, type, name, cost, reach, engagement, conversions, performance_data (JSON)

marketing_assets
  - id, campaign_id, type, file_path, url, description, created_at
```

#### Finance Module

```sql
invoices
  - id, invoice_number, client_id, event_id, items (JSON), subtotal, tax, total, due_date, status, created_at

expenses
  - id, vendor_id, event_id, department_id, category, amount, date, receipt_file, approved_by, notes

budgets
  - id, event_id, category, planned_amount, actual_amount, variance, period_start, period_end

accounts
  - id, code, name, type (Revenue/Expense/Asset/Liability), parent_id, is_active
```

#### Operations Module

```sql
projects (rename/merge with events)
  - id, name, event_id (if separate), department_id, start_date, end_date, status, budget_total

project_budgets
  - id, project_id, category, planned_amount, actual_amount

tasks
  - id, project_id, name, assigned_to, due_date, status, priority, notes

milestones
  - id, project_id, name, target_date, status, completed_at

resource_allocations
  - id, project_id, resource_type, resource_id, assigned_to, start_date, end_date, status
```

#### HR Module

```sql
departments (already listed above)

contractors
  - id, name, company, type, rate, contact_info, availability, status

staff_profiles (extend user table)
  - Add: department_id, position, hire_date, salary, availability_status

time_entries
  - id, staff_id, project_id, task_id, hours, date, notes, approved_by

performance_reviews
  - id, staff_id, reviewer_id, period_start, period_end, ratings (JSON), notes, created_at
```

### Existing Tables to Modify

#### `events` table

```sql
ALTER TABLE events ADD:
  - company_id (if multi-tenant, else default 1)
  - department_id (link to Operations department)
  - project_code
  - budget_total
  - status (draft/planning/active/completed/cancelled)
```

#### `booth` table

```sql
ALTER TABLE booth ADD:
  - floor_plan_id (from Floor Plan upgrade)
  - event_id (link to event/project)
  - asset_tag (physical tracking)
```

#### `book` table

```sql
ALTER TABLE book ADD:
  - event_id (link to event/project)
  - quote_id (if came from sales quote)
  - invoice_id (if invoiced)
```

#### `payment` table

```sql
ALTER TABLE payment ADD:
  - invoice_id (link to invoice)
  - expense_id (if payment to vendor)
  - event_id (for event-level tracking)
```

#### `client` table

```sql
ALTER TABLE client ADD:
  - type (Customer/Vendor/Partner)
  - lead_id (if converted from lead)
  - tax_id
  - payment_terms
```

#### `user` table

```sql
ALTER TABLE user ADD:
  - department_id
  - position
  - hire_date
  - employee_id
  - salary (encrypted/optional)
```

### Relationship Changes

**New Relationships:**

```
Company
  ├── hasMany Departments
  ├── hasMany Events
  └── hasMany Users

Department
  ├── belongsTo Company
  ├── hasMany Users (through user_departments)
  ├── hasMany Events
  └── hasMany Leads/Opportunities/Campaigns/etc.

Event/Project
  ├── belongsTo Company
  ├── belongsTo Department
  ├── hasMany FloorPlans
  ├── hasMany Bookings
  ├── hasMany Invoices
  ├── hasMany Expenses
  ├── hasOne Budget
  ├── hasMany Tasks
  └── hasMany ResourceAllocations

FloorPlan
  ├── belongsTo Event
  └── hasMany Booths

Booth
  ├── belongsTo FloorPlan
  └── belongsTo Event (through floor_plan)

Booking
  ├── belongsTo Event
  ├── belongsTo Client
  └── belongsTo Quote (optional)

Invoice
  ├── belongsTo Event
  ├── belongsTo Client
  └── hasMany Payments

Expense
  ├── belongsTo Event
  ├── belongsTo Vendor (Client with type=Vendor)
  └── belongsTo Department
```

---

## 6. PHASED IMPLEMENTATION PLAN

### Phase 1: Foundation - Company & Department Structure (Week 1-2)

**Goal:** Introduce Company → Department → Event hierarchy

**Data Model:**

- Create `companies` table
- Create `departments` table
- Create `user_departments` pivot
- Add `company_id`, `department_id` to `events`
- Add `department_id` to `user`
- Create default company and departments

**UI/UX:**

- Update navigation (add Company Overview, Departments)
- Create department management pages
- Update event creation (select department)
- Update user management (assign to department)
- Company dashboard (high-level KPIs)

**Dependencies:**

- None (foundation layer)

**Risks:**

- Low - Additive changes, existing data can default to "Main Company" and "Operations" department
- **⚠️ Core Protection:** Must ensure floor plan/booking functionality remains untouched
- **✅ Validation:** All existing booking/floor plan flows must continue working exactly as before

**Core Protection Measures:**

- Test all existing booking flows after adding company/department structure
- Ensure floor plan access remains quick and prominent
- Verify booth status tracking unchanged
- Confirm booking creation from floor plan still works seamlessly

---

### Phase 2: Finance Extension (Week 3-4)

**Goal:** Full financial management (invoices, expenses, budgets, P&L)

**Data Model:**

- Create `invoices` table
- Create `expenses` table
- Create `budgets` table
- Create `accounts` table (chart of accounts)
- Link `payment` to `invoice_id`
- Link `expense` to `event_id`, `vendor_id`

**UI/UX:**

- Invoice management (create, edit, send, track)
- Expense tracking (create, categorize, approve)
- Budget creation per event
- P&L reports (company, department, event)
- Financial dashboard

**Dependencies:**

- Phase 1 (need departments/events)

**Risks:**

- Medium - Payment model changes need migration
- Need to handle existing payments (create default invoices)
- **⚠️ Core Protection:** Invoices generated from bookings - must maintain booking-to-invoice flow
- **✅ Validation:** Booking → Invoice generation must work seamlessly, no breaking changes to booking process

**Core Protection Measures:**

- Invoices always generated FROM bookings (not standalone)
- Payment model changes don't affect existing booking → payment flow
- Financial reports trace back to bookings (show booking → invoice → payment chain)

---

### Phase 3: Sales Module (Week 5-6)

**Goal:** CRM-style sales pipeline and lead management

**Data Model:**

- Create `leads` table
- Create `opportunities` table
- Create `quotes` table
- Create `sales_pipeline_stages` table
- Link `client` to `lead_id` (conversion tracking)

**UI/UX:**

- Lead management (create, assign, convert)
- Sales pipeline (kanban board)
- Opportunity tracking
- Quote/proposal creation
- Sales dashboard
- Integration: Opportunity → Booking (when won)

**Dependencies:**

- Phase 1 (departments)

**Risks:**

- Low - New module, doesn't affect existing
- **⚠️ Core Protection:** Sales pipeline MUST end in booking creation, not bypass it
- **✅ Validation:** Quote approval → Booking creation flow must work seamlessly

**Core Protection Measures:**

- Opportunity "Won" status triggers booking creation (not manual step)
- Quote includes booth selection (which booths/floor plan client wants)
- Sales dashboard shows booking conversion rates, not just opportunities

---

### Phase 4: Marketing Module (Week 7-8)

**Goal:** Campaign management and channel tracking

**Data Model:**

- Create `campaigns` table
- Create `campaign_channels` table
- Create `marketing_assets` table
- Link campaigns to events

**UI/UX:**

- Campaign management
- Channel performance tracking
- Marketing asset library
- Campaign dashboard
- Integration with Email Templates

**Dependencies:**

- Phase 1 (events)

**Risks:**

- Low - New module
- **⚠️ Core Protection:** Campaign success measured by bookings, not just leads
- **✅ Validation:** Campaign → Event → Floor Plan → Booking attribution must work

**Core Protection Measures:**

- Campaign performance KPIs include "Bookings Generated" (not just leads/clicks)
- Campaigns linked to events show available floor plans in campaign materials
- Lead source tracking connects campaign → lead → opportunity → booking

---

### Phase 5: Operations Enhancement (Week 9-10)

**Goal:** Project costing, tasks, resource allocation

**Data Model:**

- Create `tasks` table
- Create `milestones` table
- Create `resource_allocations` table
- Enhance `events` with project fields
- Link tasks to events, staff

**UI/UX:**

- Task management (kanban/list)
- Milestone tracking
- Resource allocation (staff, vendors, booths)
- Project costing dashboard
- Gantt chart (optional)

**Dependencies:**

- Phase 1, Phase 2 (budgets)

**Risks:**

- Medium - Complex relationships
- **⚠️ Core Protection:** Tasks and resources must support floor plan/booking workflows, not complicate them
- **✅ Validation:** Floor plan setup tasks don't block booking creation (appropriate task dependencies)

**Core Protection Measures:**

- Task management enhances floor plan setup process (e.g., "Floor plan approved" task unlocks bookings)
- Resource allocation includes booth assignments (which staff/vendors handle which booths)
- Budget tracking supports event planning but doesn't interfere with booking flow
- Operations enhancements make booking process smoother, not more complex

---

### Phase 6: HR & Staff Enhancement (Week 11-12)

**Goal:** Department-aware staff management, time tracking

**Data Model:**

- Create `contractors` table
- Create `time_entries` table
- Create `performance_reviews` table
- Enhance `user` with HR fields
- Link staff to departments, events

**UI/UX:**

- Enhanced staff profiles
- Department assignments
- Time tracking
- Contractor management
- Performance reviews
- HR dashboard

**Dependencies:**

- Phase 1 (departments), Phase 5 (tasks)

**Risks:**

- Low - Mostly additive
- **⚠️ Core Protection:** Staff assignments must support floor plan/booking operations, not create bottlenecks
- **✅ Validation:** Staff availability and assignments don't prevent booking creation (appropriate permissions)

**Core Protection Measures:**

- Staff assignments enhance booking management (track who handles which bookings/events)
- Performance tracking includes booking metrics (bookings created, revenue per event/floor plan)
- Time tracking doesn't interfere with booking process (separate from booking creation flow)

---

## 7. RISK & CONSISTENCY AUDIT

### Critical Risks

#### 1. Data Migration

**Risk:** Existing bookings/payments not linked to events
**Mitigation:**

- Create default "Legacy Events" event
- Assign all existing data to default event
- Provide migration script to reassign

#### 2. Permission Granularity

**Risk:** Current permissions too broad for department separation
**Mitigation:**

- Add department-level permissions
- Update permission checks in all controllers
- Create department manager role

#### 3. Report Filtering

**Risk:** Reports assume single event/global view
**Mitigation:**

- Add filters: Company / Department / Event
- Update all report queries
- Add scope helpers (byCompany, byDepartment, byEvent)

#### 4. Navigation Confusion

**Risk:** Users confused by new structure
**Mitigation:**

- Clear breadcrumbs
- Department switcher
- Quick links to common actions
- User training/onboarding

#### 5. Performance

**Risk:** More joins/queries with new relationships
**Mitigation:**

- Add indexes on foreign keys
- Use eager loading
- Cache department/company data
- Optimize dashboard queries

#### 6. Backward Compatibility

**Risk:** Breaking existing functionality
**Mitigation:**

- Phased rollout
- Feature flags for new modules
- Default values for new fields
- Comprehensive testing
- **⚠️ CRITICAL:** Every phase must include regression testing of booking/floor plan workflows

#### 7. Core Feature Protection

**Risk:** New features weaken or complicate floor plan/booking core
**Mitigation:**

- **Mandatory testing:** Every phase tests that booking creation from floor plan still works
- **No breaking changes:** Existing booking flows must continue working exactly as before
- **Performance:** New features must not slow down floor plan loading or booking creation
- **UX Priority:** Floor plan and booking access must remain quick and prominent
- **Flow preservation:** Booking → Invoice → Payment chain must remain seamless

#### 8. Booking Workflow Complexity

**Risk:** New integrations make booking process feel complicated
**Mitigation:**

- Keep booking creation simple: Client + Booth Selection → Confirm → Done
- Advanced features (quotes, approvals) optional, not required for basic bookings
- Floor plan view remains primary booking entry point (visual selection)
- Quick booking option always available (bypass sales pipeline for walk-ins)

### Consistency Issues

#### Naming

- ✅ Use "Event" or "Project"? → **Recommend: "Project"** (more business-focused)
- ✅ "Booths" → "Floor Plan Management" (already planned)
- ✅ "Bookings" → Keep or rename? → **Keep** (clear meaning)

#### Filtering

- All list pages need: Company / Department / Event filters
- Reports need: Multi-level filtering
- Dashboard needs: Scope selector (My/Department/Company)

#### Permissions

- Department-level: Can only see own department data
- Event-level: Can only see assigned events
- Company-level: Admins see everything
- **⚠️ Core Protection:** Floor plan editing and booking creation permissions must be clear and granular
- **✅ Floor Plan Permissions:** Who can create/edit floor plans, who can edit booth positions, who can view
- **✅ Booking Permissions:** Who can create bookings, who can approve bookings, who can modify bookings

### Constraints for Future Changes

**Every new module or change must answer:**

1. **How does this connect to floor plans, booths, and bookings?**

   - Must show clear connection path
   - Must enhance, not bypass, the core flow

2. **Does this change make booking flows simpler and more powerful, or more complicated?**

   - Simpler = Approved ✅
   - More powerful but same complexity = Approved ✅
   - More complicated = Rejected ❌

3. **Do not introduce any architecture that:**
   - ❌ Breaks the clear chain: Event → Floor Plan → Booth → Booking
   - ❌ Makes booking management feel like a secondary feature
   - ❌ Buries floor plan access in deep navigation
   - ❌ Requires multiple clicks to create a booking from floor plan view
   - ❌ Separates booking creation from floor plan visualization

**Allowed Patterns:**

- ✅ Enhance booking with quotes, approvals, invoicing (optional layers)
- ✅ Add company-level reporting that aggregates booking data
- ✅ Integrate sales pipeline that ends in booking creation
- ✅ Add marketing campaigns that generate bookings

**Forbidden Patterns:**

- ❌ Create standalone invoicing that bypasses bookings
- ❌ Hide floor plan management under generic "Assets" or "Resources"
- ❌ Make booking creation require 5+ steps when it was 2 steps
- ❌ Separate booking management from floor plan view entirely

---

## 8. IMPLEMENTATION PRIORITY

### Must-Have (MVP) - Core Protection First

1. ✅ **Floor Plan Management Upgrade** (from FLOOR-PLAN-UPGRADE-PLAN.md)
   - Multi-floor plan support per event
   - Enhanced floor plan editor
   - **This strengthens the core before adding company features**
2. ✅ Company & Department structure (foundation layer, doesn't touch core)
3. ✅ Event → Floor Plan → Booth hierarchy (already protected, ensure it stays)
4. ✅ Finance basics (Invoices FROM bookings, Expenses, Budgets)
5. ✅ Enhanced reporting (company/department/event levels, **centered on bookings**)

### Should-Have (Phase 1) - Enhance Core

6. ✅ Sales pipeline (ends in booking creation)
7. ✅ Project costing (supports event/floor plan budgeting)
8. ✅ Task management (enhances floor plan setup, doesn't block bookings)

### Nice-to-Have (Phase 2) - Supporting Features

9. Marketing campaigns (generates bookings for events/floor plans)
10. Time tracking (tracks staff on events/floor plans)
11. Performance reviews (includes booking metrics)

---

## 9. SUCCESS METRICS

**Technical:**

- ✅ All existing booking/floor plan features still work **exactly as before**
- ✅ No data loss during migration
- ✅ Performance maintained (floor plan loading, booking creation speed)
- ✅ Zero breaking changes to booking workflow
- ✅ **Floor plan access remains quick (≤2 clicks)**
- ✅ **Booking creation from floor plan remains seamless**

**Core Protection Metrics:**

- ✅ Floor Plan Management visible and accessible from event detail (prominent button)
- ✅ Booking creation works from floor plan view (visual selection)
- ✅ Booth status tracking unchanged (available/reserved/confirmed/paid)
- ✅ Booking → Invoice → Payment flow intact
- ✅ All existing booking-related reports still work

**Business:**

- ✅ Can manage multiple events simultaneously (each with own floor plans)
- ✅ Department-level reporting works (aggregates booking data)
- ✅ Financial tracking complete (traces back to bookings)
- ✅ Sales pipeline functional (ends in booking creation)
- ✅ **Core identity maintained:** KHB EVENTS still feels like best-in-class floor plan/booking system

**UX Metrics:**

- ✅ Floor plan access: ≤2 clicks from event detail
- ✅ Booking creation: ≤3 clicks from floor plan view
- ✅ Booth availability visible at a glance (color-coded)
- ✅ Quick actions panel always visible (floor plan, booking shortcuts)

---

## 10. CORE PROTECTION CHECKLIST

**Before implementing ANY phase, verify:**

- [ ] Existing booking creation flow tested and working
- [ ] Floor plan editor tested and working
- [ ] Booth status tracking tested and working
- [ ] Payment → Booking relationship intact
- [ ] All existing reports still function
- [ ] Performance benchmarks met (floor plan load time, booking creation speed)

**After implementing EACH phase, verify:**

- [ ] Booking creation from floor plan still works (regression test)
- [ ] Floor plan access remains prominent (UX check)
- [ ] Booth status updates correctly (functional test)
- [ ] New features enhance booking flow, don't complicate it (UX review)
- [ ] No new required steps for basic booking creation (workflow check)

---

**Ready to start Phase 1?**

**Recommended Order:**

1. **First:** Complete Floor Plan Management Upgrade (from FLOOR-PLAN-UPGRADE-PLAN.md) - **strengthens core**
2. **Then:** Add Company & Department structure (foundation, doesn't touch core)
3. **Then:** Proceed with other phases, testing core protection at each step

This ensures the core engine is solid before building the company platform around it.

---

## 11. SUMMARY: CORE-FIRST ARCHITECTURE

### What This Document Protects

✅ **Booth + Floor Plan + Booking = Untouchable Core**

- Floor Plan Management = First-class feature (always prominent, quick access)
- Booking Creation = Central transaction (all workflows connect to it)
- Visual floor plan editing = Core value proposition (interactive, drag-and-drop)

✅ **Protected Workflows (Must Stay Fast & Simple):**

1. Open Event → See Floor Plans (≤2 clicks)
2. Open Floor Plan → See Booth Availability (visual, real-time)
3. Select Booths → Create Booking (≤3 clicks from floor plan)
4. Booking Created → Auto-generate Invoice (seamless)
5. Payment Received → Link to Booking (existing flow intact)

✅ **Department Modules = Supporting Layers (Enhance, Don't Replace):**

- **Sales:** Pipeline ends in booking creation (Lead → Opportunity → Quote → **Booking**)
- **Marketing:** Success measured by bookings generated (Campaign → Lead → **Booking**)
- **Finance:** Invoices generated from bookings (**Booking** → Invoice → Payment)
- **Operations:** Tasks support floor plan setup, don't block bookings
- **HR:** Staff assignments support booking workflows, don't create bottlenecks

### Implementation Priority (Core Protection First)

1. **Strengthen Core First:** Complete Floor Plan Management Upgrade
2. **Then Build Foundation:** Add Company & Department structure
3. **Then Enhance:** Add supporting modules that connect to bookings

### Success Criteria

**Technical:**

- ✅ All existing booking/floor plan features work exactly as before
- ✅ Floor plan access remains ≤2 clicks from event
- ✅ Booking creation remains ≤3 clicks from floor plan
- ✅ Performance maintained (no slowdown)

**Business:**

- ✅ KHB EVENTS still feels like best-in-class floor plan/booking system
- ✅ Company features enhance the core, don't overshadow it
- ✅ Core identity preserved while gaining company management capabilities

**UX:**

- ✅ Floor plan and booking always prominent in navigation
- ✅ Quick actions panel always visible
- ✅ Visual booth status always clear
- ✅ Booking creation feels fast and intuitive

---

**This architecture ensures KHB EVENTS becomes a full company management platform without losing its identity as the best booth, floor plan, and booking system.**
