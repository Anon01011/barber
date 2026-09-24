# Multi-Salon SaaS System: Client Feature Specification & Module Tree

This document provides a comprehensive, client-facing **Feature Specification Document** detailing all **Modules**, **Features (L1)**, **Sub-Features (L2)**, and **Sub-sub-features (L3/L4)** implemented in the multi-salon SaaS application codebase.

---

## 🌳 System Feature Tree Overview

```
Multi-Salon SaaS Application Root
├── 🏛️ Module 1: Super Admin & Multi-Tenancy Platform
│   ├── 1.1 Tenant Onboarding & Salon Lifecycle Management
│   │   ├── 1.1.1 Tenant Isolation & Query Scope Architecture
│   │   ├── 1.1.2 Self-Service Salon Registration & Domain Routing
│   │   └── 1.1.3 Salon Lifecycle States & Super Admin Impersonation
│   ├── 1.2 SaaS Subscription & Plan Governance
│   │   ├── 1.2.1 Tiered Plan Builder & Limit Quota Definitions
│   │   ├── 1.2.2 Module Access Control & Feature Guards
│   │   └── 1.2.3 Subscription Billing, Invoicing & Webhook Engine
│   └── 1.3 Super Admin Platform Governance & Intelligence
│       ├── 1.3.1 Super Admin Executive Analytics Dashboard
│       ├── 1.3.2 Global Broadcast Notifications & Email Template Builder
│       └── 1.3.3 Platform Security Audit Logging Ledger
│
├── 💈 Module 2: Salon Admin & Multi-Branch Management
│   ├── 2.1 Salon Configuration & Multi-Branch Operations
│   │   ├── 2.1.1 Multi-Branch Management & Header Context Switcher
│   │   ├── 2.1.2 Localization, Multi-Currency & Regional Settings
│   │   ├── 2.1.3 Tax Rules & Custom Thermal Receipt Branding
│   │   └── 2.1.4 Multi-Gateway SMS & WhatsApp API Setup
│   └── 2.2 Staff Roster & Role-Based Access Control (RBAC)
│       ├── 2.2.1 Granular Salon Permission Matrix & Custom Roles
│       ├── 2.2.2 Staff Profiles & Service Capability Assignments
│       ├── 2.2.3 Weekly Shift Rosters & Work Schedule Builder
│       └── 2.2.4 Staff Absence & Leave Approval Workflow
│
├── 📅 Module 3: Booking & Appointment Engine
│   ├── 3.1 Guest Online Booking Portal (Public Unauthenticated)
│   │   ├── 3.1.1 Mobile-First Step-by-Step Public Booking Flow
│   │   ├── 3.1.2 Dynamic Real-Time Slot Calculator API
│   │   ├── 3.1.3 Guest Contact Verification & Existing Customer Lookup
│   │   └── 3.1.4 Guest Rescheduling & Instant Calendar Export Invites
│   ├── 3.2 Registered Customer Portal & Self-Service
│   │   ├── 3.2.1 Authenticated Customer Account & Rebooking Engine
│   │   └── 3.2.2 Customer Self-Service Reschedule & Cancellation
│   └── 3.3 Admin Desk Calendar Operations
│       ├── 3.3.1 Multi-Staff Interactive Calendar & Drag-and-Drop
│       ├── 3.3.2 Appointment Lifecycle State Machine
│       └── 3.3.3 Direct "Send to POS Checkout" Transfer Button
│
├── 💳 Module 4: Point of Sale (POS) & Billing System
│   ├── 4.1 POS Checkout Register Engine
│   │   ├── 4.1.1 1-Click Appointment Cart Direct Import
│   │   ├── 4.1.2 Multi-Category Cart (Services, Retail, Packages, Custom)
│   │   ├── 4.1.3 Split-Tender & Multi-Payment Processing
│   │   └── 4.1.4 Dual-Language Printable Thermal Receipts (English/Arabic)
│   ├── 4.2 POS Discounts, Memberships & Package Balances
│   │   ├── 4.2.1 Line-Item & Order-Level Discount Controls
│   │   ├── 4.2.2 Membership Tier Perks Auto-Application
│   │   └── 4.2.3 Prepaid Package Session Balance Auto-Deduction
│   └── 4.3 POS Analytics & Register Audit
│       ├── 4.3.1 POS Analytics Performance Dashboard
│       ├── 4.3.2 Daily Sales, Product Sales & Service Sales Reports
│       └── 4.3.3 Refund & Void Workflows with Automatic Stock Restoration
│
├── 📦 Module 5: Inventory & Stock Management
│   ├── 5.1 Inventory Product Catalog & Tracking
│   │   ├── 5.1.1 Inventory Product Catalog & Category Management
│   │   ├── 5.1.2 Automatic Name-Based SKU & Barcode Generator
│   │   └── 5.1.3 Bulk CSV/Excel Product Import & Template Export
│   └── 5.2 Stock Procurement & Alert System
│       ├── 5.2.1 Supplier Directory & Purchase Order Tracking
│       ├── 5.2.2 Reorder Level Thresholds & Stock Alert System
│       └── 5.2.3 Inventory Audit Ledger & Stock Valuation Reports
│
├── 💼 Module 6: Staff Commission & Payroll Engine
│   ├── 6.1 Commission Profiles & Calculation Rules
│   │   ├── 6.1.1 Tiered & Multi-Service Commission Profile Builder
│   │   └── 6.1.2 Automatic Checkout Commission Posting Engine
│   └── 6.2 Payroll Ledgers & Payout Disbursal
│       ├── 6.2.1 Staff Commission Ledger & Detailed Payout Reports
│       └── 6.2.2 Payout Status Disbursal & Receipt Marking
│
├── 👥 Module 7: CRM, Customer Loyalty & Packages
│   ├── 7.1 Customer 360 & Profiles
│   │   ├── 7.1.1 Customer Profile CRM & Lifetime Spend History
│   │   └── 7.1.2 Customer Bill Activity Ledger & CSV Import/Export
│   └── 7.2 Service Packages, Memberships & Reviews
│       ├── 7.2.1 Prepaid Service Package Bundles Builder
│       ├── 7.2.2 Salon Membership Plans & Expiration Tracking
│       └── 7.2.3 Public Post-Appointment Rating & Review Feedback
│
└── 🧠 Module 8: Advanced Enterprise AI Copilot, Machine Learning & Integrated Workflows
    ├── 8.1 360° Operational Database Snapshot Engine
    │   ├── 8.1.1 Real-Time Multi-Module Memory Cache (Revenue, POS, Staff, Inventory, Bookings)
    │   ├── 8.1.2 POS Ticket & Average Basket Size Analytics
    │   └── 8.1.3 Staff Commission & Payroll Intelligence Metrics
    ├── 8.2 Natural Language Reasoning & Intent Processor
    │   ├── 8.2.1 Natural Language Query Intent Engine across 11 Operational Domains
    │   ├── 8.2.2 Conversational Command Hub with One-Click Quick Pills
    │   └── 8.2.3 Markdown Dynamic Insight Generator
    ├── 8.3 Predictive Machine Learning Analytics
    │   ├── 8.3.1 30-Day Demand & Revenue Time-Series Forecast
    │   ├── 8.3.2 Customer Churn Risk ML Classification & Scoring
    │   ├── 8.3.3 Staff Occupancy Yield & Peak Slot Demand Matrix
    │   └── 8.3.4 Inventory Stock-Out Depletion Prediction Timeline
    ├── 8.4 1-Click Autonomous Cross-Module Workflows
    │   ├── 8.4.1 Customer Churn Retention SMS Campaign Execution
    │   ├── 8.4.2 Auto Purchase Order Draft Generator
    │   ├── 8.4.3 AI Smart Slot Rebalancer & Staff Load Matcher
    │   └── 8.4.4 Dynamic Off-Peak Morning Promos & VIP Loyalty Rewards
    └── 8.5 SaaS Plan Security & Modern Activity Monitor UI
        ├── 8.5.1 Plan Feature Security Guards (`canUseFeature('AI Insights & Automation')`)
        ├── 8.5.2 Real-Time Activity Feed & Audit Monitor Card UI
        └── 8.5.3 Tenant Isolation Security Verification
```

---

## 🏛️ Module 1: Super Admin & Multi-Tenancy Platform

### 1.1 Tenant Onboarding & Salon Lifecycle Management (L1)
#### 1.1.1 Tenant Isolation & Scope Architecture (L2)
- **Multi-Tenant Database Scoping**: Enforces tenant-level data isolation via Eloquent global query scopes (`salon_id`, `branch_id`) on all tenant entities.
- **Tenant Isolation Traits**: `BelongsToSalon` and `BelongsToBranch` model traits ensuring tenant filtering on database read/write operations.
- **Context Routing Middleware**: `CheckSalonSlug`, `CheckSalonApproval`, and `SetBranchContext` middleware validating request slug against authorized tenant context.

#### 1.1.2 Self-Service Salon Registration & Domain Routing (L2)
- **Multi-Step Onboarding Form**: Responsive SaaS registration portal (`RegisterController` at `/register-salon`).
- **Automated Slug Assignment**: Automatically generates unique tenant domain slugs (`/{salon_slug}`).
- **Default Account Provisioning**: Creates tenant record in `salons` table and provisions primary Salon Admin user account.

#### 1.1.3 Salon Lifecycle States & Super Admin Impersonation (L2)
- **Status Lifecycle Workflow**: Governs tenant progression (`Pending` → `Active` → `Suspended` → `Archived`) in `SalonManagementController`.
- **Super Admin Impersonation Mode**: One-click impersonation (`salons/{id}/impersonate`) to enter tenant dashboard with floating return bar.
- **Approval Actions**: Approval and rejection buttons (`approve` route) with automated status email notifications.

