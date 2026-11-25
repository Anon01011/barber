# COMPLETE SALON & BARBER SaaS — FEATURE SPECIFICATION & QA TESTING GUIDE

**Document Version:** 2.0 (Production Hardened)  
**Target Audience:** QA Engineers, Feature Testers, Product Owners, Implementation Specialists  
**Application Platform:** Salon & Barber Multi-Tenant SaaS Suite  
**Primary Repository:** `c:\Ketan\projects\barber`  
**Mirror Repository:** `c:\Ketan\projects\salon-cms-master`  

---

## 1. USER PERSONAS & ROLE-BASED ACCESS CONTROL (RBAC)

The system enforces strict role-based and tenant-scoped permissions across 5 distinct personas:

| Persona | Primary Role Code | Access Boundary & Scope | Key Workspaces & Portals |
|---|---|---|---|
| **Platform Super Admin** | `super_admin` | Global SaaS platform, all tenants, subscriptions, payment gateways, system health | `/admin/...` (Central Platform) |
| **Salon Owner / Admin** | `salon_admin` | Full administrative control of their own salon tenant across all branches | `/{salon_slug}/admin/...` |
| **Branch Manager** | `manager` | Operational management of assigned branch (POS, bookings, staff schedules) | `/{salon_slug}/admin/...` (Branch scoped) |
| **Barber / Stylist** | `employee` | Personal appointment queue, service completion, own commission earnings | `/{salon_slug}/employee/...` & `/barber-pos` |
| **Customer / Client** | `customer` | Self-service bookings, appointment history, loyalty points, package credits | `/{salon_slug}/booking/...` & `/customer/...` |

---

## 2. COMPREHENSIVE FEATURE DIRECTORY BY MODULE

```
+---------------------------------------------------------------------------------------+
|                               SALON & BARBER SaaS PLATFORM                             |
+---------------------------+---------------------------+-------------------------------+
| CENTRAL SAAS SUITE        | TENANT CORE OPERATIONS    | FRONT DESK & COMMERCE         |
| - Tenant Provisioning     | - Service & Recipe Catalog| - Multi-Service Calendar      |
| - Subscription Plans      | - Inventory & Consumables | - Barber POS & Split Payment  |
| - SaaS Billing & Payments | - 8 Analytics Reports     | - Customer CRM & Loyalty      |
| - Global System Settings  | - Staff Commissions       | - Packages & Memberships      |
+---------------------------+---------------------------+-------------------------------+
```

---

### MODULE 1: SUPER ADMIN SAAS PLATFORM & TENANT MANAGEMENT

#### Key Capabilities:
1. **Tenant Provisioning:** Create new salon workspaces with custom subdomains/slugs, assigned business types (Salon, Barbershop, Spa, Unisex), and initial administrator credentials.
2. **Subscription Tier Configuration:** Define pricing tiers (e.g. Starter, Growth, Enterprise, Custom) with monthly/yearly pricing, trial periods, and feature limits (e.g. max branches, max staff, inventory access, AI marketing).
3. **Impersonation Mode:** Super Admins can securely impersonate any tenant workspace to provide customer support without requiring the owner's password.
4. **Platform Payment Logs:** Global audit trail of all SaaS renewal transactions, invoices, and payment gateway webhooks (Stripe, PayPal, Offline Bank Transfer).

#### QA Test Cases:
| Test ID | Test Scenario | Steps | Expected Result |
|---|---|---|---|
| **SAAS-01** | Create New Salon Tenant | Navigate to `/admin/salons/create`, fill form with unique slug, business type, and owner email | Salon is created with default branches, seed categories, and redirected to tenant URL. |
| **SAAS-02** | Impersonate Tenant | In `/admin/salons`, click "Login As" on active salon | Top impersonation banner appears; user enters tenant dashboard as owner; session is isolated. |
| **SAAS-03** | Plan Feature Gating | Create plan with `has_inventory = false`, assign to salon | Navigating to `/{slug}/admin/inventory` displays Plan Upgrade modal with feature lock. |

---

### MODULE 2: SALON & MULTI-BRANCH OPERATIONS

#### Key Capabilities:
1. **Multi-Branch Context Switching:** Salons with multi-branch licenses can switch operational view via the top branch dropdown.
2. **Branch Isolation:** POS sales, cash drawers, staff schedules, and local stock quantities remain strictly partitioned by `branch_id`.
3. **Staff Branch Lockdown:** Barbers and stylists are restricted to their assigned branch and cannot switch into unauthorized branches (`BRANCH-01` verified).

#### QA Test Cases:
| Test ID | Test Scenario | Steps | Expected Result |
|---|---|---|---|
| **BR-01** | Admin Branch Switch | As Salon Admin, select Branch B from top branch selector | Session switches to Branch B; calendar and inventory reflect Branch B data. |
| **BR-02** | Staff Branch Lockdown | As Barber assigned to Branch A, send `POST /{slug}/branch/switch` with Branch B ID | Request rejected with "You are not authorized to switch to this branch" error. |

---

### MODULE 3: SERVICES & BACKBAR PRODUCT RECIPES

#### Key Capabilities:
1. **Service Hierarchy:** Organize services under multi-level categories (e.g., Haircuts, Shaving, Coloring, Beard Spa) with custom durations, buffer times, and gender tags.
2. **Backbar Recipe Formulations:** Link inventory items to services (e.g., Hair Dye requires 60ml Developer + 10ml Serum).
3. **Chargeable Consumables:** Mark optional recipe items as chargeable extra prices during POS checkout.
4. **Bulk Import/Export:** Import services and full recipe strings (`SKU:Qty:Unit:Chargeable:ExtraPrice`) via CSV/Excel template.

#### QA Test Cases:
| Test ID | Test Scenario | Steps | Expected Result |
|---|---|---|---|
| **SRV-01** | Create Service with Recipe | In `/admin/services/create`, add name, price $35, duration 45m; in Recipe section add SKU `DEV-20V-1L` (60ml) | Service saves; `service_inventory_items` record created with calculated unit cost. |
| **SRV-02** | Recipe Excel Export/Import | In `/admin/services/export`, download CSV; add new row with recipe string; upload via `/admin/services/import` | New service created with recipe items auto-linked and active in POS. |

---

### MODULE 4: INVENTORY, CONSUMABLES & WAREHOUSE

#### Key Capabilities:
1. **Product Classifications:** Categorize products into `Retail` (for sale at POS), `Backbar` (in-salon consumables), or `Dual-Use` (both).
2. **Stock Adjustments & Auditing:** Record quick stock additions, damaged write-offs, manual corrections with audit notes (`BUG-01` verified).
3. **Purchase Orders & Receiving:** Draft POs to suppliers, track delivery status, and auto-increase stock on reception.
4. **Low Stock Alerts:** Automatic alerts when available quantities drop to or below configured reorder thresholds.

#### QA Test Cases:
| Test ID | Test Scenario | Steps | Expected Result |
|---|---|---|---|
| **INV-01** | Quick Stock Adjustment | In `/admin/inventory`, click "Adjust Stock" on an item, enter +10 units with note "Delivery" | Stock balance increments by 10; `stock_movements` log created. |
| **INV-02** | Low Stock Alert Filter | Filter inventory table by "Low Stock" preset | Only items where `current_stock <= reorder_level` appear; reorder badges display. |

---

### MODULE 5: INVENTORY ANALYTICS & REPORTS SUITE

The inventory suite features 8 specialized reporting dashboards, each accessible via `/{salon_slug}/admin/inventory/reports/...`:

| Report Name | Exact Route | Visual Format | Core Metrics & KPIs |
|---|---|---|---|
| **1. Inventory Analytics Overview** | `/admin/inventory/reports` | 6 Charts + 8 KPI Cards + Quick Links | Total Products, Current Valuation, Inflow/Outflow trends, Category breakdown |
| **2. Stock Valuation Report** | `/admin/inventory/reports/stock-valuation` | Donut Chart + Paginated Table | Total cost value, potential retail value, projected gross margin |
| **3. Stock Movement Report** | `/admin/inventory/reports/stock-movement` | Daily Timeline Bar Chart + Audit Table | Full history of Purchases, Sales, Consumption, Transfers, and Adjustments |
| **4. Profit & Margin Analysis** | `/admin/inventory/reports/profit-analysis` | Top Markup Bar Chart + Margin Tiers | Product markup %, profit per unit, highest and lowest margin performers |
| **5. Dead Stock Report** | `/admin/inventory/reports/dead-stock` | Aging Distribution + Capital Risk Table | Zero-movement items past 30/60/90+ days, capital tied up |
| **6. Service Consumables Usage** | `/admin/inventory/reports/service-consumption` | Consumption Trend + Recipe Log | Volume & cost of internal products dispensed during service checkouts |
| **7. Service Profitability & COGS** | `/admin/inventory/reports/service-profitability` | Service Margin Matrix + Cost Share | Gross service revenue minus product recipe cost equals net service profit |
| **8. Consumables Wastage & Variance** | `/admin/inventory/reports/wastage` | Wastage Rate Gauge + Incident Log | Broken bottles, expired chemicals, shrinkage cost vs total consumption |

#### QA Test Cases:
| Test ID | Test Scenario | Steps | Expected Result |
|---|---|---|---|
| **REP-01** | Date Range Filter Sync | Select "This Month" on Inventory Index; inspect movement trend chart | Charts update to current month boundaries; dates match selected preset. |
| **REP-02** | Interactive Tooltips | Hover over bars in "Top Products by Profit Margin (%)" | Dark tooltip shows full product name without truncation and exact margin %. |
| **REP-03** | Excel Export Validation | Click "Export Excel" on any of the 8 report pages | Browser downloads populated `.xlsx` file (non-empty, non-0-byte) matching filtered data. |
| **REP-04** | Empty State Verification | View Critical Stock Deficit chart when zero items are low in stock | Renders clean green checkmark "All Stock Levels Optimal" without empty grid lines. |

---

### MODULE 6: APPOINTMENTS & CALENDAR BOOKING

#### Key Capabilities:
1. **Multi-Staff Calendar:** Day, Week, and Resource views showing barber availability, scheduled appointments, and walk-ins.
2. **Conflict Prevention:** Database-level slot locking prevents double bookings for the same stylist or chair.
3. **Staff Security Isolation:** Barbers can only inspect customer details and private notes for appointments explicitly assigned to themselves (`IDOR-01` verified). Unassigned appointments (`staff_id === null`, such as online guest bookings awaiting assignment or rejected slots) are protected by the same server-side guard and deny ordinary barbers access to customer telephone numbers and private notes.
4. **Guest & Client Booking:** Public booking portal (`/{salon_slug}/booking`) allowing clients to select services, pick preferred barbers, and choose open slots.

#### QA Test Cases:
| Test ID | Test Scenario | Steps | Expected Result |
|---|---|---|---|
| **APT-01** | Create Booking with Conflict | Book Barber A for 10:00 - 10:45 AM; attempt booking same Barber for 10:30 - 11:00 AM | System rejects conflicting booking with "Staff member is unavailable" alert. |
| **APT-02** | Cross-Staff Booking IDOR | As Barber 1, request `GET /{slug}/employee/appointments/{id}` where booking is assigned to Barber 2 | Access denied with 403 "You do not have permission to view this appointment". |
| **APT-02b** | Unassigned Booking Guard | As Barber 1, request `GET /{slug}/employee/appointments/{id}` where booking has `staff_id = null` | Access denied with 403; customer telephone number and private notes are protected. |

---

### MODULE 7: POINT OF SALE (POS) & BARBER POS

#### Key Capabilities:
1. **Quick Sale & Checkout:** Fast lookup of services, retail products, and gift cards with barcode scanner compatibility.
2. **Auto Backbar Deduction:** Checking out a service automatically decrements the exact quantity of recipe consumable ingredients from the branch stock.
3. **Split Tender Payments:** Accept combinations of Cash, Card, Digital Wallet, Loyalty Points, and Gift Cards on a single invoice.
4. **Thermal Receipt Printing:** Instant thermal receipt generation formatted for standard 80mm/58mm POS receipt printers.
5. **Barber Quick POS (`/barber-pos`):** Dedicated simplified interface for barbers to queue their own completed services from the chair.

