# Multi-Salon SaaS System: Verified Features & Sub-Features Specification

This document provides a comprehensive, itemized **bullet-point specification** of all **Modules**, **Features (L1)**, **Sub-Features (L2)**, and **Sub-sub-features (L3)** currently implemented in the multi-salon SaaS codebase.

---

## 🏛️ Module 1: Super Admin & Multi-Tenancy Platform

### 1.1 Feature: Tenant Onboarding & Salon Lifecycle Management (L1)
#### 1.1.1 Sub-Feature: Tenant Isolation & Scope Architecture (L2)
##### 1.1.1.1 Sub-sub-feature: Multi-Tenant Database Query Scope (L3)
- **Global Eloquent Scopes**: Enforces `salon_id` and `branch_id` query scoping across all models (`Salon`, `Branch`, `User`, `Booking`, `PosSale`, `InventoryItem`, `Customer`, etc.).
- **Multi-Tenancy Traits**: `BelongsToSalon` and `BelongsToBranch` traits attached to database models for automatic tenant filtering.
- **Tenant Context Middleware**: `CheckSalonSlug`, `CheckSalonApproval`, and `SetBranchContext` middleware validating request domain/slug against active tenant.

##### 1.1.1.2 Sub-sub-feature: Salon Registration & Domain/Slug Routing (L3)
- **Self-Service Registration**: Multi-step registration controller (`RegisterController` at `/register-salon`).
- **Automated Slug Generator**: Generates unique salon URL slugs (`/{salon_slug}`) upon tenant account creation.
- **Admin Account Provisioning**: Automatically creates primary Salon Admin user profile linked to the newly registered salon ID.

##### 1.1.1.3 Sub-sub-feature: Salon Status Lifecycle & Impersonation (L3)
- **Status Lifecycle State Machine**: Supports `Pending` → `Active` → `Suspended` → `Archived` tenant states (`SalonManagementController`).
- **Super Admin Impersonation**: One-click Super Admin impersonation (`salons/{id}/impersonate`) to access tenant dashboards with top exit bar.
- **Approval Workflow**: Manual or automatic registration approval buttons (`approve` route) triggering confirmation notifications.

---

### 1.2 Feature: SaaS Subscription & Plan Governance (L1)
#### 1.2.1 Sub-Feature: Tiered Plan Management & Feature Guards (L2)
##### 1.2.1.1 Sub-sub-feature: Tiered Plan Builder & Limit Definitions (L3)
- **Plan Builder CRUD**: Super Admin plan management interface (`PlanManagementController` at `/admin/plans`).
- **Quota Limit Definitions**: Configurable numeric limits for `max_branches`, `max_staff`, and `max_monthly_bookings`.
- **Feature Flag Definitions**: Toggleable flags for `POS System`, `SMS Notifications`, `WhatsApp Notifications`, `AI Insights & Automation`, `Multi-Branch Support`.

##### 1.2.1.2 Sub-sub-feature: Module Access Control & Feature Guards (L3)
- **Module Manager**: `ModuleManagementController` and `ModuleHelper` controlling salon module activations.
- **Route Feature Middleware**: `plan_feature` and `module` middleware blocking route execution if feature is disabled in salon's active plan.
- **UI Guard Directives**: Blade conditional directives hiding restricted sidebar menu items based on plan permissions.

#### 1.2.2 Sub-Feature: Subscription Billing & Invoicing (L2)
##### 1.2.2.1 Sub-sub-feature: Automated Recurring Billing & Gateways (L3)
- **Stripe Integration**: Credit card subscription billing handled via `PaymentController` and `SaasPayment`.
- **Offline Bank Transfer**: Manual bank transfer submission and Super Admin approval ledger.
- **Invoice PDF Generator**: Downloadable PDF invoice generation engine (`SaasInvoiceController`).

##### 1.2.2.2 Sub-sub-feature: Stripe Webhook Event Listener (L3)
- **Webhook Controller**: `StripeWebhookController` processing real-time asynchronous Stripe events.
- **Automated Subscription Updates**: Event handling for `invoice.payment_succeeded`, `invoice.payment_failed`, and `customer.subscription.deleted`.

---

### 1.3 Feature: Super Admin Platform Governance & Intelligence (L1)
#### 1.3.1 Sub-Feature: Global Analytics & System Notifications (L2)
##### 1.3.1.1 Sub-sub-feature: Executive Super Admin Dashboard (L3)
- **Metrics Overview**: Real-time tracking of Total Salons, Active Subscriptions, Monthly Recurring Revenue (MRR), and Churn.
- **Distribution Charts**: Plan distribution breakdown and monthly revenue growth charts (`SuperAdmin/DashboardController`).

##### 1.3.1.2 Sub-sub-feature: System Broadcast Notifications & Email Templates (L3)
- **Email Template Editor**: Rich template builder (`EmailTemplateController`) with dynamic placeholders (`{{salon_name}}`, `{{expiry_date}}`, etc.).
- **Global Broadcast Engine**: `SystemNotificationController` broadcasting system maintenance and plan update alerts.

##### 1.3.1.3 Sub-sub-feature: Security Audit Logging (L3)
- **Audit Log Ledger**: `SuperAdminAuditLog` table capturing administrative actions, impersonation logs, status updates, and plan alterations.

---

## 💈 Module 2: Salon Admin & Multi-Branch Management

### 2.1 Feature: Salon Configuration & Multi-Branch Operations (L1)
#### 2.1.1 Sub-Feature: Branch Management & Context Switching (L2)
##### 2.1.1.1 Sub-sub-feature: Branch CRUD & Context Switcher (L3)
- **Branch Management**: Create, view, edit, and deactivate branch locations (`BranchController`).
- **Header Branch Switcher**: Dynamic branch switcher dropdown (`branch.switch`) scoping POS, appointments, inventory, and staff to selected branch ID.

#### 2.1.2 Sub-Feature: Localization, Tax Rules & Receipt Branding (L2)
##### 2.1.2.1 Sub-sub-feature: Salon Localization & Settings (L3)
- **Global Settings Trait**: `HandlesSalonSettings` trait and `SettingsService` managing key-value salon settings.
- **Regional Formats**: Configurable Timezone (`salon_timezone()`), Date Format, Time Format (12h/24h), and Week Start Day.
- **Multi-Currency System**: Salon Currency Symbol (`$`, `€`, `£`, `₹`, etc.), Currency Code (`USD`, `EUR`, `CUSTOM`), and helper function `format_currency()`.

##### 2.1.2.2 Sub-sub-feature: Branch Tax & Custom Receipt Configuration (L3)
- **Tax Configurations**: Tax toggle (`tax_enabled`), Tax Name (VAT/GST/Sales Tax), Tax Rate percentage, Tax Number, and POS tax toggle (`tax_enabled_pos`).
- **Receipt Customization**: Logo upload, header text, footer text, and Arabic receipt toggle (`pos_receipt_arabic`).

#### 2.1.3 Sub-Feature: Notification Gateway Setup (L2)
##### 2.1.3.1 Sub-sub-feature: Multi-Gateway SMS Integration (L3)
- **SMS Gateway Setup**: Setup interface (`SmsSetupController`, `SmsService`) supporting Twilio, Vonage (Nexmo), MessageBird, and Custom HTTP endpoints.
- **Testing Utility**: Built-in test SMS send button to verify API credential validity.

##### 2.1.3.2 Sub-sub-feature: WhatsApp Messaging API Setup (L3)
- **WhatsApp Configuration**: Setup interface (`WhatsappSetupController`, `WhatsappService`) for automated appointment notifications via WhatsApp.

---

### 2.2 Feature: Staff Roster & Role-Based Access Control (RBAC) (L1)
#### 2.2.1 Sub-Feature: Salon Roles & Permission Engine (L2)
##### 2.2.1.1 Sub-sub-feature: Custom Salon Role Definitions (L3)
- **Permission Matrix**: Granular permission assignments (`RoleController`, `SalonRoleController`, Spatie Laravel-Permissions).
- **Default Roles**: Pre-configured system roles: `super_admin`, `salon_admin`, `manager`, `employee`, `customer`.

#### 2.2.2 Sub-Feature: Staff Directory & Shift Rosters (L2)
##### 2.2.2.1 Sub-sub-feature: Staff Profiles & Service Capability Mapping (L3)
- **Staff Management**: Employee profile creation (`EmployeeController`, `StaffController`) with photo, bio, phone, and branch assignment.
- **Service Mapping**: Assigning specific salon services each staff member is qualified to perform.

##### 2.2.2.2 Sub-sub-feature: Staff Shift Rosters & Work Schedules (L3)
- **Shift Schedule Builder**: Weekly work schedule manager (`StaffSchedule`, `StaffDailySchedule`) defining daily start time, end time, and break times.

##### 2.2.2.3 Sub-sub-feature: Staff Absence & Leave Management (L3)
- **Leave Workflow**: Staff absence logger (`StaffAbsence`) tracking start date, end date, and leave reason.
- **Calendar Slot Blocking**: Automatically excludes absent staff from live booking slot availability engine.

---

## 📅 Module 3: Booking & Appointment Engine

### 3.1 Feature: Guest Online Booking Portal (Public) (L1)
#### 3.1.1 Sub-Feature: Mobile-First Public Booking Flow (L2)
##### 3.1.1.1 Sub-sub-feature: Step-by-Step Service & Staff Selection (L3)
- **Public Booking Interface**: Clean responsive public portal (`GuestBookingController` at `/{salon_slug}/booking/guest`).
- **Category & Service Picker**: Category tab filtering, service selection with pricing, duration, description, and add-ons.
- **Staff Preference Picker**: Select "Any Available Staff" or specific preferred stylist.

##### 3.1.1.2 Sub-sub-feature: Real-Time Dynamic Slot Calculator (L3)
- **Slot Calculation Engine**: High-performance time slot availability API (`getAvailableSlots`) evaluating branch hours, staff schedules, staff leave, and existing bookings.

##### 3.1.1.3 Sub-sub-feature: Guest Rescheduling & Instant Calendar Export (L3)
- **Guest Reschedule Lookup**: Lookup guest bookings by reference code/phone and reschedule date/time (`guest.booking.reschedule`).
- **Calendar Invites**: One-click "Add to Google Calendar" and downloadable iCal/Apple Calendar `.ics` event files.

---

### 3.2 Feature: Registered Customer Portal & Self-Service (L1)
#### 3.2.1 Sub-Feature: Customer Account & Rebooking Engine (L2)
##### 3.2.1.1 Sub-sub-feature: Customer Portal & Booking History (L3)
- **Customer Dashboard**: Authenticated customer account view (`Customer/AppointmentController`, `Customer/ServiceController`).
- **Rebooking Button**: 1-Click repeat rebooking pre-filling service and staff preferences.

##### 3.2.1.2 Sub-sub-feature: Self-Service Reschedule & Cancellation (L3)
- **Self-Service Actions**: Customer cancellation and rescheduling subject to salon cancellation window policy.

---

### 3.3 Feature: Admin Desk Calendar Operations (L1)
#### 3.3.1 Sub-Feature: Interactive Desk Calendar (L2)
##### 3.3.1.1 Sub-sub-feature: Multi-Staff Calendar Views (L3)
- **Interactive Calendar**: Daily, weekly, and staff column calendar views (`Admin/BookingController`).
- **Quick Availability API**: Instant time slot lookup modal (`bookings.available-slots`).

##### 3.3.1.2 Sub-sub-feature: Appointment Lifecycle State Machine (L3)
- **Lifecycle Transitions**: State machine enforcing transitions: `Pending` → `Confirmed` → `In-Progress` → `Completed` → `Cancelled` → `No-Show`.
- **Checkout Transfer**: Direct "Send to POS Checkout" button transferring completed appointments to POS register.

---

## 💳 Module 4: Point of Sale (POS) & Billing System

### 4.1 Feature: POS Checkout Register Engine (L1)
#### 4.1.1 Sub-Feature: Cart Building & Multi-Item Checkout (L2)
##### 4.1.1.1 Sub-sub-feature: Appointment Direct Cart Import (L3)
- **1-Click Booking Import**: Load completed or ongoing appointments directly into POS checkout cart (`getBookingDetailsForPos`).

##### 4.1.1.2 Sub-sub-feature: Multi-Category POS Cart (L3)
- **Cart Building**: Supports adding services, inventory retail items, prepaid service packages, and custom manual line items.

##### 4.1.1.3 Sub-sub-feature: Split-Tender & Multi-Payment Processing (L3)
- **Payment Processing**: Process payments (`processPayment`, `checkout`) supporting Cash, Card, Online payments, and split payment tenders.

##### 4.1.1.4 Sub-sub-feature: Dual-Language Thermal Receipt Printing (L3)
- **Thermal Receipts**: Native browser printing for 80mm/58mm thermal printers in English and Arabic (`receipt`, `arabicReceipt`).

---

### 4.2 Feature: POS Discounts, Memberships & Package Balances (L1)
#### 4.2.1 Sub-Feature: Discounts & Loyalty Deductions (L2)
##### 4.2.1.1 Sub-sub-feature: Line-Item & Cart Discount Engine (L3)
- **Discount Controls**: Percentage (%) or fixed amount ($) discounts per item or across total order.

##### 4.2.1.2 Sub-sub-feature: Membership Perk Auto-Application (L3)
- **Membership Detection**: Automatically detects active customer membership tier (`getCustomerMembership`) applying tier discount rates.

##### 4.2.1.3 Sub-sub-feature: Prepaid Package Session Balance Deduction (L3)
- **Package Session Tracking**: Tracks pre-purchased package balances (`CustomerPackageBalance`), auto-deducting sessions upon checkout.

---

### 4.3 Feature: POS Sales Analytics & Register Audit (L1)
#### 4.3.1 Sub-Feature: POS Reporting & Refund Controls (L2)
##### 4.3.1.1 Sub-sub-feature: POS Analytics Dashboard (L3)
- **Analytics View**: POS performance dashboard (`POS/AnalyticsController`) tracking sales by hour, payment method breakdown, and top staff.

##### 4.3.1.2 Sub-sub-feature: Sales Ledger & Refund/Void Workflow (L3)
- **Sales Reports**: Daily sales reports (`dailySalesReport`), product sales reports, and service sales reports.
- **Refund & Void**: Order refund (`processRefund`) and void sale (`voidSale`) controls with automatic inventory stock restoration.

---

## 📦 Module 5: Inventory & Stock Management

### 5.1 Feature: Inventory Product Catalog & Tracking (L1)
#### 5.1.1 Sub-Feature: Catalog & Barcode/SKU Generator (L2)
##### 5.1.1.1 Sub-sub-feature: Product Catalog & Categories (L3)
- **Catalog Management**: Product manager (`InventoryController`, `InventoryCategoryController`) tracking retail products and internal consumables.

##### 5.1.1.2 Sub-sub-feature: Automatic SKU & Barcode Generator (L3)
- **SKU Generator**: Automated name-based SKU generator (`generateNameBasedSku`) and barcode tracker.

##### 5.1.1.3 Sub-sub-feature: Bulk CSV/Excel Product Import & Export (L3)
- **Import/Export Tool**: Bulk product import and template download (`ProductImportExportController`).

---

### 5.2 Feature: Stock Procurement & Alert System (L2)
#### 5.2.1 Sub-Feature: Procurement & Inventory Audit (L2)
##### 5.2.1.1 Sub-sub-feature: Supplier Directory & Purchase Orders (L3)
- **Supplier Directory**: Supplier profile manager (`Supplier` model) with supplier contact info and supplied item mapping.

##### 5.2.1.2 Sub-sub-feature: Reorder Level Thresholds & Stock Alert System (L3)
- **Stock Alert Manager**: Automated low-stock alert generator (`StockAlertController`) triggering dashboard warning widgets.

##### 5.2.1.3 Sub-sub-feature: Stock Movement & Valuation Reports (L3)
- **Transaction Audit Ledger**: Stock movement logs (`InventoryTransaction`) and inventory valuation reports (`InventoryReportController`).

---

## 💼 Module 6: Staff Commission & Payroll Engine

### 6.1 Feature: Commission Profiles & Calculation Rules (L1)
#### 6.1.1 Sub-Feature: Commission Rule Builder (L2)
##### 6.1.1.1 Sub-sub-feature: Tiered & Multi-Service Commission Profiles (L3)
- **Commission Profile Manager**: Profile builder (`CommissionProfileController`, `CommissionRule`) supporting fixed rates, percentage rates, and tiered revenue targets.

##### 6.1.1.2 Sub-sub-feature: Automated Checkout Commission Posting (L3)
- **Automatic Ledger Posting**: Automatically calculates and posts earned commissions to `staff_commissions` table upon POS checkout completion.

---

### 6.2 Feature: Payroll Ledgers & Payout Disbursal (L1)
#### 6.2.1 Sub-Feature: Payroll Summaries & Disbursal (L2)
##### 6.2.1.1 Sub-sub-feature: Staff Commission Ledger & Payout Reports (L3)
- **Commission Reports**: Detailed staff payout ledger (`CommissionReportController`) filterable by staff, branch, and date range.
- **Payout Disbursal**: Disbursal action marking commission records as `Paid`.

---

## 👥 Module 7: CRM, Customer Loyalty & Packages

### 7.1 Feature: Customer 360 & Profiles (L1)
#### 7.1.1 Sub-Feature: Customer Relationship Management (L2)
##### 7.1.1.1 Sub-sub-feature: Customer Profile & Spend History (L3)
- **Customer CRM**: Complete profile view (`CustomerController`) consolidating lifetime spend, total visit count, and contact information.

##### 7.1.1.2 Sub-sub-feature: Customer Bill Activity Ledger & Import/Export (L3)
- **Transaction Ledger**: Detailed customer billing activity history (`getBillActivity`) and bulk CSV import/export (`customers.import`, `customers.export`).

---

### 7.2 Feature: Service Packages, Memberships & Reviews (L1)
#### 7.2.1 Sub-Feature: Service Packages & Memberships (L2)
##### 7.2.1.1 Sub-sub-feature: Prepaid Service Package Bundles (L3)
- **Package Builder**: Package creation interface (`PackageController`) bundling multi-service treatments at promotional pricing.

##### 7.2.1.2 Sub-sub-feature: Salon Membership Plans (L3)
- **Membership Plans**: Recurring membership plan manager (`MembershipController`, `CustomerMembership`).

##### 7.2.1.3 Sub-sub-feature: Customer Rating & Review Feedback (L3)
- **Post-Appointment Reviews**: Public guest rating forms (`RatingController`) with tokenized review links (`/rating/{token}`).

---

## 🧠 Module 8: AI Copilot, Machine Learning & Autonomous Workflows

### 8.1 Feature: AI Salon Copilot & Natural Language Reasoning (L1)
#### 8.1.1 Sub-Feature: Operational Database Snapshot & Copilot Reasoning (L2)
##### 8.1.1.1 Sub-sub-feature: Real-Time Operational Database Snapshot (L3)
- **Snapshot Engine**: Real-time snapshot builder (`AiMemoryService::getSalonDatabaseSnapshot`) capturing revenue, appointments, top staff, top customer, and low stock metrics.

##### 8.1.1.2 Sub-sub-feature: Natural Language Query Intent Engine (L3)
- **Query Parser**: Deep reasoning engine (`AiReasoningEngine`) parsing intent for revenue, top staff, VIP clients, inventory, bookings, and friendly chat.

##### 8.1.1.3 Sub-sub-feature: Conversational Copilot Chat Interface (L3)
- **Chat Hub**: AJAX chatbot UI (`AiAutomationController::chat`, `admin/ai/hub`) with quick query buttons and Markdown response formatting.

---

### 8.2 Feature: Predictive Machine Learning Intelligence (L1)
#### 8.2.1 Sub-Feature: Predictive Forecasts & Yield Analytics (L2)
##### 8.2.1.1 Sub-sub-feature: 30-Day Demand & Revenue Time-Series Forecast (L3)
- **Time-Series Forecasting**: 60-Day historical model (`AiAnalyticsService::generateDemandForecast`) predicting next 30 days revenue and appointment volume with day-of-week seasonality.

##### 8.2.1.2 Sub-sub-feature: Customer Churn Risk ML Classification (L3)
- **Churn Classification**: Heuristic churn risk scoring (`calculateCustomerChurnRisk`) identifying high-risk inactive clients (>45 days idle).

##### 8.2.1.3 Sub-sub-feature: Staff Occupancy Yield & Peak Slot Matrix (L3)
- **Yield Matrix**: Staff utilization analysis (`analyzeStaffUtilization`) and hourly slot demand matrix identifying peak traffic hours.

##### 8.2.1.4 Sub-sub-feature: Inventory Stock-Out Depletion Prediction (L3)
- **Depletion Predictor**: Product consumption rate calculator (`predictInventoryDepletion`) estimating days remaining before stockout.

---

### 8.3 Feature: 1-Click Autonomous AI Workflows (L1)
#### 8.3.1 Sub-Feature: Automated Operational Workflows (L2)
##### 8.3.1.1 Sub-sub-feature: Customer Churn Retention SMS Campaign (L3)
- **Retention Campaign**: Automated workflow (`AiChurnAutomationEngine`) generating unique discount voucher codes and sending retention SMS alerts.

##### 8.3.1.2 Sub-sub-feature: Auto Purchase Order Draft Generator (L3)
- **PO Draft Generator**: Automated calculation (`AiInventoryReorderEngine`) generating recommended reorder quantities for low-stock items.

##### 8.3.1.3 Sub-sub-feature: AI Smart Slot Rebalancer & Staff Matcher (L3)
- **Smart Slot Rebalancer**: Load-balancing slot recommender (`AiSmartSchedulerEngine`) matching customer preferences with staff load and historical affinity.

##### 8.3.1.4 Sub-sub-feature: Dynamic Off-Peak Promos & VIP Loyalty Rewards (L3)
- **1-Click Promotions**: Publishing off-peak morning slot discounts (9 AM - 11 AM) and awarding bonus loyalty points to top-spending clients.
