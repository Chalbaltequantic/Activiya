<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Lorry Receipt</title>
<style>
@page {
    margin: 15px 20px 15px 20px;
}
body{
    margin:0;
    padding:0;
}
.main-container{
    width:720px;              /* slightly smaller than A4 */
    margin:0 auto;
    padding-right:15px;       /* spacing from right */
    padding-left:5px;
    background:#efe48a;
    border:1px solid #000;
    font-family:"Times New Roman", serif;
    color:#000;
    box-sizing:border-box;
}
table{
    width:100%;
    border-collapse:collapse;
}

td,th{
    box-sizing:border-box;
}

</style>
</head>

<body style="margin:0;padding:0;background:#ffffff;">

<div class="main-container">

<!-- TOP BAR -->

<table style="width:100%;font-size:14px;">
<tr>
<td>CIN {{$vendor->cin ?? '-'}}</td>
<td style="text-align:center;">Subject to Bangalore Jurisdiction only</td>
<td style="text-align:right;">
Ph. {{$vendor->authorized_person_phone}}
</td>
</tr>
</table>

<!-- COMPANY TITLE -->

<div style="text-align:center;font-weight:bold;font-size:28px;color:#c40000;">
{{$vendor->vendor_name}}
</div>

<div style="text-align:center;font-size:14px;margin-top:3px;">
{{ $invoice->registeredAddress->address_line1 ?? '' }}
{{ $invoice->registeredAddress->address_line2 ?? '' }}
{{ $invoice->registeredAddress->city ?? '' }}
{{ $invoice->registeredAddress->state ?? '' }}
- {{ $invoice->registeredAddress->zip_code ?? '' }}
<br> Email : {{$vendor->authorized_person_mail}}
</div>

<!-- SECOND ROW -->

<table style="width:100%;border-collapse:collapse;margin-top:10px;font-size:13px;">
<tr>

<td style="width:30%;border:1px solid #000;padding:8px;vertical-align:top;">
<b>CAUTION</b><br>
{{$invoice->caution}}<br><br>
Address of Delivery Office:
</td>

<td style="width:40%;border:1px solid #000;padding:8px;text-align:center;vertical-align:top;">
<div style="font-weight:bold;font-size:15px;">LORRY COPY</div>
<div style="font-weight:bold;">AT OWNER'S RISK</div>
<div style="font-weight:bold;">INSURANCE</div>

<div style="margin-top:5px;text-align:center;">
{{$invoice->insurance}}
</div>

</td>

<td style="width:30%;border:1px solid #000;padding:8px;vertical-align:top;">

<div style="border:1px solid #000;padding:5px;margin-bottom:10px;">
FSSAI No. {{$invoice->fssai_no}}<br>
GSTIN : {{$invoice->gstin}}<br>
MSME : {{$invoice->msme}}
</div>

<div style="border:1px solid #000;padding:5px;font-size:12px;">
<b>NOTICE</b><br>
{{$invoice->notice}}
</div>

</td>

</tr>
</table>


<!-- CONSIGNMENT NOTE -->

<table style="width:100%;border-collapse:collapse;margin-top:10px;font-size:14px;">

<tr>

<td style="border:1px solid #000;width:200px;padding:8px;">
<b>CONSIGNMENT NOTE</b><br>
No. <span>{{$invoice->invoice_no}}</span><br>
Date {{ date('d/m/Y',strtotime($invoice->bill_date)) }}
</td>

<td style="border:1px solid #000;padding:8px;">
<b>TRUCK No.</b><br>
<span style="font-size:18px;">{{ $invoice->vehicle_no }}</span>
</td>

<td style="border:1px solid #000;width:150px;padding:8px;">
From : <br>
<b>{{ $invoice->origin ?? '' }}</b><br>
To :<br>
<b>{{ $invoice->destination ?? '' }}</b>
</td>

<td style="border:1px solid #000;width:150px;padding:8px;">
Indent / Reference No<br><br>
<b>{{ $invoice->indent_no ?? '' }}</b>
</td>

</tr>

</table>


<!-- CONSIGNOR -->

<div style="border:1px solid #000;border-top:none;padding:8px;font-size:14px;">
Consignor's Name & Address :
<b>{{ $consignor->plant_site_name ?? '' }}</b><br>
{{ $consignor->street_house_number ?? '' }}<br>
{{ $consignor->street1 ?? '' }} {{ $consignor->street2 ?? '' }}<br>
{{ $consignor->city ?? '' }} {{ $consignor->state ?? '' }}
{{ $consignor->post_code ?? '' }}
</div>

<div style="border:1px solid #000;border-top:none;padding:8px;font-size:14px;">
Consignee's Name & Address :
<b>{{ $consignee->plant_site_name ?? '' }}</b><br>
{{ $consignee->street_house_number ?? '' }}
{{ $consignee->street1 ?? '' }} {{ $consignee->street2 ?? '' }}<br>
{{ $consignee->city ?? '' }} {{ $consignee->state ?? '' }}
{{ $consignee->post_code ?? '' }}
</div>


<!-- PACKAGE TABLE -->

<table style="width:100%;border-collapse:collapse;font-size:13px;">

<tr style="font-weight:bold;">
<td style="border:1px solid #000;padding:5px;width:80px;">Packages</td>
<td style="border:1px solid #000;padding:5px;">Description <small>(Said to Contain)</td>
<td style="border:1px solid #000;padding:5px;width:80px;">Actual</td>
<td style="border:1px solid #000;padding:5px;width:90px;">Weight Charged</td>
<td style="border:1px solid #000;padding:5px;width:60px;">Rate</td>
<td style="border:1px solid #000;padding:5px;width:50px;">Amount</td>
</tr>

<tr>

<td style="border:1px solid #000;padding:5px;font-size:18px;">
{{ $invoice->packages }}
</td>

<td style="border:1px solid #000;padding:5px;height:120px;font-size:14px;">
{{ $invoice->description }}
</td>

<td style="border:1px solid #000;text-align:center;">
{{ $invoice->actual_weight ?? ''}}
</td>

<td style="border:1px solid #000;text-align:center;">
{{ $invoice->charged_weight ?? ''}}
</td>

<td style="border:1px solid #000;"></td>

<td style="border:1px solid #000;padding:0;">

<table style="width:100%;border-collapse:collapse;font-size:12px;">

<tr>
<td style="border-bottom:1px solid #000;padding:4px;">Sur Ch.</td>
<td style="border-bottom:1px solid #000;text-align:center;">{{ $invoice->sur_charge }}</td>
</tr>

<tr>
<td style="border-bottom:1px solid #000;padding:4px;">Hamali</td>
<td style="border-bottom:1px solid #000;text-align:center;">{{ $invoice->hamali }}</td>
</tr>

<tr>
<td style="border-bottom:1px solid #000;padding:4px;">Risk Ch.</td>
<td style="border-bottom:1px solid #000;text-align:center;">{{ $invoice->risk_charge }}</td>
</tr>

<tr>
<td style="border-bottom:1px solid #000;padding:4px;">B. Charge</td>
<td style="border-bottom:1px solid #000;text-align:center;">{{ $invoice->b_charge }}</td>
</tr>

<tr>
<td style="border-bottom:1px solid #000;padding:4px;">Other Ch.</td>
<td style="border-bottom:1px solid #000;text-align:center;">{{ $invoice->other_charge }}</td>
</tr>

<tr>
<td style="border-bottom:1px solid #000;padding:4px;">St Ch.</td>
<td style="border-bottom:1px solid #000;text-align:center;">{{ $invoice->st_charge }}</td>
</tr>

<tr>
<td style="padding:4px;"><b>TOTAL</b></td>
<td style="text-align:right;"><b>{{ $invoice->total_amount }}</b></td>
</tr>

</table>

</td>

</tr>

</table>


<!-- FOOTER -->

<table style="width:100%;font-size:13px;border-top:2px solid #000;">
<tr>
<td>
Carriers is not Responsible for Leakage & Breakages
</td>

<td style="text-align:right;">
For <b>{{$vendor->vendor_name}}</b>
</td>
</tr>
</table>

</div>

</body>
</html>