---

### 1.2 SaaS Subscription & Plan Governance (L1)
#### 1.2.1 Tiered Plan Builder & Limit Quota Definitions (L2)
- **Subscription Plan Builder**: Super Admin plan CRUD dashboard (`PlanManagementController` at `/admin/plans`).
- **Granular Quota Limits**: Configurable caps for `max_branches`, `max_staff`, and `max_monthly_bookings`.
- **Feature Flag Control**: Toggleable feature access flags for `POS System`, `SMS Notifications`, `WhatsApp Notifications`, `AI Insights & Automation`, and `Multi-Branch Support`.

#### 1.2.2 Module Access Control & Feature Guards (L2)
- **Module Activation Manager**: `ModuleManagementController` and `ModuleHelper` controlling active features per salon.
- **Middleware Access Guards**: `plan_feature` and `module` middleware throwing HTTP `403 Forbidden` if feature is absent from plan.
- **UI Guard Directives**: Blade template directives dynamically hiding restricted navigation links.

#### 1.2.3 Subscription Billing, Invoicing & Webhook Engine (L2)
- **Automated Billing Gateways**: Stripe API credit card processing and manual offline bank transfers (`PaymentController`, `SaasPayment`).
- **PDF Invoice Generation**: Downloadable PDF invoice generation engine (`SaasInvoiceController`).
- **Stripe Webhook Listener**: Asynchronous webhook handler (`StripeWebhookController`) processing `invoice.payment_succeeded`, `invoice.payment_failed`, and `customer.subscription.deleted`.

---

### 1.3 Super Admin Platform Governance & Intelligence (L1)
#### 1.3.1 Super Admin Executive Analytics Dashboard (L2)
- **Platform Health Metrics**: Aggregated tracking of Total Active Salons, Active Subscriptions, Monthly Recurring Revenue (MRR), and Churn.
- **Growth Breakdown**: Subscription tier breakdown charts and revenue growth visualizations (`SuperAdmin/DashboardController`).

#### 1.3.2 Global Broadcast Notifications & Email Template Builder (L2)
- **Email Template Editor**: Rich text email builder (`EmailTemplateController`) supporting dynamic placeholders (`{{salon_name}}`, `{{expiry_date}}`).
- **System Broadcast Engine**: `SystemNotificationController` broadcasting system notifications to all tenant owners.

#### 1.3.3 Security Audit Logging Ledger (L2)
- **Audit Log Ledger**: `SuperAdminAuditLog` recording Super Admin actions (impersonations, plan changes, tenant suspensions).

