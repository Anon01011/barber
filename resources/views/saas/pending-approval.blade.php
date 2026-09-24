<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status === 'pending' ? 'Pending Approval' : 'Application Status' }} - {{ config('app.name') }}</title>
    @vite(['resources/css/auth.css'])
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        .status-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .status-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        .status-rejected {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-size: 0.875rem;
        }

        .info-value {
            color: #0f172a;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.875rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: white;
            color: var(--dark);
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: var(--primary);
        }

        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Left Side -->
        <div class="auth-left">
            <div class="auth-left-content">
                <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; line-height: 1.2;">
                    @if($status === 'pending')
                        We're Reviewing Your Application
                    @else
                        Application Status Update
                    @endif
                </h1>
                <p style="font-size: 1rem; opacity: 0.9; margin-bottom: 2rem; line-height: 1.5;">
                    @if($status === 'pending')
                        Thank you for choosing {{ config('app.name') }}. Your salon registration is under review.
                    @else
                        Your application status has been updated.
                    @endif
                </p>

                <div style="margin-bottom: 2rem;">
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <span>Account Created Successfully</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">{{ $status === 'pending' ? '⏳' : '✗' }}</div>
                        <span>{{ $status === 'pending' ? 'Awaiting Admin Approval' : 'Review Completed' }}</span>
                    </div>
                    @if($status === 'pending')
                        <div class="feature-item">
                            <div class="feature-icon">📧</div>
                            <span>Email Notification on Approval</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Side -->
        <div class="auth-right">
            <div class="auth-card">
                @if($status === 'pending')
                    <div class="status-icon status-pending pulse">
                        ⏳
                    </div>
                    <h2
                        style="text-align: center; font-size: 1.5rem; font-weight: 700; color: var(--dark); margin-bottom: 0.5rem;">
                        Pending Approval
                    </h2>
                    <p style="text-align: center; color: #64748b; margin-bottom: 1.5rem; font-size: 0.875rem;">
                        Your manual payment is being verified by our team
                    </p>
                @else
                    <div class="status-icon status-rejected">
                        ✗
                    </div>
                    <h2
                        style="text-align: center; font-size: 1.5rem; font-weight: 700; color: var(--dark); margin-bottom: 0.5rem;">
                        Application Not Approved
                    </h2>
                    <p style="text-align: center; color: #64748b; margin-bottom: 1.5rem; font-size: 0.875rem;">
                        Please contact support for more information
                    </p>
                @endif

                <div class="info-card">
                    <div class="info-row">
                        <span class="info-label">Salon Name</span>
                        <span class="info-value">{{ $salon->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Owner Email</span>
                        <span class="info-value">{{ $salon->email }}</span>
                    </div>
                    @if($subscription)
                        <div class="info-row">
                            <span class="info-label">Plan</span>
                            <span class="info-value">{{ $subscription->plan->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Method</span>
                            <span class="info-value">
                                {{ $subscription->payment_method ? ucfirst($subscription->payment_method) : 'Trial' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value" style="color: {{ $status === 'pending' ? '#92400e' : '#991b1b' }};">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>
                    @endif
                </div>

                @if($status === 'pending')
                    <div
                        style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.375rem; padding: 1rem; margin-top: 1.5rem;">
                        <p style="color: #92400e; font-size: 0.875rem; margin: 0; line-height: 1.5;">
                            <strong>What's Next?</strong><br>
                            Our team will review your payment details and activate your account within 24-48 hours. You'll
                            receive an email notification once approved.
                        </p>
                    </div>
                @else
                    <div
                        style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem; padding: 1rem; margin-top: 1.5rem;">
                        <p style="color: #991b1b; font-size: 0.875rem; margin: 0; line-height: 1.5;">
                            <strong>Need Help?</strong><br>
                            Please contact our support team at {{ config('mail.from.address') }} for assistance with your
                            application.
                        </p>
                    </div>
                @endif

                <div class="action-buttons">
                    @if($status === 'pending')
                        <button onclick="location.reload()" class="btn btn-primary">
                            🔄 Check Status
                        </button>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="flex: 1; margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="width: 100%;">
                            Logout
                        </button>
                    </form>
                </div>

                @if($status === 'pending')
                    <p style="text-align: center; margin-top: 1.5rem; font-size: 0.75rem; color: #64748b;">
                        This page will automatically refresh every 30 seconds
                    </p>
                @endif
            </div>
        </div>
    </div>

    @if($status === 'pending')
        <script>
            // Auto-refresh every 30 seconds to check if status changed
            setTimeout(function () {
                location.reload();
            }, 30000);
        </script>

        <!-- Stripe JS -->
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const stripe = Stripe('{{ config('services.stripe.key') }}');

            async function initiatePayment(planId) {
                const button = document.getElementById('pay-now-btn');
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

                try {
                    // Create Payment Intent
                    const response = await fetch('{{ route('admin.saas.payment.create-intent', ['salon_slug' => $salon->slug]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ plan_id: planId })
                    });

                    const data = await response.json();

                    if (data.error) {
                        throw new Error(data.error);
                    }

                    // Confirm Payment
                    const { error, paymentIntent } = await stripe.confirmCardPayment(data.clientSecret, {
                        payment_method: {
                            card: {
                                // We need to collect card details. 
                                // Since we don't have a card element here, we should probably redirect to a payment page 
                                // or show a modal with Stripe Elements.
                                // For now, let's redirect to the subscription page which has the payment form?
                                // Or better, let's just redirect to the subscription index where they can pay.
                            }
                        }
                    });

                    // Wait, the subscription index page has the upgrade/renew buttons.
                    // But the user is restricted from accessing it by middleware?
                    // No, CheckSalonApproval middleware allows logout but redirects everything else to pending-approval.
                    // So we need to allow access to payment routes or embed payment form here.

                    // Let's use a simple redirect to a new "complete-payment" route that is allowed by middleware.
                    // Or simpler: Modify CheckSalonApproval to allow payment routes.
                } catch (error) {
                    alert('Payment failed: ' + error.message);
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }
        </script>
    @endif
</body>

</html>