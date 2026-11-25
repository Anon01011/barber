@extends('layouts.app')

@section('title', 'Service Categories')

@section('content')
    <div class="container-fluid">


        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Service Categories</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.services.categories.import-template') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-download me-2"></i>Template
                </a>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#importServicesModal">
                    <i class="fas fa-file-import me-2"></i>Import
                </button>
                <a href="{{ route('admin.services.categories.export') }}" class="btn btn-outline-success">
                    <i class="fas fa-file-export me-2"></i>Export
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="fas fa-plus me-2"></i>Add Category
                </button>
            </div>
        </div>

        <div class="row g-4">
            @forelse($categories as $category)
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm hover-shadow" data-category-id="{{ $category->id }}">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">{{ $category->name }}</h5>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge rounded-pill bg-primary-subtle text-primary">
                                            <i class="fas fa-concierge-bell me-1"></i>
                                            {{ $category->services_count ?? 0 }} Services
                                        </span>
                                        <span
                                            class="badge rounded-pill {{ $category->status === 'active' ? 'bg-success-subtle text-white' : 'bg-danger-subtle text-danger' }}">
                                            <i class="fas fa-circle me-1 small"></i>
                                            {{ ucfirst($category->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-link text-success p-0"
                                        onclick="openBulkStaffModal({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                        title="Bulk Staff Assignment">
                                        <i class="fas fa-users-cog"></i>
                                    </button>
                                    <button type="button" class="btn btn-link text-primary p-0"
                                        onclick="editCategory({{ $category->id }})"
                                        title="Edit Category">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-link text-danger p-0"
                                        onclick="deleteCategory({{ $category->id }})"
                                        title="Delete Category">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <p class="card-text text-muted mb-3">{{ $category->description }}</p>

                            @if($category->services->count() > 0)
                                <div class="services-list">
                                    <h6 class="text-muted mb-2 small text-uppercase">Services</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach($category->services as $service)
                                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center service-item"
                                                onclick="viewService({{ $service->id }})" style="cursor: pointer;">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-circle-dot text-primary me-2 small"></i>
                                                    <span>{{ $service->name }}</span>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <span
                                                        class="badge rounded-pill bg-{{ $service->status === 'active' ? 'success' : 'danger' }}-subtle text-{{ $service->status === 'active' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($service->status) }}
                                                    </span>
                                                    <span class="badge rounded-pill bg-info-subtle text-info">
                                                        {{ currency_symbol() }}{{ number_format($service->price, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4 pt-0">
                            <button type="button" class="btn btn-primary w-100"
                                onclick="openAddServicesModal({{ $category->id }}, '{{ $category->name }}')">
                                <i class="fas fa-plus me-2"></i>Add Services
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>No categories found. Click the "Add Category" button to create one.</div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Bulk Staff Assignment Modal -->
    <div class="modal fade" id="bulkStaffModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="bulkStaffForm" onsubmit="submitBulkStaff(event)">
                    @csrf
                    <input type="hidden" id="bulkStaffCategoryId" name="category_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Bulk Staff Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <input type="text" id="bulkStaffCategoryName" class="form-control" readonly style="background-color: #f8fafc;">
                        </div>
                        <div class="mb-3">
                            <label for="bulkStaffSelect" class="form-label fw-semibold">Select Staff Member</label>
                            <select class="form-select" id="bulkStaffSelect" name="staff_id" required>
                                <option value="">-- Choose Staff --</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Choose Action</label>
                            <div class="d-flex gap-4 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="bulkActionAssign" value="assign" checked>
                                    <label class="form-check-label text-success fw-medium" for="bulkActionAssign">
                                        <i class="fas fa-user-plus me-1"></i>Assign to All Services
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="bulkActionRemove" value="remove">
                                    <label class="form-check-label text-danger fw-medium" for="bulkActionRemove">
                                        <i class="fas fa-user-minus me-1"></i>Remove from All Services
                                    </label>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                This will apply the action to all service items currently listed under this category.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="bulkStaffSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                            Apply Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Category Modal -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.services.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name (English)</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_ar" class="form-label">Name (Arabic)</label>
                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                        id="name_ar" name="name_ar">
                                    @error('name_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                name="description" rows="3"></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editCategoryForm" onsubmit="handleCategoryUpdate(event)">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editName" class="form-label">Name (English)</label>
                                    <input type="text" class="form-control" id="editName" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editNameAr" class="form-label">Name (Arabic)</label>
                                    <input type="text" class="form-control" id="editNameAr" name="name_ar">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="editIsActive" name="is_active">
                                <label class="form-check-label" for="editIsActive">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="updateButton">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Service Modal -->
    <div class="modal fade" id="viewServiceModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-3">
                    <div class="d-flex align-items-center">
                        <h5 class="modal-title mb-0 d-flex align-items-center">
                            <i class="fas fa-concierge-bell text-primary me-2"></i>
                            Service Details
                        </h5>
                        <div id="serviceStatus" class="ms-3"></div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-link text-primary p-0" onclick="editService(currentServiceId)"
                            title="Edit Service">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-link text-danger p-0"
                            onclick="confirmDeleteService(currentServiceId)" title="Delete Service">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button type="button" class="btn btn-link text-muted p-0" data-bs-dismiss="modal" title="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase">Service Name</label>
                        <h6 id="serviceName" class="mb-0 text-capitalize fw-semibold"></h6>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase">Description</label>
                        <p id="serviceDescription" class="mb-0 small text-muted"></p>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card bg-light-subtle border-0 hover-card">
                                <div class="card-body p-3">
                                    <label class="form-label text-muted small text-uppercase mb-1">Price</label>
                                    <h6 id="servicePrice" class="mb-0 text-primary fw-semibold"></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-light-subtle border-0 hover-card">
                                <div class="card-body p-3">
                                    <label class="form-label text-muted small text-uppercase mb-1">Duration</label>
                                    <h6 id="serviceDuration" class="mb-0 text-primary fw-semibold"></h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($inventoryEnabled ?? false)
                    <!-- Consumables Recipe BOM & Estimated COGS -->
                    <div id="serviceConsumablesSection" class="mt-3 d-none">
                        <label class="form-label text-muted small text-uppercase mb-1 fw-bold">
                            <i class="fas fa-flask text-primary me-1"></i>Consumables (Service Recipe)
                        </label>
                        <div class="border rounded-2 p-2 bg-light">
                            <ul id="serviceConsumablesList" class="list-unstyled mb-0 small"></ul>
                            <div class="d-flex justify-content-between pt-1 border-top mt-1 fw-semibold text-secondary small">
                                <span>Estimated COGS:</span>
                                <span id="serviceEstCogs" class="text-danger fw-bold">$0.00</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer py-2 d-flex justify-content-between">
                    @if($inventoryEnabled ?? false)
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="fas fa-boxes me-1"></i> Inventory Stock
                    </a>
                    @else
                    <div></div>
                    @endif
                    <button type="button" class="btn btn-light btn-sm rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form class="modal-content" id="editServiceForm" onsubmit="handleServiceUpdate(event)">
                @csrf
                @method('PUT')
                <div class="modal-header">
                        <h5 class="modal-title">Edit Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editServiceName" class="form-label">Name (English)</label>
                                    <input type="text" class="form-control" id="editServiceName" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editServiceNameAr" class="form-label">Name (Arabic)</label>
                                    <input type="text" class="form-control" id="editServiceNameAr" name="name_ar">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editServiceDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editServiceDescription" name="description"
                                rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editServicePrice" class="form-label">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ currency_symbol() }}</span>
                                        <input type="number" class="form-control" id="editServicePrice" name="price"
                                            step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editServiceDuration" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control" id="editServiceDuration" name="duration"
                                        min="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex gap-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="editServiceIsActive"
                                        name="is_active">
                                    <label class="form-check-label" for="editServiceIsActive">Active</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="editServiceOnlineBooking"
                                        name="available_for_online_booking" checked>
                                    <label class="form-check-label" for="editServiceOnlineBooking">Show in Online
                                        Booking</label>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1">When enabled, customers and guests can book this service
                                online</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Assign Staff</label>
                            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                <div class="d-flex justify-content-between mb-2">
                                    <input type="text" class="form-control form-control-sm w-50"
                                        placeholder="Search staff..." onkeyup="filterStaff(this, 'edit_staff_container')">
                                    <div>
                                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none"
                                            onclick="toggleAllStaff('edit_staff_container', true)">Select All</button>
                                        <span class="text-muted mx-1">|</span>
                                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none"
                                            onclick="toggleAllStaff('edit_staff_container', false)">Deselect All</button>
                                    </div>
                                </div>
                                <div id="edit_staff_container">
                                    @foreach($staff as $member)
                                        <div class="form-check staff-item">
                                            <input class="form-check-input" type="checkbox" name="staff_ids[]"
                                                value="{{ $member->id }}" id="edit_staff_{{ $member->id }}">
                                            <label class="form-check-label small" for="edit_staff_{{ $member->id }}">
                                                {{ $member->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @if($inventoryEnabled ?? false)
                        <!-- Consumable Products & Recipe (Bill of Materials) -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <label class="form-label small fw-bold text-dark mb-0">
                                        <i class="fas fa-flask text-primary me-1"></i>Consumable Products (Service Recipe)
                                    </label>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Products automatically dispensed from backbar on checkout</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-sm" onclick="addRecipeRow('edit_recipe_container')">
                                    <i class="fas fa-plus-circle me-1"></i> Add Consumable
                                </button>
                            </div>
                            <div class="border rounded-3 p-2.5 bg-light" style="max-height: 220px; overflow-y: auto;">
                                <div id="edit_recipe_container"></div>
                                <div id="edit_recipe_empty" class="text-center py-3 text-muted small" style="font-size: 0.82rem;">
                                    <i class="fas fa-pump-soap text-muted opacity-50 fa-2x d-block mb-1"></i>
                                    No products linked yet. Click <strong>"+ Add Consumable"</strong> to attach backbar materials (shampoo, blades, hair color, developer).
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="updateServiceButton">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Update Service
                        </button>
                    </div>
                </form>
        </div>
    </div>

    <!-- Delete Service Modal -->
    <div class="modal fade" id="deleteServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this service? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteServiceButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Delete Service
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Services Modal -->
    <div class="modal fade" id="addServicesModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form class="modal-content" id="addServicesForm" onsubmit="handleAddServices(event)">
                @csrf
                <div class="modal-header">
                        <h5 class="modal-title">Add Services to <span id="categoryName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="serviceName" class="form-label">Name (English)</label>
                                    <input type="text" class="form-control" id="serviceName" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="serviceNameAr" class="form-label">Name (Arabic)</label>
                                    <input type="text" class="form-control" id="serviceNameAr" name="name_ar">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="serviceDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="serviceDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="servicePrice" class="form-label">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ currency_symbol() }}</span>
                                        <input type="number" class="form-control" id="servicePrice" name="price" step="0.01"
                                            min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="serviceDuration" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control" id="serviceDuration" name="duration" min="1"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex gap-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="serviceIsActive" name="is_active"
                                        checked>
                                    <label class="form-check-label" for="serviceIsActive">Active</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="serviceOnlineBooking"
                                        name="available_for_online_booking" checked>
                                    <label class="form-check-label" for="serviceOnlineBooking">Show in Online
                                        Booking</label>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1">When enabled, customers and guests can book this service
                                online</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Assign Staff</label>
                            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                <div class="d-flex justify-content-between mb-2">
                                    <input type="text" class="form-control form-control-sm w-50"
                                        placeholder="Search staff..." onkeyup="filterStaff(this, 'create_staff_container')">
                                    <div>
                                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none"
                                            onclick="toggleAllStaff('create_staff_container', true)">Select All</button>
                                        <span class="text-muted mx-1">|</span>
                                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none"
                                            onclick="toggleAllStaff('create_staff_container', false)">Deselect All</button>
                                    </div>
                                </div>
                                <div id="create_staff_container">
                                    @foreach($staff as $member)
                                        <div class="form-check staff-item">
                                            <input class="form-check-input" type="checkbox" name="staff_ids[]"
                                                value="{{ $member->id }}" id="create_staff_{{ $member->id }}">
                                            <label class="form-check-label small" for="create_staff_{{ $member->id }}">
                                                {{ $member->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @if($inventoryEnabled ?? false)
                        <!-- Consumable Products & Recipe (Bill of Materials) -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <label class="form-label small fw-bold text-dark mb-0">
                                        <i class="fas fa-flask text-primary me-1"></i>Consumable Products (Service Recipe)
                                    </label>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Products automatically dispensed from backbar on checkout</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-sm" onclick="addRecipeRow('add_recipe_container')">
                                    <i class="fas fa-plus-circle me-1"></i> Add Consumable
                                </button>
                            </div>
                            <div class="border rounded-3 p-2.5 bg-light" style="max-height: 220px; overflow-y: auto;">
                                <div id="add_recipe_container"></div>
                                <div id="add_recipe_empty" class="text-center py-3 text-muted small" style="font-size: 0.82rem;">
                                    <i class="fas fa-pump-soap text-muted opacity-50 fa-2x d-block mb-1"></i>
                                    No products linked yet. Click <strong>"+ Add Consumable"</strong> to attach backbar materials (shampoo, blades, hair color, developer).
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="addServiceButton">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Add Service
                        </button>
                    </div>
                </form>
        </div>
    </div>

    <!-- Import Services Modal -->
    <div class="modal fade" id="importServicesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="importServicesForm" onsubmit="handleImport(event)" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Services & Categories</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="importFile" class="form-label">Choose CSV File</label>
                            <input type="file" class="form-control" id="importFile" name="file" accept=".csv" required>
                            <div class="form-text">
                                Download the <a href="{{ route('admin.services.categories.import-template') }}">template</a>
                                to ensure correct format.
                            </div>
                        </div>
                        <div id="importResults" class="d-none">
                            <div class="alert alert-info">
                                <div id="importSummary"></div>
                                <ul id="importErrors" class="mt-2 mb-0 small text-danger d-none"></ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="importButton">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Import Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Category Form -->
    <form id="deleteCategoryForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('styles')
        <style>
            .hover-shadow {
                transition: all 0.3s ease;
            }

            .hover-shadow:hover {
                transform: translateY(-5px);
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            }

            .service-item {
                transition: all 0.2s ease;
            }

            .service-item:hover {
                background-color: rgba(13, 110, 253, 0.05);
            }

            .bg-primary-subtle {
                background-color: rgba(13, 110, 253, 0.1);
            }

            .bg-success-subtle {
                background-color: rgba(25, 135, 84, 0.1);
            }

            .bg-danger-subtle {
                background-color: rgba(220, 53, 69, 0.1);
            }

            .bg-info-subtle {
                background-color: rgba(13, 202, 240, 0.1);
            }

            .text-primary {
                color: #0d6efd !important;
            }

            .text-white {
                color: #198754 !important;
            }

            .text-danger {
                color: #dc3545 !important;
            }

            .text-info {
                color: #0dcaf0 !important;
            }

            /* New styles for service details modal */
            .bg-light-subtle {
                background-color: rgba(0, 0, 0, 0.03);
            }

            .modal-content {
                border-radius: 1rem;
            }

            .modal-header {
                border-top-left-radius: 1rem;
                border-top-right-radius: 1rem;
            }

            .modal-footer {
                border-bottom-left-radius: 1rem;
                border-bottom-right-radius: 1rem;
            }

            .card {
                border-radius: 0.75rem;
                transition: all 0.3s ease;
            }

            .card:hover {
                transform: translateY(-2px);
            }

            .text-primary {
                color: #0d6efd !important;
            }

            .btn-link {
                transition: all 0.2s ease;
            }

            .btn-link:hover {
                transform: scale(1.1);
            }

            /* Status badge styles */
            .status-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.5rem 1rem;
                border-radius: 2rem;
                font-weight: 500;
            }

            .status-badge i {
                font-size: 0.75rem;
                margin-right: 0.5rem;
            }

            .status-active {
                background-color: rgba(25, 135, 84, 0.1);
                color: #198754;
            }

            .status-inactive {
                background-color: rgba(220, 53, 69, 0.1);
                color: #dc3545;
            }

            /* Enhanced styles for service details modal */
            .bg-gradient {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            }

            .service-icon-wrapper {
                width: 48px;
                height: 48px;
                background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.5rem;
                box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
            }

            .info-card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
                transition: all 0.3s ease;
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .info-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }

            .info-card-header {
                padding: 1rem 1.25rem;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                display: flex;
                align-items: center;
                gap: 0.75rem;
                color: #6c757d;
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: 600;
            }

            .info-card-header i {
                font-size: 1rem;
                color: #0d6efd;
            }

            .info-card-body {
                padding: 1.25rem;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.75rem 1.25rem;
                border-radius: 12px;
                font-weight: 500;
                font-size: 0.875rem;
                letter-spacing: 0.5px;
            }

            .status-badge i {
                font-size: 0.75rem;
                margin-right: 0.75rem;
            }

            .status-active {
                background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%);
                color: #198754;
            }

            .status-inactive {
                background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
                color: #dc3545;
            }

            .btn-light {
                background: white;
                border: 1px solid rgba(0, 0, 0, 0.1);
                color: #6c757d;
                transition: all 0.2s ease;
            }

            .btn-light:hover {
                background: #f8f9fa;
                color: #0d6efd;
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .modal-content {
                border-radius: 20px;
                overflow: hidden;
            }

            .modal-header {
                padding: 1.5rem;
            }

            .modal-body {
                padding: 1.5rem;
            }

            .modal-footer {
                padding: 1.25rem 1.5rem;
            }

            .text-capitalize {
                text-transform: capitalize;
            }

            .service-category {
                font-size: 0.875rem;
                opacity: 0.8;
            }

            /* Updated modal styles */
            .modal-content {
                border: none;
                border-radius: 0.5rem;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }

            .modal-header {
                background-color: #fff;
                border-bottom: 1px solid #dee2e6;
                padding: 0.75rem 1rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-footer {
                background-color: #fff;
                border-top: 1px solid #dee2e6;
                padding: 0.75rem 1rem;
            }

            .bg-light-subtle {
                background-color: rgba(0, 0, 0, 0.02);
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.75rem;
                border-radius: 1rem;
                font-size: 0.75rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .status-badge i {
                font-size: 0.625rem;
                margin-right: 0.375rem;
            }

            .status-active {
                background-color: rgba(25, 135, 84, 0.1);
                color: #198754;
            }

            .status-inactive {
                background-color: rgba(220, 53, 69, 0.1);
                color: #dc3545;
            }

            .btn-link {
                padding: 0.25rem;
                transition: all 0.2s ease;
            }

            .btn-link:hover {
                transform: scale(1.1);
            }

            .form-label {
                margin-bottom: 0.25rem;
            }

            .small {
                font-size: 0.875rem;
            }

            .text-primary {
                color: #0d6efd !important;
            }

            /* Enhanced modal styles */
            .modal-content {
                border: none;
                border-radius: 0.75rem;
                box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            }

            .modal-header {
                background-color: #fff;
                border-bottom: 1px solid #dee2e6;
                padding: 0.75rem 1rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-footer {
                background-color: #fff;
                border-top: 1px solid #dee2e6;
                padding: 0.75rem 1rem;
            }

            .bg-light-subtle {
                background-color: rgba(0, 0, 0, 0.02);
            }

            .hover-card {
                transition: all 0.2s ease;
            }

            .hover-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.75rem;
                border-radius: 1rem;
                font-size: 0.75rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
            }

            .status-badge i {
                font-size: 0.625rem;
                margin-right: 0.375rem;
            }

            .status-active {
                background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%);
                color: #198754;
            }

            .status-inactive {
                background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
                color: #dc3545;
            }

            .btn-link {
                padding: 0.25rem;
                transition: all 0.2s ease;
                opacity: 0.7;
            }

            .btn-link:hover {
                transform: scale(1.1);
                opacity: 1;
            }

            .form-label {
                margin-bottom: 0.25rem;
                letter-spacing: 0.5px;
            }

            .small {
                font-size: 0.875rem;
            }

            .text-primary {
                color: #0d6efd !important;
            }

            .fw-semibold {
                font-weight: 600 !important;
            }

            .text-muted {
                color: #6c757d !important;
            }
        
            /* Service Modals Scrollability & Sticky Footer Fix */
            #editServiceModal .modal-dialog,
            #addServicesModal .modal-dialog {
                max-height: calc(100vh - 40px);
                margin: 20px auto;
                display: flex;
                flex-direction: column;
            }

            #editServiceModal .modal-content,
            #addServicesModal .modal-content {
                max-height: calc(100vh - 40px);
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }

            #editServiceModal .modal-body,
            #addServicesModal .modal-body {
                overflow-y: auto !important;
                max-height: calc(100vh - 175px);
                flex: 1 1 auto;
                overscroll-behavior: contain;
                -webkit-overflow-scrolling: touch;
            }

            #editServiceModal .modal-footer,
            #addServicesModal .modal-footer {
                position: sticky;
                bottom: 0;
                background: #ffffff;
                z-index: 1050;
                border-top: 1px solid #dee2e6;
                box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05);
                flex-shrink: 0;
            }

            #edit_recipe_container,
            #recipe_container {
                max-height: 260px;
                overflow-y: auto;
                padding-right: 4px;
            }

        </style>
    @endpush

    @push('scripts')
        <script>
            const currencySymbol = '{{ currency_symbol() }}';
            let currentServiceId = null;
            let viewServiceModal = null;
            let editServiceModal = null;
            let deleteServiceModal = null;
            let editCategoryModalInstance = null;
            let addServicesModalInstance = null;

            window.consumableProducts = @json($consumableProducts ?? []);

            function addRecipeRow(containerId, initialData = null) {
                const container = document.getElementById(containerId);
                if (!container) return;

                const products = window.consumableProducts || [];
                if (products.length === 0 && !initialData) {
                    if (window.notifications && typeof window.notifications.show === 'function') {
                        window.notifications.show('No consumable products found. Please add or mark products as "Backbar Consumable" or "Dual-Use" in Inventory first.', 'warning');
                    } else {
                        alert('No consumable products found in your inventory catalogue. Please add consumable products in Inventory first.');
                    }
                    return;
                }

                const emptyMsg = document.getElementById(containerId.replace('_container', '_empty'));
                if (emptyMsg) emptyMsg.style.display = 'none';

                const idx = 'recipe_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

                const row = document.createElement('div');
                row.className = 'd-flex align-items-center gap-2 mb-2 p-2 bg-white rounded-2 border shadow-sm recipe-row';
                row.style.cssText = 'min-width:0;overflow:hidden';
                row.dataset.idx = idx;
                row.dataset.isOptional = initialData ? (!!initialData.is_optional) : false;
                row.dataset.isBillableExtra = initialData ? (!!initialData.is_billable_extra) : false;
                row.dataset.extraPricePerUnit = initialData ? (parseFloat(initialData.extra_price_per_unit) || 0) : 0;

                const selectWrap = document.createElement('div');
                selectWrap.className = 'flex-grow-1';
                selectWrap.style.cssText = 'min-width:0;overflow:hidden';

                const select = document.createElement('select');
                select.className = 'form-select form-select-sm recipe-product-select rounded-2';
                select.style.cssText = 'min-width:0;max-width:100%';
                select.required = true;
                select.setAttribute('onchange', `updateRecipeUnit(this, '${idx}')`);

                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = 'Select consumable product...';
                select.appendChild(defaultOpt);

                let matchedInitial = false;
                products.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    const u = p.base_unit || p.unit_type || 'pcs';
                    opt.dataset.unit = u;
                    opt.textContent = `${p.name} (${u})`;
                    if (initialData && parseInt(initialData.inventory_item_id) === parseInt(p.id)) {
                        opt.selected = true;
                        matchedInitial = true;
                    }
                    select.appendChild(opt);
                });

                if (initialData && !matchedInitial && initialData.inventory_item_id) {
                    const opt = document.createElement('option');
                    opt.value = initialData.inventory_item_id;
                    opt.dataset.unit = initialData.unit || 'pcs';
                    opt.textContent = `${initialData.item_name || 'Item'} (${initialData.unit || 'pcs'})`;
                    opt.selected = true;
                    select.appendChild(opt);
                }

                selectWrap.appendChild(select);
                row.appendChild(selectWrap);

                const qtyWrap = document.createElement('div');
                qtyWrap.className = 'input-group input-group-sm flex-shrink-0';
                qtyWrap.style.width = '110px';

                const qtyInput = document.createElement('input');
                qtyInput.type = 'number';
                qtyInput.className = 'form-control form-control-sm recipe-qty-input';
                qtyInput.placeholder = 'Qty';
                qtyInput.step = 'any';
                qtyInput.min = '0.001';
                qtyInput.required = true;
                if (initialData && initialData.quantity) {
                    qtyInput.value = initialData.quantity;
                }
                qtyWrap.appendChild(qtyInput);

                const unitLabel = document.createElement('span');
                unitLabel.className = 'input-group-text bg-light px-2 small recipe-unit-label';
                unitLabel.id = 'unit_label_' + idx;
                unitLabel.textContent = (initialData && initialData.unit) ? initialData.unit : 'pcs';
                qtyWrap.appendChild(unitLabel);
                row.appendChild(qtyWrap);

                const unitInput = document.createElement('input');
                unitInput.type = 'hidden';
                unitInput.className = 'recipe-unit-input';
                unitInput.id = 'unit_val_' + idx;
                unitInput.value = (initialData && initialData.unit) ? initialData.unit : 'pcs';
                row.appendChild(unitInput);

                // Billable Extra Toggle & Automatic Price Display
                const isBillable = initialData ? (!!initialData.is_billable_extra) : false;

                const billableWrap = document.createElement('div');
                billableWrap.className = 'd-flex align-items-center gap-1 flex-shrink-0';

                const checkWrap = document.createElement('div');
                checkWrap.className = 'form-check form-switch m-0 d-flex align-items-center';
                checkWrap.title = 'Charge this consumable to customer in POS';

                const billableCheck = document.createElement('input');
                billableCheck.type = 'checkbox';
                billableCheck.className = 'form-check-input recipe-billable-check';
                billableCheck.id = 'billable_' + idx;
                billableCheck.checked = isBillable;

                const billableLabel = document.createElement('label');
                billableLabel.className = 'form-check-label small ms-1 text-muted fw-semibold';
                billableLabel.setAttribute('for', 'billable_' + idx);
                billableLabel.style.fontSize = '0.72rem';
                billableLabel.textContent = 'Charge';

                checkWrap.appendChild(billableCheck);
                checkWrap.appendChild(billableLabel);
                billableWrap.appendChild(checkWrap);

                const priceWrap = document.createElement('div');
                priceWrap.className = 'input-group input-group-sm recipe-extra-price-wrap';
                priceWrap.style.width = '115px';
                priceWrap.style.display = isBillable ? 'flex' : 'none';

                const priceAddon = document.createElement('span');
                priceAddon.className = 'input-group-text bg-light px-1 text-muted small';
                priceAddon.style.fontSize = '0.70rem';
                priceAddon.textContent = '+{{ currency_symbol() }}';

                const priceInput = document.createElement('input');
                priceInput.type = 'number';
                priceInput.className = 'form-control form-control-sm recipe-extra-price-input';
                priceInput.placeholder = '0.00';
                priceInput.step = '0.01';
                priceInput.min = '0';
                priceInput.readOnly = true;
                priceInput.style.backgroundColor = '#f8fafc';
                priceInput.style.fontWeight = '600';
                priceInput.style.cursor = 'default';
                priceInput.title = 'Auto-calculated price for used quantity';

                priceWrap.appendChild(priceAddon);
                priceWrap.appendChild(priceInput);
                billableWrap.appendChild(priceWrap);

                function autoCalcRecipePrice() {
                    const selectedVal = select.value;
                    const prod = products.find(p => parseInt(p.id) === parseInt(selectedVal));
                    if (!prod) {
                        priceInput.value = '0.00';
                        return;
                    }
                    const capacity = parseFloat(prod.pack_capacity) > 0 ? parseFloat(prod.pack_capacity) : 1;
                    const basePrice = (parseFloat(prod.selling_price) > 0) 
                        ? parseFloat(prod.selling_price) 
                        : (parseFloat(prod.purchase_price) || 0);
                    const qty = parseFloat(qtyInput.value) > 0 ? parseFloat(qtyInput.value) : 1;
                    const unitPrice = basePrice / capacity;
                    const calculated = unitPrice * qty;
                    priceInput.value = calculated > 0 ? calculated.toFixed(2) : '0.00';
                    priceInput.title = `${qty} ${prod.base_unit || 'units'} @ {{ currency_symbol() }}${unitPrice.toFixed(4)} per unit (Pack: {{ currency_symbol() }}${basePrice.toFixed(2)} / ${capacity} ${prod.base_unit || ''})`;
                }

                // Initial price population
                if (isBillable) {
                    autoCalcRecipePrice();
                }

                billableCheck.addEventListener('change', function () {
                    priceWrap.style.display = this.checked ? 'flex' : 'none';
                    if (this.checked) {
                        autoCalcRecipePrice();
                    }
                });

                qtyInput.addEventListener('input', function () {
                    if (billableCheck.checked) {
                        autoCalcRecipePrice();
                    }
                });
                qtyInput.addEventListener('change', function () {
                    if (billableCheck.checked) {
                        autoCalcRecipePrice();
                    }
                });

                select.addEventListener('change', function () {
                    if (billableCheck.checked) {
                        autoCalcRecipePrice();
                    }
                });

                row.appendChild(billableWrap);

                const delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.className = 'btn btn-sm btn-outline-danger rounded-circle p-1 d-flex align-items-center justify-content-center';
                delBtn.style.width = '28px';
                delBtn.style.height = '28px';
                delBtn.title = 'Remove Consumable';
                delBtn.setAttribute('onclick', `removeRecipeRow(this, '${containerId}')`);
                delBtn.innerHTML = '<i class="fas fa-times fa-xs"></i>';
                row.appendChild(delBtn);

                container.appendChild(row);
            }

            function updateRecipeUnit(select, idx) {
                const opt = select.selectedOptions[0];
                const unit = opt?.dataset?.unit || 'pcs';
                const label = document.getElementById('unit_label_' + idx);
                const input = document.getElementById('unit_val_' + idx);
                if (label) label.textContent = unit;
                if (input) input.value = unit;
            }

            function removeRecipeRow(btn, containerId) {
                const row = btn.closest('.recipe-row');
                if (row) row.remove();
                const container = document.getElementById(containerId);
                if (container && container.querySelectorAll('.recipe-row').length === 0) {
                    const emptyMsg = document.getElementById(containerId.replace('_container', '_empty'));
                    if (emptyMsg) emptyMsg.style.display = '';
                }
            }

            function getConsumablesFromContainer(containerId) {
                const container = document.getElementById(containerId);
                if (!container) return [];
                const rows = container.querySelectorAll('.recipe-row');
                const items = [];
                rows.forEach(row => {
                    const productSelect = row.querySelector('.recipe-product-select');
                    const qtyInput = row.querySelector('.recipe-qty-input');
                    const unitInput = row.querySelector('.recipe-unit-input');
                    const billableCheck = row.querySelector('.recipe-billable-check');
                    const extraPriceInput = row.querySelector('.recipe-extra-price-input');

                    const itemId = productSelect ? productSelect.value : null;
                    const qty = qtyInput ? parseFloat(qtyInput.value) : 0;
                    const unit = unitInput ? unitInput.value : 'pcs';
                    const isBillable = billableCheck ? billableCheck.checked : false;
                    const extraPrice = (isBillable && extraPriceInput && extraPriceInput.value) ? (parseFloat(extraPriceInput.value) || 0) : 0;
                    const perUnitPrice = qty > 0 ? (extraPrice / qty) : extraPrice;

                    if (itemId && qty > 0) {
                        items.push({
                            inventory_item_id: parseInt(itemId),
                            quantity: qty,
                            unit: unit,
                            is_optional: false,
                            is_billable_extra: isBillable,
                            extra_price_per_unit: perUnitPrice
                        });
                    }
                });
                return items;
            }

            const salonSlug = getSalonSlug();
                    const baseUrl = salonSlug ? `/${salonSlug}/admin` : '/admin';

                    // Validate baseUrl to prevent malformed URLs
                    if (!salonSlug) {
                        console.error('Salon slug is missing! URLs may not work correctly.');
                    }

                    // Initialize modals when the DOM is loaded
                    document.addEventListener('DOMContentLoaded', function () {
                        try {
                            const viewModalElement = document.getElementById('viewServiceModal');
                            const editModalElement = document.getElementById('editServiceModal');
                            const deleteModalElement = document.getElementById('deleteServiceModal');

                            if (viewModalElement) {
                                viewServiceModal = new bootstrap.Modal(viewModalElement);
                            }
                            if (editModalElement) {
                                editServiceModal = new bootstrap.Modal(editModalElement);
                            }
                            if (deleteModalElement) {
                                deleteServiceModal = new bootstrap.Modal(deleteModalElement);
                            }
                            const editCategoryModalElement = document.getElementById('editCategoryModal');
                            if (editCategoryModalElement) {
                                editCategoryModalInstance = new bootstrap.Modal(editCategoryModalElement);
                            }
                            const addServicesModalElement = document.getElementById('addServicesModal');
                            if (addServicesModalElement) {
                                addServicesModalInstance = new bootstrap.Modal(addServicesModalElement);
                            }
                        } catch (error) {
                            console.error('Error initializing modals:', error);
                        }
                    });

                    function showModal(modal) {
                        if (!modal) {
                            console.error('Modal instance is not initialized');
                            return;
                        }
                        try {
                            modal.show();
                        } catch (error) {
                            console.error('Error showing modal:', error);
                        }
                    }

                    function hideModal(modal) {
                        if (!modal) {
                            console.error('Modal instance is not initialized');
                            return;
                        }
                        try {
                            modal.hide();
                        } catch (error) {
                            console.error('Error hiding modal:', error);
                        }
                    }

                    function viewService(serviceId) {
                        currentServiceId = serviceId;
                        fetch(`${baseUrl}/services/${serviceId}`)
                            .then(response => response.json())
                            .then(data => {
                                // Update service name with proper capitalization
                                const serviceName = document.getElementById('serviceName');
                                serviceName.textContent = data.name.toLowerCase().split(' ').map(word =>
                                    word.charAt(0).toUpperCase() + word.slice(1)
                                ).join(' ');

                                // Update description
                                const description = document.getElementById('serviceDescription');
                                description.textContent = data.description || 'No description available';

                                // Update price
                                const price = document.getElementById('servicePrice');
                                price.textContent = `${currencySymbol}${parseFloat(data.price).toFixed(2)}`;

                                // Update duration
                                const duration = document.getElementById('serviceDuration');
                                duration.textContent = `${data.duration} minutes`;

                                // Update status
                                const statusElement = document.getElementById('serviceStatus');
                                const isActive = data.status === 'active';
                                statusElement.innerHTML = `
                                                                                                                                        <div class="status-badge ${isActive ? 'status-active' : 'status-inactive'}">
                                                                                                                                            <i class="fas fa-circle"></i>
                                                                                                                                            ${data.status}
                                                                                                                                        </div>
                                                                                                                            `;

                                // Update Consumables & COGS in view modal
                                const consumablesSection = document.getElementById('serviceConsumablesSection');
                                const consumablesList = document.getElementById('serviceConsumablesList');
                                const estCogs = document.getElementById('serviceEstCogs');
                                if (consumablesSection && consumablesList) {
                                    if (data.consumables && data.consumables.length > 0) {
                                        consumablesSection.classList.remove('d-none');
                                        consumablesList.innerHTML = data.consumables.map(c => {
                                            const cost = parseFloat(c.total_cost || 0);
                                            const costPerUnit = parseFloat(c.cost_per_unit || 0);
                                            const noCostSet = costPerUnit <= 0;
                                            const costDisplay = noCostSet
                                                ? `<span class="text-warning small text-nowrap" title="Set purchase price in Inventory to calculate cost"><i class="fas fa-exclamation-triangle me-1"></i>Cost not set</span>`
                                                : `<span class="fw-semibold text-muted text-nowrap">${currencySymbol}${cost.toFixed(2)}</span>`;
                                            return `<li class="d-flex align-items-start gap-2 py-1 border-bottom border-light" style="min-width:0">
                                                <i class="fas fa-check text-success mt-1 flex-shrink-0 small"></i>
                                                <span class="flex-grow-1" style="min-width:0;overflow:hidden">
                                                    <span class="d-block text-truncate small">${c.item_name}</span>
                                                    <span class="badge bg-secondary-subtle text-secondary">${c.quantity} ${c.unit}</span>
                                                    ${c.is_billable_extra ? `<span class="badge bg-success-subtle text-success border border-success-subtle ms-1">+${currencySymbol}${(parseFloat(c.extra_price_per_unit || 0) * parseFloat(c.quantity || 1)).toFixed(2)} Billable</span>` : ''}
                                                </span>
                                                <span class="flex-shrink-0 ms-auto">${costDisplay}</span>
                                            </li>`;
                                        }).join('');
                                        const totalCogs = parseFloat(data.estimated_cogs || 0);
                                        if (estCogs) {
                                            estCogs.textContent = `${currencySymbol}${totalCogs.toFixed(2)}`;
                                            estCogs.className = totalCogs > 0 ? 'text-danger fw-bold' : 'text-warning fw-bold';
                                        }
                                    } else {
                                        consumablesSection.classList.add('d-none');
                                    }
                                }

                                showModal(viewServiceModal);
                            })
                            .catch(error => {
                                console.error('Error loading service:', error);
                                window.notifications.show('Failed to load service details', 'error');
                            });
                    }

                    function editService(serviceId) {
                        fetch(`${baseUrl}/services/${serviceId}`)
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById('editServiceName').value = data.name;
                                document.getElementById('editServiceNameAr').value = data.name_ar || '';
                                document.getElementById('editServiceDescription').value = data.description || '';
                                document.getElementById('editServicePrice').value = data.price;
                                document.getElementById('editServiceDuration').value = data.duration;
                                document.getElementById('editServiceIsActive').checked = data.status === 'active';
                                document.getElementById('editServiceOnlineBooking').checked = data.available_for_online_booking;

                                // Handle staff checkboxes
                                const staffIds = data.staff_ids || [];
                                document.querySelectorAll('#edit_staff_container input[type="checkbox"]').forEach(cb => {
                                    cb.checked = staffIds.includes(parseInt(cb.value));
                                });

                                // Populate Recipe Consumables
                                const editContainer = document.getElementById('edit_recipe_container');
                                const editEmpty = document.getElementById('edit_recipe_empty');
                                if (editContainer) editContainer.innerHTML = '';
                                if (data.consumables && data.consumables.length > 0) {
                                    if (editEmpty) editEmpty.style.display = 'none';
                                    data.consumables.forEach(c => {
                                        addRecipeRow('edit_recipe_container', c);
                                    });
                                } else {
                                    if (editEmpty) editEmpty.style.display = '';
                                }

                                document.getElementById('editServiceForm').action = `${baseUrl}/services/${serviceId}`;

                                hideModal(viewServiceModal);
                                showModal(editServiceModal);
                            })
                            .catch(error => {
                                console.error('Error loading service data:', error);
                                window.notifications.show('Failed to load service data', 'error');
                            });
                    }

                    function handleServiceUpdate(event) {
                        event.preventDefault();

                        const form = event.target;
                        const submitButton = document.getElementById('updateServiceButton');
                        const spinner = submitButton.querySelector('.spinner-border');

                        // Show loading state
                        submitButton.disabled = true;
                        spinner.classList.remove('d-none');

                        const formData = new FormData(form);
                        const data = {
                            name: formData.get('name'),
                            name_ar: formData.get('name_ar'),
                            description: formData.get('description'),
                            price: parseFloat(formData.get('price')),
                            duration: parseInt(formData.get('duration')),
                            status: formData.get('is_active') === 'on' ? 'active' : 'inactive',
                            available_for_online_booking: formData.get('available_for_online_booking') === 'on',
                            staff_ids: Array.from(form.querySelectorAll('input[name="staff_ids[]"]:checked')).map(cb => cb.value),
                            consumables: getConsumablesFromContainer('edit_recipe_container')
                        };

                        fetch(form.action, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    window.notifications.show('Service updated successfully', 'success');
                                    hideModal(editServiceModal);
                                    setTimeout(() => window.location.reload(), 1000);
                                } else {
                                    window.notifications.show(data.message || 'Failed to update service', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error updating service:', error);
                                window.notifications.show('Failed to update service', 'error');
                            })
                            .finally(() => {
                                // Reset button state
                                submitButton.disabled = false;
                                spinner.classList.add('d-none');
                            });
                    }

                    function confirmDeleteService(serviceId) {
                        currentServiceId = serviceId;
                        hideModal(viewServiceModal);
                        showModal(deleteServiceModal);
                    }

                    document.getElementById('confirmDeleteServiceButton').addEventListener('click', function () {
                        const button = this;
                        const spinner = button.querySelector('.spinner-border');

                        // Show loading state
                        button.disabled = true;
                        spinner.classList.remove('d-none');

                        fetch(`${baseUrl}/services/${currentServiceId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    window.notifications.show('Service deleted successfully', 'success');
                                    hideModal(deleteServiceModal);
                                    setTimeout(() => window.location.reload(), 1000);
                                } else {
                                    window.notifications.show(data.message || 'Failed to delete service', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error deleting service:', error);
                                window.notifications.show('Failed to delete service', 'error');
                            })
                            .finally(() => {
                                // Reset button state
                                button.disabled = false;
                                spinner.classList.add('d-none');
                            });
                    });

                    function editCategory(id) {
                        fetch(`${baseUrl}/services/categories/${id}/edit`)
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById('editName').value = data.name;
                                document.getElementById('editNameAr').value = data.name_ar || '';
                                document.getElementById('editDescription').value = data.description;
                                document.getElementById('editIsActive').checked = data.status === 'active';
                                document.getElementById('editCategoryForm').action = `${baseUrl}/services/categories/${id}`;

                                // Show the modal using the initialized instance
                                if (editCategoryModalInstance) {
                                    editCategoryModalInstance.show();
                                } else {
                                    console.error('Edit Category Modal not initialized');
                                }
                            })
                            .catch(error => {
                                window.notifications.show('Failed to load category data', 'error');
                            });
                    }

                    function handleCategoryUpdate(event) {
                        event.preventDefault();

                        const form = event.target;
                        const submitButton = document.getElementById('updateButton');
                        const spinner = submitButton.querySelector('.spinner-border');

                        // Show loading state
                        submitButton.disabled = true;
                        spinner.classList.remove('d-none');

                        const formData = new FormData(form);
                        const data = {
                            name: formData.get('name'),
                            name_ar: formData.get('name_ar'),
                            description: formData.get('description'),
                            status: formData.get('is_active') === 'on' ? 'active' : 'inactive'
                        };

                        // Extract category ID from the form action URL
                        const categoryId = form.action.split('/').pop();

                        fetch(`${baseUrl}/services/categories/${categoryId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(data => {
                                        throw new Error(data.message || 'Failed to update category');
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    window.notifications.show('Category updated successfully', 'success');

                                    // Close the modal
                                    if (editCategoryModalInstance) {
                                        editCategoryModalInstance.hide();
                                    }

                                    // Find the category card
                                    const categoryCard = document.querySelector(`.card[data-category-id="${categoryId}"]`);
                                    if (categoryCard) {
                                        // Update the category name
                                        const nameElement = categoryCard.querySelector('.card-title');
                                        if (nameElement) {
                                            nameElement.textContent = data.category.name;
                                        }

                                        // Update the description
                                        const descriptionElement = categoryCard.querySelector('.card-text');
                                        if (descriptionElement) {
                                            descriptionElement.textContent = data.category.description || '';
                                        }

                                        // Update the status badge
                                        const statusBadge = categoryCard.querySelector('.badge:last-child');
                                        if (statusBadge) {
                                            const isActive = data.category.status === 'active';
                                            statusBadge.className = `badge rounded-pill ${isActive ? 'bg-success-subtle text-white' : 'bg-danger-subtle text-danger'}`;
                                            statusBadge.innerHTML = `<i class="fas fa-circle me-1 small"></i>${data.category.status.charAt(0).toUpperCase() + data.category.status.slice(1)}`;
                                        }
                                    }
                                } else {
                                    window.notifications.show(data.message || 'Failed to update category', 'error');
                                }
                            })
                            .catch(error => {
                                window.notifications.show(error.message || 'Failed to update category', 'error');
                            })
                            .finally(() => {
                                // Reset button state
                                submitButton.disabled = false;
                                spinner.classList.add('d-none');
                            });
                    }

                    function deleteCategory(id) {
                        if (confirm('Are you sure you want to delete this category?')) {
                            const form = document.getElementById('deleteCategoryForm');
                            form.action = `${baseUrl}/services/categories/${id}`;

                            // Submit the form
                            form.submit();
                        }
                    }

                    // Add event listener for form submission
                    document.getElementById('deleteCategoryForm').addEventListener('submit', function (e) {
                        e.preventDefault();

                        const form = this;
                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Failed to delete category');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    window.notifications.show('Category deleted successfully', 'success');
                                    // Remove the category card from the DOM
                                    const categoryId = form.action.split('/').pop();
                                    const categoryCard = document.querySelector(`.card[data-category-id="${categoryId}"]`);
                                    if (categoryCard) {
                                        categoryCard.remove();
                                    }
                                } else {
                                    window.notifications.show(data.message || 'Failed to delete category', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Delete error:', error);
                                window.notifications.show('Failed to delete category. Please try again.', 'error');
                            });
                    });

                    let currentCategoryId = null;

                    function openAddServicesModal(categoryId, categoryName) {
                        currentCategoryId = categoryId;
                        document.getElementById('categoryName').textContent = categoryName;
                        document.getElementById('addServicesForm').reset();

                        // Clear recipe container
                        const addContainer = document.getElementById('add_recipe_container');
                        const addEmpty = document.getElementById('add_recipe_empty');
                        if (addContainer) addContainer.innerHTML = '';
                        if (addEmpty) addEmpty.style.display = '';

                        if (addServicesModalInstance) {
                            addServicesModalInstance.show();
                        } else {
                            console.error('Add Services Modal not initialized');
                        }
                    }

                    function handleAddServices(event) {
                        event.preventDefault();

                        const form = event.target;
                        const submitButton = document.getElementById('addServiceButton');
                        const spinner = submitButton.querySelector('.spinner-border');

                        // Show loading state
                        submitButton.disabled = true;
                        spinner.classList.remove('d-none');

                        const formData = new FormData(form);
                        const data = {
                            name: formData.get('name'),
                            name_ar: formData.get('name_ar'),
                            description: formData.get('description'),
                            price: parseFloat(formData.get('price')),
                            duration: parseInt(formData.get('duration')),
                            is_active: formData.get('is_active') === 'on',
                            available_for_online_booking: formData.get('available_for_online_booking') === 'on',
                            staff_ids: Array.from(form.querySelectorAll('input[name="staff_ids[]"]:checked')).map(cb => cb.value),
                            consumables: getConsumablesFromContainer('add_recipe_container')
                        };

                        fetch(`${baseUrl}/services/categories/${currentCategoryId}/services`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(data => {
                                        throw new Error(data.message || 'Failed to add service');
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    window.notifications.show('Service added successfully', 'success');
                                    setTimeout(() => window.location.reload(), 1000);
                                } else {
                                    window.notifications.show(data.message || 'Failed to add service', 'error');
                                }
                            })
                            .catch(error => {
                                window.notifications.show(error.message || 'Failed to add service', 'error');
                            })
                            .finally(() => {
                                // Reset button state
                                submitButton.disabled = false;
                                spinner.classList.add('d-none');
                            });
                    }

                    function filterStaff(input, containerId) {
                        const filter = input.value.toLowerCase();
                        const container = document.getElementById(containerId);
                        const items = container.getElementsByClassName('staff-item');

                        for (let i = 0; i < items.length; i++) {
                            const label = items[i].getElementsByTagName('label')[0];
                            if (label.innerText.toLowerCase().indexOf(filter) > -1) {
                                items[i].style.display = "";
                            } else {
                                items[i].style.display = "none";
                            }
                        }
                    }

                    function toggleAllStaff(containerId, checked) {
                        const container = document.getElementById(containerId);
                        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
                        checkboxes.forEach(cb => {
                            if (cb.closest('.staff-item').style.display !== 'none') {
                                cb.checked = checked;
                            }
                        });
                    }

                    function handleImport(event) {
                        event.preventDefault();
                        const form = event.target;
                        const submitButton = document.getElementById('importButton');
                        const spinner = submitButton.querySelector('.spinner-border');
                        const resultsDiv = document.getElementById('importResults');
                        const summaryDiv = document.getElementById('importSummary');
                        const errorsList = document.getElementById('importErrors');

                        submitButton.disabled = true;
                        spinner.classList.remove('d-none');
                        resultsDiv.classList.add('d-none');
                        errorsList.classList.add('d-none');
                        errorsList.innerHTML = '';

                        const formData = new FormData(form);

                        fetch(`{{ route('admin.services.categories.import') }}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                resultsDiv.classList.remove('d-none');
                                if (data.success) {
                                    summaryDiv.innerHTML = `<div class="text-success fw-bold">${data.message}</div>`;
                                    window.notifications.show(data.message, 'success');
                                    setTimeout(() => window.location.reload(), 2000);
                                } else {
                                    summaryDiv.innerHTML = `<div class="text-danger fw-bold">${data.message}</div>`;
                                    if (data.results && data.results.errors && data.results.errors.length > 0) {
                                        errorsList.classList.remove('d-none');
                                        data.results.errors.forEach(err => {
                                            const li = document.createElement('li');
                                            li.textContent = `Row ${err.row}: ${err.message}`;
                                            errorsList.appendChild(li);
                                        });
                                    }
                                }
                            })
                            .catch(error => {
                                resultsDiv.classList.remove('d-none');
                                summaryDiv.innerHTML = `<div class="text-danger fw-bold">An error occurred during import.</div>`;
                                window.notifications.show('An error occurred during import.', 'error');
                            })
                            .finally(() => {
                                submitButton.disabled = false;
                                spinner.classList.add('d-none');
                            });
                    }

                    function openBulkStaffModal(categoryId, categoryName) {
                        document.getElementById('bulkStaffCategoryId').value = categoryId;
                        document.getElementById('bulkStaffCategoryName').value = categoryName;
                        document.getElementById('bulkStaffSelect').value = '';
                        document.getElementById('bulkActionAssign').checked = true;
                        
                        const modal = new bootstrap.Modal(document.getElementById('bulkStaffModal'));
                        modal.show();
                    }

                    function submitBulkStaff(event) {
                        event.preventDefault();
                        const form = event.target;
                        const categoryId = document.getElementById('bulkStaffCategoryId').value;
                        const submitButton = document.getElementById('bulkStaffSubmitBtn');
                        const spinner = submitButton.querySelector('.spinner-border');

                        submitButton.disabled = true;
                        spinner.classList.remove('d-none');

                        const formData = new FormData(form);

                        @if(auth()->user()->salon)
                            const salonSlug = '{{ auth()->user()?->salon?->slug }}';
                        @else
                            const pathParts = window.location.pathname.split('/');
                            const salonSlug = pathParts[1] || 'admin';
                        @endif

                        const url = `/${salonSlug}/admin/services/categories/${categoryId}/bulk-staff`;

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.message || 'Failed to update assignments');
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                window.notifications.show(data.message, 'success');
                                setTimeout(() => window.location.reload(), 1200);
                            } else {
                                window.notifications.show(data.message || 'Failed to update assignments', 'error');
                            }
                        })
                        .catch(error => {
                            window.notifications.show(error.message || 'An error occurred', 'error');
                        })
                        .finally(() => {
                            submitButton.disabled = false;
                            spinner.classList.add('d-none');
                            const modal = bootstrap.Modal.getInstance(document.getElementById('bulkStaffModal'));
                            if (modal) modal.hide();
                        });
                    }
                </script>
    @endpush
@endsection