---

## 💈 Module 2: Salon Admin & Multi-Branch Management

### 2.1 Salon Configuration & Multi-Branch Operations (L1)
#### 2.1.1 Multi-Branch Management & Header Context Switcher (L2)
- **Branch CRUD Operations**: Add, edit, view, and deactivate physical branch locations (`BranchController`).
- **Header Branch Switcher**: Dynamic dropdown (`branch.switch`) switching operational scope across POS, calendar, inventory, and staff.

#### 2.1.2 Localization, Multi-Currency & Regional Settings (L2)
- **Salon Localization**: `HandlesSalonSettings` trait and `SettingsService` configuring Timezone (`salon_timezone()`), Date Format, and Time Format.
- **Multi-Currency Engine**: Salon Currency Symbol (`$`, `€`, `£`, `₹`), Currency Code (`USD`, `EUR`, `CUSTOM`), and currency helper `format_currency()`.
- **Tax Configurations**: Tax toggle (`tax_enabled`), Tax Name (VAT/GST/Sales Tax), Tax Rate percentage, and POS tax toggle (`tax_enabled_pos`).
- **Thermal Receipt Branding**: Logo upload, receipt header/footer text, and Arabic receipt support (`pos_receipt_arabic`).

#### 2.1.3 Multi-Gateway SMS & WhatsApp API Setup (L2)
- **Multi-Gateway SMS**: Setup interface (`SmsSetupController`, `SmsService`) supporting Twilio, Vonage (Nexmo), MessageBird, and Custom HTTP APIs.
- **WhatsApp Integration**: Setup interface (`WhatsappSetupController`, `WhatsappService`) for automated WhatsApp notifications.

---

### 2.2 Staff Roster & Role-Based Access Control (RBAC) (L1)
#### 2.2.1 Granular Salon Permission Matrix & Custom Roles (L2)
- **Permission Assignment Matrix**: Custom role creation and permission mapping (`RoleController`, `SalonRoleController`, Spatie Permissions).
- **Default Roles**: Pre-configured system roles: `super_admin`, `salon_admin`, `manager`, `employee`, `customer`.

#### 2.2.2 Staff Profiles & Service Capability Assignments (L2)
- **Staff Profile Directory**: Employee creation (`EmployeeController`, `StaffController`) with photo, bio, phone, and branch assignment.
- **Service Mapping**: Granular mapping of specific services each staff member is qualified to perform.

#### 2.2.3 Weekly Shift Rosters & Work Schedule Builder (L2)
- **Roster Management**: Weekly schedule builder (`StaffSchedule`, `StaffDailySchedule`) defining daily start time, end time, and break hours.

#### 2.2.4 Staff Absence & Leave Approval Workflow (L2)
- **Leave Logger**: Staff absence manager (`StaffAbsence`) tracking start date, end date, and leave reason.
- **Calendar Slot Blocking**: Excludes absent staff members from dynamic online slot availability calculations.

---

## 📅 Module 3: Booking & Appointment Engine

### 3.1 Guest Online Booking Portal (Public Unauthenticated) (L1)
#### 3.1.1 Mobile-First Step-by-Step Public Booking Flow (L2)
- **Public Portal Interface**: Responsive public booking page (`GuestBookingController` at `/{salon_slug}/booking/guest`).
- **Step 1: Category & Service Selection**: Category filtering tabs, service details (price, duration, description), and add-on services.
- **Step 2: Staff Preference Selection**: Select "Any Available Staff" or choose a specific preferred stylist.

#### 3.1.2 Dynamic Real-Time Slot Calculator API (L2)
- **Slot Availability Engine**: Time slot calculation API (`getAvailableSlots`) evaluating branch hours, staff schedules, absences, and existing appointments.

#### 3.1.3 Guest Contact Verification & Existing Customer Lookup (L2)
- **Guest Verification**: Captures guest name, email, and phone, automatically matching existing customer records.

#### 3.1.4 Guest Rescheduling & Instant Calendar Export Invites (L2)
- **Guest Reschedule Lookup**: Lookup guest bookings by reference code and reschedule date/time (`guest.booking.reschedule`).
- **Calendar Event Export**: One-click "Add to Google Calendar" and iCal/Apple Calendar `.ics` event download buttons.

