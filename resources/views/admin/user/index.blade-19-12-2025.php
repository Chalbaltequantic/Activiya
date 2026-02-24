@extends('admin.admin')



@push('css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
@endpush
@section('bodycontent')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h2 class="m-0">Users</h2>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <a href="{{ route('admin.users.create') }}" class="btn-shadow mr-3 btn btn-primary">
                            <i class="fa fa-plus-circle"></i>
                            Add User
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
                                        <th class="text-center">#</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Mobile</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Joined At</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $key => $user)
                                        <tr>
                                            <td class="text-center text-muted">{{ $key+1 }}</td>
                                            <td class="text-center">
                                                <div class="widget-content p-0">
                                                    <div class="widget-content-wrapper">
                                                        <div class="widget-content-left mr-3">
                                                            {{-- <div class="widget-content-left">
                                                            <img width="40" class="rounded-circle"
                                                                src="{{ $user->getFirstMediaUrl('avatar') != null ? $user->getFirstMediaUrl('avatar') : config('app.placeholder') . '160' }}"
                                                                alt="User Avatar">
                                                        </div> --}}
                                                        </div>
                                                        <div class="widget-content-left flex2">
                                                            <div class="widget-heading">{{ $user->name }}</div>
                                                            <div class="widget-subheading opacity-7">
                                                                @if ($user->role)
                                                                    <span
                                                                        class="badge badge-info">{{ $user->role->name }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">No role found :(</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                <div class=" ">{{ $user->email }}</div>
                                            </td>
											<td class="text-center">
                                                <div class=" ">{{ $user->mobile }}</div>
                                            </td>
                                            <td>
                                                @if ($user->status)
                                                    <span class="badge badge-info">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class=" ">{{ $user->updated_at->diffForHumans() }}</div>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.users.show', $user->id) }}" id=""
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye "></i>
                                                    Show
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user->id) }}" id=""
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit "></i>
                                                    Edit
                                                </a>
                                               
                                                    <button onclick='deleteData({{ $user->id }})' type="button"
                                                        class="btn btn-danger btn-sm"> <i class="fas fa-trash-alt    "></i>
                                                        Delete
                                                    </button>
                                                    <form id='delete-form-{{ $user->id }}'
                                                        action="{{ route('admin.users.destroy', $user->id) }}"
                                                        method="post" class='d-none'>
                                                        @csrf
                                                        @method('delete')
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
