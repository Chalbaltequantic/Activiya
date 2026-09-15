@extends('admin.admin')

@section('bodycontent')

<style>
.claim-page {
    background: #ffffff;
    color: #000000;
    padding: 10px;
    font-family: Arial, Helvetica, sans-serif;
}

.claim-toolbar {
    margin-bottom: 20px;
}

.claim-heading {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 5px;
}

.claim-policy-table {
    width: auto;
    margin-bottom: 20px;
    font-size: 14px;
}

.claim-policy-table td {
    padding: 3px 10px 3px 0;
}

.claim-loss-date {
    font-size: 15px;
    margin-top: 15px;
    margin-bottom: 15px;
}

.claim-table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.claim-table {
    width: 100%;
    min-width: 1500px;
    border-collapse: collapse;
    font-size: 12px;
}

.claim-table th,
.claim-table td {
    border: 1px solid #000000 !important;
    padding: 6px 7px;
    vertical-align: middle;
}

.claim-table th {
    background: #fce4d6 !important;
    color: #0070c0 !important;
    text-align: center;
    font-weight: 600;
    white-space: nowrap;
}

.claim-table td {
    background: #ffffff;
}

.claim-table .nowrap {
    white-space: nowrap;
}

.claim-table .text-right {
    text-align: right;
}

.claim-table .text-center {
    text-align: center;
}

.claim-table .voyage {
    min-width: 170px;
}

.claim-table .transporter {
    min-width: 150px;
}

.claim-table .product-name {
    min-width: 180px;
}

.claim-table .claim-number {
    min-width: 140px;
}

.claim-table .amount {
    min-width: 90px;
}

.claim-total-label {
    text-align: right;
    font-weight: 600;
}

.claim-total-value {
    text-align: right;
    font-weight: bold;
}

.claim-final-row td {
    font-weight: bold;
}

.insurance-list{min-width:2200px;font-size:12px}
.insurance-list th,.insurance-list td{white-space:nowrap;padding:5px;vertical-align:middle}
.insurance-list thead th{position:sticky;top:0;background:#fce4d6;color:#0070c0;z-index:2}

@media print {

    @page {
        size: A4 landscape;
        margin: 7mm;
    }

    body {
        background: #ffffff !important;
    }

    .main-header,
    .main-sidebar,
    .main-footer,
    .content-header,
    .claim-toolbar,
    .no-print {
        display: none !important;
    }

    .content-wrapper {
        margin-left: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        min-height: auto !important;
    }

    .content {
        padding: 0 !important;
        margin: 0 !important;
    }

    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .claim-page {
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .claim-table-wrapper {
        overflow: visible !important;
    }

    .claim-table {
        width: 100% !important;
        min-width: 0 !important;
        font-size: 9px !important;
    }

    .claim-table th,
    .claim-table td {
        padding: 3px 4px !important;
    }

    .claim-table th {
        background: #fce4d6 !important;
        color: #0070c0 !important;

        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .claim-table .voyage,
    .claim-table .transporter,
    .claim-table .product-name,
    .claim-table .claim-number,
    .claim-table .amount {
        min-width: 0 !important;
    }
}
</style>

<div class="content">

    <div class="container-fluid">

        <div class="claim-page">

            <div class="claim-toolbar no-print">

                <a href="{{ route('admin.insurance.claim-bills') }}"
                    class="btn btn-secondary">

                    <i class="fas fa-arrow-left"></i>
                    Back

                </a>

                <button type="button"
                    class="btn btn-primary"
                    onclick="window.print()">

                    <i class="fas fa-print"></i>
                    Print

                </button>

            </div>

            <div class="claim-heading">

                CLAIM BILL LOG

            </div>

            <table class="claim-policy-table">

                <tr>

                    <td>
                        <strong>POLICY NO.</strong>
                    </td>

                    <td>
                        {{ $policyNo ?: '-' }}
                    </td>

                </tr>

                <tr>

                    <td>
                        <strong>PERIOD :</strong>
                    </td>

                    <td>

                        @if($policyFrom || $policyTo)

                        {{ $policyFrom }}
                        TO
                        {{ $policyTo }}

                        @else

                        -

                        @endif

                    </td>

                </tr>

            </table>

            <div class="claim-loss-date">

                <strong>Date of Loss :</strong>

                {{ $insurance->loss_date ? $insurance->loss_date->format('d/m/Y') : '-' }}

            </div>

            <div class="claim-table-wrapper">
                <table class="claim-table">
                    <thead>
                        <tr>
							<th>Claim No.</th>
                            <th>Date</th>
                            <th>Voyage From</th>
                            <th>Invoice No.</th>
							<th>Date</th>
							<th>Transporter's<br>Name</th>
							<th>LR No.</th>
                            <th>LR Date</th>
                            <th>Nature of<br>Claim</th>
                            <th>Product<br>Code</th>
                            <th>Name of Product</th>
							<th>Rate</th>
							<th>Quantity</th>
							<th>Claim Rs.</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($insurance->products as $product)

                        @php
                            $damageQty = (float)$product->damage_quantity;
                            $shortageQty = (float)$product->shortage_quantity;

                            $quantity = $damageQty + $shortageQty;

                            if ($damageQty > 0 && $shortageQty > 0) {
                                $nature = 'DAMAGE/SHORTAGE';
                            } elseif ($damageQty > 0) {
                                $nature = 'DAMAGE';
                            } elseif ($shortageQty > 0) {
                                $nature = 'SHORTAGE';
                            } else {
                                $nature = strtoupper($insurance->nature_of_claim ?? '');
                            }
                        @endphp

                        <tr>
                            <td class="claim-number nowrap">{{ $insurance->claim_no ?: '-' }}</td>
                            <td class="nowrap">{{ $insurance->loss_date ? $insurance->loss_date->format('d/m/Y') : '' }}</td>
                            <td class="voyage">
                                {{ strtoupper($insurance->from_location ?? '') }}

                                @if($insurance->from_location || $insurance->to_location)
                                TO
                                @endif

                                {{ strtoupper($insurance->to_location ?? '') }}

                            </td>

                            <td class="nowrap">{{ $product->invoice_no ?: $insurance->invoice_no }}</td>

                            <td class="nowrap">{{ $insurance->invoice_date ? $insurance->invoice_date->format('d/m/Y') : '' }}</td>
                            <td class="transporter">{{ strtoupper($insurance->transporter_name ?? '') }}</td>
                            <td class="nowrap">{{ $product->lr_no ?: $insurance->lr_no }}</td>
                            <td class="nowrap">{{ $insurance->lr_date ? $insurance->lr_date->format('d/m/Y') : '' }}</td>
                            <td class="text-center">{{ $nature }}</td>

                            <td class="text-center">{{ $product->product_code }}</td>

                            <td class="product-name">{{ $product->product_description }}</td>

                            <td class="text-right">{{ number_format((float)$product->recovery_mrp, 2) }}</td>

                            <td class="text-right">{{ number_format($quantity, 3) }}</td>
                            <td class="text-right amount">{{ number_format((float)$product->total_value, 2) }}</td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="14" class="text-center">
                                No products have been added to this Insurance claim.
                            </td>
                        </tr>
                        @endforelse
                        <tr>

                            <td colspan="12"></td>

                            <td class="claim-total-label">
                                Total
                            </td>

                            <td class="claim-total-value">

                                {{ number_format($basicClaimAmount, 2) }}

                            </td>

                        </tr>

                        <tr>

                            <td colspan="12"></td>

                            <td class="claim-total-label">
                                Add GST {{ $gstPercent }}%
                            </td>

                            <td class="claim-total-value">
                                {{ number_format($gstAmount, 2) }}

                            </td>

                        </tr>

                        <tr>

                            <td colspan="12"></td>

                            <td class="claim-total-label">
                                Sub Total
                            </td>

                            <td class="claim-total-value">

                                {{ number_format($amountAfterGst, 2) }}

                            </td>

                        </tr>

                        <tr>

                            <td colspan="12"></td>

                            <td class="claim-total-label">

                                Add {{ $additionalPercent }}%

                            </td>

                            <td class="claim-total-value">

                                {{ number_format($additionalAmount, 2) }}

                            </td>

                        </tr>

                        <tr class="claim-final-row">

                            <td colspan="12"></td>

                            <td class="claim-total-label">

                                Final Claim Amount

                            </td>

                            <td class="claim-total-value">

                                {{ number_format($finalClaimAmount, 2) }}

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection