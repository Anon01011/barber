@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">
                <i class="fas fa-star text-warning me-2"></i>Customer Feedback &amp; Reviews
            </h1>
            <p class="text-muted small mb-0">Monitor service quality, barber ratings, and client satisfaction across your salon.</p>
        </div>
    </div>

    <!-- Summary KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center py-4">
                    <span class="text-muted small fw-bold text-uppercase mb-1">Average Salon Rating</span>
                    <div class="display-4 fw-bold text-dark mb-1">{{ number_format($averageRating, 1) }}</div>
                    <div class="text-warning mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($averageRating) ? '' : 'text-muted opacity-25' }}"></i>
                        @endfor
                    </div>
                    <span class="text-muted small">Based on {{ number_format($totalReviews) }} verified reviews</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">Rating Distribution</h6>
                    @foreach($distribution as $star => $data)
                        <div class="d-flex align-items-center mb-2">
                            <span class="small fw-semibold text-dark me-2" style="width: 45px;">{{ $star }} <i class="fas fa-star text-warning"></i></span>
                            <div class="progress flex-grow-1" style="height: 10px;">
                                <div class="progress-bar {{ $star >= 4 ? 'bg-success' : ($star == 3 ? 'bg-warning' : 'bg-danger') }}"
                                     role="progressbar" style="width: {{ $data['percent'] }}%"></div>
                            </div>
                            <span class="small text-muted ms-3 text-end" style="width: 70px;">{{ $data['count'] }} ({{ $data['percent'] }}%)</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-muted">Filter by Specialist</label>
                    <select name="staff_id" class="form-select form-select-sm">
                        <option value="">All Staff Members</option>
                        @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-muted">Filter by Star Rating</label>
                    <select name="rating" class="form-select form-select-sm">
                        <option value="">All Ratings</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.feedback.index', ['salon_slug' => request()->route('salon_slug')]) }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Specialist</th>
                            <th>Rating</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td class="small text-muted">{{ $review->updated_at?->format('M d, Y') }}</td>
                                <td class="fw-semibold text-dark">{{ $review->customer->name ?? 'Guest' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $review->service->name ?? 'Service' }}</span></td>
                                <td class="small">{{ $review->staff->name ?? 'Unassigned' }}</td>
                                <td>
                                    <span class="badge {{ $review->rating >= 4 ? 'bg-success' : ($review->rating == 3 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $review->rating }} <i class="fas fa-star ms-1"></i>
                                    </span>
                                </td>
                                <td class="small text-muted" style="max-width: 380px;">
                                    {{ $review->feedback ?: 'No written review provided.' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-comment-slash fa-2x mb-2 d-block opacity-50"></i>
                                    No customer reviews match the selected filters yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($reviews->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