---

### 3.2 Registered Customer Portal & Self-Service (L1)
#### 3.2.1 Authenticated Customer Account & Rebooking Engine (L2)
- **Customer Dashboard**: Authenticated customer account view (`Customer/AppointmentController`, `Customer/ServiceController`).
- **1-Click Rebooking**: Pre-fills service and staff preferences from past appointment history.

#### 3.2.2 Customer Self-Service Reschedule & Cancellation (L2)
- **Self-Service Actions**: Customer cancellation and rescheduling subject to salon cancellation window policy.

---

### 3.3 Admin Desk Calendar Operations (L1)
#### 3.3.1 Multi-Staff Interactive Calendar & Drag-and-Drop (L2)
- **Desk Calendar Views**: Daily, weekly, and staff column calendar views (`Admin/BookingController`).
- **Quick Availability API**: Instant time slot lookup modal (`bookings.available-slots`).

#### 3.3.2 Appointment Lifecycle State Machine (L2)
- **State Machine Transitions**: `Pending` → `Confirmed` → `In-Progress` → `Completed` → `Cancelled` → `No-Show`.
- **Checkout Transfer**: Direct "Send to POS Checkout" button transferring completed appointments to POS register cart.

---

## 💳 Module 4: Point of Sale (POS) & Billing System

### 4.1 POS Checkout Register Engine (L1)
#### 4.1.1 1-Click Appointment Cart Direct Import (L2)
- **Direct Import**: Imports completed or ongoing appointments directly into POS checkout cart (`getBookingDetailsForPos`).

#### 4.1.2 Multi-Category Cart (Services, Retail, Packages, Custom) (L2)
- **Multi-Item Cart**: Supports adding services, inventory retail items, prepaid service packages, and custom manual items.

#### 4.1.3 Split-Tender & Multi-Payment Processing (L2)
- **Payment Processing**: Process payments (`processPayment`, `checkout`) supporting Cash, Card, Online payments, and split payment tenders.

#### 4.1.4 Dual-Language Printable Thermal Receipts (English/Arabic) (L2)
- **Thermal Receipts**: Native browser printing for 80mm/58mm thermal printers in English and Arabic (`receipt`, `arabicReceipt`).

---

### 4.2 POS Discounts, Memberships & Package Balances (L1)
#### 4.2.1 Line-Item & Order-Level Discount Controls (L2)
- **Discount Engine**: Percentage (%) or fixed amount ($) discounts per item or across total order.

#### 4.2.2 Membership Tier Perks Auto-Application (L2)
- **Membership Detection**: Automatically detects active customer membership tier (`getCustomerMembership`) applying tier discount rates.

#### 4.2.3 Prepaid Package Session Balance Auto-Deduction (L2)
- **Package Session Tracking**: Tracks pre-purchased package balances (`CustomerPackageBalance`), auto-deducting sessions upon checkout.

---

### 4.3 POS Analytics & Register Audit (L1)
#### 4.3.1 POS Analytics Performance Dashboard (L2)
- **Analytics View**: POS performance dashboard (`POS/AnalyticsController`) tracking sales by hour, payment method breakdown, and top staff.

#### 4.3.2 Daily Sales, Product Sales & Service Sales Reports (L2)
- **Sales Reports**: Daily sales reports (`dailySalesReport`), product sales reports, and service sales reports.

#### 4.3.3 Refund & Void Workflows with Automatic Stock Restoration (L2)
- **Refund & Void**: Order refund (`processRefund`) and void sale (`voidSale`) controls with automatic inventory stock restoration.

---

## 📦 Module 5: Inventory & Stock Management

### 5.1 Inventory Product Catalog & Tracking (L1)
#### 5.1.1 Inventory Product Catalog & Category Management (L2)
- **Catalog Management**: Product manager (`InventoryController`, `InventoryCategoryController`) tracking retail products and internal consumables.

#### 5.1.2 Automatic Name-Based SKU & Barcode Generator (L2)
- **SKU Generator**: Automated name-based SKU generator (`generateNameBasedSku`) and barcode tracker.

#### 5.1.3 Bulk CSV/Excel Product Import & Template Export (L2)
- **Import/Export Tool**: Bulk product import and template download (`ProductImportExportController`).

---

### 5.2 Stock Procurement & Alert System (L1)
#### 5.2.1 Supplier Directory & Purchase Order Tracking (L2)
- **Supplier Directory**: Supplier profile manager (`Supplier` model) with supplier contact info and supplied item mapping.

#### 5.2.2 Reorder Level Thresholds & Stock Alert System (L2)
- **Stock Alert Manager**: Automated low-stock alert generator (`StockAlertController`) triggering dashboard warning widgets.

#### 5.2.3 Inventory Audit Ledger & Stock Valuation Reports (L2)
- **Transaction Audit Ledger**: Stock movement logs (`InventoryTransaction`) and inventory valuation reports (`InventoryReportController`).

---

## 💼 Module 6: Staff Commission & Payroll Engine

### 6.1 Commission Profiles & Calculation Rules (L1)
#### 6.1.1 Tiered & Multi-Service Commission Profile Builder (L2)
- **Commission Profile Manager**: Profile builder (`CommissionProfileController`, `CommissionRule`) supporting fixed rates, percentage rates, and tiered revenue targets.

#### 6.1.2 Automatic Checkout Commission Posting Engine (L2)
- **Automatic Ledger Posting**: Automatically calculates and posts earned commissions to `staff_commissions` table upon POS checkout completion.

---

### 6.2 Payroll Ledgers & Payout Disbursal (L1)
#### 6.2.1 Staff Commission Ledger & Detailed Payout Reports (L2)
- **Commission Reports**: Detailed staff payout ledger (`CommissionReportController`) filterable by staff, branch, and date range.

#### 6.2.2 Payout Status Disbursal & Receipt Marking (L2)
- **Payout Disbursal**: Disbursal action marking commission records as `Paid`.

---

## 👥 Module 7: CRM, Customer Loyalty & Packages

### 7.1 Customer 360 & Profiles (L1)
#### 7.1.1 Customer Profile CRM & Lifetime Spend History (L2)
- **Customer CRM**: Complete profile view (`CustomerController`) consolidating lifetime spend, total visit count, and contact information.

#### 7.1.2 Customer Bill Activity Ledger & CSV Import/Export (L2)
- **Transaction Ledger**: Detailed customer billing activity history (`getBillActivity`) and bulk CSV import/export (`customers.import`, `customers.export`).

---

### 7.2 Service Packages, Memberships & Reviews (L1)
#### 7.2.1 Prepaid Service Package Bundles Builder (L2)
- **Package Builder**: Package creation interface (`PackageController`) bundling multi-service treatments at promotional pricing.

#### 7.2.2 Salon Membership Plans & Expiration Tracking (L2)
- **Membership Plans**: Recurring membership plan manager (`MembershipController`, `CustomerMembership`).

#### 7.2.3 Public Post-Appointment Rating & Review Feedback (L2)
- **Post-Appointment Reviews**: Public guest rating forms (`RatingController`) with tokenized review links (`/rating/{token}`).

---

## 🧠 Module 8: Advanced Enterprise AI Copilot, Machine Learning & Integrated Workflows

### 8.1 360° Operational Database Snapshot Engine (L1)
#### 8.1.1 Real-Time Multi-Module Memory Cache (L2)
- **Multi-Module Snapshot Cache**: High-performance snapshot engine (`AiMemoryService::getSalonDatabaseSnapshot`) capturing real-time metrics across all 7 operational modules.

#### 8.1.2 POS Ticket & Average Basket Size Analytics (L2)
- **Basket Size Intelligence**: Computes today's sales count, total tips, and average ticket/basket size (`avg_basket_size`) for POS register optimization.

#### 8.1.3 Staff Commission & Payroll Intelligence Metrics (L2)
- **Commission Snapshot**: Captures month-to-date staff commissions (`month_commissions`), active staff count, and pending payroll disbursals.

---

