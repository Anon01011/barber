# ARCHITECTURAL PLAN & IMPLEMENTATION SPECIFICATION
## Multi-Tenant Loyalty Points & Customer Feedback Systems with SaaS Plan Gating

**Author:** Senior Software Architect & Full-Stack Developer  
**Status:** Architecture & Engineering Plan (Ready for Review & Execution)  
**Target Environments:** Primary (`c:\Ketan\projects\barber`) & Mirror (`c:\Ketan\projects\salon-cms-master`)  
**Laravel Version:** 10.x | **PHP:** 8.2+ | **Database:** MySQL 8.0  

---

## 1. EXECUTIVE OVERVIEW & BUSINESS OBJECTIVES

This plan details the implementation of two enterprise-grade customer retention and quality assurance modules for the Salon & Barber SaaS platform:

1. **Loyalty Points & Rewards Engine:**
   * Allows salon administrators to configure flexible points earning (e.g., 1 pt / $1 spent) and redemption rules (e.g., 100 pts = $5 discount, min 50 pts to redeem, max 50% discount per bill).
   * Fully integrated into POS Checkout (split tender / line discounts) with an immutable transactional audit ledger (`loyalty_transactions`).
   * Governed by SaaS Subscription Plans: Only salons subscribing to plans with the `Loyalty Program` feature can activate and use the module.

2. **Customer Feedback, Rating & Review System:**
   * Bridges existing schema placeholders (`rating`, `is_rated`, `rating_token`, `rating_token_expires_at` on `bookings`) into a complete customer review system.
   * Secure, tokenized public review submission portal (`/booking/rate/{token}`) requiring zero login for guest and client convenience.
   * Dedicated Salon Admin Feedback Portal (`/{salon_slug}/admin/feedback`) with staff-level and service-level satisfaction metrics, filterable review lists, and a public testimonial toggle.
   * Governed by SaaS Subscription Plans: Gated via `Customer Feedback & Reviews` feature in SaaS subscription tiers.

---

## 2. SAAS SUPER ADMIN & SUBSCRIPTION PLAN GATING

### 2.1 Plan Feature Catalog Expansion
In `App\Models\Plan`, feature names are stored as JSON arrays in `plans.features`. We establish two standardized feature keys:
* `'Loyalty Program'`
* `'Customer Feedback & Reviews'`

#### Files to Update:
* `resources/views/super-admin/plans/create.blade.php`: Add quick-add pills for `+ Loyalty Program` and `+ Customer Feedback & Reviews`.
* `resources/views/super-admin/plans/edit.blade.php`: Add quick-add pills matching `create.blade.php`.
* `app/Models/Salon.php`: Add helper methods:
  ```php
  public function hasLoyaltyProgram(): bool
  {
      $plan = $this->activeSubscription?->plan;
      return $plan && ($plan->hasFeature('Loyalty Program') || $plan->hasFeature('All Features'));
  }

  public function hasFeedbackModule(): bool
  {
      $plan = $this->activeSubscription?->plan;
      return $plan && ($plan->hasFeature('Customer Feedback & Reviews') || $plan->hasFeature('All Features'));
  }
  ```

### 2.2 Middleware Protection
Existing `CheckPlanFeature` middleware (`plan_feature:{feature_name}`) already intercepts unauthorized requests and renders `errors.feature-locked`. We will attach it to:
* `/{salon_slug}/admin/loyalty/...` -> `middleware(['auth', 'plan_feature:Loyalty Program'])`
* `/{salon_slug}/admin/feedback/...` -> `middleware(['auth', 'plan_feature:Customer Feedback & Reviews'])`

---

## 3. LOYALTY POINTS SYSTEM SPECIFICATION

### 3.1 Database Schema Design

#### Migration 1: Add `loyalty_points` to `customers` table
```php
Schema::table('customers', function (Blueprint $table) {
    if (!Schema::hasColumn('customers', 'loyalty_points')) {
        $table->unsignedInteger('loyalty_points')->default(0)->after('total_spent');
    }
});
```

