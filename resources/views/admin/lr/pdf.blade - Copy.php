<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>LR Copy</title>

<style>

body{
    font-family: "Times New Roman", serif;
    font-size:13px;
    margin:0;
    padding:0;
}

table{
    width:100%;
    border-collapse:collapse;
}

td,th{
    border:1px solid #000;
    padding:5px;
    vertical-align:top;
}

.no-border{
    border:none;
}

.center{
    text-align:center;
}

.right{
    text-align:right;
}

.bold{
    font-weight:bold;
}

.header{
    font-size:20px;
    font-weight:bold;
}

.small{
    font-size:12px;
}

</style>

</head>

<body>

<table>

<tr>
<td class="no-border small">
CIN {{$vendor->cin ?? '-'}}
</td>

<td class="no-border center small">
Subject to Bangalore Jurisdiction only
</td>

<td class="no-border right small">
Ph. {{$vendor->authorized_person_phone}}<br>

</td>
</tr>

<tr>
<td colspan="3" class="center header">
{{$vendor->vendor_name}}
</td>
</tr>

<tr>
<td colspan="3" class="center small">
{{ $invoice->registeredAddress->address_line1 ?? '' }} {{ $invoice->registeredAddress->address_line2 ?? ''  }} {{ $invoice->registeredAddress->city ?? ''  }} {{ $invoice->registeredAddress->state ?? ''  }} - {{ $invoice->registeredAddress->zip_code ?? ''  }}<br>
Email : {{$vendor->authorized_person_mail}}
</td>
</tr>

</table>

<br>

<table>

<tr>

<td width="35%">
<b>CAUTION</b><br><br>
{{$invoice->caution}}

Address of Delivery Office:
</td>

<td width="30%" class="center">
<b>LORRY COPY</b><br>
AT OWNER'S RISK<br><br>

<b>INSURANCE</b>

<table style="border:none">

<tr>
<td style="border:none">{{$invoice->insurance}}</td>
</tr>


</table>

</td>

<td width="35%">
<b>GSTIN :</b> {{$invoice->gstin}}<br>
<b>MSME :</b> {{$invoice->msme}}<br>
<b>FSSAI :</b> {{$invoice->fssai_no}}<br>
</td>

</tr>

</table>

<br>

<table>

<tr>

<td width="50%">
<b>CONSIGNMENT NOTE</b><br><br>

No : <b>{{ $invoice->lr_number }}</b><br><br>

Date : {{ date('d/m/Y',strtotime($invoice->bill_date)) }}

</td>

<td width="25%">
<b>TRUCK No :</b><br><br>

{{ $invoice->vehicle_no }}

</td>

<td width="25%">
<b>From</b><br><br>
{{ $invoice->origin ?? '' }}
<b>To</b><br><br>
{{ $invoice->destination ?? '' }}
</td>

</tr>

</table>

<table>

<tr>
<td>
<b>Consignor's Name & Address :</b><br>

{{ $consignor->plant_site_name ?? '' }}<br>
{{ $consignor->street_house_number ?? '' }}<br>
{{ $consignor->street1 ?? '' }}  {{ $consignor->street2 ?? '' }}<br>{{ $consignor->city ?? '' }} {{ $consignor->state ?? '' }}
{{ $consignor->post_code ?? '' }}

</td>
</tr>

<tr>
<td>
<b>Consignee's Name & Address :</b><br>

{{ $consignee->plant_site_name ?? '' }}<br>
{{ $consignee->street_house_number ?? '' }}, {{ $consignee->street1 ?? '' }}  {{ $consignee->street2 ?? '' }}<br {{ $consignee->city ?? '' }} {{ $consignee->state ?? '' }}
{{ $consignee->post_code ?? '' }}


</td>
</tr>

</table>

<br>

<table>

<tr>

<th width="8%">Packages</th>
<th width="32%">Description (Said to Contain)</th>
<th width="8%">Actual</th>
<th width="10%">Weight Charged</th>
<th width="15%">Rate</th>
<th width="27%">Amount To Pay / Paid / TBB</th>

</tr>

<tr>

<td>{{ $invoice->packages }}</td>

<td>{{ $invoice->description }}</td>

<td class="center">{{ $invoice->actual_weight }}</td>

<td class="center">{{ $invoice->charged_weight }}</td>

<td>

<table style="border:none;width:100%">

<tr>
<td style="border:none">Sur Ch.</td>
</tr>

<tr>
<td style="border:none">Hamali</td>
</tr>

<tr>
<td style="border:none">Risk Ch.</td>
</tr>

<tr>
<td style="border:none">B. Charge</td>
</tr>

<tr>
<td style="border:none">Other Ch.</td>
</tr>

<tr>
<td style="border:none">St. Ch.</td>
</tr>

<tr>
<td style="border:none"><b>Total</b></td>
</tr>

</table>

</td>

<td>

<table style="border:none;width:100%">

<tr>
<td style="border:none" class="right">{{ $invoice->sur_charge }}</td>
</tr>

<tr>
<td style="border:none" class="right">{{ $invoice->hamali }}</td>
</tr>

<tr>
<td style="border:none" class="right">{{ $invoice->risk_charge }}</td>
</tr>

<tr>
<td style="border:none" class="right">{{ $invoice->b_charge }}</td>
</tr>

<tr>
<td style="border:none" class="right">{{ $invoice->other_charge }}</td>
</tr>

<tr>
<td style="border:none" class="right">{{ $invoice->st_charge }}</td>
</tr>

<tr>
<td style="border:none" class="right bold">{{ $invoice->total_amount }}</td>
</tr>

</table>

</td>

</tr>

</table>

<br>

<table>

<tr>

<td class="no-border small">
Carriers is not Responsible for Leakage & Breakages
</td>

<td class="no-border right small">
For {{$vendor->name}}
</td>

</tr>

</table>

</body>
</html>
