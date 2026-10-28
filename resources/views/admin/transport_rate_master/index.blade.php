@extends('admin.admin')
@section('bodycontent')
    <div class="content-header">
        <div class="container-fluid">
            <h1>Transport Rate Master Upload (Non-FTL)</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    @include('admin.transport_rate_master._messages')
                    <div class="mb-3">
                        <a class="btn btn-outline-primary" href="{{ route('admin.transport-rate-master.template') }}">Download Excel Template</a>
                        <a class="btn btn-primary" href="{{ route('admin.transport-rate-master.manual') }}">Manual Upload</a>
                        <a class="btn btn-secondary" href="{{ route('admin.transport-rate-master.list') }}">Rate Master List</a>
                    </div>
                    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.transport-rate-master.import') }}">
                        @csrf
                        <div class="form-group">
                            <label>Excel File (.xls/.xlsx)</label>
                            <input type="file" name="excel_file" accept=".xls,.xlsx" required class="form-control">
                        </div>
                        <button class="btn btn-success" type="submit">Upload Excel</button>
                    </form>
                    <p class="text-muted mt-3">Use the downloaded template with the exact header order. FTL rates are not accepted.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
