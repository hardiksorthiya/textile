<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <title>Proforma Invoice - {{ $proformaInvoice->proforma_invoice_number }}</title>
    <style>
        @page {
            margin-top: 10mm;
            margin-bottom: 10mm;
            margin-left: 15mm;
            margin-right: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .company-header {
            text-align: center;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .company-address {
            font-size: 11px;
            margin-bottom: 10px;
        }
        .separator-line {
            border-top: 1px solid #000;
            margin: 10px 0 15px 0;
        }
        .invoice-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 15px 0 20px 0;
            text-transform: uppercase;
        }
        .invoice-meta {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .invoice-number {
            display: table-cell;
            text-align: left;
            width: 50%;
        }
        .invoice-date {
            display: table-cell;
            text-align: right;
            width: 50%;
        }
        .recipient-section {
            margin-bottom: 20px;
        }
        .recipient-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .recipient-box {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 5px;
            min-height: 80px;
        }
        .recipient-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .recipient-address {
            font-size: 10px;
            line-height: 1.4;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 10px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px 5px;
            text-align: left;
            vertical-align: top;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .items-table .col-sno {
            width: 8%;
            text-align: center;
        }
        .items-table .col-description {
            width: 60%;
        }
        .items-table .col-qty {
            width: 12%;
            text-align: center;
        }
        .items-table .col-amount {
            width: 20%;
            text-align: right;
        }
        .item-description {
            line-height: 1.6;
        }
        .item-detail-line {
            margin-bottom: 3px;
        }
        .item-category {
            font-weight: bold;
        }
        .summary-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .summary-row td {
            text-align: right;
            padding-right: 15px;
        }
        .summary-label {
            text-align: left !important;
            padding-left: 15px;
        }
        .final-amount-row {
            border-top: 2px solid #000;
        }
        .payment-terms {
            margin-top: 25px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        .beneficiary-section {
            margin-top: 20px;
            font-size: 11px;
            line-height: 1.8;
        }
        .beneficiary-label {
            font-weight: bold;
            text-transform: uppercase;
        }
        .beneficiary-info {
            margin-bottom: 8px;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-left {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
        }
        .signature-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: bottom;
        }
        .signature-box {
            display: inline-block;
            border: 1px solid #000;
            padding: 20px;
            min-width: 150px;
            min-height: 80px;
            text-align: center;
        }
        .signature-name {
            margin-top: 40px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Company Header -->
    <div class="company-header">
        <div class="company-name">{{ $proformaInvoice->seller->seller_name ?? 'COMPANY NAME' }}</div>
        @if($proformaInvoice->seller->address)
        <div class="company-address">{{ $proformaInvoice->seller->address }}</div>
        @endif
    </div>

    <!-- Separator Line -->
    <div class="separator-line"></div>

    <!-- Invoice Title -->
    <div class="invoice-title">PROFORMA INVOICE</div>

    <!-- Invoice Number and Date -->
    <div class="invoice-meta">
        <div class="invoice-number">
            <strong>INVOICE NO.:</strong>{{ $proformaInvoice->proforma_invoice_number }}
        </div>
        <div class="invoice-date">
            <strong>Date:</strong> {{ $proformaInvoice->created_at->format('d-m-Y') }}
        </div>
    </div>

    <!-- Recipient Information -->
    <div class="recipient-section">
        <div class="recipient-label">To</div>
        <div class="recipient-box">
            <div class="recipient-name">{{ $proformaInvoice->buyer_company_name }}</div>
            <div class="recipient-address">
                @if($proformaInvoice->billing_address)
                    {{ $proformaInvoice->billing_address }}
                @elseif($proformaInvoice->shipping_address)
                    {{ $proformaInvoice->shipping_address }}
                @else
                    {{ $proformaInvoice->contract->contact_address ?? 'Address not provided' }}
                @endif
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-sno">S. No.</th>
                <th class="col-description">Item Description (TEXTILE MACHINE)</th>
                <th class="col-qty">Qty</th>
                <th class="col-amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
            @endphp
            @foreach($proformaInvoice->proformaInvoiceMachines as $index => $piMachine)
                @php
                    $contractMachine = $piMachine->contractMachine;
                    $unitPrice = $piMachine->pi_price_plus_amc ?? ($piMachine->amount + ($piMachine->amc_price ?? 0));
                    $lineTotal = $unitPrice * $piMachine->quantity;
                    $totalAmount += $lineTotal;
                    
                    // Build detailed description in specific format
                    $descriptionParts = [];
                    if($contractMachine->machineCategory) {
                        $descriptionParts[] = 'category: ' . $contractMachine->machineCategory->name;
                    }
                    if($contractMachine->machineModel) {
                        $descriptionParts[] = 'Model: ' . $contractMachine->machineModel->model_no;
                    }
                    if($contractMachine->machineHook) {
                        $hookValue = is_numeric($contractMachine->machineHook->name) ? $contractMachine->machineHook->name : ($contractMachine->machineHook->name ?? '');
                        if($hookValue) {
                            $descriptionParts[] = 'Hooks: ' . $hookValue;
                        }
                    }
                    if($contractMachine->machineBeam) {
                        $beamName = $contractMachine->machineBeam->name ?? '';
                        $descriptionParts[] = 'Beam: ' . $beamName . ' : ' . $piMachine->quantity . ' pieces';
                    }
                    if($contractMachine->machineClothRoller) {
                        $descriptionParts[] = 'Cloth Roller: ' . $piMachine->quantity . ' pieces';
                    }
                    if($contractMachine->machineChain) {
                        $chainName = $contractMachine->machineChain->name ?? 'CAM';
                        $descriptionParts[] = 'CAM-Chain: ' . $chainName;
                    }
                    if($contractMachine->machineHealdWire) {
                        $healdValue = is_numeric($contractMachine->machineHealdWire->name) ? $contractMachine->machineHealdWire->name : ($contractMachine->machineHealdWire->name ?? '');
                        if($healdValue) {
                            $descriptionParts[] = 'Heald Wires: ' . $healdValue;
                        }
                    }
                    if($contractMachine->machineERead) {
                        $descriptionParts[] = 'E Read: ' . $piMachine->quantity . ' pieces';
                    }
                    
                    $mainDescription = implode(', ', $descriptionParts);
                @endphp
                <tr>
                    <td class="col-sno">{{ $index + 1 }}</td>
                    <td class="col-description">
                        <div class="item-description">
                            <div class="item-detail-line">{{ $mainDescription }}</div>
                            @if($contractMachine->wir)
                            <div class="item-detail-line">WIR - {{ $contractMachine->wir->name }}</div>
                            @endif
                            @if($contractMachine->hsnCode)
                            <div class="item-detail-line">HSN CODE - {{ $contractMachine->hsnCode->name }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="col-qty">{{ $piMachine->quantity }}</td>
                    <td class="col-amount">{{ $proformaInvoice->currency === 'INR' ? '₹' : '$' }}{{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
            
            <!-- Summary Rows -->
            <tr class="summary-row">
                <td colspan="3" class="summary-label">Total PI Price</td>
                <td class="col-amount">{{ $proformaInvoice->currency === 'INR' ? '₹' : '$' }}{{ number_format($totalAmount, 2) }}</td>
            </tr>
            <tr class="summary-row final-amount-row">
                <td colspan="3" class="summary-label">Final Amount</td>
                <td class="col-amount">{{ $proformaInvoice->currency === 'INR' ? '₹' : '$' }}{{ number_format($proformaInvoice->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Payment Terms -->
    @if($proformaInvoice->contract->payment_terms)
    <div class="payment-terms">
        PAYMENT TERMS: {{ strtoupper($proformaInvoice->contract->payment_terms) }}
    </div>
    @else
    <div class="payment-terms">
        PAYMENT TERMS: 25% ADVANCE (PART PAYMENT ALLOWED) & 75% BALANCE BY T/T OR L/C BEFORE SHIPMENT
    </div>
    @endif

    <!-- Beneficiary Bank Details -->
    @if($proformaInvoice->seller->bankDetails->count() > 0)
        @php
            $firstBank = $proformaInvoice->seller->bankDetails->first();
        @endphp
        <div class="beneficiary-section">
            <div class="beneficiary-info">
                <span class="beneficiary-label">BENEFICIARY'S BANK:</span> {{ $firstBank->bank_name }}
                @if($firstBank->bank_address)
                    (FIELD 57A IN FIN103)
                @endif
            </div>
            @if($firstBank->bank_address)
            <div class="beneficiary-info">
                {{ $firstBank->bank_address }}
            </div>
            @endif
            @if($firstBank->ifsc_code)
            <div class="beneficiary-info">
                <span class="beneficiary-label">SWIFT CODE:</span> {{ $firstBank->ifsc_code }}
            </div>
            @endif
            <div class="beneficiary-info" style="margin-top: 15px;">
                <span class="beneficiary-label">BENEFICIARY'S NAME:</span> {{ $proformaInvoice->seller->seller_name }}
            </div>
            <div class="beneficiary-info">
                <span class="beneficiary-label">ACCOUNT NO.:</span> {{ $firstBank->account_number }}
            </div>
            <div class="beneficiary-info">
                <span class="beneficiary-label">ADDRESS:</span> {{ $proformaInvoice->seller->address }}
            </div>
        </div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-left">
            <!-- Space for company seal/logo -->
            @if($proformaInvoice->seller->logo)
            <div class="signature-box">
                <img src="{{ asset('storage/' . $proformaInvoice->seller->logo) }}" alt="Company Seal" style="max-width: 100%; max-height: 60px;">
            </div>
            @elseif($proformaInvoice->seller->signature)
            <div class="signature-box">
                <img src="{{ asset('storage/' . $proformaInvoice->seller->signature) }}" alt="Signature" style="max-width: 100%; max-height: 60px;">
            </div>
            @else
            <div class="signature-box">
                <!-- Empty box for seal -->
            </div>
            @endif
        </div>
        <div class="signature-right">
            <div class="signature-name">{{ $proformaInvoice->seller->seller_name ?? 'Authorized Signature' }}</div>
        </div>
    </div>
</body>
</html>
