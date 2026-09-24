@extends('layouts.app')

@section('title', 'Memberships')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Memberships</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Memberships</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">


                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Membership List</h4>
                            <a href="{{ route('admin.memberships.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add New Membership
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Discount Value</th>
                                        <th>Taxable</th>
                                        <th>Validity (Days)</th>
                                        <th>Services</th>
                                        <th>Products</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($memberships as $membership)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $membership->name }}</td>
                                            <td>{{ format_currency($membership->discount_value) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $membership->is_taxable ? 'success' : 'secondary' }}">
                                                    {{ $membership->is_taxable ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>{{ $membership->validity_days }}</td>
                                            <td>{{ $membership->services_count }}</td>
                                            <td>{{ $membership->inventory_items_count }}</td>
                                            <td>
                                                <span class="badge bg-{{ $membership->is_active ? 'success' : 'danger' }}">
                                                    {{ $membership->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.memberships.show', $membership) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.memberships.edit', $membership) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.memberships.destroy', $membership) }}"
                                                    method="POST" class="d-inline">
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
                                            <td colspan="9" class="text-center">No memberships found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $memberships->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection