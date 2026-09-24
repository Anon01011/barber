<x-mail::message>
    # Subscription Renewal Request

    **Salon:** {{ $salon->name }} ({{ $salon->slug }})
    **Plan:** {{ $plan->name }}
    **Price:** $ {{ number_format($plan->price, 2) }}
    **Payment Method:** Manual

    A manual payment request has been submitted for this renewal. Please review the payment and approve the request in
    the admin panel.

    <x-mail::button :url="route('admin.subscriptions.index', ['status' => 'pending'])">
        View Pending Subscriptions
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>