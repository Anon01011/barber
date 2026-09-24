@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Page Header -->
        <div class="mb-4">
            <h2 class="h3 mb-1">My Dashboard</h2>
            <p class="text-muted mb-0">View your performance ratings and customer feedback</p>
        </div>

        <!-- Rating Statistics Cards -->
        <div class="row g-4 mb-4">
            <!-- Average Rating Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-star fa-3x text-warning"></i>
                        </div>
                        <h3 class="mb-1 fw-bold">{{ number_format($averageRating, 1) }}/5.0</h3>
                        <p class="text-muted mb-0">Average Rating</p>
                        <div class="mt-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= floor($averageRating) ? 'text-warning' : 'text-light' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Ratings Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-comments fa-3x text-primary"></i>
                        </div>
                        <h3 class="mb-1 fw-bold">{{ $totalRatings }}</h3>
                        <p class="text-muted mb-0">Total Ratings</p>
                        <small class="text-muted">From customers</small>
                    </div>
                </div>
            </div>

            <!-- Rating Breakdown Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="mb-3 fw-bold">Rating Breakdown</h6>
                        @foreach ($ratingBreakdown as $stars => $count)
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-2" style="width: 60px;">
                                    <small>{{ $stars }} <i class="fas fa-star text-warning small"></i></small>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" role="progressbar"
                                            style="width: {{ $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="ms-2" style="width: 30px;">
                                    <small class="text-muted">{{ $count }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Ratings -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0">Recent Customer Feedback</h5>
            </div>
            <div class="card-body p-0">
                @if ($recentRatings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">Customer</th>
                                    <th class="py-3">Service</th>
                                    <th class="py-3">Rating</th>
                                    <th class="py-3">Comment</th>
                                    <th class="py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentRatings as $rating)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <div class="avatar-title rounded-circle bg-primary text-white shadow-sm">
                                                        {{ strtoupper(substr($rating->booking->customer->name ?? 'G', 0, 1)) }}
                                                    </div>
                                                </div>
                                                <span>{{ $rating->booking->customer->name ?? 'Guest Customer' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-light text-dark border">{{ $rating->service->name }}</span>
                                            @if($rating->booking && $rating->booking->package)
                                                <div class="mt-1">
                                                    <span class="badge bg-purple-subtle text-purple-emphasis rounded-pill px-2 py-1"
                                                        style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.65em;">
                                                        <i class="fas fa-box me-1"></i>{{ $rating->booking->package->name }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star small {{ $i <= $rating->rating ? 'text-warning' : 'text-light' }}"></i>
                                                @endfor
                                                <span class="ms-2 small text-muted">({{ $rating->rating }}/5)</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @if ($rating->comment)
                                                <span class="text-truncate d-inline-block" style="max-width: 300px;"
                                                    title="{{ $rating->comment }}">
                                                    {{ $rating->comment }}
                                                </span>
                                            @else
                                                <span class="text-muted small">No comment</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <small class="text-muted">{{ $rating->created_at->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-star fa-3x text-muted opacity-25 mb-3"></i>
                        <p class="text-muted mb-0">No ratings yet</p>
                        <small class="text-muted">Customer ratings will appear here after completed bookings</small>
                    </div>
                @endif
            </div>
        </div>

        <!-- All Ratings -->
        @if ($ratings->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">All Ratings</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">Customer</th>
                                    <th class="py-3">Service</th>
                                    <th class="py-3">Rating</th>
                                    <th class="py-3">Comment</th>
                                    <th class="py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ratings as $rating)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <div class="avatar-title rounded-circle bg-primary text-white shadow-sm">
                                                        {{ strtoupper(substr($rating->booking->customer->name ?? 'G', 0, 1)) }}
                                                    </div>
                                                </div>
                                                <span>{{ $rating->booking->customer->name ?? 'Guest Customer' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-light text-dark border">{{ $rating->service->name }}</span>
                                            @if($rating->booking && $rating->booking->package)
                                                <div class="mt-1">
                                                    <span class="badge bg-purple-subtle text-purple-emphasis rounded-pill px-2 py-1" style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.65em;">
                                                        <i class="fas fa-box me-1"></i>{{ $rating->booking->package->name }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star small {{ $i <= $rating->rating ? 'text-warning' : 'text-light' }}"></i>
                                                @endfor
                                                <span class="ms-2 small text-muted">({{ $rating->rating }}/5)</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @if ($rating->comment)
                                                <span class="text-truncate d-inline-block" style="max-width: 300px;"
                                                    title="{{ $rating->comment }}">
                                                    {{ $rating->comment }}
                                                </span>
                                            @else
                                                <span class="text-muted small">No comment</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <small class="text-muted">{{ $rating->created_at->format('M d, Y') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $ratings->links() }}
                </div>
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .avatar {
                width: 35px;
                height: 35px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .avatar-title {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
                font-weight: 600;
            }
        </style>
    @endpush
@endsection