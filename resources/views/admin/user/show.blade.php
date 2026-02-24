@extends('admin.admin')
@section('bodycontent')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h2 class="m-0">User Details</h2>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-shadow btn btn-info">
                            <span class="btn-icon-wrapper pr-2 opacity-7">
                                <i class="fas fa-edit fa-w-20"></i>
                            </span>
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn-shadow btn btn-danger">
                            <span class="btn-icon-wrapper pr-2 opacity-7">
                                <i class="fas fa-arrow-circle-left fa-w-20"></i>
                            </span>
                            {{ __('Back to list') }}
                        </a>
                    </ol>
                </div><!-- /.col -->
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            {{-- <img src="{{ $user->getFirstMediaUrl('avatar') != null ? $user->getFirstMediaUrl('avatar') : config('app.placeholder') . '160' }}"
                            class="img-fluid img-thumbnail" alt="avatar"> --}}
                        </div>

                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-md-10">
                    <div class="main-card card">
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row">Name:</th>
                                        <td>{{ $user->name }}</td>
                                    </tr>

                                    <tr>
                                        <th scope="row">Email:</th>
                                        <td>{{ $user->email }}</td>
                                    </tr> 
									<tr>
                                        <th scope="row">Mobile:</th>
                                        <td>{{ $user->mobile }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Role:</th>
                                        <td>
                                            @if ($user->role)
                                                <span class="badge badge-info">{{ $user->role->name }}</span>
                                            @else
                                                <span class="badge badge-danger">No role found :(</span>
                                            @endif
                                        </td>
                                    </tr>
									<tr>
                                        <th scope="row">Vendor Code:</th>
                                        <td>
                                            @if ($user->vendor_code)
                                                <span class="badge badge-info">{{ $user->vendor_code }}</span>
                                            @else
                                                <span class="badge badge-danger">No vendor associated :(</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Status:</th>
                                        <td>
                                            @if ($user->status)
                                                <div class="badge badge-success">Active</div>
                                            @else
                                                <div class="badge badge-danger">Inactive</div>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Joined At:</th>
                                        <td>{{ $user->created_at->diffForHumans() }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Last Modified At:</th>
                                        <td>{{ $user->updated_at->diffForHumans() }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Last Login At:</th>
                                        <td>{{ $user->last_login_at }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
