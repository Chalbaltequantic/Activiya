@extends('admin.admin')

@section('bodycontent')

<style>
.table-box{overflow:auto;max-height:560px;border:1px solid #ddd}
.insurance-list{min-width:2200px;font-size:12px}
.insurance-list th,.insurance-list td{white-space:nowrap;padding:5px;vertical-align:middle}
.insurance-list thead th{position:sticky;top:0;background:#fce4d6;color:#0070c0;z-index:2}
.photo-item{border:1px solid #ddd;padding:5px;margin-bottom:10px;text-align:center}
.photo-item img{width:100%;height:140px;object-fit:cover;margin-bottom:5px}
</style>

<div class="content-header">
<div class="container-fluid">
<h1>Insurance List</h1>
</div>
</div>

<div class="content">
<div class="container-fluid">

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
<div class="card-header p-2">
<ul class="nav nav-pills">
<li class="nav-item"><a class="nav-link" href="{{ route('admin.insurance.index') }}">XLS Upload</a></li>
<li class="nav-item"><a class="nav-link" href="{{ route('admin.insurance.manual-upload') }}">Manual Upload</a></li>
<li class="nav-item"><a class="nav-link active" href="{{ route('admin.insurance.datalist') }}">Insurance List</a></li>
</ul>
</div>

<div class="card-body">

<form method="GET" action="{{ route('admin.insurance.datalist') }}" class="mb-3">
<div class="row">
<div class="col-md-4">
<input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Invoice / LR / Location / Transporter">
</div>
<div class="col-md-2">
<select name="per_page" class="form-control">
@foreach([10,25,50,100] as $size)
<option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
@endforeach
</select>
</div>
<div class="col-md-6">
<button class="btn btn-primary">Search</button>
<a href="{{ route('admin.insurance.datalist') }}" class="btn btn-secondary">Reset</a>
<a href="{{ route('admin.insurance.export',request()->except('page')) }}" class="btn btn-success">Download XLS</a>
</div>
</div>
</form>

<div class="table-box">
<table class="table table-bordered insurance-list">
<thead>
<tr>
<th>Loss Date</th>
<th>Nature</th>
<th>From Code</th>
<th>From Location</th>
<th>To Code</th>
<th>To Location</th>
<th>Invoice No.</th>
<th>Invoice Date</th>
<th>Transporter</th>
<th>LR No.</th>
<th>LR Date</th>
<th>Damage</th>
<th>Shortage</th>
<th>Total</th>
<th>Invoice</th>
<th>POD/LR</th>
<th>Photos</th>
</tr>
</thead>
<tbody>

@forelse($datalist as $insurance)
<tr>
<td>{{ $insurance->loss_date ? $insurance->loss_date->format('Y-m-d') : '' }}</td>
<td>{{ $insurance->nature_of_claim }}</td>
<td>{{ $insurance->from_location_code }}</td>
<td>{{ $insurance->from_location }}</td>
<td>{{ $insurance->to_location_code }}</td>
<td>{{ $insurance->to_location }}</td>
<td>{{ $insurance->invoice_no }}</td>
<td>{{ $insurance->invoice_date ? $insurance->invoice_date->format('Y-m-d') : '' }}</td>
<td>{{ $insurance->transporter_name }}</td>
<td>{{ $insurance->lr_no }}</td>
<td>{{ $insurance->lr_date ? $insurance->lr_date->format('Y-m-d') : '' }}</td>
<td>{{ number_format((float)$insurance->damage_value,2) }}</td>
<td>{{ number_format((float)$insurance->shortage_value,2) }}</td>
<td>{{ number_format((float)$insurance->total_value,2) }}</td>
<td>
@if($insurance->invoice_file)
<a href="{{ asset($insurance->invoice_file) }}" target="_blank" class="btn btn-info btn-xs">View</a>
@else
-
@endif
</td>
<td>
@if($insurance->pod_lr_copy)
<a href="{{ asset($insurance->pod_lr_copy) }}" target="_blank" class="btn btn-info btn-xs">View</a>
@else
-
@endif
</td>
<td>
<button type="button" class="btn btn-primary btn-xs view-photos" data-id="{{ $insurance->id }}" data-invoice="{{ $insurance->invoice_no }}">View Photos <span class="badge badge-light photo-count-{{ $insurance->id }}">{{ $insurance->photographs->count() }}</span></button>
</td>
</tr>
@empty
<tr>
<td colspan="17" class="text-center">No Insurance records found.</td>
</tr>
@endforelse

</tbody>
</table>
</div>

<div class="row mt-3">
<div class="col-md-6">Showing {{ $datalist->firstItem() ?? 0 }} to {{ $datalist->lastItem() ?? 0 }} of {{ $datalist->total() }}</div>
<div class="col-md-6"><div class="float-right">{{ $datalist->onEachSide(1)->links('pagination::bootstrap-4') }}</div></div>
</div>

</div>
</div>

</div>
</div>

<div class="modal fade" id="photoModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Insurance Photographs <span id="modalInvoice"></span></h5>
<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
</div>

<div class="modal-body">
<input type="hidden" id="modalInsuranceId">

<div class="mb-2">
<strong>Uploaded:</strong> <span id="modalPhotoCount">0</span> / 10
&nbsp;
<strong>Remaining:</strong> <span id="modalRemaining">10</span>
</div>

<div class="row" id="modalPhotoList"></div>

<hr>

<div id="modalUploadArea">
<input type="file" id="modalPhotoFiles" class="form-control-file" accept=".jpg,.jpeg,.png" multiple>
<small class="text-muted">Maximum 10 photographs, maximum 1 MB each.</small>
<button type="button" class="btn btn-success btn-sm mt-2" id="modalUploadButton">Upload Photos</button>
</div>

</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>

</div>
</div>
</div>

@push('js')
<script>
$(document).ready(function(){
$.ajaxSetup({headers:{'X-CSRF-TOKEN':"{{ csrf_token() }}"}});

let currentInsuranceId=null;

function loadModalPhotos(){
$.get("{{ url('/admin/insurance') }}/"+currentInsuranceId+"/photographs",function(r){
if(!r.success)return;

$('#modalPhotoCount').text(r.photo_count);
$('#modalRemaining').text(r.remaining);
$('.photo-count-'+currentInsuranceId).text(r.photo_count);

let html='';

$.each(r.photographs,function(i,p){
html+='<div class="col-md-4"><div class="photo-item"><a href="'+p.url+'" target="_blank"><img src="'+p.url+'"></a><div><a href="'+p.url+'" target="_blank" class="btn btn-info btn-xs">View</a> <button type="button" class="btn btn-danger btn-xs modal-delete-photo" data-id="'+p.id+'">Delete</button></div></div></div>';
});

if(!html)html='<div class="col-md-12 text-center text-muted">No photographs uploaded.</div>';

$('#modalPhotoList').html(html);

if(r.remaining<=0)$('#modalUploadArea').hide();
else $('#modalUploadArea').show();
});
}

$(document).on('click','.view-photos',function(){
currentInsuranceId=$(this).data('id');
$('#modalInsuranceId').val(currentInsuranceId);
$('#modalInvoice').text($(this).data('invoice')?' - Invoice '+$(this).data('invoice'):'');
$('#modalPhotoFiles').val('');
$('#photoModal').modal('show');
loadModalPhotos();
});

$('#modalPhotoFiles').on('change',function(){
let files=this.files;
let remaining=parseInt($('#modalRemaining').text())||0;

if(files.length>remaining){
alert('Only '+remaining+' more photograph(s) can be uploaded.');
$(this).val('');
return;
}

for(let i=0;i<files.length;i++){
if(files[i].size>1024*1024){
alert(files[i].name+' is larger than 1 MB.');
$(this).val('');
return;
}
}
});

$('#modalUploadButton').on('click',function(){
let input=document.getElementById('modalPhotoFiles');
if(!input.files.length){
alert('Please select photograph(s).');
return;
}

let form=new FormData();
for(let i=0;i<input.files.length;i++)form.append('photographs[]',input.files[i]);

$.ajax({
url:"{{ url('/admin/insurance') }}/"+currentInsuranceId+"/photographs",
type:'POST',
data:form,
processData:false,
contentType:false,
success:function(r){
if(!r.success){alert(r.message);return;}
$('#modalPhotoFiles').val('');
loadModalPhotos();
},
error:function(xhr){
alert(xhr.responseJSON&&xhr.responseJSON.message?xhr.responseJSON.message:'Unable to upload photographs.');
}
});
});

$(document).on('click','.modal-delete-photo',function(){
let photoId=$(this).data('id');
if(!confirm('Are you sure you want to delete this photograph?'))return;

$.ajax({
url:"{{ url('/admin/insurance/photographs') }}/"+photoId,
type:'DELETE',
success:function(r){
if(r.success)loadModalPhotos();
}
});
});
});
</script>
@endpush

@endsection