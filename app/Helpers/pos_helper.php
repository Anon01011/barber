<?php

use App\Models\PosSale;
use App\Models\InventoryItem;
use App\Models\Service;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

if (!function_exists('format_currency')) {
    /**
     * Format a number as currency using salon's currency settings
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    function format_currency($amount, $currency = null)
    {
        try {
            $settings = app(\App\Services\SettingsService::class);
            $salon = app()->bound('current_salon') ? app('current_salon') : null;
            $salonId = $salon ? $salon->id : null;

            // Get currency symbol from salon settings
            $symbol = $settings->get('currency_symbol', '$', $salonId);

            // Handle null or non-numeric values
            $amount = is_numeric($amount) ? (float) $amount : 0.00;

            // Format the number
            $formatted = number_format($amount, 2, '.', ',');

            // Add currency symbol
            return $symbol . ' ' . $formatted;
        } catch (\Exception $e) {
            // Fallback to default if any error
            return '$ ' . number_format($amount ?? 0, 2);
        }
    }
}

if (!function_exists('generate_sale_number')) {
    /**
     * Generate a unique sale number
     *
     * @return string
     */
    function generate_sale_number()
    {
        $prefix = 'SALE-';
        $date = now()->format('Ymd');
        $lastSale = PosSale::where('sale_number', 'like', $prefix . $date . '%')
            ->orderBy('sale_number', 'desc')
            ->first();

        if ($lastSale) {
            $lastNumber = (int) substr($lastSale->sale_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $date . '-' . $nextNumber;
    }
}

if (!function_exists('calculate_tax')) {
    /**
     * Calculate tax for a given amount
     *
     * @param float $amount
     * @param float|null $taxRate
     * @return float
     */
    function calculate_tax($amount, $taxRate = null)
    {
        if ($taxRate === null) {
            $settings = app(\App\Services\SettingsService::class);
            $salon = app()->bound('current_salon') ? app('current_salon') : null;
            $salonId = $salon ? $salon->id : null;
            $taxRate = $settings->get('tax_enabled', false, $salonId) ? $settings->get('tax_rate', 0, $salonId) : 0;
        }
        return ($amount * $taxRate) / 100;
    }
}

if (!function_exists('get_cart_subtotal')) {
    /**
     * Calculate subtotal for cart items
     *
     * @param array $items
     * @return float
     */
    function get_cart_subtotal($items)
    {
        return array_reduce($items, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }
}

if (!function_exists('get_cart_tax')) {
    /**
     * Calculate tax for cart items
     *
     * @param array $items
     * @param float|null $taxRate
     * @return float
     */
    function get_cart_tax($items, $taxRate = null)
    {
        $subtotal = get_cart_subtotal($items);
        return calculate_tax($subtotal, $taxRate);
    }
}

if (!function_exists('get_cart_total')) {
    /**
     * Calculate total for cart items including tax
     *
     * @param array $items
     * @param float $discount
     * @param float|null $taxRate
     * @return array [subtotal, tax, total]
     */
    function get_cart_total($items, $discount = 0, $taxRate = null)
    {
        $subtotal = get_cart_subtotal($items);
        $tax = calculate_tax($subtotal - $discount, $taxRate);
        $total = ($subtotal - $discount) + $tax;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => max(0, $total) // Ensure total is not negative
        ];
    }
}

if (!function_exists('update_inventory')) {
    /**
     * Update inventory levels after a sale
     *
     * @param array $items
     * @return bool
     */
    function update_inventory($items)
    {
        try {
            foreach ($items as $item) {
                if ($item['type'] === 'product') {
                    $product = InventoryItem::find($item['id']);
                    if ($product) {
                        $product->decrement('quantity', $item['quantity']);

                        // Log the inventory change
                        $product->inventoryLogs()->create([
                            'user_id' => Auth::id(),
                            'quantity' => -$item['quantity'],
                            'notes' => 'POS Sale',
                            'reference_type' => PosSale::class,
                            'reference_id' => $item['sale_id'] ?? null,
                        ]);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Error updating inventory: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('get_customer_balance')) {
    /**
     * Get customer's current balance
     *
     * @param int $customerId
     * @return float
     */
    function get_customer_balance($customerId)
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return 0;
        }

        // Calculate balance from payments and invoices
        $totalPaid = $customer->payments()->sum('amount');
        $totalInvoiced = $customer->invoices()->sum('total');

        return $totalPaid - $totalInvoiced;
    }
}

if (!function_exists('get_item_details')) {
    /**
     * Get item details by type and ID
     *
     * @param string $type
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    function get_item_details($type, $id)
    {
        if ($type === 'product') {
            return InventoryItem::find($id);
        } elseif ($type === 'service') {
            return Service::find($id);
        }
        return null;
    }
}

if (!function_exists('apply_discount')) {
    /**
     * Apply discount to a price
     *
     * @param float $price
     * @param float $discount
     * @param string $type percentage or fixed
     * @return float
     */
    function apply_discount($price, $discount, $type = 'percentage')
    {
        if ($type === 'percentage') {
            return $price * (1 - ($discount / 100));
        } else {
            return max(0, $price - $discount);
        }
    }
}

if (!function_exists('get_payment_methods')) {
    /**
     * Get available payment methods
     *
     * @return array
     */
    function get_payment_methods()
    {
        $methods = config('pos.payment_methods', []);
        return array_filter($methods, function ($method) {
            return $method['enabled'] ?? false;
        });
    }
}

if (!function_exists('get_receipt_settings')) {
    /**
     * Get receipt settings
     *
     * @return array
     */
    function get_receipt_settings()
    {
        return config('pos.receipt', []);
    }
}

if (!function_exists('get_default_tax_rate')) {
    /**
     * Get default tax rate
     *
     * @return float
     */
    function get_default_tax_rate()
    {
        return (float) config('pos.tax_rate', 10);
    }
}
