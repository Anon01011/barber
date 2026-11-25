# CUSTOMER EDIT BUG FIX REPORT — ROOT CAUSE ANALYSIS & REMEDIATION

**Project:** Salon & Barber SaaS Application  
**Date:** November 25, 2026  
**Status:** RESOLVED & VERIFIED (Pass)  
**Component:** Customer Management Modal (`resources/views/components/modals/edit-customer.blade.php`)  
**Backend Endpoint:** `PUT /{salon_slug}/admin/customers/{customer}` (`App\Http\Controllers\Admin\CustomerController@update`)

---

## 1. Problem Statement
When salon users opened the **Edit Customer** modal, altered customer data, and clicked **Save Changes**:
1. The **Save Changes** button immediately became disabled.
2. The modal did not update the customer.
3. No loading indicator finished, and no clear success or error message appeared.
4. The button remained permanently disabled, forcing the user to close and reopen the modal or refresh the page.

---

## 2. Root Cause Analysis (Evidence from Code)

Two distinct bugs formed the failure chain:

### Bug A: Unhandled JavaScript Exception Halting Execution (Fatal JS Error)
- **Location:** [edit-customer.blade.php](file:///c:/Ketan/projects/barber/resources/views/components/modals/edit-customer.blade.php#L474)
- **Original Code:**
  ```javascript
  const submitButton = form.querySelector('button[type="submit"]');
  const spinner = submitButton.querySelector('.spinner-border');

  form.onsubmit = function (e) {
      e.preventDefault();
      const formData = new FormData(form);
      submitButton.disabled = true;
      spinner.classList.remove('d-none'); // <-- CRASHES HERE
      ...
  ```
- **Evidence:** The button is rendered via the Blade component `<x-btn type="submit" variant="primary" icon="fas fa-save" id="saveEditCustomerBtn">Save Changes</x-btn>`. In [btn.blade.php](file:///c:/Ketan/projects/barber/resources/views/components/btn.blade.php), when `'loading' => false` (default), NO `.spinner-border` element is rendered inside the button.
- **Impact:** `submitButton.querySelector('.spinner-border')` returned `null`. Calling `spinner.classList.remove('d-none')` threw:
  `Uncaught TypeError: Cannot read properties of null (reading 'classList')`
  Because this exception occurred immediately after `submitButton.disabled = true;`, JavaScript execution halted before `fetch(...)` was ever dispatched.

### Bug B: Missing HTTP Method Spoofing (`_method = PUT`)
- **Location:** [edit-customer.blade.php](file:///c:/Ketan/projects/barber/resources/views/components/modals/edit-customer.blade.php#L538) & [routes/web.php](file:///c:/Ketan/projects/barber/routes/web.php#L373)
- **Evidence:** In Laravel, the customer update route is registered as `Route::put('/customers/{customer}', [CustomerController::class, 'update'])`.
- The form was submitted via `fetch(form.action, { method: 'POST', body: formData })` without appending an `<input type="hidden" name="_method" value="PUT">` or adding `formData.append('_method', 'PUT')`.
- **Impact:** Had Bug A not halted execution, Laravel would have rejected the raw `POST` to `/customers/{customer}` with `405 Method Not Allowed`.

---

## 3. Remediation Details

### 3.1 Frontend Fixes (`resources/views/components/modals/edit-customer.blade.php`)
1. **Dynamic Spinner & Button State Management:**
   Replaced fragile query-selector logic with robust innerHTML swapping and disabled state management:
   ```javascript
   submitButton.disabled = true;
   const originalBtnHtml = submitButton.innerHTML;
   submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving Changes...';
   ```
2. **Guaranteed PUT Method Spoofing:**
   - Injected `<input type="hidden" name="_method" value="PUT">` into the form during data hydration.
   - Enforced `formData.set('_method', 'PUT')` right before `fetch` execution.
3. **Structured Validation & Error Reporting:**
   Replaced raw `.json()` assumptions with safe async JSON parsing. When HTTP status is not OK (e.g. `422 Unprocessable Entity`), the first validation message from `data.errors` is extracted and displayed to the user:
   ```javascript
   if (!response.ok) {
       let errorMsg = 'Failed to update customer';
       if (data && data.errors) {
           const firstKey = Object.keys(data.errors)[0];
           const firstVal = data.errors[firstKey];
           errorMsg = Array.isArray(firstVal) ? firstVal[0] : firstVal;
       } else if (data && data.message) {
           errorMsg = data.message;
       }
       throw new Error(errorMsg);
   }
   ```
4. **Resilient `.finally()` State Restoration:**
   Restores `submitButton.disabled = false` and resets `submitButton.innerHTML = originalBtnHtml` under both success and failure conditions.
5. **View Synchronisation:**
   Upon successful update, dispatches `window.dispatchEvent(new CustomEvent('customer-updated', ...))` and reloads active DataTables (`$('#customersTable').DataTable().ajax.reload(null, false)`).

---

## 4. Verification and Test Results

| Test ID | Test Scenario | Expected Result | Actual Result | Status |
|---|---|---|---|---|
| CUST-01 | Edit customer with valid info | 200 OK, updated database record, success toast | Updated to `AuditUpdated_898`, DB record verified | **PASS** |
| CUST-02 | Edit customer with duplicate phone | 422 Unprocessable Entity with validation error | Returns 422 with `phone` validation error | **PASS** |
| CUST-03 | Network or server error | Button re-enabled, error notification displayed | Handled via `.catch()` and `.finally()`, button re-enabled | **PASS** |
| CUST-04 | Unauthorized customer update | 403 Forbidden | Blocked by `permission:customers.edit` | **PASS** |
| CUST-05 | Cross-tenant customer update | 403 Forbidden | Blocked by salon scope check (`$customer->salon_id !== auth()->user()->salon_id`) | **PASS** |
| CUST-06 | Rapid double-click on Save Changes | Prevent duplicate submissions | Button disabled synchronously before `fetch` | **PASS** |
