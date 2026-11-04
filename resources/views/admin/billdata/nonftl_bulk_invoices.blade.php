@extends('admin.admin')

@section('bodycontent')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h3>Non-FTL Bulk Invoice Upload</h3>
            <p>Select multiple PDF invoices. The filename (without .pdf) must match the submitted Freight Invoice Number.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if (session('errorRows'))
        <div class="alert alert-warning">
            <strong>Skipped files:</strong>
            <ul class="mb-0">
                @foreach (session('errorRows') as $error)
                    <li>{{ $error['reason'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header"><strong>Upload Invoice PDFs</strong></div>
        <div class="card-body">
            <form action="{{ route('admin.nonftl.bulk-invoices.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="invoice_files">Select PDF invoices (up to 25 files, 1 MB each)</label>
                    <input type="file" class="form-control-file" name="invoice_files[]" id="invoice_files" accept=".pdf,application/pdf" multiple required>
                    <small class="form-text text-muted"> You can upload up to 25 PDF invoices at once. Each PDF must not exceed 2 MB. Filename must match the Invoice Number (e.g. INV10025.pdf).</small>
                </div>
                <button type="submit" class="btn btn-primary">Upload and Match Invoices</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Submitted Non-FTL Invoice Documents</strong></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>Invoice No.</th>
                        <th>LR/CN No.</th>
                        <th>Mode</th>
                        <th>Invoice Date</th>
                        <th>Invoice Amount</th>
                        <th>Invoice PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $bill)
                        <tr>
                            <td>{{ $bill->freight_invoice_no }}</td>
                            <td>{{ $bill->lr_no }}</td>
                            <td>{{ $bill->custom ?: $bill->mode }}</td>
                            <td>{{ $bill->freight_invoice_date }}</td>
                            <td>{{ $bill->freight_amount }}</td>
                            <td>
                                @if ($bill->freight_invoice_file)
                                    <a href="{{ route('admin.nonftl.bulk-invoices.view', $bill->id) }}" target="_blank" rel="noopener" class="btn btn-sm btn-info">View Invoice</a>
                                @else
                                    <span class="text-muted">Not attached</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No submitted Non-FTL invoice records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $invoices->links() }}
        </div>
    </div>
</div>
<script>
document.getElementById('invoice_files').addEventListener('change', function () {

    const files = this.files;
    const maxFiles = 25;
    const maxSize = 2 * 1024 * 1024;

    if (files.length > maxFiles) {
        alert('You can select a maximum of 25 PDF invoices.');
        this.value = '';
        return;
    }

    for (let file of files) {

        if (file.size > maxSize) {
            alert(file.name + ' exceeds the 2 MB limit.');
            this.value = '';
            return;
        }

        if (!file.name.toLowerCase().endsWith('.pdf')) {
            alert('Only PDF files are allowed.');
            this.value = '';
            return;
        }
    }
});
</script>
@endsection
