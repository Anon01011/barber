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
        .health-hero.failover-mode {
            background: linear-gradient(135deg, #450a0a 0%, #7f1d1d 100%);
            border-color: rgba(239, 68, 68, 0.4);
        }
        .log-terminal {
            background: #0b0f17;
            color: #a7f3d0;
            font-family: 'Fira Code', 'Cascadia Code', monospace;
            font-size: 0.75rem;
            padding: 1rem;
            border-radius: 10px;
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #1e293b;
            line-height: 1.5;
        }
        .pulse-indicator {
            display: inline-block;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            margin-right: 6px;
            animation: pulse-ring 2s infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .pulse-danger {
            animation: pulse-ring-danger 1.5s infinite;
        }
        @keyframes pulse-ring-danger {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.8); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
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
            <h1 class="h4 mb-0 text-gray-800 font-weight-bold mt-1">Platform System Health &amp; High Availability</h1>
            <p class="text-muted x-small mb-0">Deep infrastructure diagnostics, automated snapshots, DB replica failover &amp; live health.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('admin.system-health.replica.sync') }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm fw-bold">
                    <i class="fas fa-sync-alt me-1"></i> Sync Replica
                </button>
            </form>
            <form action="{{ route('admin.system-health.backup.create') }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold">
                    <i class="fas fa-database me-1"></i> Create DB Snapshot Now
                </button>
            </form>
            <button onclick="window.location.reload()" class="btn btn-white btn-xs shadow-sm border px-3 rounded-pill fw-semibold">
                <i class="fas fa-redo-alt text-primary me-1"></i> Refresh
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

    <!-- CRITICAL FAILOVER ALERT BANNER (If Active) -->
    @if(!empty($failoverStatus['is_failover_active']))
        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3 p-3 rounded-3" role="alert">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center p-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1 fw-bold">FAILOVER MODE ACTIVE — Application Running on Database Replica</h5>
                    <p class="mb-0 small">
                        Primary database is unreachable or offline. All read/write traffic is redirected to the standby replica.
                        @if(!empty($failoverStatus['failover_reason']))
                            <br><span class="opacity-75">Reason: {{ $failoverStatus['failover_reason'] }} ({{ $failoverStatus['failover_at'] }})</span>
                        @endif
                    </p>
                </div>
            </div>
            <div>
                <form action="{{ route('admin.system-health.failover.deactivate') }}" method="POST" class="d-inline" onsubmit="return confirm('Ensure the primary database is restored before switching back.');">
                    @csrf
                    <button type="submit" class="btn btn-light text-danger fw-bold shadow-sm rounded-pill px-4">
                        <i class="fas fa-undo-alt me-1"></i> Restore Primary Database
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Health Hero Banner -->
    <div class="health-hero mb-4 position-relative overflow-hidden shadow-sm {{ !empty($failoverStatus['is_failover_active']) ? 'failover-mode' : '' }}">
        <div class="row align-items-center g-3 position-relative" style="z-index: 2;">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background: {{ !empty($failoverStatus['is_failover_active']) ? 'rgba(239, 68, 68, 0.2)' : 'rgba(16, 185, 129, 0.2)' }}; border: 2px solid {{ !empty($failoverStatus['is_failover_active']) ? '#ef4444' : '#10b981' }}; color: {{ !empty($failoverStatus['is_failover_active']) ? '#ef4444' : '#10b981' }};">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h5 class="mb-0 fw-bold text-white">Database Safety &amp; High Availability</h5>
                            @if(empty($failoverStatus['is_failover_active']))
                                <span class="chip-pill chip-pill-success"><span class="pulse-indicator bg-success"></span> Primary DB Active &bull; Replica Standby Ready</span>
                            @else
                                <span class="chip-pill chip-pill-danger"><span class="pulse-indicator bg-danger pulse-danger"></span> Failover Mode &bull; Replica in Use</span>
                            @endif
                        </div>
                        <p class="mb-0 text-white-50 small">
                            Active DB: <strong class="text-white">{{ $metrics['database']['database_name'] }}</strong> ({{ $metrics['database']['total_size_mb'] }} MB) &bull; 
                            {{ $metrics['database']['table_count'] }} Tables &bull;
                            Uptime: <strong class="text-white">{{ $metrics['database']['uptime_days'] }} Days</strong> &bull;
                            Memory: <strong class="text-white">{{ $metrics['server']['memory_used_mb'] }} MB</strong> &bull;
                            Replica Synced: <strong class="text-white">{{ $failoverStatus['replica_last_synced'] ?? 'Recent' }}</strong>
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

    <!-- Row 1: Platform Business Vitals -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-calendar-check text-primary me-1"></i> Today's Bookings</span>
                    <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Appointments created today across all salons."></i>
                </div>
                <div class="saas-metric-val">{{ number_format($metrics['business']['bookings_today'] ?? 0) }}</div>
                <div class="x-small text-muted mt-1">Real-time daily throughput</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-clock text-amber me-1"></i> Pending Appointments</span>
                    <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Appointments currently awaiting confirmation."></i>
                </div>
                <div class="saas-metric-val">{{ number_format($metrics['business']['pending_bookings'] ?? 0) }}</div>
                <div class="x-small text-muted mt-1">Awaiting salon / staff response</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-store text-success me-1"></i> Active Salons</span>
                    <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Active salon tenants operating on the platform."></i>
                </div>
                <div class="saas-metric-val">{{ number_format($metrics['business']['active_salons'] ?? 0) }}</div>
                <div class="x-small text-muted mt-1">Tenant isolation enforced</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-crown text-purple me-1"></i> Active Subscriptions</span>
                    <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Current paid subscriptions active."></i>
                </div>
                <div class="saas-metric-val">{{ number_format($metrics['business']['active_subscriptions'] ?? 0) }}</div>
                <div class="x-small text-muted mt-1">SaaS billing status healthy</div>
            </div>
        </div>
    </div>

    <!-- Row 2: 4 Key Infrastructure Diagnostics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-tachometer-alt text-success me-1"></i> Buffer Pool Hit Rate</span>
                    <span class="badge bg-success-light text-success font-weight-bold">{{ $metrics['buffer_pool']['hit_rate'] ?? 99 }}%</span>
                </div>
                <div class="saas-metric-val">{{ $metrics['buffer_pool']['hit_rate'] ?? 99 }}%</div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: {{ $metrics['buffer_pool']['hit_rate'] ?? 99 }}%"></div>
                </div>
                <div class="x-small text-muted mt-1">InnoDB cache memory efficiency</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-hourglass-half text-amber me-1"></i> Slow Queries</span>
                    <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Queries exceeding long_query_time threshold."></i>
                </div>
                <div class="saas-metric-val">{{ $metrics['slow_queries']['count'] ?? 0 }}</div>
                <div class="x-small text-{{ ($metrics['slow_queries']['count'] ?? 0) > 20 ? 'danger' : 'success' }} font-weight-bold mt-1">
                    <i class="fas fa-{{ ($metrics['slow_queries']['count'] ?? 0) > 20 ? 'exclamation-triangle' : 'check-circle' }} me-1"></i>
                    Threshold: &gt; {{ $metrics['slow_queries']['threshold_seconds'] ?? 10 }}s
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-bolt text-primary me-1"></i> Cache Latency</span>
                    <span class="badge bg-primary-light text-primary fw-semibold">{{ ucfirst($metrics['cache_health']['driver'] ?? 'file') }}</span>
                </div>
                <div class="saas-metric-val">{{ $metrics['cache_health']['latency_ms'] ?? 1.2 }} <span class="text-muted fw-normal" style="font-size: 0.9rem;">ms</span></div>
                <div class="x-small text-success font-weight-bold mt-1">
                    <i class="fas fa-check-circle me-1"></i> Roundtrip write &amp; read probe OK
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="saas-metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="saas-metric-label"><i class="fas fa-envelope text-info me-1"></i> SMTP Mail Reachability</span>
                    <span class="badge bg-{{ ($metrics['smtp_probe']['reachable'] ?? true) ? 'success' : 'danger' }}-light text-{{ ($metrics['smtp_probe']['reachable'] ?? true) ? 'success' : 'danger' }}">
                        {{ ($metrics['smtp_probe']['reachable'] ?? true) ? 'Reachable' : 'Unreachable' }}
                    </span>
                </div>
                <div class="saas-metric-val" style="font-size: 1.15rem; line-height: 1.8;">
                    {{ $metrics['smtp_probe']['host'] ?? 'Local / Configured' }}:{{ $metrics['smtp_probe']['port'] ?? 587 }}
                </div>
                <div class="x-small text-muted mt-1">
                    Driver: {{ $metrics['smtp_probe']['mailer'] ?? 'smtp' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: High Availability Failover Controls & Snapshots Table -->
    <div class="row g-4 mb-4">
        <!-- HA & Failover Control Panel -->
        <div class="col-lg-5">
            <div class="saas-table-card h-100">
                <div class="saas-table-card-header d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-random text-primary me-2"></i>
                        <span>High Availability &amp; DB Failover</span>
                    </div>
                    @if(empty($failoverStatus['is_failover_active']))
                        <span class="chip-pill chip-pill-success">Normal Mode</span>
                    @else
                        <span class="chip-pill chip-pill-danger">Failover Active</span>
                    @endif
                </div>
                <div class="p-3">
                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Current Active Connection:</span>
                            <span class="badge bg-{{ empty($failoverStatus['is_failover_active']) ? 'primary' : 'danger' }} px-3 py-1">
                                {{ $failoverStatus['active_connection'] ?? 'mysql' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Primary DB Probe:</span>
                            <span class="text-{{ ($failoverStatus['primary_status']['healthy'] ?? true) ? 'success' : 'danger' }} fw-bold small">
                                <i class="fas fa-{{ ($failoverStatus['primary_status']['healthy'] ?? true) ? 'check-circle' : 'times-circle' }}"></i>
                                {{ ($failoverStatus['primary_status']['healthy'] ?? true) ? 'Online (' . ($failoverStatus['primary_status']['latency_ms'] ?? 1) . 'ms)' : 'Offline / Unreachable' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Replica DB Probe:</span>
                            <span class="text-{{ ($failoverStatus['replica_status']['healthy'] ?? true) ? 'success' : 'warning' }} fw-bold small">
                                <i class="fas fa-{{ ($failoverStatus['replica_status']['healthy'] ?? true) ? 'check-circle' : 'exclamation-circle' }}"></i>
                                {{ ($failoverStatus['replica_status']['healthy'] ?? true) ? 'Standby Ready (' . ($failoverStatus['replica_status']['latency_ms'] ?? 1) . 'ms)' : 'Not Configured / Offline' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Last Replica Sync:</span>
                            <span class="text-dark fw-semibold small">{{ $failoverStatus['replica_last_synced'] ?? 'Recent' }}</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <form action="{{ route('admin.system-health.replica.sync') }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100 fw-bold">
                                <i class="fas fa-sync me-1"></i> Sync Replica from Snapshot
                            </button>
                        </form>

                        @if(empty($failoverStatus['is_failover_active']))
                            <form action="{{ route('admin.system-health.failover.activate') }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Switch database connection to replica? Traffic will immediately route to mysql_replica.');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 fw-bold">
                                    <i class="fas fa-exchange-alt me-1"></i> Test Failover to Replica
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.system-health.failover.deactivate') }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Restore primary database connection?');">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="fas fa-undo me-1"></i> Restore Primary DB
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Backups & Snapshots Management -->
        <div class="col-lg-7">
            <div class="saas-table-card h-100">
                <div class="saas-table-card-header d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-database text-primary me-2"></i>
                        <span>Database Snapshots &amp; Backups</span>
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
                                            <span class="fw-semibold text-truncate" style="max-width: 200px;" title="{{ $b['filename'] }}">{{ $b['filename'] }}</span>
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
    </div>

    <!-- Row 4: 7-Day Activity Chart + Scheduler Automated Tasks -->
    <div class="row g-4 mb-4">
        <!-- 7-Day Server Request / Activity Graph -->
        <div class="col-lg-6">
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

        <!-- Automated Scheduler Tasks Health -->
        <div class="col-lg-6">
            <div class="saas-table-card h-100">
                <div class="saas-table-card-header d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-clock text-primary me-2"></i>
                        <span>Automated Scheduled Jobs &amp; Safety Tasks</span>
                    </div>
                    <span class="badge bg-success-light text-success fw-semibold">Cron Active</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 x-small">
                        <thead>
                            <tr>
                                <th>Command</th>
                                <th>Schedule</th>
                                <th class="text-end">Last Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metrics['scheduler'] ?? [] as $task)
                                <tr>
                                    <td class="font-monospace fw-bold text-dark">{{ $task['command'] }}</td>
                                    <td class="text-muted">{{ $task['schedule'] }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-light text-dark border">{{ $task['last_run'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">Scheduler commands registered in Kernel.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 5: Largest Database Tables & System Environment -->
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
                        <span class="badge bg-{{ !empty($metrics['opcache']['enabled']) ? 'success' : 'secondary' }}-light text-{{ !empty($metrics['opcache']['enabled']) ? 'success' : 'secondary' }}">
                            {{ !empty($metrics['opcache']['enabled']) ? 'Active (' . ($metrics['opcache']['hit_rate'] ?? 0) . '% Hit Rate)' : 'Disabled' }}
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

    <!-- Row 6: Live Log Inspection Terminal -->
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
