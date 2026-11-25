<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Your Visit - {{ $salon->name ?? config('app.name') }}</title>
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
        .rating-card {
            max-width: 520px;
            width: 100%;
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 10px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 2.5rem;
            color: #cbd5e1;
            cursor: pointer;
            transition: color 0.15s ease, transform 0.15s ease;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #f59e0b;
            transform: scale(1.08);
        }
    </style>
</head>
<body>
    <div class="card rating-card">
        <div class="card-body p-4 p-md-5 text-center">
            @if(!empty($salon?->logo))
                <img src="{{ asset('storage/' . $salon->logo) }}" alt="{{ $salon->name }}" class="mb-3 rounded-3" style="max-height: 56px;">
            @endif
            <h4 class="fw-bold text-dark mb-1">{{ $salon->name ?? 'Salon Feedback' }}</h4>
            <p class="text-muted small mb-4">We value your experience! How was your recent visit?</p>

            <div class="bg-light rounded-3 p-3 mb-4 text-start">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">Service</span>
                    <span class="fw-semibold small text-dark">{{ $booking->service->name ?? 'Appointment Service' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">Specialist</span>
                    <span class="fw-semibold small text-dark">{{ $booking->staff->name ?? 'Our Team' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Date</span>
                    <span class="fw-semibold small text-dark">{{ $booking->start_time?->format('M d, Y') }}</span>
                </div>
            </div>

            <form action="{{ route('ratings.guest.store', ['token' => $token]) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark d-block mb-2">Tap a Star to Rate</label>
                    <div class="star-rating">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" required {{ old('rating') == $i ? 'checked' : '' }}>
                            <label for="star{{ $i }}" title="{{ $i }} stars"><i class="fas fa-star"></i></label>
                        @endfor
                    </div>
                    @error('rating')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 text-start">
                    <label for="feedback" class="form-label fw-semibold text-dark small">Share your thoughts (optional)</label>
                    <textarea class="form-control" id="feedback" name="feedback" rows="3" maxlength="1000"
                        placeholder="What did you love? Anything we can improve?">{{ old('feedback') }}</textarea>
                    @error('feedback')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm">
                    <i class="fas fa-paper-plane me-2"></i>Submit Review
                </button>
            </form>
        </div>
    </div>
</body>
</html>
