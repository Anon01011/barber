# SAAS MODULE ENTITLEMENT MATRIX

**Project:** Salon & Barber SaaS Application  
**Date:** November 25, 2026  
**Central Enforcement Service:** `App\Services\ModuleEntitlementService`  
**Governing Rule:**  
`module_access = global_enabled AND plan_included AND subscription_eligible AND tenant_module_active AND dependencies_available AND user_authorized`

---

## 1. Authoritative 16-Module Entitlement Matrix

| # | Module Name | Module Key | Global Config Key | Plan Feature Strings | Dev Status | Dependencies | Frontend Enforcement | Backend Enforcement |
|---|---|---|---|---|---|---|---|---|
| 1 | Multi-Branch Management | `branches` | `enabled_modules.branches` | `Multi-Branch Management`, `Multi-Branch Support`, `multi_branch`, `branches` | Stable | None | Hidden in topbar, sidebar, switcher | `plan_feature:Multi-Branch Support`, `module:branches`, `SetBranchContext` |
| 2 | Inventory Management | `inventory` | `enabled_modules.inventory` | `Inventory Management`, `inventory`, `Products` | In Development | None | Hidden in sidebar, POS products tab, reports | `plan_feature:Inventory Management`, `module:inventory`, `permission:inventory.*` |
| 3 | Point of Sale (POS) | `pos` | `enabled_modules.pos` | `Point of Sale (POS)`, `POS System`, `pos` | Stable | `services` | Hidden in sidebar, quick sale links | `plan_feature:POS System`, `module:pos`, `permission:pos.*` |
| 4 | Booking System | `appointments` | `enabled_modules.appointments` | `Booking System`, `appointments`, `Online Booking` | Stable | `services`, `staff`, `customers` | Hidden in sidebar calendar & appointments | `module:appointments`, `permission:bookings.*` |
| 5 | Customer Management | `customers` | `enabled_modules.customers` | `Customer Management`, `customers` | Stable | None | Hidden in sidebar, customer datatable | `plan_feature:Customer Management`, `module:customers`, `permission:customers.*` |
| 6 | Service Management | `services` | `enabled_modules.services` | `Service Management`, `services` | Stable | None | Hidden in catalog sidebar | `module:services`, `permission:services.*` |
| 7 | Staff Management | `staff` | `enabled_modules.staff` | `Staff Management`, `staff` | Stable | None | Hidden in staff sidebar menu | `plan_feature:Staff Management`, `module:staff`, `permission:staff.*` |
| 8 | Analytics & Reports | `reports` | `enabled_modules.reports` | `Analytics & Reports`, `reports`, `Advanced Reports` | Stable | None | Hidden in reports sidebar accordion | `plan_feature:Analytics & Reports`, `module:reports`, `permission:reports.*` |
| 9 | Packages | `packages` | `enabled_modules.packages` | `Packages`, `packages` | Stable | `services` | Hidden in catalog sidebar, POS package tab | `plan_feature:Packages`, `module:packages`, `permission:packages.*` |
| 10 | Memberships | `memberships` | `enabled_modules.memberships` | `Memberships`, `memberships` | Stable | `customers` | Hidden in catalog sidebar, POS membership tab | `plan_feature:Memberships`, `module:memberships`, `permission:memberships.*` |
| 11 | Commission Management | `commissions` | `enabled_modules.commissions` | `Commission Management`, `commissions` | Stable | `staff` | Hidden in sidebar, commission reports | `plan_feature:Commission Management`, `module:commissions`, `permission:commissions.*` |
| 12 | Role Management | `roles` | `enabled_modules.roles` | `Role Management`, `roles` | Stable | None | Hidden in Salon Management sidebar | `plan_feature:Role Management`, `module:roles`, `role:salon_admin` |
| 13 | Email Notifications | `marketing` | `enabled_modules.marketing` | `Email Notifications`, `marketing`, `Email Marketing` | Stable | `customers` | Hidden in settings mail tabs | `module:marketing`, `GlobalMailConfigService` |
| 14 | SMS Notifications | `sms` | `enabled_modules.sms` | `SMS Notifications`, `sms` | Stable | `customers` | Hidden in settings SMS tab | `module:sms`, `SmsService::canUseFeature` |
| 15 | WhatsApp Notifications | `whatsapp` | `enabled_modules.whatsapp` | `WhatsApp Notifications`, `whatsapp` | Stable | `customers` | Hidden in settings WhatsApp tab | `module:whatsapp`, `WhatsappService::canUseFeature` |
| 16 | AI Insights & Automation | `ai` | `enabled_modules.ai` | `AI Insights & Automation`, `ai`, `AI Copilot` | Stable | None | Hidden in sidebar PRO AI pill, reports | `plan_feature:AI Insights & Automation`, `module:ai`, Hub & API guards |
| 17* | Loyalty Program | `loyalty` | `enabled_modules.loyalty` | `Loyalty Program`, `loyalty_program`, `loyalty` | Stable | `customers`, `pos` | Hidden in settings tab, POS cart badge | `hasLoyaltyProgram()`, `LoyaltyService`, POS bounds |
| 18* | Customer Feedback & Reviews | `feedback` | `enabled_modules.feedback` | `Customer Feedback & Reviews`, `feedback_reviews`, `feedback` | Stable | `appointments` | Hidden in sidebar, completion emails | `plan_feature:Customer Feedback & Reviews`, `module:feedback`, `permission:marketing.manage_reviews` |

*\*Sub-modules integrated into the SaaS architecture.*

---

## 2. Hard Global Override Guarantee
When the SaaS Super Admin disables a module globally via `/super-admin/modules`:
1. `ModuleEntitlementService::isGloballyEnabled($module)` returns `false`.
2. `Salon::canUseFeature($feature)` returns `false` — even if included in the purchased subscription plan or currently on trial.
3. `CheckPlanFeature` middleware intercepts incoming requests and returns an immediate `403 Forbidden` response:  
   *"The '<feature>' module is currently disabled by the system administrator."*
4. All UI links, sidebar icons, dropdowns, and dashboard badges are automatically removed.
