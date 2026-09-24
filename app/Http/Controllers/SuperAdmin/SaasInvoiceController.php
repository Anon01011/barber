<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SaasPayment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SaasInvoiceController extends Controller
{
    public function show($id)
    {
        $payment = SaasPayment::with(['salon', 'subscription.plan'])->findOrFail($id);
        
        // Check authorization (only super admin or the salon owner can view)
        if (!auth()->user()->isSuperAdmin() && auth()->user()->salon_id !== $payment->salon_id) {
            abort(403);
        }

        return view('super-admin.invoices.show', compact('payment'));
    }

    public function download($id)
    {
        $payment = SaasPayment::with(['salon', 'subscription.plan'])->findOrFail($id);

        if (!auth()->user()->isSuperAdmin() && auth()->user()->salon_id !== $payment->salon_id) {
            abort(403);
        }

        $pdf = Pdf::loadView('super-admin.invoices.pdf', compact('payment'));
        return $pdf->download("invoice-{$payment->transaction_id}.pdf");
    }
}
