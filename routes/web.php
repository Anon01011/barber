<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SalonRoleController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\BookingController;
// use App\Http\Controllers\Appointments\AppointmentsController as AppointmentController; // Commenting out to verify before fix
use App\Http\Controllers\POS\PosController as POSController;
use App\Http\Controllers\Services\ServicesController as ServiceController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Customer\AppointmentController as CustomerAppointmentController;
use App\Http\Controllers\Customer\ServiceController as CustomerServiceController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Models\Service;
use App\Http\Controllers\Booking\GuestBookingController;
use App\Http\Controllers\Admin\SmsSetupController;
use App\Http\Controllers\Admin\WhatsappSetupController;

// Default root route
// Default root route
Route::get('/', function () {
    if (env('SINGLE_SALON_MODE', false)) {
        return redirect()->route('login');
    }

    if (auth()->check()) {
        return redirect()->route('home.dashboard');
    }

    // Fetch SaaS app name and logo from super admin settings
    $settingsService = app(\App\Services\SettingsService::class);
    $appName = $settingsService->get('app_name', config('app.name'));
    $appLogo = $settingsService->getLogoUrl();

    // Fetch active plans for the pricing section
    $plans = \App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get();

    return view('welcome', compact('appName', 'appLogo', 'plans'));
})->name('home');

// About Us Page
Route::get('/about', function () {
    $settingsService = app(\App\Services\SettingsService::class);
    $appName = $settingsService->get('app_name', config('app.name'));
    $appLogo = $settingsService->getLogoUrl();
    return view('about', compact('appName', 'appLogo'));
})->name('about');

// Contact Us Page
Route::get('/contact', function () {
    $settingsService = app(\App\Services\SettingsService::class);
    $appName = $settingsService->get('app_name', config('app.name'));
    $appLogo = $settingsService->getLogoUrl();
    return view('contact', compact('appName', 'appLogo'));
})->name('contact');

// SaaS Registration (Rate limited to prevent abuse)
Route::middleware(['throttle:10,1'])->group(function () {
    Route::get('/register-salon', [App\Http\Controllers\SaaS\RegisterController::class, 'showRegistrationForm'])->name('saas.register');
    Route::post('/register-salon', [App\Http\Controllers\SaaS\RegisterController::class, 'register'])->name('saas.register.store');
});

Route::get('/pending-approval', [App\Http\Controllers\SaaS\RegisterController::class, 'pendingApproval'])
    ->name('saas.pending-approval')
    ->middleware('auth');

// Subscription Expired Page
Route::get('/subscription-expired', function () {
    return view('saas.subscription.expired');
})->name('subscription.expired');

// Guest Rating Routes (Public - no auth required)
Route::get('/rating/{token}', [App\Http\Controllers\RatingController::class, 'showGuestRatingForm'])->name('ratings.guest.form');
Route::post('/rating/{token}', [App\Http\Controllers\RatingController::class, 'submitGuestRating'])->name('ratings.guest.submit');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [App\Http\Controllers\Auth\LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [App\Http\Controllers\Auth\LoginController::class, 'adminLogin'])->middleware('throttle:10,1');
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->middleware('throttle:10,1');
});


// Global Dashboard Redirect
Route::get('/dashboard', [App\Http\Controllers\DashboardRedirectController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('home.dashboard');

// SaaS Error Pages
Route::middleware(['auth', 'verified'])->prefix('saas/errors')->name('saas.errors.')->group(function () {
    Route::get('/salon-inactive', function () {
        return view('errors.salon-inactive');
    })->name('salon-inactive');

    Route::get('/no-active-plan', function () {
        return view('errors.no-active-plan');
    })->name('no-active-plan');

    Route::get('/subscription-paused', function () {
        return view('errors.subscription-paused');
    })->name('subscription-paused');
});

// System Management routes (Super Admin Only) - GLOBAL (No Slug)
Route::middleware(['auth', 'verified', 'role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('super.dashboard');

        // Salon Management
        Route::prefix('salons')->name('salons.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\SalonManagementController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\SuperAdmin\SalonManagementController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\SuperAdmin\SalonManagementController::class, 'store'])->name('store');
            Route::get('/{salon}', [App\Http\Controllers\SuperAdmin\SalonManagementController::class, 'show'])->name('show');
            Route::get('/{salon}/edit', [App\Http\Controllers\SuperAdmin\SalonManagementController::class, 'edit'])->name('edit');
            Route::put('/{salon}', [App\Http\Controllers\SuperAdmin\SalonManagementController::class, 'update'])->name('update');
            Route::post('/{salon}/approve', [App\Http\Controllers\SuperAdmin\SalonManagementController::class, 'approve'])->name('approve');
            Route::post('/{salon}/impersonate', [App\Http\Controllers\SuperAdmin\SalonManagementController::class, 'impersonate'])->name('impersonate');
            Route::delete('/{salon}', [App\Http\Controllers\SuperAdmin\SalonManagementController::class, 'destroy'])->name('destroy');
        });

        // Plan Management
        Route::prefix('plans')->name('plans.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/duplicate', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'duplicate'])->name('duplicate');
        });

        // Subscription Management
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\SubscriptionManagementController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\SuperAdmin\SubscriptionManagementController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\SuperAdmin\SubscriptionManagementController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\SuperAdmin\SubscriptionManagementController::class, 'show'])->name('show');
            Route::put('/{id}', [App\Http\Controllers\SuperAdmin\SubscriptionManagementController::class, 'update'])->name('update');
            Route::post('/{id}/extend', [App\Http\Controllers\SuperAdmin\SubscriptionManagementController::class, 'extend'])->name('extend');
            Route::post('/{id}/approve', [App\Http\Controllers\SuperAdmin\SubscriptionManagementController::class, 'approve'])->name('approve');
            Route::post('/{id}/cancel', [App\Http\Controllers\SuperAdmin\SubscriptionManagementController::class, 'cancel'])->name('cancel');
            Route::post('/{id}/pause', [App\Http\Controllers\SuperAdmin\SubscriptionManagementController::class, 'pause'])->name('pause');
            Route::post('/{id}/resume', [App\Http\Controllers\SuperAdmin\SubscriptionManagementController::class, 'resume'])->name('resume');
        });

        // Payment Management
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'reject'])->name('reject');
            Route::post('/{id}/refund', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'refund'])->name('refund');
        });

        // Reports
        Route::prefix('reports')->name('saas.reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\ReportController::class, 'index'])->name('index');
            Route::get('/revenue', [App\Http\Controllers\SuperAdmin\ReportController::class, 'revenue'])->name('revenue');
            Route::get('/subscriptions', [App\Http\Controllers\SuperAdmin\ReportController::class, 'subscriptions'])->name('subscriptions');
            Route::get('/export/revenue', [App\Http\Controllers\SuperAdmin\ReportController::class, 'exportRevenue'])->name('export.revenue');
            Route::get('/export/subscriptions', [App\Http\Controllers\SuperAdmin\ReportController::class, 'exportSubscriptions'])->name('export.subscriptions');
        });

        // Invoice Management
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/{id}', [App\Http\Controllers\SuperAdmin\SaasInvoiceController::class, 'show'])->name('show');
            Route::get('/{id}/download', [App\Http\Controllers\SuperAdmin\SaasInvoiceController::class, 'download'])->name('download');
        });

        // System Settings
        Route::get('/system-settings', [App\Http\Controllers\SuperAdmin\SuperAdminSettingsController::class, 'index'])->name('system-settings.index');
        Route::put('/system-settings', [App\Http\Controllers\SuperAdmin\SuperAdminSettingsController::class, 'update'])->name('system-settings.update');
        Route::post('/system-settings/test-email', [App\Http\Controllers\SuperAdmin\SuperAdminSettingsController::class, 'testEmail'])->name('system-settings.test-email');
        Route::post('/system-settings/clear-cache', [App\Http\Controllers\SuperAdmin\SuperAdminSettingsController::class, 'clearCache'])->name('system-settings.clear-cache');

        // Module Management
        Route::get('/modules', [App\Http\Controllers\SuperAdmin\ModuleManagementController::class, 'index'])->name('modules.index');
        Route::put('/modules', [App\Http\Controllers\SuperAdmin\ModuleManagementController::class, 'update'])->name('modules.update');

        // System Notifications
        // System Notifications
        Route::resource('notifications', App\Http\Controllers\SuperAdmin\SystemNotificationController::class)->except(['show', 'edit']);
        Route::patch('/notifications/{notification}/toggle', [App\Http\Controllers\SuperAdmin\SystemNotificationController::class, 'toggle'])->name('notifications.toggle');

        // Email Templates
        Route::get('/email-templates', [App\Http\Controllers\SuperAdmin\EmailTemplateController::class, 'index'])->name('email-templates.index');
        Route::get('/email-templates/{id}/edit', [App\Http\Controllers\SuperAdmin\EmailTemplateController::class, 'edit'])->name('email-templates.edit');
        Route::put('/email-templates/{id}', [App\Http\Controllers\SuperAdmin\EmailTemplateController::class, 'update'])->name('email-templates.update');

        // Legacy Settings (for backward compatibility)
        Route::prefix('settings')
            ->name('settings.')
            ->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::put('/', [SettingsController::class, 'update'])->name('update');
        });

        // Roles & Permissions
        Route::prefix('roles')
            ->name('super.roles.') // Renamed from roles. to super.roles. to distinguish from salon roles
            ->middleware('permission:system.manage_roles')
            ->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            // Route::get('/create', [RoleController::class, 'create'])->name('create'); // Removed as it's modal based
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
            Route::get('/templates', [RoleController::class, 'getTemplates'])->name('templates');
            Route::post('/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('toggle-status');

            // Role User Management
            Route::get('/{role}/users', [RoleController::class, 'getUsers'])->name('users');
            Route::get('/{role}/available-users', [RoleController::class, 'getAvailableUsers'])->name('available-users');
            Route::post('/{role}/users', [RoleController::class, 'addUser'])->name('add-user');
            Route::delete('/{role}/users/{user}', [RoleController::class, 'removeUser'])->name('remove-user');
        });

        // Employees
        Route::prefix('employees')
            ->name('employees.')
            ->middleware('permission:system.manage_employees')
            ->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('index');
            Route::get('/create', [EmployeeController::class, 'create'])->name('create');
            Route::post('/', [EmployeeController::class, 'store'])->name('store');
            Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
            Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
            Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
            Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
            Route::get('/{employee}/bookings', [EmployeeController::class, 'bookings'])->name('bookings');
        });

        // Role Hierarchy
        Route::get('/roles/hierarchy', [RoleController::class, 'getHierarchy'])->name('roles.hierarchy');
    });

// Global Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notification routes
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});

// SALON ROUTES (Scoped by Slug)
Route::prefix('{salon_slug}')->middleware([\App\Http\Middleware\CheckSalonSlug::class, \App\Http\Middleware\ConfigureSalonMail::class, \App\Http\Middleware\SetBranchContext::class, \App\Http\Middleware\CheckSalonApproval::class])->group(function () {

    // Guest Online Booking Routes (Public)
    Route::get('/booking/guest', [GuestBookingController::class, 'index'])->name('booking.guest');
    Route::post('/booking/guest', [GuestBookingController::class, 'store'])->name('booking.guest.store');
    Route::get('/booking/guest/services', [GuestBookingController::class, 'getServices'])->name('booking.guest.services');
    Route::get('/booking/guest/slots', [GuestBookingController::class, 'getAvailableSlots'])->name('booking.guest.slots');
    Route::post('/booking/guest/check', [GuestBookingController::class, 'checkExistingBookings'])->name('booking.guest.check');
    Route::post('/booking/guest/reschedule/{id}', [GuestBookingController::class, 'reschedule'])->name('guest.booking.reschedule');

    // Customer Services API (Public)
    Route::get('/customer/services', function () {
        $services = Service::select('id', 'name', 'price')
            ->where('salon_id', app()->bound('current_salon') ? app('current_salon')->id : 0)
            ->where('is_active', true)
            ->get();
        return response()->json($services);
    })->name('customer.services.index');

    // Branch Switching (Authenticated)
    Route::middleware(['auth'])->group(function () {
        Route::post('/branch/switch', [BranchController::class, 'switch'])->name('branch.switch');
        Route::get('/branch/current', [BranchController::class, 'current'])->name('branch.current');
    });

    // Authenticated Salon Routes
    Route::middleware(['auth', 'verified'])->group(function () {

        // Common Dashboard
        Route::middleware(['subscription'])->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        });



        // Salon Admin Routes
        Route::prefix('admin')->name('admin.')->middleware('subscription')->group(function () {
            // Recent bookings
            Route::get('/bookings/recent', [BookingController::class, 'getRecentBookings'])->name('bookings.recent');
            Route::get('/bookings/employee/{employeeId}', [BookingController::class, 'getEmployeeBookings'])->name('bookings.employee');

            // POS Routes
            Route::middleware(['plan_feature:POS System', 'module:pos'])->group(function () {
                Route::prefix('pos')->name('pos.')->group(function () {
                    // Main POS page - requires view permission
                    Route::get('/', [POSController::class, 'index'])->name('index')->middleware('permission:pos.view');
                    Route::get('/booking-details', [POSController::class, 'getBookingDetailsForPos'])->name('booking-details')->middleware('permission:pos.view');

                    // Analytics - requires reports permission
                    Route::get('/analytics', [\App\Http\Controllers\POS\AnalyticsController::class, 'index'])
                        ->name('analytics.index')
                        ->middleware('permission:pos.view_reports');

                    // Cart operations - requires view permission
                    Route::post('/add-to-cart', [POSController::class, 'addToCart'])->name('add-to-cart')->middleware('permission:pos.view');
                    Route::post('/update-cart', [POSController::class, 'updateCart'])->name('update-cart')->middleware('permission:pos.view');
                    Route::post('/remove-from-cart', [POSController::class, 'removeFromCart'])->name('remove-from-cart')->middleware('permission:pos.view');
                    Route::post('/clear-cart', [POSController::class, 'clearCart'])->name('clear-cart')->middleware('permission:pos.view');

                    // Customer operations - requires view permission
                    Route::get('/customers/search', [POSController::class, 'searchCustomers'])->name('customers.search')->middleware('permission:pos.view');
                    Route::post('/customers', [POSController::class, 'storeCustomer'])->name('customers.store')->middleware('permission:pos.view');
                    Route::get('/customers/{customer}', [POSController::class, 'getCustomer'])->name('customers.show')->middleware('permission:pos.view');

                    // Product/Service search - requires view permission
                    Route::get('/products/search', [POSController::class, 'searchProducts'])->name('products.search')->middleware('permission:pos.view');
                    Route::get('/services/search', [POSController::class, 'searchServices'])->name('services.search')->middleware('permission:pos.view');
                    Route::get('/search', [POSController::class, 'search'])->name('search')->middleware('permission:pos.view');
                    Route::get('/customer/mobile/search', [POSController::class, 'searchCustomerByMobile'])->name('customer.mobile.search')->middleware('permission:pos.view');
                    Route::get('/package/services/{id}', [POSController::class, 'getPackageServices'])->name('package.services')->middleware('permission:pos.view');

                    // Membership operations - requires view permission
                    Route::get('/customers/{customer}/membership', [POSController::class, 'getCustomerMembership'])->name('customer.membership')->middleware('permission:pos.view');
                    Route::post('/customers/{customer}/assign-membership', [POSController::class, 'assignMembership'])->name('customers.assign-membership')->middleware('permission:pos.view');
                    Route::post('/customers/{customer}/membership-discounts', [POSController::class, 'getMembershipDiscountsForItems'])->name('customers.membership-discounts')->middleware('permission:pos.view');

                    // Sales operations - requires create permission
                    Route::post('/store', [POSController::class, 'store'])->name('store')->middleware('permission:pos.create_sale');
                    Route::post('/checkout', [POSController::class, 'checkout'])->name('checkout')->middleware('permission:pos.create_sale');
                    Route::post('/process-payment', [POSController::class, 'processPayment'])->name('process-payment')->middleware('permission:pos.manage_payment');

                    // View sales - requires view permission
                    Route::get('/receipt/{sale}', [POSController::class, 'receipt'])->name('receipt')->middleware('permission:pos.view');
                    Route::get('/receipt/{sale}/arabic', [POSController::class, 'arabicReceipt'])->name('receipt.arabic')->middleware('permission:pos.view');
                    Route::get('/sales', [POSController::class, 'sales'])->name('sales.index')->middleware('permission:pos.view');
                    Route::get('/sales/{sale}', [POSController::class, 'showSale'])->name('sales.show')->middleware('permission:pos.view');
                    Route::get('/sales/{sale}/edit', [POSController::class, 'editSale'])->name('sales.edit')->middleware('permission:pos.edit_sale');
                    Route::put('/sales/{sale}', [POSController::class, 'updateSale'])->name('sales.update')->middleware('permission:pos.edit_sale');

                    // Refund/Void - requires specific permissions
                    Route::post('/sales/{sale}/refund', [POSController::class, 'processRefund'])->name('sales.refund')->middleware('permission:pos.process_refunds');
                    Route::match(['post', 'put'], '/sales/{sale}/void', [POSController::class, 'voidSale'])->name('sales.void')->middleware('permission:pos.delete_sale');

                    // Reports - requires reports permission
                    Route::get('/reports/daily-sales', [POSController::class, 'dailySalesReport'])->name('reports.daily-sales')->middleware('permission:pos.view_reports');
                    Route::get('/reports/product-sales', [POSController::class, 'productSalesReport'])->name('reports.product-sales')->middleware('permission:pos.view_reports');
                    Route::get('/reports/service-sales', [POSController::class, 'serviceSalesReport'])->name('reports.service-sales')->middleware('permission:pos.view_reports');

                    // Settings - requires manage settings permission
                    Route::get('/settings', [POSController::class, 'settings'])->name('settings')->middleware('permission:salon.manage_settings');
                    Route::post('/settings', [POSController::class, 'updateSettings'])->name('settings.update')->middleware('permission:salon.manage_settings');
                    Route::get('/receipt-templates', [POSController::class, 'receiptTemplates'])->name('receipt-templates')->middleware('permission:salon.manage_settings');
                    Route::post('/receipt-templates', [POSController::class, 'updateReceiptTemplate'])->name('receipt-templates.update')->middleware('permission:salon.manage_settings');
                });
            });

            // Branch Management
            Route::middleware(['plan_feature:Multi-Branch Support', 'module:branches'])->group(function () {
                Route::post('branches/switch', [BranchController::class, 'switchBranch'])->name('branches.switch');
                Route::resource('branches', BranchController::class);
            });

            // Common admin routes - Protected by permission middleware
            // Customers
            Route::middleware(['plan_feature:Customer Management', 'module:customers', 'permission:customers.view'])->group(function () {
                Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export')->middleware('permission:customers.export');
                Route::post('/customers/import', [CustomerController::class, 'import'])->name('customers.import')->middleware('permission:customers.import');
                Route::get('/customers/import-template', [CustomerController::class, 'downloadTemplate'])->name('customers.import-template')->middleware('permission:customers.import');
                Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
                Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create')->middleware('permission:customers.create');
                Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit')->middleware('permission:customers.edit');
                Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store')->middleware('permission:customers.create');
                Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
                Route::get('/customers/{customer}/details', [CustomerController::class, 'showDetails'])->name('customers.details');
                Route::get('/customers/{customer}/bill-activity', [CustomerController::class, 'getBillActivity'])->name('customers.bill-activity');
                Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update')->middleware('permission:customers.edit');
                Route::put('/customers/{customer}/update-notes', [CustomerController::class, 'updateNotes'])->name('customers.update-notes')->middleware('permission:customers.edit');
                Route::post('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status')->middleware('permission:customers.edit');
                Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('permission:customers.delete');
            });

            // Bookings
            Route::middleware(['plan_feature:Booking System', 'module:appointments', 'permission:bookings.view'])->group(function () {
                // Available slots (Must be before bookings/{booking})
                Route::get('/bookings/available-slots', [BookingController::class, 'getAvailableSlots'])->name('bookings.available-slots');

                // Booking management routes - permission middleware handles access control
                Route::get('/bookings/ongoing', [BookingController::class, 'ongoing'])->name('bookings.ongoing');
                Route::get('/bookings/{booking}/receipt', [BookingController::class, 'receipt'])->name('bookings.receipt');
                Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
                Route::get('/bookings/settings', [BookingController::class, 'settings'])->name('bookings.settings')->middleware('permission:salon.manage_settings');
                Route::post('/bookings/settings', [BookingController::class, 'updateSettings'])->name('bookings.settings.update')->middleware('permission:salon.manage_settings');
                Route::get('/bookings/stats', [BookingController::class, 'stats'])->name('bookings.stats')->middleware('permission:bookings.view_stats');
                Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create')->middleware('permission:bookings.create');
                Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store')->middleware('permission:bookings.create');
                Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
                Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit')->middleware('permission:bookings.edit');
                Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update')->middleware('permission:bookings.edit');
                Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status.update')->middleware('permission:bookings.edit');
                Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy')->middleware('permission:bookings.delete');

                // Guest Booking Management
                Route::get('/guest-booking', [\App\Http\Controllers\Admin\GuestBookingController::class, 'index'])->name('guest-booking.index')->middleware('permission:bookings.manage_guest');

                // API Routes for Bookings
                Route::get('/api/bookings', [BookingController::class, 'getBookings'])->name('bookings.list');
                Route::get('/api/bookings/{id}', [BookingController::class, 'show'])->name('bookings.api.show');
                Route::get('/api/customers', [BookingController::class, 'getCustomers'])->name('bookings.customers');
                Route::get('/api/services', [BookingController::class, 'getServices'])->name('bookings.services');
                Route::get('/api/packages', [BookingController::class, 'getPackages'])->name('bookings.packages');
                Route::get('/api/staff', [BookingController::class, 'getStaff'])->name('bookings.staff');
                Route::get('/api/staff/{user}/details', [BookingController::class, 'getStaffDetails'])->name('bookings.staff.details');
                Route::get('/api/appointment-settings', [BookingController::class, 'getAppointmentSettings'])->name('bookings.appointment-settings');
                Route::get('/api/available-time-slots', [BookingController::class, 'getAvailableTimeSlots'])->name('bookings.available-time-slots');
                Route::get('/api/customers/{customer}/package-balances', [BookingController::class, 'getCustomerPackageBalances'])->name('bookings.customer-package-balances');
                Route::get('/api/customers/{customer}/package-service-limits', [BookingController::class, 'getCustomerPackageServiceLimits'])->name('bookings.customer-package-service-limits');

                // Appointments
                // Appointments (Commented out due to missing Controller)
                /*
                Route::prefix('appointments')->name('appointments.')->group(function () {
                    Route::get('/', [AppointmentController::class, 'index'])->name('index')->middleware('permission:appointments.view');
                    Route::get('/create', [AppointmentController::class, 'create'])->name('create')->middleware('permission:appointments.create');
                    Route::post('/', [AppointmentController::class, 'store'])->name('store')->middleware('permission:appointments.create');
                    Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show')->middleware('permission:appointments.view');
                    Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('edit')->middleware('permission:appointments.edit');
                    Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('update')->middleware('permission:appointments.edit');
                    Route::delete('/{appointment}', [AppointmentController::class, 'destroy'])->name('destroy')->middleware('permission:appointments.delete');
                });
                */
            });

            // Services
            Route::middleware(['plan_feature:Service Management', 'module:services'])->group(function () {
                // Service Import/Export
                Route::get('services/categories/import/template', [\App\Http\Controllers\Admin\ServiceImportExportController::class, 'downloadTemplate'])->name('services.categories.import-template');
                Route::post('services/categories/import', [\App\Http\Controllers\Admin\ServiceImportExportController::class, 'import'])->name('services.categories.import')->middleware('permission:services.create');
                Route::get('services/categories/export', [\App\Http\Controllers\Admin\ServiceImportExportController::class, 'export'])->name('services.categories.export')->middleware('permission:services.view');

                Route::post('services/categories/{category}/bulk-staff', [\App\Http\Controllers\Admin\ServiceCategoryController::class, 'bulkStaffAssignment'])->name('services.categories.bulk-staff')->middleware('permission:services.edit');

                Route::resource('services/categories', \App\Http\Controllers\Admin\ServiceCategoryController::class)
                    ->names(['index' => 'services.categories.index', 'create' => 'services.categories.create', 'store' => 'services.categories.store', 'show' => 'services.categories.show', 'edit' => 'services.categories.edit', 'update' => 'services.categories.update', 'destroy' => 'services.categories.destroy'])
                    ->middleware(['permission:services.view']);

                // Packages
                Route::middleware(['plan_feature:Packages', 'module:packages'])->group(function () {
                    Route::prefix('packages')->name('packages.')->group(function () {
                        Route::get('/', [\App\Http\Controllers\Admin\PackageController::class, 'index'])->name('index')->middleware('permission:packages.view');
                        Route::get('/create', [\App\Http\Controllers\Admin\PackageController::class, 'create'])->name('create')->middleware('permission:packages.create');
                        Route::post('/', [\App\Http\Controllers\Admin\PackageController::class, 'store'])->name('store')->middleware('permission:packages.create');
                        Route::get('/{package}', [\App\Http\Controllers\Admin\PackageController::class, 'show'])->name('show')->middleware('permission:packages.view');
                        Route::get('/{package}/edit', [\App\Http\Controllers\Admin\PackageController::class, 'edit'])->name('edit')->middleware('permission:packages.edit');
                        Route::put('/{package}', [\App\Http\Controllers\Admin\PackageController::class, 'update'])->name('update')->middleware('permission:packages.edit');
                        Route::delete('/{package}', [\App\Http\Controllers\Admin\PackageController::class, 'destroy'])->name('destroy')->middleware('permission:packages.delete');
                    });
                });

                // Memberships
                Route::middleware(['plan_feature:Memberships', 'module:memberships'])->group(function () {
                    Route::prefix('memberships')->name('memberships.')->group(function () {
                        Route::get('/', [\App\Http\Controllers\Admin\MembershipController::class, 'index'])->name('index')->middleware('permission:memberships.view');
                        Route::get('/create', [\App\Http\Controllers\Admin\MembershipController::class, 'create'])->name('create')->middleware('permission:memberships.create');
                        Route::post('/', [\App\Http\Controllers\Admin\MembershipController::class, 'store'])->name('store')->middleware('permission:memberships.create');
                        Route::get('/{membership}', [\App\Http\Controllers\Admin\MembershipController::class, 'show'])->name('show')->middleware('permission:memberships.view');
                        Route::get('/{membership}/edit', [\App\Http\Controllers\Admin\MembershipController::class, 'edit'])->name('edit')->middleware('permission:memberships.edit');
                        Route::put('/{membership}', [\App\Http\Controllers\Admin\MembershipController::class, 'update'])->name('update')->middleware('permission:memberships.edit');
                        Route::delete('/{membership}', [\App\Http\Controllers\Admin\MembershipController::class, 'destroy'])->name('destroy')->middleware('permission:memberships.delete');
                    });
                });

                // Service Category Extras
                Route::post('services/categories/{category}/services', [ServiceCategoryController::class, 'storeService'])->name('services.categories.services.store')->middleware('permission:services.create');
                Route::get('services/{service}', [ServiceCategoryController::class, 'showService'])->name('services.show')->middleware('permission:services.view');
                Route::put('services/{service}', [ServiceCategoryController::class, 'updateService'])->name('services.update')->middleware('permission:services.edit');
                Route::delete('services/{service}', [ServiceCategoryController::class, 'destroyService'])->name('services.destroy')->middleware('permission:services.delete');
            });

            // Inventory Management
            Route::middleware(['plan_feature:Inventory Management', 'module:inventory'])->group(function () {
                Route::prefix('inventory')->name('inventory.')->group(function () {
                    // Main inventory routes
                    Route::get('/', [InventoryController::class, 'index'])->name('index')->middleware('permission:inventory.view');
                    Route::get('/create', [InventoryController::class, 'create'])->name('create')->middleware('permission:inventory.create');
                    Route::post('/', [InventoryController::class, 'store'])->name('store')->middleware('permission:inventory.create');
                    Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit')->middleware('permission:inventory.edit');
                    Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update')->middleware('permission:inventory.edit');
                    Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('destroy')->middleware('permission:inventory.delete');
                    Route::post('/{inventory}/stock', [InventoryController::class, 'updateStock'])->name('stock.update')->middleware('permission:inventory.manage_stock');
                    Route::get('/{inventory}/transactions', [InventoryController::class, 'transactions'])->name('transactions')->middleware('permission:inventory.view');
                    Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock')->middleware('permission:inventory.view');
                    Route::get('/out-of-stock', [InventoryController::class, 'outOfStock'])->name('out-of-stock')->middleware('permission:inventory.view');
                    Route::get('/generate-sku', [InventoryController::class, 'generateSku'])->name('generate-sku')->middleware('permission:inventory.create');
                    Route::post('/generate-name-sku', [InventoryController::class, 'generateNameBasedSku'])->name('generate-name-sku')->middleware('permission:inventory.create');

                    // Stock Alerts
                    Route::prefix('alerts')->name('alerts.')->middleware('permission:inventory.view')->group(function () {
                        Route::get('/', [\App\Http\Controllers\Inventory\StockAlertController::class, 'index'])->name('index');
                        Route::post('/generate', [\App\Http\Controllers\Inventory\StockAlertController::class, 'generate'])->name('generate')->middleware('permission:inventory.manage_stock');
                        Route::patch('/{alert}/resolve', [\App\Http\Controllers\Inventory\StockAlertController::class, 'resolve'])->name('resolve')->middleware('permission:inventory.manage_stock');
                        Route::delete('/cleanup', [\App\Http\Controllers\Inventory\StockAlertController::class, 'cleanup'])->name('cleanup')->middleware('permission:inventory.manage_stock');
                    });

                    // Inventory Reports - requires reports permission
                    Route::prefix('reports')->name('reports.')->middleware('permission:inventory.view_reports')->group(function () {
                        Route::get('/', [\App\Http\Controllers\Inventory\InventoryReportController::class, 'index'])->name('index');
                        Route::get('/stock-valuation', [\App\Http\Controllers\Inventory\InventoryReportController::class, 'stockValuation'])->name('stock-valuation');
                        Route::get('/stock-movement', [\App\Http\Controllers\Inventory\InventoryReportController::class, 'stockMovement'])->name('stock-movement');
                        Route::get('/dead-stock', [\App\Http\Controllers\Inventory\InventoryReportController::class, 'deadStock'])->name('dead-stock');
                        Route::get('/profit-analysis', [\App\Http\Controllers\Inventory\InventoryReportController::class, 'profitAnalysis'])->name('profit-analysis');
                        Route::get('/low-stock', [\App\Http\Controllers\Inventory\InventoryReportController::class, 'lowStock'])->name('low-stock');
                        Route::get('/export/{type}', [\App\Http\Controllers\Inventory\InventoryReportController::class, 'export'])->name('export');
                    });
                });

                // Inventory Categories
                Route::prefix('inventory/categories')->name('inventory.categories.')->middleware('permission:inventory.categories.view')->group(function () {
                    Route::get('/', [\App\Http\Controllers\Inventory\InventoryCategoryController::class, 'index'])->name('index');
                    Route::post('/', [\App\Http\Controllers\Inventory\InventoryCategoryController::class, 'store'])->name('store')->middleware('permission:inventory.categories.create');
                    Route::put('/{category}', [\App\Http\Controllers\Inventory\InventoryCategoryController::class, 'update'])->name('update')->middleware('permission:inventory.categories.edit');
                    Route::delete('/{category}', [\App\Http\Controllers\Inventory\InventoryCategoryController::class, 'destroy'])->name('destroy')->middleware('permission:inventory.categories.delete');
                    Route::get('/active', [\App\Http\Controllers\Inventory\InventoryCategoryController::class, 'getActiveCategories'])->name('active');
                });
            });

            // Product Import/Export
            Route::prefix('products')->name('products.')->group(function () {
                Route::get('/template', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'downloadTemplate'])->name('template');
                Route::post('/import', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'import'])->name('import')->middleware('permission:products.import');
                Route::get('/export', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'export'])->name('export')->middleware('permission:products.export');
            });

            // Staff
            Route::middleware(['plan_feature:Staff Management', 'module:staff'])->group(function () {
                // Staff schedules - requires schedule management permission
                Route::get('staff/schedules', [StaffController::class, 'schedules'])
                    ->name('staff.schedules')
                    ->middleware('permission:staff.manage_schedule');

                // Staff CRUD - granular permissions
                Route::get('staff', [StaffController::class, 'index'])
                    ->name('staff.index')
                    ->middleware('permission:staff.view');
                Route::get('staff/create', [StaffController::class, 'create'])
                    ->name('staff.create')
                    ->middleware('permission:staff.create');
                Route::post('staff', [StaffController::class, 'store'])
                    ->name('staff.store')
                    ->middleware('permission:staff.create');
                Route::get('staff/{staff}', [StaffController::class, 'show'])
                    ->name('staff.show')
                    ->middleware('permission:staff.view');
                Route::get('staff/{staff}/edit', [StaffController::class, 'edit'])
                    ->name('staff.edit')
                    ->middleware('permission:staff.edit');
                Route::put('staff/{staff}', [StaffController::class, 'update'])
                    ->name('staff.update')
                    ->middleware('permission:staff.edit');
                Route::delete('staff/{staff}', [StaffController::class, 'destroy'])
                    ->name('staff.destroy')
                    ->middleware('permission:staff.delete');

                // Staff Scheduling
                Route::prefix('staff/{staff}/schedule')->name('staff.schedule.')->middleware('permission:staff.manage_schedule')->group(function () {
                    Route::get('/', [\App\Http\Controllers\Staff\StaffScheduleController::class, 'getSchedule'])->name('index');
                    Route::put('/', [\App\Http\Controllers\Staff\StaffScheduleController::class, 'updateSchedule'])->name('update');
                    Route::put('/date', [\App\Http\Controllers\Staff\StaffScheduleController::class, 'updateDateSchedule'])->name('update.date');
                    Route::put('/week', [\App\Http\Controllers\Staff\StaffScheduleController::class, 'updateWeekSchedule'])->name('update.week');
                    Route::get('/absences', [\App\Http\Controllers\Staff\StaffScheduleController::class, 'getAbsences'])->name('absences.index');
                    Route::post('/absences', [\App\Http\Controllers\Staff\StaffScheduleController::class, 'storeAbsence'])->name('absences.store');
                    Route::delete('/absences/{absence}', [\App\Http\Controllers\Staff\StaffScheduleController::class, 'destroyAbsence'])->name('absences.destroy');
                });
            });

            // Branches Management
            Route::middleware(['plan_feature:Multi-Branch Support', 'module:branches'])->group(function () {
                Route::middleware(['permission:salon.manage_branches'])->group(function () {
                    Route::resource('branches', BranchController::class);
                });
            });

            // Commissions
            Route::middleware(['plan_feature:Commission Management', 'module:commissions'])->group(function () {
                Route::prefix('commissions')->name('commissions.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\Admin\CommissionProfileController::class, 'index'])->name('index');
                    Route::get('/create', [\App\Http\Controllers\Admin\CommissionProfileController::class, 'create'])->name('create');
                    Route::post('/', [\App\Http\Controllers\Admin\CommissionProfileController::class, 'store'])->name('store');
                    Route::get('/{profile}/edit', [\App\Http\Controllers\Admin\CommissionProfileController::class, 'edit'])->name('edit');
                    Route::put('/{profile}', [\App\Http\Controllers\Admin\CommissionProfileController::class, 'update'])->name('update');
                    Route::delete('/{profile}', [\App\Http\Controllers\Admin\CommissionProfileController::class, 'destroy'])->name('destroy');
                    Route::get('/reports', [\App\Http\Controllers\Admin\CommissionProfileController::class, 'reports'])->name('reports');
                    Route::post('/approve', [\App\Http\Controllers\Admin\CommissionProfileController::class, 'approveCommissions'])->name('approve');
                    Route::post('/mark-paid', [\App\Http\Controllers\Admin\CommissionProfileController::class, 'markAsPaid'])->name('mark-paid');
                });
            });

            // AI Copilot & Autonomous Workflows
            Route::middleware(['plan_feature:AI Insights & Automation', 'module:ai'])->prefix('ai')->name('ai.')->group(function () {
                Route::get('/hub', [\App\Http\Controllers\Admin\AiAutomationController::class, 'index'])->name('hub');
                Route::post('/copilot', [\App\Http\Controllers\Admin\AiAutomationController::class, 'chat'])->name('copilot');
                Route::post('/automation', [\App\Http\Controllers\Admin\AiAutomationController::class, 'runAutomation'])->name('automation');
                Route::post('/consultation', [\App\Http\Controllers\Admin\AiAutomationController::class, 'consultation'])->name('consultation');
                Route::post('/assistant/chat', [\App\Http\Controllers\Admin\AiAutomationController::class, 'customerAssistant'])->name('assistant.chat');
                Route::post('/marketing/generate', [\App\Http\Controllers\Admin\AiAutomationController::class, 'generateMarketing'])->name('marketing.generate');
            });

            // Reports
            Route::middleware(['plan_feature:Analytics & Reports', 'module:reports'])->group(function () {
                Route::prefix('reports')->name('reports.')->group(function () {
                    Route::get('/ai-insights', [\App\Http\Controllers\Admin\AiReportController::class, 'index'])
                        ->name('ai-insights')
                        ->middleware('permission:reports.view');
                    Route::get('/ai-insights/data', [\App\Http\Controllers\Admin\AiReportController::class, 'getForecastData'])
                        ->name('ai-insights.data')
                        ->middleware('permission:reports.view');
                    Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])
                        ->name('index')
                        ->middleware('permission:reports.view');
                    Route::get('/sales', [\App\Http\Controllers\Admin\ReportController::class, 'sales'])
                        ->name('sales')
                        ->middleware('permission:reports.sales');
                    Route::get('/appointments', [\App\Http\Controllers\Admin\ReportController::class, 'appointments'])
                        ->name('appointments')
                        ->middleware('permission:reports.appointments');
                    Route::get('/staff', [\App\Http\Controllers\Admin\ReportController::class, 'staff'])
                        ->name('staff')
                        ->middleware('permission:reports.staff');
                    Route::get('/customers', [\App\Http\Controllers\Admin\ReportController::class, 'customers'])
                        ->name('customers')
                        ->middleware('permission:reports.customers');
                    Route::get('/inventory', [\App\Http\Controllers\Admin\ReportController::class, 'inventory'])
                        ->name('inventory')
                        ->middleware('permission:reports.view');
                    Route::get('/transaction-details/{type}/{id}', [\App\Http\Controllers\Admin\ReportController::class, 'transactionDetails'])
                        ->name('transaction-details')
                        ->middleware('permission:reports.view');

                    // Report Exports
                    Route::get('/sales/export', [\App\Http\Controllers\Admin\ReportController::class, 'exportSales'])
                        ->name('sales.export')
                        ->middleware('permission:reports.sales');
                    Route::get('/appointments/export', [\App\Http\Controllers\Admin\ReportController::class, 'exportAppointments'])
                        ->name('appointments.export')
                        ->middleware('permission:reports.appointments');
                    Route::get('/staff/export', [\App\Http\Controllers\Admin\ReportController::class, 'exportStaff'])
                        ->name('staff.export')
                        ->middleware('permission:reports.staff');
                    Route::get('/customers/export', [\App\Http\Controllers\Admin\ReportController::class, 'exportCustomersReport'])
                        ->name('customers.export')
                        ->middleware('permission:reports.customers');
                    Route::post('/customers/import', [\App\Http\Controllers\Admin\ReportController::class, 'importCustomersReport'])
                        ->name('customers.import')
                        ->middleware('permission:reports.customers');
                    Route::get('/customers/import-template', [\App\Http\Controllers\Admin\ReportController::class, 'downloadCustomersImportTemplate'])
                        ->name('customers.import-template')
                        ->middleware('permission:reports.customers');
                    Route::get('/inventory/export', [\App\Http\Controllers\Admin\ReportController::class, 'exportInventory'])
                        ->name('inventory.export')
                        ->middleware('permission:reports.view');
                    Route::get('/transaction/{type}/{id}', [\App\Http\Controllers\Admin\ReportController::class, 'transactionDetails'])
                        ->name('transaction')
                        ->middleware('permission:reports.view');

                    // Commission Reports
                    Route::prefix('commissions')->name('commissions.')->group(function () {
                        Route::get('/', [\App\Http\Controllers\Admin\CommissionReportController::class, 'index'])->name('index');
                        Route::get('/staff', [\App\Http\Controllers\Admin\CommissionReportController::class, 'byStaff'])->name('by-staff');
                        Route::get('/pending', [\App\Http\Controllers\Admin\CommissionReportController::class, 'pending'])->name('pending');
                        Route::get('/payouts', [\App\Http\Controllers\Admin\CommissionReportController::class, 'payouts'])->name('payouts');
                        Route::post('/revert/{commission}', [\App\Http\Controllers\Admin\CommissionReportController::class, 'revert'])->name('revert');
                        Route::post('/bulk-approve', [\App\Http\Controllers\Admin\CommissionReportController::class, 'bulkApprove'])->name('bulk-approve');
                        Route::post('/bulk-pay', [\App\Http\Controllers\Admin\CommissionReportController::class, 'bulkPay'])->name('bulk-pay');
                    });
                });
            });



            // Salon Settings (SaaS)
            Route::middleware(['auth', 'verified', 'role:salon_admin|manager', 'check_branch'])->group(function () {
                // Mail Settings
                Route::get('/settings/mail', [App\Http\Controllers\SaaS\SalonSettingsController::class, 'editMail'])->name('saas.settings.mail');
                Route::put('/settings/mail', [App\Http\Controllers\SaaS\SalonSettingsController::class, 'updateMail'])->name('saas.settings.mail.update');
                Route::post('/settings/mail/test', [App\Http\Controllers\SaaS\SalonSettingsController::class, 'testMail'])->name('saas.settings.mail.test');
                Route::post('/settings/clear-cache', [App\Http\Controllers\SaaS\SalonSettingsController::class, 'clearCache'])->name('saas.settings.clear-cache');

                // Email Templates
                Route::get('/settings/email-templates', [App\Http\Controllers\SaaS\SalonSettingsController::class, 'emailTemplates'])->name('saas.settings.email-templates');
                Route::get('/settings/email-templates/{type}', [App\Http\Controllers\SaaS\SalonSettingsController::class, 'editEmailTemplate'])->name('saas.settings.email-templates.edit');
                Route::put('/settings/email-templates/{type}', [App\Http\Controllers\SaaS\SalonSettingsController::class, 'updateEmailTemplate'])->name('saas.settings.email-templates.update');
                Route::delete('/settings/email-templates/{type}', [App\Http\Controllers\SaaS\SalonSettingsController::class, 'deleteEmailTemplate'])->name('saas.settings.email-templates.delete');

                // Subscription Management
                Route::get('/subscription', [App\Http\Controllers\SaaS\SubscriptionController::class, 'index'])->name('saas.subscription.index');
                Route::get('/subscription/history', [App\Http\Controllers\SaaS\SubscriptionController::class, 'history'])->name('saas.subscription.history');
                Route::get('/subscription/invoice/{id}', [App\Http\Controllers\SaaS\SubscriptionController::class, 'invoice'])->name('saas.subscription.invoice');
                Route::post('/subscription/upgrade', [App\Http\Controllers\SaaS\SubscriptionController::class, 'upgrade'])->name('saas.subscription.upgrade');
                Route::post('/subscription/renew', [App\Http\Controllers\SaaS\SubscriptionController::class, 'renew'])->name('saas.subscription.renew');
                Route::post('/subscription/cancel', [App\Http\Controllers\SaaS\SubscriptionController::class, 'cancel'])->name('saas.subscription.cancel');

                // Payment Routes
                Route::post('/payment/create-intent', [App\Http\Controllers\SaaS\PaymentController::class, 'createPaymentIntent'])->name('saas.payment.create-intent');
                Route::post('/payment/confirm', [App\Http\Controllers\SaaS\PaymentController::class, 'confirmPayment'])->name('saas.payment.confirm');
                Route::get('/payment/history', [App\Http\Controllers\SaaS\PaymentController::class, 'history'])->name('saas.payment.history');
                Route::get('/payment/{id}', [App\Http\Controllers\SaaS\PaymentController::class, 'show'])->name('saas.payment.show');
            });

            // Salon Settings - Protected by permission middleware
            Route::prefix('salon-settings')->name('salon-settings.')->middleware('permission:salon.manage_settings')->group(function () {
                Route::get('/', [SettingsController::class, 'salonIndex'])->name('index');
                Route::put('/', [SettingsController::class, 'salonUpdate'])->name('update');
            });

            // SMS Setup Wizard
            Route::middleware(['module:sms'])->group(function () {
                Route::get('/settings/sms-setup', [SmsSetupController::class, 'index'])->name('settings.sms-setup');
                Route::post('/settings/sms-setup/test', [SmsSetupController::class, 'testSms'])->name('settings.sms-setup.test');
            });

            // WhatsApp Setup Wizard
            Route::middleware(['module:whatsapp'])->group(function () {
                Route::get('/settings/whatsapp-setup', [WhatsappSetupController::class, 'index'])->name('settings.whatsapp-setup');
                Route::post('/settings/whatsapp-setup/test', [WhatsappSetupController::class, 'testWhatsapp'])->name('settings.whatsapp-setup.test');
            });

            // Salon Role Management - Protected by plan feature, module gating, and permission middleware
            Route::middleware(['plan_feature:Role Management', 'module:roles', 'permission:system.manage_roles'])->group(function () {
                Route::prefix('roles')
                    ->name('roles.')
                    ->group(function () {
                        Route::get('/', [RoleController::class, 'index'])->name('index');
                        Route::post('/', [RoleController::class, 'store'])->name('store');
                        Route::get('/{role}/edit', [SalonRoleController::class, 'edit'])->name('edit');
                        Route::put('/{role}', [SalonRoleController::class, 'update'])->name('update');
                        Route::delete('/{role}', [SalonRoleController::class, 'destroy'])->name('destroy');
                        Route::get('/templates', [RoleController::class, 'getTemplates'])->name('templates');
                        Route::post('/{role}/toggle-status', [SalonRoleController::class, 'toggleStatus'])->name('toggle-status');

                        // Role User Management
                        Route::get('/{role}/users', [SalonRoleController::class, 'getUsers'])->name('users');
                        Route::get('/{role}/available-users', [SalonRoleController::class, 'getAvailableUsers'])->name('available-users');
                        Route::post('/{role}/users', [SalonRoleController::class, 'addUser'])->name('add-user');
                        Route::delete('/{role}/users/{user}', [SalonRoleController::class, 'removeUser'])->name('remove-user');


                    });
            });

        });

        // Salon Settings - Protected by permission middleware




    });

    // Customer Routes
    Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/appointments', [CustomerAppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments/{booking}/rate', [CustomerAppointmentController::class, 'rate'])->name('appointments.rate');
        Route::get('/appointments/create', [CustomerAppointmentController::class, 'create'])->name('appointments.create');
        Route::get('/appointments/calendar', [CustomerAppointmentController::class, 'calendar'])->name('appointments.calendar');
        Route::post('/appointments', [CustomerAppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{booking}', [CustomerAppointmentController::class, 'show'])->name('appointments.show');
        Route::delete('/appointments/{booking}', [CustomerAppointmentController::class, 'destroy'])->name('appointments.destroy');
        Route::get('/appointments/events', [CustomerAppointmentController::class, 'index'])->name('appointments.events');
        Route::get('/api/available-slots', [CustomerAppointmentController::class, 'getAvailableSlots'])->name('api.appointments.available-slots');
        Route::get('/api/services', [CustomerAppointmentController::class, 'getServices'])->name('api.services');
        Route::get('/api/staff', [CustomerAppointmentController::class, 'getStaff'])->name('api.staff');
        Route::get('/api/appointments', [CustomerAppointmentController::class, 'apiAppointments'])->name('api.appointments.index');
        Route::get('/api/appointments/{id}', [CustomerAppointmentController::class, 'apiAppointmentDetails'])->name('api.appointments.show');
        Route::post('/appointments/{booking}/reschedule', [CustomerAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    });

    // Employee Routes (Group 2)
    Route::prefix('employee')->name('employee.')->middleware('subscription')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Staff\ScheduleController::class, 'index'])->name('dashboard');
        Route::get('/appointments', [App\Http\Controllers\Staff\ScheduleController::class, 'appointments'])->name('appointments.index');
        Route::get('/appointments/today', [App\Http\Controllers\Staff\ScheduleController::class, 'today'])->name('appointments.today');
        Route::get('/appointments/upcoming', [App\Http\Controllers\Staff\ScheduleController::class, 'upcoming'])->name('appointments.upcoming');
        Route::get('/appointments/completed', [App\Http\Controllers\Staff\ScheduleController::class, 'completed'])->name('appointments.completed');
        Route::get('/bookings', [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('employee.bookings.index');
        Route::get('/bookings/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('employee.bookings.show');
        Route::get('/appointments/{id}', [App\Http\Controllers\Employee\AppointmentController::class, 'show'])->name('appointments.show');
        Route::get('/appointments/{id}/check-slot', [App\Http\Controllers\Employee\AppointmentController::class, 'checkSlot'])->name('appointments.check-slot');
        Route::post('/appointments/{id}/accept', [App\Http\Controllers\Employee\AppointmentController::class, 'accept'])->name('appointments.accept');
        Route::post('/appointments/{id}/reject', [App\Http\Controllers\Employee\AppointmentController::class, 'reject'])->name('appointments.reject');
        Route::post('/appointments/{id}/complete', [App\Http\Controllers\Employee\AppointmentController::class, 'complete'])->name('appointments.complete');
        Route::post('/appointments/{id}/complete-payment', [App\Http\Controllers\Employee\AppointmentController::class, 'completeWithPayment'])->name('appointments.complete-payment');
        Route::get('/earnings', [App\Http\Controllers\Staff\ScheduleController::class, 'earnings'])->name('earnings');
        Route::get('/profile', [App\Http\Controllers\Staff\ProfileController::class, 'edit'])->name('employee.profile.edit');
        Route::patch('/profile', [App\Http\Controllers\Staff\ProfileController::class, 'update'])->name('employee.profile.update');
        Route::delete('/profile', [App\Http\Controllers\Staff\ProfileController::class, 'destroy'])->name('employee.profile.destroy');
    });

    // Common routes
    Route::post('/admin/bookings/{booking}/assign', [\App\Http\Controllers\Admin\BookingController::class, 'assign'])->name('admin.bookings.assign');
    Route::post('/admin/bookings/{booking}/complete-payment', [\App\Http\Controllers\Admin\BookingController::class, 'completeWithPayment'])->name('admin.bookings.complete-payment');
});
// Stripe Webhook (must be outside auth middleware)
Route::post('/stripe/webhook', [App\Http\Controllers\SaaS\StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

Route::get('/test-package-bug', [App\Http\Controllers\TestController::class, 'testPackageBug']);

require __DIR__ . '/auth.php';
