@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
            <div>
                <h2 class="h3 fw-bold text-gray-800 mb-1">Branches</h2>
                <p class="text-muted mb-0">Manage your salon branches and locations.</p>
            </div>
            @can('salon.manage_branches')
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Add New Branch
                    </a>
                </div>
            @endcan
        </div>



        <!-- Content Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                    Branch Info</th>
                                <th scope="col"
                                    class="px-4 py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                    Contact</th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                    Status</th>
                                <th scope="col"
                                    class="px-4 py-3 text-end text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($branches as $branch)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold me-3"
                                                style="width: 40px; height: 40px;">
                                                {{ substr($branch->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-sm fw-bold text-dark">{{ $branch->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ Str::limit($branch->address, 30) }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm font-weight-bold mb-0 text-dark">
                                            <i class="fas fa-envelope me-2 text-secondary" style="width: 16px;"></i>
                                            {{ $branch->email }}
                                        </p>
                                        <p class="text-xs text-secondary mb-0">
                                            <i class="fas fa-phone me-2 text-secondary" style="width: 16px;"></i>
                                            {{ $branch->phone }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($branch->is_active)
                                            <span
                                                class="badge bg-success-subtle text-white border border-success-subtle rounded-pill px-3">Active</span>
                                        @else
                                            <span
                                                class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.branches.show', $branch) }}"
                                                class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.branches.edit', $branch) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit Branch">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST"
                                                class="d-inline-block"
                                                onsubmit="return confirm('Are you sure you want to delete this branch? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Delete Branch">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <div class="bg-light rounded-circle p-3 mb-3">
                                                <i class="fas fa-building text-secondary fa-2x"></i>
                                            </div>
                                            <h5 class="text-muted">No branches found</h5>
                                            <p class="text-muted small mb-3">Get started by creating your first branch.</p>
                                            @can('salon.manage_branches')
                                                <a href="{{ route('admin.branches.create') }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus me-2"></i> Create Branch
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($branches->hasPages())
                    <div class="card-footer bg-white border-top-0 py-3">
                        {{ $branches->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection