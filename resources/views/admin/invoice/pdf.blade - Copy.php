<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>GST Invoice</title>

<style>
@page {
    size: A4;
    margin: 8mm 6mm 18mm 6mm;
}

body{
    font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
    font-size: 11px;
    color:#000;
}

.invoice-wrapper{
    width:100%;
}

table{
    width:100%;
    border-collapse: collapse;
}

th, td{
    border:1px solid #000;
    padding:5px;
    font-size:10px;
}

.no-border td{
    border:none;
}

.text-center{ text-align:center; }
.text-right{ text-align:right; }
.font-bold{ font-weight:bold; }

.header-title{
    font-size:18px;
    font-weight:bold;
}

.sub-title{
    font-size:14px;
    font-weight:bold;
}

.section-title{
    background:#f2f2f2;
    font-weight:bold;
    text-align:center;
}

.signature-box{
    height:70px;
    border:1px solid #000;
}

.footer-bar{
    position: fixed;
    bottom: -10px;
    left: 0;
    right: 0;
    background:#1c2b5a;
    color:#fff;
    padding:5px;
    font-size:10px;
    text-align:center;
}

.page-break{
    page-break-before: always;
}
</style>
</head>
<body>

<div class="invoice-wrapper">

<!-- HEADER -->
<table class="no-border" style="margin-bottom:8px;">
<tr>

    <!-- LOGO -->
    <td width="20%" valign="middle" style="border:none;">
        @if($vendor->logo)
            <img src="{{ public_path('uploads/vendorlogo/'.$vendor->logo) }}" 
                 style="height:60px;">
        @endif
    </td>

    <!-- COMPANY NAME -->
    <td width="60%" valign="middle" class="text-center" style="border:none;">
        <div class="header-title">{{$vendor->vendor_name}}</div>
        <div class="sub-title">TAX INVOICE</div>
    </td>

    <!-- EMPTY SPACE (for balance) -->
    <td width="20%" style="border:none;"></td>

</tr>
</table>

<br>

<!-- ADDRESS + QR -->
<table>
<tr>
    <td width="45%" valign="top">
        <b>Branch Address</b><br>
        {{ $invoice->branchAddress->address_line1 ?? '' }},
        {{ $invoice->branchAddress->address_line2  ?? ''  }},
        {{ $invoice->branchAddress->city ?? ''  }}<br>
        {{ $invoice->branchAddress->state ?? ''  }} - {{ $invoice->branchAddress->zip_code ?? ''  }}<br><br>

        <b>Billing Address</b><br>
        {{ $invoice->billingAddress->address_line1 ?? ''  }}
        {{ $invoice->billingAddress->address_line2 ?? ''  }}
        {{ $invoice->billingAddress->city ?? ''  }},
        {{ $invoice->billingAddress->state ?? ''  }} - {{ $invoice->billingAddress->zip_code ?? ''  }}<br><br>

        <b>GSTIN :</b> {{$vendor->gstin_number ?? '' }}
    </td>

    <td width="35%" valign="top">
        <b>Registered Office Address</b><br>
        {{ $invoice->registeredAddress->address_line1 ?? '' }},
        {{ $invoice->registeredAddress->address_line2 ?? '' }},
        {{ $invoice->registeredAddress->city ?? '' }},
        {{ $invoice->registeredAddress->state ?? '' }} - {{ $invoice->registeredAddress->zip_code ?? '' }}
    </td>

    <td width="20%" class="text-center" valign="top">
        <b>QR Code</b><br><br>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $invoice->invoice_no }}" width="90">
    </td>
</tr>
</table>

<br>

<!-- CUSTOMER + INVOICE INFO -->
<table>
<tr>
    <td width="50%" valign="top">
        <b>Bill To :</b><br>
        Code : {{$invoice->sitePlant->sitePlant ?? ''}}<br>
        Name : {{$invoice->sitePlant->plant_site_name ?? ''}}<br>
        {{$invoice->sitePlant->street_house_number ?? ''}}<br>
        {{$invoice->sitePlant->street1 ?? ''}}<br>
        {{$invoice->sitePlant->street2 ?? ''}}, {{$invoice->sitePlant->city ?? ''}}<br>
        {{$invoice->sitePlant->state ?? ''}} ({{$invoice->sitePlant->state_code ?? ''}})  - {{ $invoice->sitePlant->post_code ?? '' }}<br>
        GSTIN : {{$invoice->sitePlant->gstin_number ?? ''}}
    </td>

    <td width="50%" valign="top">
        <b>Invoice No :</b> {{$invoice->invoice_no ?? ''}}<br>
        <b>Invoice Date :</b> {{ \Carbon\Carbon::parse($invoice->bill_date)->format('d-m-Y') }}<br>
        <b>Invoice Type :</b> Original
    </td>
</tr>
</table>

<br>

<!-- SERVICE TABLE -->
<table>
<thead>
<tr class="section-title">
    <th>Sr</th>
    <th>LR No</th>
    <th>LR Date</th>
    <th>Dispatch</th>
    <th>From</th>
    <th>To</th>
    <th>PO No</th>
    <th>Taxable</th>
    <th>CGST</th>
    <th>SGST</th>
    <th>IGST</th>
    <th>Total</th>
</tr>
</thead>
<tbody>

@php
    $total_taxable = 0;
    $total_cgst = 0;
    $total_sgst = 0;
    $total_igst = 0;
    $grand_total = 0;
    $sr = 1;
@endphp
@foreach($invoice->items as $item)
@php
    $total_taxable += $item->taxable;
    $total_cgst += $item->cgst;
    $total_sgst += $item->sgst;
    $total_igst += $item->igst;
    $grand_total += $item->total;
@endphp

<tr class="text-center">
    <td>{{ $sr++ }}</td>
    <td>{{ $item->lr_no ?? ''  }}</td>
    <td>{{ $item->lr_date ?? ''  }}</td>
    <td>{{ $item->vehicle_dispatch_date ?? ''  }}</td>
    <td>{{ $item->from_location ?? ''  }}</td>
    <td>{{ $item->to_location ?? ''  }}</td>
    <td>{{ $item->po_no ?? ''  }}</td>
    <td class="text-right">{{ number_format($item->taxable,2) }}</td>
    <td class="text-right">{{ number_format($item->cgst,2) }}</td>
    <td class="text-right">{{ number_format($item->sgst,2) }}</td>
    <td class="text-right">{{ number_format($item->igst,2) }}</td>
    <td class="text-right">{{ number_format($item->total,2) }}</td>
</tr>
@endforeach

<tr class="font-bold text-center">
    <td colspan="7" class="text-right">Grand Total (Rs)</td>
    <td class="text-right">{{ number_format($total_taxable,2) }}</td>
    <td class="text-right">{{ number_format($total_cgst,2) }}</td>
    <td class="text-right">{{ number_format($total_sgst,2) }}</td>
    <td class="text-right">{{ number_format($total_igst,2) }}</td>
    <td class="text-right">{{ number_format($grand_total,2) }}</td>
</tr>


</tbody>
</table>

<br>

<!-- DECLARATION + SIGNATURE -->
<table>
<tr>
    <td width="65%" valign="top">
        <b>Amount in Words :</b><br>
        {{ amountInWords($grand_total) }}<br><br>

        <b>Declaration :</b><br>
        1. Payment by NEFT/RTGS/Cheque/Draft only.<br>
        2. Discrepancy must be reported within 7 days.<br>
        3. Late payment interest @24% p.a.<br>
        4. All disputes subject to Delhi Jurisdiction.
    </td>

    <td width="35%" valign="top" class="text-center">
        <b>For {{$vendor->vendor_name ?? ''}}</b><br><br>
        <div class="signature-box"></div><br>
        Authorised Signatory
    </td>
</tr>
</table>

</div>

<!-- ANNEXURE -->
@if($invoice->annexures->count() > 0)
<div class="page-break"></div>

<table>
<tr>
    <td colspan="16" class="section-title">ANNEXURE</td>
</tr>
</table>

<table>
<thead>
<tr class="section-title">
    <th>Sr</th>
    <th>LR No</th>
    <th>Customer Ref</th>
    <th>Arrival</th>
    <th>Delivery</th>
    <th>Transit</th>
    <th>Vehicle</th>
    <th>Size</th>
    <th>Weight</th>
    <th>Pkgs</th>
    <th>Freight</th>
    <th>Charge Wt</th>
    <th>Loading</th>
    <th>Unloading</th>
    <th>Toll</th>
    <th>Green Tax</th>
</tr>
</thead>
<tbody>

@php $sr=1; @endphp
@foreach($invoice->annexures as $a)
<tr class="text-center">
    <td>{{ $sr++ }}</td>
    <td>{{ $a->lr_no ?? ''  }}</td>
    <td>{{ $a->customer_ref_no ?? ''  }}</td>
    <td>{{ $a->arrival_date ?? ''  }}</td>
    <td>{{ $a->delivery_date ?? ''  }}</td>
    <td>{{ $a->transit_days ?? ''  }}</td>
    <td>{{ $a->vehicle_no ?? ''  }}</td>
    <td>{{ $a->vehicle_size ?? ''  }}</td>
    <td class="text-right">{{ number_format($a->actual_weight,2) }}</td>
    <td>{{ $a->no_of_packages ?? ''  }}</td>
    <td class="text-right">{{ number_format($a->freight,2) }}</td>
    <td class="text-right">{{ number_format($a->charge_weight,2) }}</td>
    <td class="text-right">{{ number_format($a->loading_charge,2) }}</td>
    <td class="text-right">{{ number_format($a->unloading_charge,2) }}</td>
    <td class="text-right">{{ number_format($a->toll_tax,2) }}</td>
    <td class="text-right">{{ number_format($a->green_tax,2) }}</td>
</tr>
@endforeach

</tbody>
</table>
@endif

<!-- FOOTER -->
<div class="footer-bar">
    {{$vendor->vendor_name}} | EXPERIENCE EXCELLENCE
</div>

</body>
</html>