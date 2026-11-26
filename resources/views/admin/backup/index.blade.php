@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/salon-dashboard.css') }}">
    <style>
        .backup-hero {
            background: linear-gradient(135deg, var(--theme-gradient-start, var(--theme-primary, #0b0f17)) 0%, var(--theme-sidebar, #1a2232) 50%, var(--theme-surface, #0f172a) 100%);
            border-radius: 16px;
            padding: 1.5rem 1.75rem;
            color: #ffffff;
            border: 1px solid var(--theme-border, rgba(255,255,255,0.15));
        }
        .module-checkbox-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            background: #ffffff;
            transition: all 0.2s ease;
            cursor: pointer;
            height: 100%;
        }
        .module-checkbox-card:hover {
            border-color: var(--theme-accent, #c5a880);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
            transform: translateY(-2px);
        }
        .module-checkbox-card.selected {
            border-color: var(--theme-accent, #c5a880);
            background: rgba(197, 168, 128, 0.05);
        }
        .plan-locked-card {
            background: linear-gradient(135deg, #0b0f17 0%, #1e293b 100%);
            border-radius: 20px;
            border: 1px solid rgba(197, 168, 128, 0.35);
            color: #ffffff;
            padding: 3rem 2rem;
            text-align: center;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dashboard', ['salon_slug' => $salon->slug]) }}" class="text-muted text-decoration-none small">&larr; Dashboard</a>
                <span class="text-muted small">/</span>
                <span class="small fw-semibold" style="color: var(--theme-accent, #c5a880);">Data Protection</span>
            </div>
            <h1 class="h4 mb-0 fw-bold text-dark mt-1">Salon Data Backup &amp; Disaster Recovery</h1>
            <p class="text-muted small mb-0">Automated 24-hour backups, modular dataset exports, and tenant-isolated restoration.</p>
        </div>
        @if($canUseBackup)
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold" onclick="triggerEmailBackup()">
                    <i class="fas fa-paper-plane me-1"></i> Email Backup Now
                </button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="triggerDownloadBackup()">
                    <i class="fas fa-download me-1"></i> Download Backup
                </button>
            </div>
        @endif
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

    @if(!$canUseBackup)
        <!-- Plan Upgrade Locked Banner -->
        <div class="plan-locked-card shadow-lg mb-5">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                 style="width: 72px; height: 72px; background: rgba(197, 168, 128, 0.15); border: 2px solid var(--theme-accent, #c5a880);">
                <i class="fas fa-lock fa-2x" style="color: var(--theme-accent, #c5a880);"></i>
            </div>
            <h3 class="fw-bold text-white mb-2">Data Backup &amp; Safety Module Locked</h3>
            <p class="text-white-50 mx-auto mb-4" style="max-width: 620px; font-size: 0.95rem;">
                Automated 24-hour backups, custom modular data exports, and disaster recovery imports are only accessible for salons subscribed to plans with the <strong>Data Backup &amp; Safety</strong> module enabled.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('admin.saas.subscription.index', ['salon_slug' => $salon->slug]) }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow">
                    <i class="fas fa-crown me-2"></i> View Plans &amp; Upgrade Now
                </a>
                <a href="{{ route('dashboard', ['salon_slug' => $salon->slug]) }}" class="btn btn-outline-light btn-lg rounded-pill px-4 fw-semibold">
                    Return to Dashboard
                </a>
            </div>
        </div>
    @else
        <!-- Hero Card -->
        <div class="backup-hero mb-4 position-relative overflow-hidden shadow-sm">
            <div class="row align-items-center g-3 position-relative" style="z-index: 2;">
                <div class="col-lg-7">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 52px; height: 52px; background: rgba(197, 168, 128, 0.2); border: 2px solid var(--theme-accent, #c5a880); color: var(--theme-accent, #c5a880);">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h5 class="mb-0 fw-bold text-white">{{ $salon->name }} Data Safety Shield</h5>
                                <span class="badge rounded-pill bg-success text-white" style="font-size: 0.72rem;">
                                    <i class="fas fa-check-circle me-1"></i> Plan Module Active
                                </span>
                                <span class="badge rounded-pill bg-info text-white" style="font-size: 0.72rem;">
                                    <i class="fas fa-clock me-1"></i> Auto-Sync 24h
                                </span>
                                <span class="badge rounded-pill bg-light text-dark border" style="font-size: 0.72rem;">
                                    <i class="fas fa-database me-1 text-primary"></i> Size Limit: {{ $maxBackupSizeMb ? $maxBackupSizeMb . ' MB' : 'Unlimited (∞)' }}
                                </span>
                                <span class="badge rounded-pill bg-light text-dark border" style="font-size: 0.72rem;">
                                    <i class="fas fa-history me-1 text-secondary"></i> Snapshots: {{ count($backups) }} / {{ $maxSnapshots ? $maxSnapshots : '∞' }}
                                </span>
                            </div>
                            <p class="mb-0 text-white-50 small">
                                Complete tenant isolation. Your clients, services, packages, memberships, inventory, and point-of-sale logs are securely backed up and exportable anytime.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <div class="d-inline-flex flex-wrap gap-2 justify-content-lg-end">
                        <div class="bg-white bg-opacity-10 rounded px-2 px-md-3 py-2 text-center" style="border: 1px solid rgba(255,255,255,0.15);">
                            <div class="h6 mb-0 fw-bold text-white">{{ number_format($stats['services'] ?? 0) }}</div>
                            <div class="x-small text-white-50">Services</div>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded px-2 px-md-3 py-2 text-center" style="border: 1px solid rgba(255,255,255,0.15);">
                            <div class="h6 mb-0 fw-bold text-white">{{ number_format($stats['packages'] ?? 0) }}</div>
                            <div class="x-small text-white-50">Packages</div>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded px-2 px-md-3 py-2 text-center" style="border: 1px solid rgba(255,255,255,0.15);">
                            <div class="h6 mb-0 fw-bold text-white">{{ number_format($stats['memberships'] ?? 0) }}</div>
                            <div class="x-small text-white-50">Members</div>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded px-2 px-md-3 py-2 text-center" style="border: 1px solid rgba(255,255,255,0.15);">
                            <div class="h6 mb-0 fw-bold text-white">{{ number_format($stats['customers'] ?? 0) }}</div>
                            <div class="x-small text-white-50">Clients</div>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded px-2 px-md-3 py-2 text-center" style="border: 1px solid rgba(255,255,255,0.15);">
                            <div class="h6 mb-0 fw-bold text-white">{{ number_format($stats['bookings'] ?? 0) }}</div>
                            <div class="x-small text-white-50">Bookings</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modular Selection & Export Form -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-layer-group me-2" style="color: var(--theme-accent, #c5a880);"></i> Select Salon Data to Backup
                    </h6>
                    <small class="text-muted">Choose which modules to include in your export. By default, all modules are included.</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-3" onclick="toggleAllBackupModules(true)">
                        <i class="fas fa-check-double me-1"></i> Select All
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-3" onclick="toggleAllBackupModules(false)">
                        <i class="fas fa-times me-1"></i> Deselect All
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <form id="backupExportForm" action="{{ route('admin.backup.export', ['salon_slug' => $salon->slug]) }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-4">
                        <!-- 1. Services -->
                        <div class="col-md-6 col-lg-3">
                            <label class="module-checkbox-card selected d-flex align-items-start gap-2 w-100" for="mod_services">
                                <input class="form-check-input mt-1 backup-mod-check" type="checkbox" name="modules[]" value="services" id="mod_services" checked>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold small text-dark"><i class="fas fa-cut text-primary me-1"></i> Services</span>
                                        <span class="badge bg-light text-muted border">{{ $stats['services'] ?? 0 }}</span>
                                    </div>
                                    <div class="x-small text-muted">Categories, pricing, durations &amp; service catalogs</div>
                                </div>
                            </label>
                        </div>

                        <!-- 2. Packages -->
                        <div class="col-md-6 col-lg-3">
                            <label class="module-checkbox-card selected d-flex align-items-start gap-2 w-100" for="mod_packages">
                                <input class="form-check-input mt-1 backup-mod-check" type="checkbox" name="modules[]" value="packages" id="mod_packages" checked>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold small text-dark"><i class="fas fa-gift text-warning me-1"></i> Packages</span>
                                        <span class="badge bg-light text-muted border">{{ $stats['packages'] ?? 0 }}</span>
                                    </div>
                                    <div class="x-small text-muted">Bundles, package services &amp; customer balances</div>
                                </div>
                            </label>
                        </div>

                        <!-- 3. Memberships -->
                        <div class="col-md-6 col-lg-3">
                            <label class="module-checkbox-card selected d-flex align-items-start gap-2 w-100" for="mod_memberships">
                                <input class="form-check-input mt-1 backup-mod-check" type="checkbox" name="modules[]" value="memberships" id="mod_memberships" checked>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold small text-dark"><i class="fas fa-id-card text-success me-1"></i> Memberships</span>
                                        <span class="badge bg-light text-muted border">{{ $stats['memberships'] ?? 0 }}</span>
                                    </div>
                                    <div class="x-small text-muted">VIP tiers, member perks &amp; active subscriptions</div>
                                </div>
                            </label>
                        </div>

                        <!-- 4. Inventory -->
                        <div class="col-md-6 col-lg-3">
                            <label class="module-checkbox-card selected d-flex align-items-start gap-2 w-100" for="mod_inventory">
                                <input class="form-check-input mt-1 backup-mod-check" type="checkbox" name="modules[]" value="inventory" id="mod_inventory" checked>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold small text-dark"><i class="fas fa-boxes text-info me-1"></i> Inventory</span>
                                        <span class="badge bg-light text-muted border">{{ $stats['inventory'] ?? 0 }}</span>
                                    </div>
                                    <div class="x-small text-muted">Products, suppliers, categories &amp; transactions</div>
                                </div>
                            </label>
                        </div>

                        <!-- 5. Customers -->
                        <div class="col-md-6 col-lg-3">
                            <label class="module-checkbox-card selected d-flex align-items-start gap-2 w-100" for="mod_customers">
                                <input class="form-check-input mt-1 backup-mod-check" type="checkbox" name="modules[]" value="customers" id="mod_customers" checked>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold small text-dark"><i class="fas fa-users text-secondary me-1"></i> Customers</span>
                                        <span class="badge bg-light text-muted border">{{ $stats['customers'] ?? 0 }}</span>
                                    </div>
                                    <div class="x-small text-muted">Client contact info, loyalty &amp; favorite services</div>
                                </div>
                            </label>
                        </div>

                        <!-- 6. Bookings -->
                        <div class="col-md-6 col-lg-3">
                            <label class="module-checkbox-card selected d-flex align-items-start gap-2 w-100" for="mod_bookings">
                                <input class="form-check-input mt-1 backup-mod-check" type="checkbox" name="modules[]" value="bookings" id="mod_bookings" checked>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold small text-dark"><i class="fas fa-calendar-alt text-danger me-1"></i> Bookings</span>
                                        <span class="badge bg-light text-muted border">{{ $stats['bookings'] ?? 0 }}</span>
                                    </div>
                                    <div class="x-small text-muted">Appointments, schedules, status &amp; notes</div>
                                </div>
                            </label>
                        </div>

                        <!-- 7. POS Sales -->
                        <div class="col-md-6 col-lg-3">
                            <label class="module-checkbox-card selected d-flex align-items-start gap-2 w-100" for="mod_pos">
                                <input class="form-check-input mt-1 backup-mod-check" type="checkbox" name="modules[]" value="pos" id="mod_pos" checked>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold small text-dark"><i class="fas fa-cash-register text-success me-1"></i> POS Sales</span>
                                        <span class="badge bg-light text-muted border">{{ $stats['pos_sales'] ?? 0 }}</span>
                                    </div>
                                    <div class="x-small text-muted">Sales invoices, items, consumables &amp; refunds</div>
                                </div>
                            </label>
                        </div>

                        <!-- 8. Settings & Profile -->
                        <div class="col-md-6 col-lg-3">
                            <label class="module-checkbox-card selected d-flex align-items-start gap-2 w-100" for="mod_settings">
                                <input class="form-check-input mt-1 backup-mod-check" type="checkbox" name="modules[]" value="profile_settings" id="mod_settings" checked>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold small text-dark"><i class="fas fa-sliders-h text-primary me-1"></i> Settings</span>
                                        <span class="badge bg-light text-muted border">Config</span>
                                    </div>
                                    <div class="x-small text-muted">Salon profile, preferences &amp; email templates</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top">
                        <div class="small text-muted">
                            <i class="fas fa-shield-alt text-success me-1"></i> Checksum SHA-256 verified and scoped exclusively to <strong>{{ $salon->name }}</strong>.
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" onclick="triggerEmailBackup()">
                                <i class="fas fa-envelope me-1"></i> Send Selected to Admin Email
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-download me-1"></i> Download Selected Backup (.json)
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Automated Daily Backups Info Card -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark">
                            <i class="fas fa-clock me-2 text-info"></i> Automated 24h Cloud Backup
                        </span>
                        <span class="badge bg-success-subtle text-success border">Active</span>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small">
                            The platform automatically runs an operational backup routine every night at <strong>02:00 AM</strong>. All salon records are snapshot and dispatched directly to your registered administrator email address.
                        </p>
                        <div class="alert alert-light border rounded-3 p-3 mb-0">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-envelope-check text-primary me-2"></i>
                                <span class="fw-bold small text-dark">Backup Recipient Email:</span>
                            </div>
                            <div class="small text-muted fw-semibold">
                                {{ auth()->user()->email ?? $salon->owner?->email ?? 'Admin Email' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import / Restore Salon Data Card -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark">
                            <i class="fas fa-file-import me-2 text-success"></i> Import &amp; Restore Salon Data
                        </span>
                        <span class="badge bg-warning-subtle text-warning-emphasis border">Admin Only</span>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small">
                            Restore salon data from a previously generated JSON backup file. All entities will be securely synced and linked strictly to <strong>{{ $salon->name }}</strong>.
                        </p>

                        <form action="{{ route('admin.backup.import', ['salon_slug' => $salon->slug]) }}" method="POST" enctype="multipart/form-data" id="importBackupForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Select Salon Backup File (.json)</label>
                                <input type="file" name="backup_file" class="form-control" accept=".json" required>
                                <div class="form-text x-small text-muted">Maximum file size allowed by your plan: {{ $maxBackupSizeMb ? $maxBackupSizeMb . ' MB' : '50 MB' }}. Must be a valid JSON backup generated by this system.</div>
                            </div>

                            <div class="alert alert-warning border-0 rounded-3 p-3 mb-3" style="background-color: #fef3c7; color: #92400e;">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                                    <div class="x-small">
                                        <strong>Safety &amp; Integrity:</strong> Relationships (services, categories, packages, memberships) are remapped to prevent collisions. Operations run inside a database transaction with automatic rollback if any validation fails.
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="return confirm('Are you sure you want to restore salon data from this backup file? Existing records will be synced.');">
                                <i class="fas fa-upload me-1"></i> Upload &amp; Restore Backup
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- History of Local Backups -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <span class="fw-bold text-dark">
                    <i class="fas fa-history me-2 text-primary"></i> Previous Salon Backups
                </span>
                <span class="badge bg-light text-muted border">{{ count($backups) }} Available</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Backup Filename</th>
                            <th>File Size</th>
                            <th>Created Date</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $b)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-file-code text-primary"></i>
                                        <span class="fw-semibold text-dark">{{ $b['filename'] }}</span>
                                    </div>
                                </td>
                                <td class="fw-bold text-muted">{{ $b['size_formatted'] }}</td>
                                <td class="text-muted">{{ $b['created_at'] }}</td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.backup.export', ['salon_slug' => $salon->slug]) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                            <i class="fas fa-download me-1"></i> Download
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">
                                    No local backup snapshots stored yet. Click "Download Selected Backup" above to generate your first snapshot.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function toggleAllBackupModules(select) {
        document.querySelectorAll('.backup-mod-check').forEach(cb => {
            cb.checked = select;
            updateCardStyle(cb);
        });
    }

    function updateCardStyle(checkbox) {
        const card = checkbox.closest('.module-checkbox-card');
        if (!card) return;
        if (checkbox.checked) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    }

    document.querySelectorAll('.backup-mod-check').forEach(cb => {
        cb.addEventListener('change', function() {
            updateCardStyle(this);
        });
    });

    function triggerDownloadBackup() {
        const form = document.getElementById('backupExportForm');
        form.action = "{{ route('admin.backup.export', ['salon_slug' => $salon->slug]) }}";
        form.submit();
    }

    function triggerEmailBackup() {
        const form = document.getElementById('backupExportForm');
        form.action = "{{ route('admin.backup.email', ['salon_slug' => $salon->slug]) }}";
        form.submit();
    }
</script>
@endpush
