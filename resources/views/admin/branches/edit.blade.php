@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h2 class="h3 fw-bold text-gray-800 mb-1">Edit Branch</h2>
                        <p class="text-muted mb-0">Update the details for {{ $branch->name }}.</p>
                    </div>
                    <a href="{{ route('admin.branches.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to List
                    </a>
                </div>



                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h5 class="mb-0 text-primary fw-bold">Branch Details</h5>
                        <small class="text-muted">Update the information below.</small>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('admin.branches.update', $branch) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <!-- Branch Name -->
                                <div class="col-md-8">
                                    <label for="name" class="form-label fw-bold">Branch Name <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="fas fa-store text-secondary"></i></span>
                                        <input type="text" name="name" id="name" value="{{ old('name', $branch->name) }}"
                                            required
                                            class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror"
                                            placeholder="e.g. Downtown Branch">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-4">
                                    <label for="is_active" class="form-label fw-bold">Status</label>
                                    <select id="is_active" name="is_active"
                                        class="form-select @error('is_active') is-invalid @enderror">
                                        <option value="1" {{ old('is_active', $branch->is_active) == 1 ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="0" {{ old('is_active', $branch->is_active) == 0 ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Email Address <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="fas fa-envelope text-secondary"></i></span>
                                        <input type="email" name="email" id="email"
                                            value="{{ old('email', $branch->email) }}" required
                                            class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                            placeholder="branch@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold">Phone Number <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="fas fa-phone text-secondary"></i></span>
                                        <input type="text" name="phone" id="phone"
                                            value="{{ old('phone', $branch->phone) }}" required
                                            class="form-control border-start-0 ps-0 @error('phone') is-invalid @enderror"
                                            placeholder="(555) 123-4567">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="col-12">
                                    <label for="address" class="form-label fw-bold">Address <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 align-items-start pt-2"><i
                                                class="fas fa-map-marker-alt text-secondary"></i></span>
                                        <textarea name="address" id="address" rows="3" required
                                            class="form-control border-start-0 ps-0 @error('address') is-invalid @enderror"
                                            placeholder="123 Main St, City, State, Zip">{{ old('address', $branch->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                <a href="{{ route('admin.branches.index') }}" class="btn btn-light border me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i> Update Branch
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection