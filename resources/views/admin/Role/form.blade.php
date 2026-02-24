@extends('admin.admin')
@push('css')
@endpush

@section('bodycontent')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h2 class="m-0">{{ isset($role) ? 'Edit' : 'Create New' }} Role</h2>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <a href="{{ route('admin.roles.index') }}" class="btn-shadow btn btn-danger">
                            <span class="btn-icon-wrapper pr-2 opacity-7">
                                <i class="fas fa-arrow-circle-left fa-w-20"></i>
                            </span>
                            Back to list
                        </a>
                    </ol>
                </div><!-- /.col -->
            </div>
        </div>
    </div>
 
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-12">
                    @include('admin/session_message')
                    <div class="main-card mb-3 card">

                        <div class="table-responsive p-3">
                            <form method="post"
                                action="{{ isset($role) ? route('admin.roles.update', $role->id) : route('admin.roles.store') }}">
                                @csrf

                                @if (isset($role))
                                    @method('PUT')
                                @endif

                                <div class='card-body'>
                                    {{-- <h5 class="card-title">Manage Roles</h5> --}}


                                    <label for="name">
                                        Role name
                                    </label>

                                    <input id="name" type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ $role->name ?? '' }}" placeholder="Enter role name" autofocus>

                                    @error('name')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="text-center">
                                    <h4 class="mb-4"> Manage permission for role</h4>
                                    @error('permissions')
                                        <span class="text-danger" role="alert">

                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" class="select-all"
                                            id="select-all">
                                        <label class="custom-control-label" for="select-all">Select All</label>
                                    </div>
                                </div>
                                @forelse($modules->chunk(2) as $key=>$chunks)
                                    <div class="form-row">
                                        @foreach ($chunks as $key => $module)
                                            <div class="col-6">
                                                <h5>Module : {{ $module->name }}</h5>
                                                @foreach ($module->permissions as $key => $permission)
                                                    <div class="mb-3 ml-4">
                                                        <div class="custom-control custom-checkbox mb-2">
                                                            <input type="checkbox" id="permission-{{ $permission->id }}"
                                                                class="custom-control-input" type="text"
                                                                name="permissions[]" value={{ $permission->id }}
                                                                @isset($role) @foreach ($role->permissions as $key => $RolePermissions) {{ $permission->id == $RolePermissions->id ? 'checked' : '' }} @endforeach @endisset>
                                                            <label class="custom-control-label"
                                                                for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                @empty
                                    <div class="row">
                                        <div class="text-center col">
                                            <p>No modules found</p>
                                        </div>
                                    </div>
                                    <p>No users</p>
                                @endforelse

                                <button type="submit" class="btn btn-primary">
                                    @isset($role)
                                        <i class="fas fa-arrow-circle-up"></i>
                                        <span>Update</span>
                                    @else
                                        <i class="fas fa-plus-circle"></i>
                                        <span>Create</span>
                                    @endisset
                                </button>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection


@push('js')
    <script type="text/javascript">
        // Listen for click on toggle checkbox
        $('#select-all').click(function(event) {
            if (this.checked) {
                // Iterate each checkbox
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $(':checkbox').each(function() {
                    this.checked = false;
                });
            }
        });
    </script>
@endpush
