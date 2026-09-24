@extends('layouts.app')

@section('title', 'Services')

@section('content')
    <div class="container-fluid px-4 py-5">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Services</h2>
                <p class="text-muted">Manage salon services and pricing</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                <i class="fas fa-plus me-2"></i>New Service
            </button>
        </div>

        <!-- Main Content -->
        <div class="row g-4">
            <!-- Services List -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-primary">Service List</h5>
                            <div class="d-flex gap-2">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" placeholder="Search services...">
                                </div>
                                <select class="form-select" style="width: auto;">
                                    <option value="">All Categories</option>
                                    <option value="hair">Hair</option>
                                    <option value="skin">Skin</option>
                                    <option value="nails">Nails</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0">Name</th>
                                        <th class="border-0">Category</th>
                                        <th class="border-0">Price</th>
                                        <th class="border-0">Duration</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services as $service)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-cut text-primary me-2"></i>
                                                    <span>{{ $service->name }}</span>
                                                </div>
                                            </td>
                                            <td><span
                                                    class="badge bg-light text-dark">{{ $service->category->name ?? 'Uncategorized' }}</span>
                                            </td>
                                            <td>{{ currency_symbol() }}{{ number_format($service->price, 2) }}</td>
                                            <td>{{ $service->duration }} min</td>
                                            <td>
                                                @if($service->status == 'active')
                                                    <span class="badge bg-success-subtle text-white">Active</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-light me-1"
                                                    onclick="editService({{ $service->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light text-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Service Modal -->
    @component('components.modal', ['id' => 'addServiceModal', 'title' => 'Add Service'])
    <form action="{{ route('admin.services.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Service Name</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="fas fa-cut text-muted"></i>
                </span>
                <input type="text" name="name" class="form-control" placeholder="Enter service name" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                <option value="">Select Category</option>
                @foreach(\App\Models\ServiceCategory::where('status', 'active')->get() as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Price</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">{{ currency_symbol() }}</span>
                    <input type="number" name="price" class="form-control" placeholder="0.00" step="0.01" required>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Duration</label>
                <div class="input-group">
                    <input type="number" name="duration" class="form-control" placeholder="30" required>
                    <span class="input-group-text bg-light">minutes</span>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-bold text-muted mb-1">Assign Staff</label>
            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                <div class="d-flex justify-content-between mb-2">
                    <input type="text" class="form-control form-control-sm w-50" placeholder="Search staff..."
                        onkeyup="filterStaff(this, 'create_staff_container')">
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
                            <input class="form-check-input" type="checkbox" name="staff_ids[]" value="{{ $member->id }}"
                                id="create_staff_{{ $member->id }}">
                            <label class="form-check-label small" for="create_staff_{{ $member->id }}">
                                {{ $member->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Service</button>
        </div>
    </form>
    @endcomponent

    <!-- Edit Service Modal -->
    @component('components.modal', ['id' => 'editServiceModal', 'title' => 'Edit Service'])
    <form id="editServiceForm" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Service Name</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="fas fa-cut text-muted"></i>
                </span>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" id="edit_category_id" class="form-select" required>
                <option value="">Select Category</option>
                @foreach(\App\Models\ServiceCategory::where('status', 'active')->get() as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Price</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">{{ currency_symbol() }}</span>
                    <input type="number" name="price" id="edit_price" class="form-control" step="0.01" required>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Duration</label>
                <div class="input-group">
                    <input type="number" name="duration" id="edit_duration" class="form-control" required>
                    <span class="input-group-text bg-light">minutes</span>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" id="edit_status" class="form-select" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-bold text-muted mb-1">Assign Staff</label>
            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                <div class="d-flex justify-content-between mb-2">
                    <input type="text" class="form-control form-control-sm w-50" placeholder="Search staff..."
                        onkeyup="filterStaff(this, 'edit_staff_container')">
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
                            <input class="form-check-input" type="checkbox" name="staff_ids[]" value="{{ $member->id }}"
                                id="edit_staff_{{ $member->id }}">
                            <label class="form-check-label small" for="edit_staff_{{ $member->id }}">
                                {{ $member->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Service</button>
        </div>
    </form>
    @endcomponent

    @push('styles')
        <style>
            .bg-success-subtle {
                background-color: rgba(25, 135, 84, 0.1);
            }

            .bg-secondary-subtle {
                background-color: rgba(108, 117, 125, 0.1);
            }
        </style>
    @endpush
    @push('scripts')
        <script>
            function editService(id) {
                fetch(`{{ url('') }}/{{ request()->current_salon->slug ?? '' }}/admin/services/${id}/edit`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('edit_name').value = data.name;
                        document.getElementById('edit_category_id').value = data.category_id;
                        document.getElementById('edit_price').value = data.price;
                        document.getElementById('edit_duration').value = data.duration;
                        document.getElementById('edit_status').value = data.status;

                        // Handle staff checkboxes
                        const staffIds = data.staff_ids || [];
                        document.querySelectorAll('#edit_staff_container input[type="checkbox"]').forEach(cb => {
                            cb.checked = staffIds.includes(parseInt(cb.value));
                        });

                        document.getElementById('editServiceForm').action = `{{ url('') }}/{{ request()->current_salon->slug ?? '' }}/admin/services/${id}`;

                        var myModal = new bootstrap.Modal(document.getElementById('editServiceModal'));
                        myModal.show();
                    })

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
        </script>
    @endpush
@endsection