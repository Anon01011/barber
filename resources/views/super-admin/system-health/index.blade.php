@extends('layouts.super-admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/saas-dashboard.css') }}">
    <style>
        .health-hero {
            background: linear-gradient(135deg, #0b0f17 0%, #1e293b 100%);
            border-radius: 16px;
            padding: 1.5rem 1.75rem;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .log-terminal {
            background: #0b0f17;
            color: #a7f3d0;
            font-family: 'Fira Code', 'Cascadia Code', monospace;
            font-size: 0.75rem;
            padding: 1rem;
            border-radius: 10px;
            max-height: 320px;
            overflow-y: auto;
            border: 1px solid #1e293b;
            line-height: 1.5;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.super.dashboard') }}" class="text-muted text-decoration-none small">&larr; Dashboard</a>
                <span class="text-muted small">/</span>
                <span class="text-primary small fw-semibold">System Health &amp; DB Safety</span>
            </div>
            <h1 class="h4 mb-0 text-gray-800 font-weight-bold mt-1">Platform System Health &amp; Database Safety</h1>
            <p class="text-muted x-small mb-0">Deep infrastructure diagnostics, real-time database safety, replica snapshots &amp; logs.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('admin.system-health.backup.create') }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold">
                    <i class="fas fa-database me-1"></i> Create DB Snapshot Now
                </button>
            </form>
            <button onclick="window.location.reload()" class="btn btn-white btn-xs shadow-sm border px-3 rounded-pill fw-semibold">
                <i class="fas fa-sync-alt text-primary me-1"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Health Hero Banner -->
    <div class="health-hero mb-4 position-relative overflow-hidden shadow-sm">
        <div class="row align-items-center g-3 position-relative" style="z-index: 2;">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background: rgba(16, 185, 129, 0.2); border: 2px solid #10b981; color: #10b981;">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h5 class="mb-0 fw-bold text-white">Database Safety &amp; High Availability</h5>
                            <span class="chip-pill chip-pill-success"><i class="fas fa-check-circle"></i> Standby Replica Active</span>
                        </div>
                        <p class="mb-0 text-white-50 small">
                            Active DB: <strong class="text-white">{{ $metrics['database']['database_name'] }}</strong> ({{ $metrics['database']['total_size_mb'] }} MB) &bull; 
                            {{ $metrics['database']['table_count'] }} Tables &bull;
                            Uptime: <strong class="text-white">{{ $metrics['database']['uptime_days'] }} Days</strong> &bull;
                            Memory Used: <strong class="text-white">{{ $metrics['server']['memory_used_mb'] }} MB</strong>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="d-inline-flex flex-column align-items-lg-end">
                    <div class="small text-white-50">Disk Utilization</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <div class="progress" style="width: 140px; height: 8px; background: rgba(255,255,255,0.2); border-radius: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $metrics['storage']['disk_used_percentage'] }}%"></div>
                        </div>
                        <span class="fw-bold text-white small">{{ $metrics['storage']['disk_used_percentage'] }}%</span>
                    </div>
                    <div class="x-small text-white-50 mt-1">{{ $metrics['storage']['disk_free_gb'] }} GB free of {{ $metrics['storage']['disk_total_gb'] }} GB</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Key Health Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-database text-primary me-1"></i> Total DB Size</span>
                    <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Total size of MySQL database including data and indexes."></i>
                </div>
                <div class="saas-metric-val">{{ $metrics['database']['total_size_mb'] }} <span class="text-muted fw-normal" style="font-size: 0.9rem;">MB</span></div>
                <div class="x-small text-muted mt-1">
                    Data: {{ $metrics['database']['data_size_mb'] }} MB &bull; Indexes: {{ $metrics['database']['index_size_mb'] }} MB
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-file-alt text-info me-1"></i> Log Monitoring</span>
                    <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Current size of storage/logs/laravel.log and error count."></i>
                </div>
                <div class="saas-metric-val">{{ $metrics['logs']['size_formatted'] }}</div>
                <div class="x-small text-{{ $metrics['logs']['error_count'] > 0 ? 'warning' : 'success' }} font-weight-bold mt-1">
                    <i class="fas fa-{{ $metrics['logs']['error_count'] > 0 ? 'exclamation-circle' : 'check-circle' }} me-1"></i>
                    {{ $metrics['logs']['error_count'] }} Error events in latest logs
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-server text-success me-1"></i> Server Memory</span>
                    <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="PHP memory usage and peak allocated memory."></i>
                </div>
                <div class="saas-metric-val">{{ $metrics['server']['memory_used_mb'] }} <span class="text-muted fw-normal" style="font-size: 0.9rem;">MB</span></div>
                <div class="x-small text-muted mt-1">
                    Peak: {{ $metrics['server']['memory_peak_mb'] }} MB &bull; Limit: {{ $metrics['server']['memory_limit'] }}
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-tasks text-purple me-1"></i> Queue &amp; Background</span>
                    <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Current queue jobs state."></i>
                </div>
                <div class="saas-metric-val">{{ $metrics['queue']['pending'] }} <span class="text-muted fw-normal" style="font-size: 0.9rem;">Pending</span></div>
                <div class="x-small text-{{ $metrics['queue']['failed'] > 0 ? 'danger' : 'success' }} font-weight-bold mt-1">
                    <i class="fas fa-{{ $metrics['queue']['failed'] > 0 ? 'exclamation-triangle' : 'check-circle' }} me-1"></i>
                    {{ $metrics['queue']['failed'] }} Failed background jobs
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Database Backups & Runtime Replicas + Activity Chart -->
    <div class="row g-4 mb-4">
        <!-- Database Backups Management -->
        <div class="col-lg-7">
            <div class="saas-table-card h-100">
                <div class="saas-table-card-header d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-database text-primary me-2"></i>
                        <span>Database Snapshots &amp; Runtime Replicas</span>
                    </div>
                    <form action="{{ route('admin.system-health.backup.create') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <input type="hidden" name="replica_only" value="0">
                        <button type="submit" class="btn btn-xs btn-primary rounded-pill px-3 fw-semibold">
                            <i class="fas fa-plus me-1"></i> Snapshot Now
                        </button>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 x-small">
                        <thead>
                            <tr>
                                <th>Backup File</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Created</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups as $b)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-file-code text-{{ $b['is_replica'] ? 'success' : 'primary' }}"></i>
                                            <span class="fw-semibold text-truncate" style="max-width: 220px;" title="{{ $b['filename'] }}">{{ $b['filename'] }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($b['is_replica'])
                                            <span class="chip-pill chip-pill-success"><i class="fas fa-sync-alt"></i> Standby Replica</span>
                                        @else
                                            <span class="chip-pill chip-pill-blue">Archive Snapshot</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ $b['size_formatted'] }}</td>
                                    <td class="text-muted">{{ $b['created_at'] }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('admin.system-health.backup.download', $b['filename']) }}" class="btn btn-xs btn-outline-primary" title="Download SQL Dump">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @if(!$b['is_replica'])
                                                <form action="{{ route('admin.system-health.backup.delete', $b['filename']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this backup file?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No database backups generated yet. Click "Snapshot Now" above.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 7-Day Server Request / Activity Graph -->
        <div class="col-lg-5">
            <div class="saas-table-card h-100">
                <div class="saas-table-card-header d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-chart-area text-info me-2"></i>
                        <span>Platform Activity &amp; Load (Last 7 Days)</span>
                    </div>
                    <span class="chip-pill chip-pill-neutral">Bookings vs Tenants</span>
                </div>
                <div class="p-3">
                    <div style="height: 250px;">
                        <canvas id="healthActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Largest Database Tables & System Environment -->
    <div class="row g-4 mb-4">
        <!-- Top Database Tables Breakdown -->
        <div class="col-lg-7">
            <div class="saas-table-card h-100">
                <div class="saas-table-card-header">
                    <i class="fas fa-table text-primary me-2"></i>
                    <span>Top Database Tables Breakdown (Size &amp; Row Count)</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 x-small">
                        <thead>
                            <tr>
                                <th>Table</th>
                                <th>Engine</th>
                                <th>Rows</th>
                                <th>Data Size</th>
                                <th>Index Size</th>
                                <th class="text-end">Total Size</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tables as $tbl)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $tbl->name }}</td>
                                    <td><span class="badge bg-light text-muted border">{{ $tbl->engine ?? $tbl->ENGINE ?? 'InnoDB' }}</span></td>
                                    <td>{{ number_format($tbl->rows) }}</td>
                                    <td class="text-muted">{{ $tbl->data_mb }} MB</td>
                                    <td class="text-muted">{{ $tbl->index_mb }} MB</td>
                                    <td class="text-end fw-bold text-primary">{{ $tbl->size_mb }} MB</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- System & Runtime Configuration -->
        <div class="col-lg-5">
            <div class="saas-table-card h-100">
                <div class="saas-table-card-header">
                    <i class="fas fa-sliders-h text-primary me-2"></i>
                    <span>Environment &amp; Runtime Settings</span>
                </div>
                <div class="list-group list-group-flush x-small">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="text-muted">PHP Version</span>
                        <span class="fw-bold text-dark">{{ $metrics['server']['php_version'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="text-muted">Laravel Framework</span>
                        <span class="fw-bold text-dark">{{ $metrics['server']['laravel_version'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="text-muted">MySQL Server</span>
                        <span class="fw-bold text-dark">{{ $metrics['database']['version'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="text-muted">Operating System</span>
                        <span class="fw-bold text-dark">{{ $metrics['server']['os'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="text-muted">OPcache Enabled</span>
                        <span class="badge bg-{{ $metrics['server']['opcache_enabled'] ? 'success' : 'secondary' }}-light text-{{ $metrics['server']['opcache_enabled'] ? 'success' : 'secondary' }}">
                            {{ $metrics['server']['opcache_enabled'] ? 'Active' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="text-muted">Upload Max File Size</span>
                        <span class="fw-bold text-dark">{{ $metrics['server']['upload_max_filesize'] }} (Post: {{ $metrics['server']['post_max_size'] }})</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="text-muted">Cache Driver</span>
                        <span class="fw-bold text-dark">{{ ucfirst($metrics['server']['cache_driver']) }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="text-muted">Session Driver</span>
                        <span class="fw-bold text-dark">{{ ucfirst($metrics['server']['session_driver']) }}</span>
                    </div>
                </div>
                <div class="p-3 border-top bg-light">
                    <form action="{{ route('admin.system-settings.clear-cache') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm w-100 shadow-sm fw-semibold">
                            <i class="fas fa-broom me-2"></i> Flush Framework Cache
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Live Log Inspection Terminal -->
    <div class="row g-4">
        <div class="col-12">
            <div class="saas-table-card">
                <div class="saas-table-card-header d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-terminal text-info me-2"></i>
                        <span>Recent System Log Entries (storage/logs/laravel.log)</span>
                    </div>
                    <span class="text-muted x-small">Showing latest entries (Reverse chronological)</span>
                </div>
                <div class="p-3">
                    <div class="log-terminal">
                        @forelse($metrics['logs']['recent_entries'] as $entry)
                            <div class="mb-1 text-wrap {{ stripos($entry, 'ERROR') !== false || stripos($entry, 'Exception') !== false ? 'text-danger fw-bold' : '' }}">
                                {{ $entry }}
                            </div>
                        @empty
                            <div class="text-muted">No log entries found. System log is clean.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('healthActivityChart')?.getContext('2d');
        if (!ctx) return;

        const trendData = @json($metrics['activity_trend']);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendData.labels || [],
                datasets: [
                    {
                        label: 'Appointments',
                        data: trendData.bookings || [],
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'New Salons',
                        data: trendData.salons || [],
                        borderColor: '#10b981',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.35,
                        borderDash: [4, 4]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { size: 11, family: 'Plus Jakarta Sans' } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 10 } },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: {
                        ticks: { font: { size: 10 } },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
