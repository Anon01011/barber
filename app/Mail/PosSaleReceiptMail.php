<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PosSale;
use App\Models\EmailTemplate;

class PosSaleReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sale;
    public $salonData;
    public $settings;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PosSale $sale, $salonData, $settings)
    {
        $this->sale = $sale;
        $this->salonData = $salonData;
        $this->settings = $settings;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Generate PDF
        $pdf = Pdf::loadView('pos.receipt', [
            'sale' => $this->sale,
            'salonData' => $this->salonData,
            'settings' => $this->settings,
            // Pass a flag to view to potentially hide print buttons if needed, though they are hidden in print css mostly
            'isPdf' => true
        ]);

        $subject = 'Receipt for Invoice #' . $this->sale->invoice_number;
        $content = null;

        // Try to fetch email template
        $template = EmailTemplate::forSalon($this->sale->salon_id, 'sale_receipt');
        if ($template) {
            $variables = [
                'customer_name' => $this->sale->customer->name ?? 'Guest',
                'salon_name' => $this->salonData['name'],
                'invoice_number' => $this->sale->invoice_number,
                'date' => $this->sale->created_at->format('M d, Y H:i'),
                'total' => format_currency($this->sale->total)
            ];

            $content = $template->render($variables);

            // Subject replacement
            $subject = $template->subject;
            foreach ($variables as $key => $value) {
                $subject = str_replace('{{' . $key . '}}', $value, $subject);
            }
        }

        return $this->subject($subject)
            ->view('emails.pos.sale_receipt', compact('content')) // Pass content to view
            ->attachData($pdf->output(), 'Receipt-' . $this->sale->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