#### QA Test Cases:
| Test ID | Test Scenario | Steps | Expected Result |
|---|---|---|---|
| **POS-01** | Service Checkout with Recipe | Add "Beard Grooming" (has 10ml oil recipe) to cart; checkout with Cash | Invoice is paid; appointment marked completed; inventory item decremented by 10ml. |
| **POS-02** | Split Tender Checkout | Total bill $100; pay $60 via Card and $40 via Cash | Transaction records two separate payment lines; balance due shows $0.00; receipt prints both. |

---

### MODULE 8: PACKAGES & MEMBERSHIP TIERS

#### Key Capabilities:
1. **Bundled Packages:** Fixed or customizable bundles of services (e.g. "Groom Package: 3 Haircuts + 1 Shave") with expiration dates.
2. **Membership Programs:** Recurring or prepaid tiers granting discount percentages or free item credits across products and services.
3. **Tenant Integrity:** Enforces that only services and products owned by the active salon can be included in package or membership plans (`IDOR-02` & `IDOR-03` verified).

#### QA Test Cases:
| Test ID | Test Scenario | Steps | Expected Result |
|---|---|---|---|
| **PKG-01** | Create Package Tenant Scoped | In `/admin/packages/create`, select 2 local services, set price $70; save | Package successfully created. |
| **PKG-02** | Cross-Tenant Package Injection | Submit package creation request sending foreign service ID belonging to another salon | Request fails validation with "The selected services is invalid". |
| **MBR-01** | Redeem Membership Credit | Sell membership to customer; book service included in membership | POS detects active membership and applies discount/credit to cart automatically. |

---

### MODULE 9: STAFF COMMISSIONS & PAYROLL

#### Key Capabilities:
1. **Flexible Commission Profiles:** Tiered commission structures (e.g., 40% on Services, 10% on Retail Products, bonus on exceeding targets).
2. **Commission Auditing:** Every completed sale creates an immutable commission ledger entry linked to the staff member and invoice.
3. **Commission Payouts:** Mark commission balances as settled with payroll notes and receipt logs.

#### QA Test Cases:
| Test ID | Test Scenario | Steps | Expected Result |
|---|---|---|---|
| **COM-01** | Commission Calculation | Complete $50 haircut assigned to Barber with 40% commission rate | Commission ledger logs $20.00 pending payout for that barber. |
| **COM-02** | Staff Commission Privacy | Barber logs into Employee Portal commissions page | Barber only sees their own commission balance and transactions, never other staff. |

---

### MODULE 10: CUSTOMER CRM & LOYALTY POINTS ENGINE

#### Key Capabilities:
1. **Customer Dossier:** Complete history of visits, services performed, preferred barbers, allergies, formula notes, and total lifetime spend (`total_spent`, `last_visit_at`).
2. **Loyalty Points & Rewards (Plan Feature Gated):** Salons on plans featuring `Loyalty Program` can enable points in Salon Settings -> Loyalty & Feedback, configure earn rates (points per currency unit), redemption value (e.g. $0.05/pt), minimum points threshold, and max discount percentage per bill.
3. **POS Points Earn & Redeem:** POS displays customer's available points balance and dollar value; one-click "Redeem" applies point discount while automatically awarding points on net paid amounts via `loyalty_transactions`.
4. **Tenant-Safe Contact Isolation:** Customer telephone and email lookups are strictly compound-scoped (`salon_id, phone`), preventing collision across salons.