### 8.2 Natural Language Reasoning & Intent Processor (L1)
#### 8.2.1 Natural Language Query Intent Engine across 11 Operational Domains (L2)
- **Multi-Domain Reasoning**: Advanced intent matching engine (`AiReasoningEngine`) supporting 11 core operational domains:
  1. *Greetings & Friendly Chat*
  2. *Top Customer & VIP Client Profiling*
  3. *Top Service & Treatment Popularity*
  4. *Staff Performance & Absenteeism*
  5. *POS Register, Ticket Size & Tips*
  6. *Staff Commission & Payroll*
  7. *Customer Loyalty, Memberships & Packages*
  8. *Appointment Health, Cancellations & No-Shows*
  9. *Revenue Comparison & Financial Growth*
  10. *Inventory Stock Depletion & Asset Valuation*
  11. *General Operational Strategy*

#### 8.2.2 Conversational Command Hub with One-Click Quick Pills (L2)
- **Command Hub UI**: Interactive Copilot dashboard (`/admin/ai/hub`) featuring 1-click query pills for instant operational analysis.

#### 8.2.3 Markdown Dynamic Insight Generator (L2)
- **Structured Formatting**: Renders responses in clean GitHub-Flavored Markdown with bullet points, bold metrics, and suggested action buttons.

---

### 8.3 Predictive Machine Learning Intelligence (L1)
#### 8.3.1 30-Day Demand & Revenue Time-Series Forecast (L2)
- **Time-Series Forecasting**: 60-Day historical regression model (`AiAnalyticsService::generateDemandForecast`) predicting next 30 days revenue and appointment volume with day-of-week seasonality.

#### 8.3.2 Customer Churn Risk ML Classification & Scoring (L2)
- **Churn Classification**: Heuristic churn risk scoring (`calculateCustomerChurnRisk`) identifying high-risk inactive clients (>45 days idle).

#### 8.3.3 Staff Occupancy Yield & Peak Slot Demand Matrix (L2)
- **Yield Matrix**: Staff utilization analysis (`analyzeStaffUtilization`) and hourly slot demand matrix identifying peak traffic hours.

#### 8.3.4 Inventory Stock-Out Depletion Prediction Timeline (L2)
- **Depletion Predictor**: Product consumption rate calculator (`predictInventoryDepletion`) estimating days remaining before stockout.

---

### 8.4 1-Click Autonomous Cross-Module Workflows (L1)
#### 8.4.1 Customer Churn Retention SMS Campaign Execution (L2)
- **Retention Campaign**: Automated workflow (`AiChurnAutomationEngine`) generating unique discount voucher codes and sending retention SMS alerts.

#### 8.4.2 Auto Purchase Order Draft Generator (L2)
- **PO Draft Generator**: Automated calculation (`AiInventoryReorderEngine`) generating recommended reorder quantities for low-stock items.

#### 8.4.3 AI Smart Slot Rebalancer & Staff Load Matcher (L2)
- **Smart Slot Rebalancer**: Load-balancing slot recommender (`AiSmartSchedulerEngine`) matching customer preferences with staff load and historical affinity.

#### 8.4.4 Dynamic Off-Peak Morning Promos & VIP Loyalty Rewards (L2)
- **1-Click Promotions**: Publishing off-peak morning slot discounts (9 AM - 11 AM) and awarding bonus loyalty points to top-spending clients.

---

### 8.5 SaaS Plan Security & Modern Activity Monitor UI (L1)
#### 8.5.1 Plan Feature Security Guards (L2)
- **Strict Plan Feature Check**: Controller level validation (`$salon->canUseFeature('AI Insights & Automation')`) on `index()`, `chat()`, `runAutomation()`, and `getForecastData()` returning HTTP 403 if unauthorized.
- **Tenant Scope Isolation**: Strict verification checking authenticated salon ID against requested salon slug.

#### 8.5.2 Real-Time Activity Feed & Audit Monitor Card UI (L2)
- **Modern UI Card**: Replaced plain text log box with modern live operational audit monitor featuring pulse status indicator (`LIVE ENGINE ACTIVE`), event category badges (`[SYSTEM]`, `[SUCCESS]`, `[ERROR]`), and formatted timeline items.
