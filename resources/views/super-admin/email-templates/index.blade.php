@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Email Templates</h1>
        </div>



        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Email Templates</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                                <tr>
                                    <td>
                                        <span
                                            class="badge bg-info">{{ ucwords(str_replace('_', ' ', str_replace('subscription_', '', $template->type))) }}</span>
                                    </td>
                                    <td>{{ $template->subject }}</td>
                                    <td>{{ $template->updated_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.email-templates.edit', $template->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection