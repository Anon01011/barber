@extends('layouts.app')

@section('title', 'Packages')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Packages</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Packages</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">


                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Package List</h4>
                            <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add New Package
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Tax Rate</th>
                                        <th>Validity</th>
                                        <th>Services</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($packages as $package)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $package->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $package->type == 'fixed' ? 'primary' : 'info' }}">
                                                    {{ ucfirst($package->type) }}
                                                </span>
                                            </td>
                                            <td>{{ format_currency($package->price) }}</td>
                                            <td>{{ $package->tax_rate }}%</td>
                                            <td>{{ $package->validity_value }} {{ ucfirst($package->validity_unit) }}</td>
                                            <td>{{ $package->services_count }}</td>
                                            <td>
                                                <span class="badge bg-{{ $package->is_active ? 'success' : 'danger' }}">
                                                    {{ $package->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.packages.show', $package) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.packages.edit', $package) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.packages.destroy', $package) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No packages found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $packages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection