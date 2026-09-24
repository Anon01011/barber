@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Earnings Overview</h2>

        <div class="row">
            <!-- Today's Earnings -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Today's Earnings</h5>
                    </div>
                    <div class="card-body">
                        @if($todayEarnings->total_earnings > 0)
                            <h3 class="text-primary">
                                {{ currency_symbol() }}{{ number_format($todayEarnings->total_earnings, 2) }}</h3>
                        @else
                            <p class="text-muted mb-0">No earnings today</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Weekly Earnings -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">This Week's Earnings</h5>
                    </div>
                    <div class="card-body">
                        @if($weeklyEarnings->total_earnings > 0)
                            <h3 class="text-white">
                                {{ currency_symbol() }}{{ number_format($weeklyEarnings->total_earnings, 2) }}</h3>
                        @else
                            <p class="text-muted mb-0">No earnings this week</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- All-time Earnings -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">All-time Earnings</h5>
                    </div>
                    <div class="card-body">
                        @if($allTimeEarnings->total_earnings > 0)
                            <h3 class="text-info">
                                {{ currency_symbol() }}{{ number_format($allTimeEarnings->total_earnings, 2) }}</h3>
                        @else
                            <p class="text-muted mb-0">No earnings recorded</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Earnings History -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Earnings History</h5>
            </div>
            <div class="card-body">
                @if($recentEarnings->isEmpty())
                    <div class="alert alert-info">No recent earnings found.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentEarnings as $earning)
                                    <tr>
                                        <td>{{ $earning->start_time->format('Y-m-d') }}</td>
                                        <td>{{ $earning->start_time->format('h:i A') }}</td>
                                        <td>{{ $earning->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $earning->service->name ?? 'N/A' }}</td>
                                        <td>{{ currency_symbol() }}{{ number_format($earning->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection