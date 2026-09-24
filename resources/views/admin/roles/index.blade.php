@extends('layouts.app')

@section('title', 'Role Management')

@push('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="container-fluid px-4 animate-fade-in" id="roleManagementContainer"
        data-base-url="{{ route($routePrefix . '.index') }}">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Role Management</h1>
                <p class="text-muted mb-0">Manage user roles and their permissions</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                <i class="fas fa-plus me-2"></i>Add New Role
            </button>
        </div>

        <!-- Quick Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stats-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1 text-white-50">Total Roles</h6>
                                <h2 class="mt-2 mb-0">{{ $roles->count() }}</h2>
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-user-shield fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1 text-white-50">Active Roles</h6>
                                <h2 class="mt-2 mb-0">{{ $roles->where('status', true)->count() }}</h2>
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1 text-white-50">Inactive Roles</h6>
                                <h2 class="mt-2 mb-0">{{ $roles->where('status', false)->count() }}</h2>
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-pause-circle fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1 text-white-50">Total Permissions</h6>
                                <h2 class="mt-2 mb-0">{{ $permissions->count() }}</h2>
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-key fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="roleSearch"
                                placeholder="Search roles by name or description...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal"
                            data-bs-target="#roleTemplatesModal">
                            <i class="fas fa-copy me-2"></i>Role Templates
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role List -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Description</th>
                                <th>Permissions</th>
                                <th>Users</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr data-role-id="{{ $role->id }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-wrapper bg-primary bg-opacity-10 text-primary me-3">
                                                <i class="fas fa-user-shield"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $role->name }}</h6>
                                                <small class="text-muted">Created
                                                    {{ $role->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="text-muted">{{ Str::limit($role->description ?? 'No description provided', 50) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($role->permissions->take(3) as $permission)
                                                <span class="permission-badge" data-bs-toggle="tooltip"
                                                    title="{{ $permission->description }}">
                                                    <i class="fas fa-{{ $permission->icon ?? 'check-circle' }}"></i>
                                                    {{ \App\Services\PermissionService::getPermissionLabel($permission->name) }}
                                                </span>
                                            @endforeach
                                            @if($role->permissions->count() > 3)
                                                <span class="permission-badge">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                    +{{ $role->permissions->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $role->users_count }} users
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input role-status-toggle" type="checkbox"
                                                data-role-id="{{ $role->id }}" {{ $role->status ? 'checked' : '' }} {{ ($role->name === 'super_admin' || $role->name === 'salon_admin') ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-action manage-users"
                                                data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}"
                                                data-bs-toggle="modal" data-bs-target="#manageUsersModal" title="Manage Users">
                                                <i class="fas fa-users"></i>
                                            </button>
                                            @if($role->name !== 'super_admin' && $role->name !== 'salon_admin')
                                                <button type="button" class="btn btn-action edit" data-role-id="{{ $role->id }}"
                                                    data-bs-toggle="modal" data-bs-target="#editRoleModal" title="Edit Role">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-action delete" data-role-id="{{ $role->id }}"
                                                    data-bs-toggle="tooltip" title="Delete Role">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form id="addRoleForm" class="modal-content border-0 shadow-lg overflow-hidden" method="POST" action="{{ route($routePrefix . '.store') }}" autocomplete="off"
                novalidate>
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas fa-plus-circle me-2"></i>
                        Create New Role
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-0">
                    @csrf
                        <div class="row g-0">
                            <!-- Left Column - Basic Information -->
                            <div class="col-lg-4 p-4 border-end bg-light">
                                <h6 class="text-uppercase text-muted mb-4 d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>
                                    Role Information
                                </h6>

                                <div class="mb-4">
                                    <label for="roleName" class="form-label fw-semibold mb-2 d-flex align-items-center">
                                        <i class="fas fa-tag text-primary me-2"></i>
                                        Role Name <span class="text-danger ms-1">*</span>
                                    </label>
                                    <div class="input-group input-group-lg mb-2">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-user-tag text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control form-control-lg border-start-0 ps-0"
                                            id="roleName" name="name" required pattern="[a-z0-9_]+"
                                            title="Only lowercase letters, numbers, and underscores are allowed"
                                            placeholder="e.g., salon_manager" style="border-left: none !important;">
                                    </div>
                                    <div class="form-text text-muted small ms-4">Use lowercase letters, numbers, and
                                        underscores only</div>
                                    <div class="invalid-feedback ms-4">Please enter a valid role name</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-2 d-flex align-items-center">
                                        <i class="fas fa-toggle-on text-primary me-2"></i>
                                        Status
                                    </label>
                                    <div class="form-check form-switch form-switch-lg ms-4 mb-2">
                                        <input class="form-check-input" type="checkbox" id="roleStatus" name="status"
                                            value="1" checked role="switch" style="width: 3em; height: 1.5em;">
                                        <label class="form-check-label fw-medium ms-2" for="roleStatus">
                                            <span class="status-text">Active</span>
                                        </label>
                                    </div>
                                    <div class="form-text text-muted small ms-4">Inactive roles cannot be assigned to users
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="roleDescription"
                                        class="form-label fw-semibold mb-2 d-flex align-items-center">
                                        <i class="fas fa-align-left text-primary me-2"></i>
                                        Description
                                    </label>
                                    <div class="input-group input-group-lg mb-2">
                                        <span class="input-group-text bg-white border-end-0 align-items-start pt-3">
                                            <i class="fas fa-pen text-muted"></i>
                                        </span>
                                        <textarea class="form-control border-start-0 ps-0" id="roleDescription"
                                            name="description" rows="4" maxlength="1000"
                                            placeholder="Describe the purpose and responsibilities of this role"
                                            style="border-left: none !important; padding-left: 0;"></textarea>
                                    </div>
                                    <div class="form-text text-muted small ms-4">Provide a brief description (max 1000
                                        characters)</div>
                                </div>

                                <div id="addRoleFormErrors" class="alert alert-danger d-none mt-3"></div>
                            </div>

                            <!-- Right Column - Permissions -->
                            <div class="col-lg-8 p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="text-uppercase text-muted mb-0 d-flex align-items-center">
                                        <i class="fas fa-key me-2 text-primary"></i>
                                        Permissions
                                    </h6>
                                    <div class="d-flex gap-2">
                                        <div class="input-group input-group-sm" style="min-width: 200px;">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0" id="addPermissionSearch"
                                                placeholder="Search permissions...">
                                            <button class="btn btn-outline-secondary border-start-0" type="button"
                                                id="addClearSearch">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="overflow-auto custom-scrollbar" style="max-height: 600px;">
                                    <div class="accordion" id="addPermissionsAccordion">
                                        @foreach($groupedPermissions as $module => $modulePermissions)
                                            <div class="accordion-item border-0 mb-3 shadow-sm rounded overflow-hidden"
                                                data-group="group-{{ Str::slug($module) }}">
                                                <h2 class="accordion-header" id="addHeading{{ Str::slug($module) }}">
                                                    <button
                                                        class="accordion-button collapsed bg-white text-dark fw-medium py-3 px-4"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#addCollapse{{ Str::slug($module) }}"
                                                        aria-expanded="false"
                                                        aria-controls="addCollapse{{ Str::slug($module) }}">
                                                        <div class="d-flex align-items-center w-100">
                                                            <div class="rounded-circle bg-light p-2 me-3 text-primary">
                                                                <i
                                                                    class="fas fa-{{ $moduleMetadata[$module]['icon'] ?? 'cog' }}"></i>
                                                            </div>
                                                            <span
                                                                class="me-auto fs-6">{{ $moduleMetadata[$module]['name'] ?? ucfirst($module) }}</span>
                                                            <span
                                                                class="badge bg-primary bg-opacity-10 text-primary rounded-pill ms-2">{{ count($modulePermissions) }}</span>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="addCollapse{{ Str::slug($module) }}"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="addHeading{{ Str::slug($module) }}">
                                                    <div class="accordion-body p-0">
                                                        <div
                                                            class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                                                            <span class="text-muted small text-uppercase fw-bold">Select
                                                                Permissions</span>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input module-select-all"
                                                                    type="checkbox" id="addModuleSelectAll{{ $module }}"
                                                                    data-module="{{ $module }}">
                                                                <label class="form-check-label small"
                                                                    for="addModuleSelectAll{{ $module }}">Select All</label>
                                                            </div>
                                                        </div>
                                                        <div class="p-4 bg-white">
                                                            <div class="row g-3">
                                                                @foreach($modulePermissions as $permission)
                                                                    <div class="col-md-6">
                                                                        <div
                                                                            class="permission-item p-2 rounded hover-bg-light transition-all {{ isset($permission->is_available) && !$permission->is_available ? 'opacity-50' : '' }}">
                                                                            <div class="form-check custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="form-check-input permission-checkbox"
                                                                                    id="addPermission{{ $permission->id }}"
                                                                                    name="permissions[]"
                                                                                    value="{{ $permission->id }}"
                                                                                    data-module="{{ $module }}"
                                                                                    data-permission-name="{{ $permission->name }}"
                                                                                    data-bs-toggle="tooltip" data-bs-html="true"
                                                                                    title="{{ $permission->description }}{{ isset($permission->is_available) && !$permission->is_available && $permission->requires_feature ? '<br><small class=\'text-warning\'><i class=\'fas fa-lock\'></i> Requires: ' . $permission->requires_feature . '</small>' : '' }}">
                                                                                <label
                                                                                    class="form-check-label d-flex align-items-center cursor-pointer"
                                                                                    for="addPermission{{ $permission->id }}">
                                                                                    <span
                                                                                        class="permission-icon me-2 {{ isset($permission->is_available) && !$permission->is_available ? 'text-warning' : 'text-muted' }}">
                                                                                        <i
                                                                                            class="fas fa-{{ isset($permission->is_available) && !$permission->is_available ? 'lock' : ($permission->icon ?? 'check-circle') }}"></i>
                                                                                    </span>
                                                                                    <span
                                                                                        class="permission-name">{{ \App\Services\PermissionService::getPermissionLabel($permission->name) }}</span>
                                                                                    @if(isset($permission->is_available) && !$permission->is_available)
                                                                                        <span
                                                                                            class="badge bg-warning bg-opacity-10 text-warning ms-2 small">
                                                                                            <i class="fas fa-crown"></i> Upgrade
                                                                                        </span>
                                                                                    @endif
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="addSelectAllPermissions">
                                        <label class="form-check-label fw-medium" for="addSelectAllPermissions">
                                            Select all <span id="addSelectedCount"
                                                class="badge bg-primary rounded-pill ms-1">0</span> /
                                            {{ $permissions->count() }}
                                        </label>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                            id="addExpandAll">
                                            <i class="fas fa-expand-alt me-1"></i> Expand All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="addCollapseAll">
                                            <i class="fas fa-compress-alt me-1"></i> Collapse All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer bg-light border-top p-3">
                        <div class="d-flex justify-content-end gap-2 w-100">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status"
                                    aria-hidden="true"></span>
                                <i class="fas fa-save me-2"></i>Create Role
                            </button>
                        </div>
                    </div>
            </form>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form id="editRoleForm" class="modal-content border-0 shadow-lg overflow-hidden" method="POST"
                action="{{ route($routePrefix . '.update', ['role' => '__ROLE_ID__']) }}" novalidate>
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas fa-user-shield me-2"></i>
                        Edit Role
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-0">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="role_id" value="">
                        <div class="row g-0">
                            <!-- Left Column - Basic Information -->
                            <div class="col-lg-5 p-4 border-end">
                                <h6 class="text-uppercase text-muted mb-4 d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>
                                    Role Information
                                </h6>

                                <div class="mb-4">
                                    <label for="editRoleName" class="form-label fw-semibold mb-2 d-flex align-items-center">
                                        <i class="fas fa-tag text-primary me-2"></i>
                                        Role Name <span class="text-danger ms-1">*</span>
                                    </label>
                                    <div class="input-group input-group-lg mb-2">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-user-tag text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control form-control-lg border-start-0 ps-0"
                                            id="editRoleName" name="name" required pattern="[a-z0-9_]+"
                                            title="Only lowercase letters, numbers, and underscores are allowed"
                                            placeholder="e.g., salon_manager" style="border-left: none !important;">
                                    </div>
                                    <div class="form-text text-muted small ms-4">Use lowercase letters, numbers, and
                                        underscores only</div>
                                    <div class="invalid-feedback ms-4">Please enter a valid role name</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-2 d-flex align-items-center">
                                        <i class="fas fa-toggle-on text-primary me-2"></i>
                                        Status
                                    </label>
                                    <div class="form-check form-switch form-switch-lg ms-4 mb-2">
                                        <input class="form-check-input" type="checkbox" id="editRoleStatus" name="status"
                                            value="1" role="switch" style="width: 3em; height: 1.5em;">
                                        <label class="form-check-label fw-medium ms-2" for="editRoleStatus">
                                            <span class="status-text">Active</span>
                                        </label>
                                    </div>
                                    <div class="form-text text-muted small ms-4">Inactive roles cannot be assigned to users
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="editRoleDescription"
                                        class="form-label fw-semibold mb-2 d-flex align-items-center">
                                        <i class="fas fa-align-left text-primary me-2"></i>
                                        Description
                                    </label>
                                    <div class="input-group input-group-lg mb-2">
                                        <span class="input-group-text bg-white border-end-0 align-items-start pt-3">
                                            <i class="fas fa-pen text-muted"></i>
                                        </span>
                                        <textarea class="form-control border-start-0 ps-0" id="editRoleDescription"
                                            name="description" rows="3" maxlength="1000"
                                            placeholder="Describe the purpose and responsibilities of this role"
                                            style="border-left: none !important; padding-left: 0;"></textarea>
                                    </div>
                                    <div class="form-text text-muted small ms-4">Provide a brief description of this role's
                                        purpose (max 1000 characters)</div>
                                </div>

                                <div class="card border-0 shadow-sm mt-4">
                                    <div class="card-body">
                                        <h6 class="card-title d-flex align-items-center text-muted mb-3">
                                            <i class="fas fa-chart-pie me-2 text-primary"></i>
                                            Role Statistics
                                        </h6>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-soft-primary p-3 me-3">
                                                    <i class="fas fa-users text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="text-muted small">Assigned Users</div>
                                                    <div class="h5 mb-0" id="roleUsersCount">0</div>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-soft-success p-3 me-3">
                                                    <i class="fas fa-key text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="text-muted small">Permissions</div>
                                                    <div class="h5 mb-0" id="rolePermissionsCount">0</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                <span class="small">Role Status</span>
                                            </div>
                                            <span class="badge bg-success" id="roleStatusBadge">
                                                <i class="fas fa-check-circle me-1"></i> Active
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-light border mt-4 role-info-section">
                                    <div class="d-flex">
                                        <i class="fas fa-info-circle me-2 mt-1 text-primary"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Role Information</h6>
                                            <p class="mb-0 small">
                                                <span id="roleUsersCountText">0</span> users have this role assigned.
                                                <a href="#" class="alert-link" data-bs-toggle="modal"
                                                    data-bs-target="#manageUsersModal">Manage users</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div id="editRoleFormErrors" class="alert alert-danger d-none mt-3"></div>
                            </div>

                            <!-- Right Column - Permissions -->
                            <div class="col-lg-7 p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="text-uppercase text-muted mb-0 d-flex align-items-center">
                                        <i class="fas fa-key me-2 text-primary"></i>
                                        Permissions
                                    </h6>
                                    <div class="d-flex gap-2">
                                        <div class="input-group input-group-sm" style="min-width: 200px;">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0" id="permissionSearch"
                                                placeholder="Search permissions...">
                                            <button class="btn btn-outline-secondary border-start-0" type="button"
                                                id="clearSearch">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-filter me-1"></i> Filter
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                                                <li><a class="dropdown-item filter-option active" href="#"
                                                        data-filter="all">All Permissions</a></li>
                                                <li><a class="dropdown-item filter-option" href="#"
                                                        data-filter="selected">Selected Only</a></li>
                                                <li><a class="dropdown-item filter-option" href="#"
                                                        data-filter="unselected">Unselected Only</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                @foreach($permissions->pluck('group')->filter()->unique() as $group)
                                                    <li><a class="dropdown-item filter-option" href="#"
                                                            data-filter="group-{{ Str::slug($group) }}">{{ $group ? ucfirst($group) : 'Ungrouped' }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="overflow-auto" style="max-height: 500px;">
                                    <div class="accordion" id="permissionsAccordion">
                                        @foreach($groupedPermissions as $module => $modulePermissions)
                                            <div class="accordion-item border-0 mb-2"
                                                data-group="group-{{ Str::slug($module) }}">
                                                <h2 class="accordion-header" id="heading{{ Str::slug($module) }}">
                                                    <button class="accordion-button bg-light text-dark fw-medium py-2 px-3"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapse{{ Str::slug($module) }}" aria-expanded="true"
                                                        aria-controls="collapse{{ Str::slug($module) }}">
                                                        <div class="d-flex align-items-center w-100">
                                                            <i
                                                                class="fas fa-{{ $module === 'user' ? 'users' : ($module === 'role' ? 'user-shield' : 'cog') }} text-primary me-2"></i>
                                                            <span class="me-auto">{{ ucfirst($module) }}</span>
                                                            <span
                                                                class="badge bg-primary rounded-pill ms-2">{{ count($modulePermissions) }}</span>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse{{ Str::slug($module) }}"
                                                    class="accordion-collapse collapse show"
                                                    aria-labelledby="heading{{ Str::slug($module) }}"
                                                    data-bs-parent="#permissionsAccordion">
                                                    <div class="accordion-body p-2">
                                                        <div
                                                            class="module-header d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                                                            <h6 class="mb-0 d-flex align-items-center">
                                                                {{ ucfirst($module) }}
                                                            </h6>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input module-select-all"
                                                                    type="checkbox" id="editModuleSelectAll{{ $module }}"
                                                                    data-module="{{ $module }}"
                                                                    style="width: 2.5em; height: 1.25em;">
                                                                <label class="form-check-label"
                                                                    for="editModuleSelectAll{{ $module }}">Select All</label>
                                                            </div>
                                                        </div>
                                                        <div class="row g-3">
                                                            @foreach($modulePermissions as $permission)
                                                                <div class="col-md-6" data-group="group-{{ Str::slug($permission->group ?? 'ungrouped') }}">
                                                                    <div
                                                                        class="permission-item {{ isset($permission->is_available) && !$permission->is_available ? 'opacity-50' : '' }}">
                                                                        <div class="form-check custom-checkbox">
                                                                            <input type="checkbox"
                                                                                class="form-check-input permission-checkbox"
                                                                                id="editPermission{{ $permission->id }}"
                                                                                name="permissions[]" value="{{ $permission->id }}"
                                                                                data-module="{{ $module }}"
                                                                                data-permission-name="{{ $permission->name }}"
                                                                                data-bs-toggle="tooltip" data-bs-html="true"
                                                                                title="{{ $permission->description }}{{ isset($permission->is_available) && !$permission->is_available && $permission->requires_feature ? '<br><small class=\'text-warning\'><i class=\'fas fa-lock\'></i> Requires: ' . $permission->requires_feature . '</small>' : '' }}">
                                                                            <label
                                                                                class="form-check-label d-flex align-items-center"
                                                                                for="editPermission{{ $permission->id }}">
                                                                                <span
                                                                                    class="permission-icon me-2 {{ isset($permission->is_available) && !$permission->is_available ? 'text-warning' : '' }}">
                                                                                    <i
                                                                                        class="fas fa-{{ isset($permission->is_available) && !$permission->is_available ? 'lock' : ($permission->icon ?? 'check-circle') }}"></i>
                                                                                </span>
                                                                                <span
                                                                                    class="permission-name">{{ \App\Services\PermissionService::getPermissionLabel($permission->name) }}</span>
                                                                                @if(isset($permission->is_available) && !$permission->is_available)
                                                                                    <span
                                                                                        class="badge bg-warning bg-opacity-10 text-warning ms-2 small">
                                                                                        <i class="fas fa-crown"></i> Upgrade
                                                                                    </span>
                                                                                @endif
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="text-center py-4 d-none" id="noPermissionsFound">
                                        <div class="text-muted mb-3">
                                            <i class="fas fa-search fa-2x opacity-25 mb-3"></i>
                                            <h5>No permissions found</h5>
                                            <p class="mb-0">Try adjusting your search or filter criteria</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                                        <label class="form-check-label fw-medium" for="selectAllPermissions">
                                            Select all <span id="selectedCount"
                                                class="badge bg-primary rounded-pill ms-1">0</span> /
                                            {{ $permissions->count() }}
                                        </label>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="expandAll">
                                            <i class="fas fa-expand-alt me-1"></i> Expand All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAll">
                                            <i class="fas fa-compress-alt me-1"></i> Collapse All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer bg-light border-top p-3">
                        <div class="d-flex justify-content-between w-100 align-items-center">
                            <div>
                                <button type="button" class="btn btn-link text-danger" id="deleteRoleBtn">
                                    <i class="far fa-trash-alt me-1"></i> Delete Role
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <span class="spinner-border spinner-border-sm d-none me-2" role="status"
                                        aria-hidden="true"></span>
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
    </div>

    <!-- Role Templates Modal -->
    <div class="modal fade" id="roleTemplatesModal" tabindex="-1" aria-labelledby="roleTemplatesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content overflow-hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleTemplatesModalLabel">Role Templates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 template-card" data-template="manager">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-user-tie me-2 text-primary"></i>
                                        Manager
                                    </h5>
                                    <p class="card-text">Full access to all system features with user management
                                        capabilities.</p>
                                    <div class="template-permissions">
                                        <span class="badge bg-light text-dark">User Management</span>
                                        <span class="badge bg-light text-dark">Role Management</span>
                                        <span class="badge bg-light text-dark">All Permissions</span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button type="button" class="btn btn-primary w-100 use-template"
                                        data-template="manager">
                                        Use Template
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 template-card" data-template="staff">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-user me-2 text-white"></i>
                                        Staff
                                    </h5>
                                    <p class="card-text">Standard access to core system features for daily operations.</p>
                                    <div class="template-permissions">
                                        <span class="badge bg-light text-dark">View Reports</span>
                                        <span class="badge bg-light text-dark">Basic Operations</span>
                                        <span class="badge bg-light text-dark">Limited Access</span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button type="button" class="btn btn-primary w-100 use-template" data-template="staff">
                                        Use Template
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 template-card" data-template="pos_staff">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-cash-register me-2 text-info"></i>
                                        POS Staff
                                    </h5>
                                    <p class="card-text">Access to point of sale and basic inventory management.</p>
                                    <div class="template-permissions">
                                        <span class="badge bg-light text-dark">POS Access</span>
                                        <span class="badge bg-light text-dark">Basic Inventory</span>
                                        <span class="badge bg-light text-dark">Sales Reports</span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button type="button" class="btn btn-primary w-100 use-template"
                                        data-template="pos_staff">
                                        Use Template
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 template-card" data-template="inventory_staff">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-boxes me-2 text-warning"></i>
                                        Inventory Staff
                                    </h5>
                                    <p class="card-text">Full access to inventory management and stock control.</p>
                                    <div class="template-permissions">
                                        <span class="badge bg-light text-dark">Inventory Management</span>
                                        <span class="badge bg-light text-dark">Stock Control</span>
                                        <span class="badge bg-light text-dark">Reports</span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button type="button" class="btn btn-primary w-100 use-template"
                                        data-template="inventory_staff">
                                        Use Template
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Users Modal -->
    <div class="modal fade" id="manageUsersModal" tabindex="-1" aria-labelledby="manageUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content overflow-hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageUsersModalLabel">Manage Role Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0">Current Users</h6>
                                    <button type="button" class="btn btn-primary btn-sm" id="addUserToRole">
                                        <i class="fas fa-plus me-2"></i>Add User
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="roleUsersTable">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Status</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Users will be loaded here dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User to Role Modal -->
    <div class="modal fade" id="addUserToRoleModal" tabindex="-1" aria-labelledby="addUserToRoleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content overflow-hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserToRoleModalLabel">Add User to Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="userSearch" class="form-label">Search Users</label>
                        <input type="text" class="form-control" id="userSearch" placeholder="Search by name or email...">
                    </div>
                    <div class="table-responsive" style="max-height: 300px;">
                        <table class="table table-hover" id="availableUsersTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Available users will be loaded here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove User Confirmation Modal -->
    <div class="modal fade" id="removeUserConfirmModal" tabindex="-1" aria-labelledby="removeUserConfirmModalLabel"
        aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content overflow-hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeUserConfirmModalLabel">Confirm User Removal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        tabindex="-1"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove this user from the role?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" tabindex="-1">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemoveUser" tabindex="1">
                        <i class="fas fa-user-minus me-2"></i>Remove User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Role Confirmation Modal -->
    <div class="modal fade" id="deleteRoleConfirmModal" tabindex="-1" aria-labelledby="deleteRoleConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow overflow-hidden">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title" id="deleteRoleConfirmModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirm Role Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete the role "<span id="deleteRoleName"
                            class="fw-bold"></span>"?</p>
                    <p class="text-danger mt-2 mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteRole">
                        <i class="fas fa-trash-alt me-2"></i>Delete Role
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <!-- Role Management JavaScript -->
    @vite(['resources/js/roles.js'])
@endpush

@push('styles')
    <style>
        /* Custom checkbox styling */
        .permission-item .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-top: 0.2em;
            cursor: pointer;
            border: 2px solid #dee2e6;
            transition: all 0.2s ease-in-out;
        }

        .permission-item .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .permission-item .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .permission-item .form-check-label {
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease-in-out;
        }

        .permission-item .form-check-label:hover {
            background-color: #f8f9fa;
        }

        .permission-item .permission-icon {
            width: 24px;
            text-align: center;
            color: #6c757d;
        }

        .permission-item .form-check-input:checked+.form-check-label .permission-icon {
            color: #0d6efd;
        }

        /* Module header styling */
        .module-header {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            transition: all 0.2s ease-in-out;
        }

        .module-header:hover {
            background-color: #e9ecef;
        }

        /* Form floating label styling */
        .form-floating>.form-control,
        .form-floating>.form-select {
            height: calc(3.5rem + 2px);
            line-height: 1.25;
        }

        .form-floating>label {
            padding: 1rem 0.75rem;
        }

        /* Card styling */
        .card {
            transition: all 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        /* Modal styling */
        .modal-content {
            border-radius: 1rem !important;
            border: none !important;
            padding: 0 !important;
            overflow: hidden !important;
        }

        form.modal-content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .modal-header {
            margin: 0 !important;
            border-top-left-radius: 0 !important;
            border-top-right-radius: 0 !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1) !important;
        }

        .modal-footer {
            margin: 0 !important;
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        /* Permission badge styling */
        .permission-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35em 0.65em;
            font-size: 0.875em;
            font-weight: 500;
            line-height: 1;
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
            border-radius: 0.375rem;
            margin: 0.25rem;
        }

        .permission-badge i {
            margin-right: 0.5em;
        }

        /* Form switch styling */
        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
            margin-top: 0.2em;
        }

        .form-switch .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        /* Loading spinner animation */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner-border {
            animation: spin 1s linear infinite;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Utility Classes */
        .hover-bg-light:hover {
            background-color: #f8f9fa !important;
        }

        .transition-all {
            transition: all 0.2s ease-in-out;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* Accordion Styling */
        .accordion-button:not(.collapsed) {
            background-color: #fff;
            color: #0d6efd;
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0, 0, 0, .125);
        }

        .accordion-button::after {
            background-size: 1rem;
            opacity: 0.5;
        }

        .accordion-item {
            border: 1px solid rgba(0, 0, 0, .05);
        }
    </style>
@endpush