#### Migration 2: Create `loyalty_transactions` table (Audit Ledger)
```php
Schema::create('loyalty_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('salon_id')->constrained('salons')->onDelete('cascade');
    $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
    $table->foreignId('pos_sale_id')->nullable()->constrained('pos_sales')->onDelete('set null');
    $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
    $table->integer('points'); // Positive for earned/adjusted, negative for redeemed
    $table->enum('type', ['earned', 'redeemed', 'refund_reversal', 'manual_adjustment']);
    $table->decimal('conversion_rate', 8, 4)->nullable(); // e.g., 0.05 ($ per point)
    $table->decimal('discount_amount', 10, 2)->default(0); // Currency savings if redeemed
    $table->string('notes')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();

    $table->index(['salon_id', 'customer_id']);
    $table->index('pos_sale_id');
});
```

### 3.2 Eloquent Model: `App\Models\LoyaltyTransaction`
* Uses `BelongsToSalon` trait for strict tenant isolation.
* Casts `points` to integer, `discount_amount` to decimal.
* Relationships: `belongsTo(Customer::class)`, `belongsTo(PosSale::class)`, `belongsTo(Salon::class)`.

### 3.3 Salon Settings for Loyalty (Configurable via UI)
Stored in `salon_settings` table and managed by `SettingsService`:
| Key | Type | Default | Description |
|---|---|---|---|
| `loyalty_enabled` | boolean | `false` | Master toggle for tenant (locked if plan lacks feature) |
| `loyalty_points_per_currency` | integer | `1` | Points earned per 1 currency unit spent (e.g. 1 pt per $1) |
| `loyalty_point_redemption_rate` | decimal | `0.05` | Currency value of 1 point (100 pts = $5.00 discount) |
| `loyalty_min_points_to_redeem` | integer | `50` | Minimum points balance required before redemption allowed |
| `loyalty_max_discount_percentage`| integer | `50` | Max % of bill that can be paid using points (prevents $0 abuse) |

### 3.4 POS Checkout Integration Flow
1. **Frontend (`resources/views/pos/index.blade.php`):**
   * When a customer is attached to the sale, an interactive badge shows:  
     `⭐ 120 Loyalty Points ($6.00 value)`
   * Cashier can click "Apply Loyalty Discount":
     * System checks if `points >= loyalty_min_points_to_redeem`.
     * Calculates allowable discount (capped at bill subtotal and `loyalty_max_discount_percentage`).
     * Renders a `Loyalty Points Redemption` deduction line in cart totals.
2. **Backend (`PosController@checkout`):**
   * If points are redeemed:
     * Atomic DB transaction verifies customer has sufficient balance.
     * Decrements `customers.loyalty_points`.
     * Records `loyalty_transactions` record (`type = 'redeemed'`).
   * When sale succeeds:
     * Calculates eligible earning: `floor(net_payable * loyalty_points_per_currency)`.
     * Increments `customers.loyalty_points`.
     * Records `loyalty_transactions` record (`type = 'earned'`).

---

## 4. CUSTOMER FEEDBACK & REVIEW SYSTEM SPECIFICATION

### 4.1 Existing Foundation in `app/Models/Booking.php`
The `bookings` table already has:
* `rating` (smallint, 1-5)
* `feedback` (text, customer comments)
* `is_rated` (boolean)
* `rating_token` (string 64, unique)
* `rating_token_expires_at` (datetime)
* Methods: `addRating($rating, $feedback)`, `generateRatingToken()`, `isRatingTokenValid()`

### 4.2 Public Feedback Portal
1. **Route:** `GET /booking/rate/{token}` -> `App\Http\Controllers\Booking\RatingController@show`
2. **Form View:** Clean, mobile-optimized page branded with salon name and barber avatar:
   * Interactive 5-star rating selector (Hover states, clickable stars 1 to 5).
   * Review text area ("How was your cut/service with [Barber Name]?").
   * Optional tags: "Cleanliness", "Great Technique", "Punctual", "Friendly".
3. **Submission:** `POST /booking/rate/{token}` -> `App\Http\Controllers\Booking\RatingController@store`
   * Validates token is valid and unexpired (`rating_token_expires_at->isFuture()`).
   * Validates `rating` (`required|integer|between:1,5`) and `feedback` (`nullable|string|max:1000`).
   * Sets `is_rated = true`, stores rating, invalidates token to prevent replay.
   * Displays celebratory thank-you card with optional link to salon website or next booking.

