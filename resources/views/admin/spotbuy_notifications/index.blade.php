@extends('admin.admin')

@section('title', $title ?? 'Spot Buy Notifications')

@section('bodycontent')

<div class="content-wrapper">

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>
                        <i class="far fa-bell mr-2"></i>
                        Spot Buy Notifications
                    </h1>

                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item">
                            <a href="{{ url('/admin') }}">
                                Home
                            </a>
                        </li>

                        <li class="breadcrumb-item active">
                            Notifications
                        </li>

                    </ol>

                </div>

            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">



            @if(session('success'))

                <div class="alert alert-success alert-dismissible fade show">

                    <button
                        type="button"
                        class="close"
                        data-dismiss="alert"
                    >
                        &times;
                    </button>

                    <i class="fas fa-check-circle mr-1"></i>

                    {{ session('success') }}

                </div>

            @endif

            @if(session('error'))

                <div class="alert alert-danger alert-dismissible fade show">

                    <button
                        type="button"
                        class="close"
                        data-dismiss="alert"
                    >
                        &times;
                    </button>

                    <i class="fas fa-exclamation-circle mr-1"></i>

                    {{ session('error') }}

                </div>

            @endif


            <div class="card card-outline card-primary">

                {{-- Card Header --}}

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-file-invoice-dollar mr-1"></i>

                        Quotation Notifications

                    </h3>


                    <div class="card-tools">


                        {{-- Mark All As Read --}}

                        @if(isset($notifications) && $notifications->count() > 0)

                            <form
                                method="POST"
                                action="{{ route('admin.spotbuy.notifications.read-all') }}"
                                style="display:inline;"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-primary"
                                >

                                    <i class="fas fa-check-double mr-1"></i>

                                    Mark All As Read

                                </button>

                            </form>

                        @endif

                    </div>

                </div>


                {{-- Card Body --}}

                <div class="card-body p-0">


                    @if(isset($notifications) && $notifications->count() > 0)


                        <div class="table-responsive">

                            <table class="table table-hover table-striped mb-0">

                                <thead>

                                    <tr>

                                        <th style="width:60px;">
                                            #
                                        </th>

                                        <th style="width:120px;">
                                            Status
                                        </th>

                                        <th style="width:110px;">
                                            Round
                                        </th>

                                        <th>
                                            Notification
                                        </th>

                                        <th style="width:180px;">
                                            Date & Time
                                        </th>

                                        <th
                                            style="width:120px;"
                                            class="text-center"
                                        >
                                            Action
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach($notifications as $key => $notification)

                                        <tr
                                            @if(empty($notification->is_read))
                                                style="background:#f4f8fc;"
                                            @endif
                                        >


                                            {{-- Serial Number --}}

                                            <td>

                                                {{ $key + 1 }}

                                            </td>


                                            {{-- Read / Unread --}}

                                            <td>

                                                @if(empty($notification->is_read))

                                                    <span class="badge badge-warning">

                                                        <i class="fas fa-circle mr-1"
                                                           style="font-size:7px;">
                                                        </i>

                                                        Unread

                                                    </span>

                                                @else

                                                    <span class="badge badge-success">

                                                        <i class="fas fa-check mr-1"></i>

                                                        Read

                                                    </span>

                                                @endif

                                            </td>


                                            {{-- Round --}}

                                            <td>

                                                @if($notification->round_no == 1)

                                                    <span class="badge badge-primary">
                                                        Round 1
                                                    </span>

                                                @elseif($notification->round_no == 2)

                                                    <span class="badge badge-info">
                                                        Round 2
                                                    </span>

                                                @elseif($notification->round_no == 3)

                                                    <span class="badge badge-success">
                                                        Round 3
                                                    </span>

                                                @else

                                                    <span class="badge badge-secondary">

                                                        Round
                                                        {{ $notification->round_no }}

                                                    </span>

                                                @endif

                                            </td>


                                            <td>

                                                <div>

                                                    <strong>

                                                        {{ $notification->title }}

                                                    </strong>

                                                </div>


                                                <div class="text-muted mt-1">

                                                    {{ $notification->message }}

                                                </div>


                                                @if(!empty($notification->spotby_id))

                                                    <div class="mt-2">

                                                        <small class="text-muted">

                                                            <i class="fas fa-file-alt mr-1"></i>

                                                            RFQ / Spot Buy ID:

                                                            <strong>
                                                                {{ $notification->spotby_id }}
                                                            </strong>

                                                        </small>

                                                    </div>

                                                @endif

                                            </td>


                                            <td>

                                                @if(!empty($notification->created_at))

                                                    <i class="far fa-clock mr-1 text-muted"></i>

                                                    {{ \Carbon\Carbon::parse($notification->created_at)->format('d M Y') }}

                                                    <br>

                                                    <small class="text-muted">

                                                        {{ \Carbon\Carbon::parse($notification->created_at)->format('h:i A') }}

                                                    </small>

                                                @endif

                                            </td>


                                            {{-- Action --}}

                                            <td class="text-center">

                                                <a
                                                    href="{{ route('admin.spotbuy.notifications.open', $notification->id) }}"
                                                    class="btn btn-sm btn-primary"
                                                    title="Open Notification"
                                                >

                                                    <i class="fas fa-eye mr-1"></i>

                                                    View

                                                </a>

                                            </td>


                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>


                    @else


                        {{-- =================================================
                             NO NOTIFICATION
                        ================================================= --}}

                        <div
                            class="text-center"
                            style="padding:60px 20px;"
                        >

                            <div
                                class="text-muted"
                                style="font-size:45px;"
                            >

                                <i class="far fa-bell-slash"></i>

                            </div>


                            <h5 class="mt-3">

                                No Notifications

                            </h5>


                            <p class="text-muted mb-0">

                                You currently do not have any Spot Buy
                                quotation notifications.

                            </p>

                        </div>


                    @endif


                </div>

            </div>

        </div>

    </section>

</div>

@endsection