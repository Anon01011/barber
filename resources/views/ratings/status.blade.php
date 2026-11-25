<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $salon->name ?? config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }
        .status-card {
            max-width: 460px;
            width: 100%;
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body>
    <div class="card status-card">
        <div class="card-body p-4 p-md-5 text-center">
            @if($status === 'success')
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                    <i class="fas fa-check-circle fa-3x"></i>
                </div>
            @elseif($status === 'already_rated')
                <div class="rounded-circle bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                    <i class="fas fa-star fa-3x"></i>
                </div>
            @else
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
            @endif

            <h4 class="fw-bold text-dark mb-2">{{ $title }}</h4>
            <p class="text-muted mb-4">{{ $message }}</p>

            @if(!empty($salon?->slug))
                <a href="{{ route('booking.guest', ['salon_slug' => $salon->slug]) }}" class="btn btn-outline-primary rounded-3 px-4">
                    Book Another Appointment
                </a>
            @endif
        </div>
    </div>
</body>
</html>