### 4.3 Salon Admin Feedback Dashboard
1. **Route:** `GET /{salon_slug}/admin/feedback` -> `App\Http\Controllers\Admin\FeedbackController@index`
2. **Header Analytics:**
   * Average Overall Rating (e.g. `4.8 ★` with total review count).
   * Star Distribution Bar (5-star, 4-star, 3-star, 2-star, 1-star counts).
   * Top-Rated Staff Member of the month.
3. **Review Ledger Table:**
   * Date, Customer Name, Staff Member, Service Name, Star Rating badge, Feedback text.
   * Filter by Staff member, Star rating (e.g. Show only negative reviews < 3 stars for quick management resolution).
   * Export reviews to Excel/CSV.

---

## 5. STEP-BY-STEP IMPLEMENTATION PHASES

```
+---------------------------------------------------------------------------------------------------+
| PHASE 1: SAAS PLAN MODULE DEFINITIONS                                                             |
| - Add feature checkboxes/pills to Super Admin Plan Create/Edit views                              |
| - Implement Salon.php feature helpers & plan_feature middleware aliases                           |
+---------------------------------------------------------------------------------------------------+
                                                  |
                                                  v
+---------------------------------------------------------------------------------------------------+
| PHASE 2: LOYALTY DATABASE & BACKEND SERVICES                                                      |
| - Create migration for `loyalty_transactions` and `customers.loyalty_points`                      |
| - Create LoyaltyTransaction model & BelongsToSalon trait scoping                                  |
| - Implement LoyaltyService for earn/redeem business calculations                                  |
+---------------------------------------------------------------------------------------------------+
                                                  |
                                                  v
+---------------------------------------------------------------------------------------------------+
| PHASE 3: SALON ADMIN LOYALTY SETTINGS & POS INTEGRATION                                           |
| - Add "Loyalty Program" tab in Salon Settings (with plan gating check)                            |
| - Integrate points display and redemption controls into POS checkout view & PosController        |
| - Add Customer Loyalty History tab in Admin Customer view                                        |
+---------------------------------------------------------------------------------------------------+
                                                  |
                                                  v
+---------------------------------------------------------------------------------------------------+
| PHASE 4: FEEDBACK CONTROLLER, PUBLIC PORTAL & ADMIN DASHBOARD                                     |
| - Create public RatingController with token validation & mobile-responsive review view            |
| - Create Admin FeedbackController & dashboard view with analytics and staff filters              |
| - Auto-trigger rating token generation and review links on appointment completion                 |
+---------------------------------------------------------------------------------------------------+
                                                  |
                                                  v
+---------------------------------------------------------------------------------------------------+
| PHASE 5: VERIFICATION, SYNCHRONIZATION & QA DOCUMENTATION                                         |
| - Execute automated feature tests for Loyalty & Feedback flows                                    |
| - Synchronize all migrations, models, controllers, and views to salon-cms-master                  |
| - Update SAAS_SALON_BARBER_FEATURES_TESTING_GUIDE.md with test cases                             |
+---------------------------------------------------------------------------------------------------+
```

---

## 6. DATA INTEGRITY & ZERO-REGRESSION SAFEGUARDS

1. **Transaction Safety:** All points earning and redemption operations in POS checkout will run inside `DB::transaction()` to prevent race conditions or balance desynchronization.
2. **Tenant Isolation:** All queries on `LoyaltyTransaction` and feedback dashboards strictly enforce `salon_id` scoping via `BelongsToSalon`.
3. **Graceful Fallbacks:** If a salon is on a plan without Loyalty or Feedback, existing checkout and booking flows continue functioning seamlessly with zero errors or UI clutter.
4. **Exact Mirroring:** Primary repository (`barber`) and mirror repository (`salon-cms-master`) will be updated simultaneously and verified.

---

## 7. APPROVAL & EXECUTION READY

This plan provides an end-to-end blueprint designed to deliver robust, production-hardened features while preserving 100% of existing functionality. 

Upon your confirmation, we will proceed immediately with **Phase 1** (SaaS Plan definitions) and **Phase 2** (Loyalty Points database & backend services).
