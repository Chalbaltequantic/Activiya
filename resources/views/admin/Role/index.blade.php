@extends('admin.admin')

@push('css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <link href="
    https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css
    " rel="stylesheet">
@endpush

@section('bodycontent')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h2 class="m-0">Roles</h2>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <a href="{{ route('admin.roles.create') }}" class="btn-shadow mr-3 btn btn-primary">
                            <i class="fa fa-plus-circle"></i>
                            Add Role
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
                            <table id='dataTableId'
                                class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center"> permission</th>
                                        <th class="text-center">Updated At</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $key => $role)
                                        <tr>
                                            <td class="text-center text-muted">{{ $key + 1 }}</td>
                                            <td class="text-center">{{ $role->name }}</td>
                                            @if ($role->permissions->count() > 0)
                                                <td class="text-center">
                                                    {{role_permission_list_format($role)}}  <div class="badge badge-primary">{{ $role->permissions->count() }}</div>
                                                  {{-- {{ json_encode($role->permissions)}} --}}
                                                    {{-- {{json_encode($role->permissions())}} --}}
                                                </td>
                                            @else
                                                <td class="text-center">
                                                    <div class="badge badge-danger">No permissions Found :( </div>
                                                </td>
                                            @endif

                                            <td class="text-center">
                                                <div class=" ">{{ $role->updated_at->diffForHumans() }}</div>
                                            </td>
                                            <td class="text-center">
                                                @if ($role->deletable == true)
                                                <a href="{{ route('admin.roles.edit', $role->id) }}" id="PopoverCustomT-1"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit "></i>
                                                    Edit
                                                </a>
                                                
                                                    <button onclick='deleteData({{ $role->id }})' type="button"
                                                        id="PopoverCustomT-4" class="btn btn-danger btn-sm"> <i
                                                            class="fas fa-trash-alt    "></i>
                                                        Delete
                                                    </button>
                                                    <form id='delete-form-{{ $role->id }}'
                                                        action="{{ route('admin.roles.destroy', $role->id) }}"
                                                        method="post" class='d-none'>
                                                        @csrf
                                                        @method('delete')
                                                    </form>
                                                @endif
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
@endsection


@push('js')
    <script type="text/javascript" src="//cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript">
        function deleteData(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
        $(document).ready(function() {
            $('#dataTableId').DataTable();


        });
    </script>
@endpush