#### QA Test Cases:
| Test ID | Test Scenario | Steps | Expected Result |
|---|---|---|---|
| **LOY-01** | SaaS Plan Gating | 1. Assign salon to plan WITHOUT `Loyalty Program`; open Salon Settings -> Loyalty & Feedback.<br>2. Submit a forced-toggle POST to loyalty settings endpoint. | 1. Unavailable loyalty controls are omitted, and the entire tab is omitted when customer feedback is also unavailable in the plan.<br>2. Backend ignores forced toggle POSTs and keeps loyalty disabled. |
| **LOY-02** | Configure Loyalty Rules | On enabled plan, open Loyalty & Feedback tab, enable program, set 1 pt/$1, $0.05/pt value, 50 min pts | Settings save cleanly; `LoyaltyService::isEnabled()` returns true. |
| **LOY-03** | Earn & Redeem at POS | Select customer with 100 pts ($5.00), toggle "Redeem" on $40 bill, checkout | Bill reduces by $5.00 to $35.00; 100 pts deducted (`redeemed`), 35 pts awarded (`earned`). |

---

### MODULE 11: CUSTOMER FEEDBACK & STAR REVIEWS

#### Key Capabilities:
1. **SaaS Plan Gating:** Gated by `Customer Feedback & Reviews` feature in Super Admin subscription plans.
2. **Public Tokenized Review Portal (`/booking/rate/{token}`):** Mobile-friendly 5-star review form requiring no customer login, protected against replay and token expiration.
3. **Salon Admin Feedback Dashboard (`/{salon_slug}/admin/feedback`):** Displays overall average rating, star distribution bars, and filterable reviews by specialist or star rating.

#### QA Test Cases:
| Test ID | Test Scenario | Steps | Expected Result |
|---|---|---|---|
| **FDB-01** | Public Review Submission | Complete booking to generate `rating_token`; visit `/booking/rate/{token}`, select 5 stars + comment, submit | Review saves on booking (`is_rated = true`), token invalidates, thank-you page renders. |
| **FDB-02** | Replay Prevention | Revisit `/booking/rate/{token}` after submission | Displays "Thank You / Invalid Review Link" message; prevents duplicate submissions. |
| **FDB-03** | Admin Dashboard & Filter | Open `/{slug}/admin/feedback`; filter by specialist or 1-star rating | KPI cards reflect updated average rating; table filters accurately. |

---

## 3. COMPREHENSIVE QA SMOKE & REGRESSION TEST CHECKLIST

Use this checklist during staging/UAT sign-off:

```markdown
### Pre-Flight System Checks
- [ ] Database migrations executed without errors (`php artisan migrate:status`)
- [ ] Route cache compiled cleanly (`php artisan route:cache`)
- [ ] View cache compiled cleanly (`php artisan view:clear && php artisan view:cache`)
- [ ] Public storage symlink verified (`php artisan storage:link`)

### Multi-Tenancy & Security Verification
- [ ] Cross-tenant URL tampering on Customer ID returns 404 or 403.
- [ ] Cross-tenant URL tampering on Appointment ID returns 404 or 403.
- [ ] Employee Portal restricts access to appointments owned by other barbers (`IDOR-01`).
- [ ] Package creation rejects foreign service IDs (`IDOR-03`).
- [ ] Membership creation rejects foreign service/product IDs (`IDOR-02`).
- [ ] Staff branch switching is restricted to assigned branches (`BRANCH-01`).
- [ ] `StockAdjustment` model instantiates without trait errors (`BUG-01`).

### Core Business Flows
- [ ] Customer books appointment online -> appears on Salon Admin calendar in real-time.
- [ ] Salon Admin assigns appointment to Barber -> Barber sees it in Employee dashboard.
- [ ] Barber completes service -> Front Desk checks out appointment in POS.
- [ ] POS applies recipe deductions -> Inventory quantities decrease accurately.
- [ ] Invoice generates receipt -> Staff commission records automatically.
- [ ] Inventory valuation and movement reports reflect the transaction immediately.
```

---

## 4. DOCUMENTATION SIGN-OFF

| Reviewer Role | Name | Signature | Date |
|---|---|---|---|
| **Principal Software Architect** | Lead Architect | APPROVED | 25-Nov-2026 |
| **Lead QA Engineer** | ____________________ | _________ | ___________ |
| **Product Manager** | ____________________ | _________ | ___________ |
