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
            font-size: 18px;
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
            margin: 15px 0 10px 0;
            text-transform: uppercase;
        }
        .invoice-meta {
            text-align: right;
            margin-bottom: 20px;
        }
        .invoice-meta-line {
            margin: 3px 0;
        }
        .recipient-section {
            margin-bottom: 20px;
        }
        .recipient-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .recipient-name {
            font-weight: normal;
            margin-bottom: 5px;
        }
        .recipient-address {
            font-size: 11px;
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
        .items-table .col-marks {
            width: 20%;
        }
        .items-table .col-description {
            width: 50%;
        }
        .items-table .col-qty {
            width: 15%;
            text-align: center;
        }
        .items-table .col-amount {
            width: 15%;
            text-align: center;
        }
        .item-description {
            line-height: 1.6;
        }
        .item-detail-line {
            margin-bottom: 3px;
        }
        .summary-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .summary-row td {
            text-align: center;
        }
        .summary-label {
            text-align: left !important;
        }
        .final-amount-row {
            border-top: 2px solid #000;
        }
        .delivery-terms {
            margin-top: 25px;
            font-size: 11px;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        .section-number {
            font-weight: bold;
        }
        .bank-section {
            margin-top: 20px;
            font-size: 11px;
            line-height: 1.8;
        }
        .bank-label {
            font-weight: bold;
            text-transform: uppercase;
        }
        .bank-info {
            margin-bottom: 8px;
        }
        .footer-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .footer-left {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
            text-align: left;
        }
        .footer-right {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
            text-align: right;
        }
        .company-logo {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .signature-area {
            margin-top: 30px;
            min-height: 60px;
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

    <!-- Invoice Number and Date (Right-aligned) -->
    <div class="invoice-meta">
        <div class="invoice-meta-line">
            <strong>INVOICE NO:</strong> {{ $proformaInvoice->proforma_invoice_number }}
        </div>
        <div class="invoice-meta-line">
            <strong>DATE:</strong> {{ $proformaInvoice->created_at->format('d-m-Y') }}
        </div>
    </div>

    <!-- Recipient Information -->
    <div class="recipient-section">
        <div class="recipient-label">TO:</div>
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

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-marks">SHIPPING MARKS</th>
                <th class="col-description">GOOD DESCRIPTION</th>
                <th class="col-qty">QUANTITY (sets)</th>
                <th class="col-amount">TOTAL AMOUNT (USD)</th>
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
                    
                    // Build detailed description
                    $descriptionParts = [];
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
                    
                    // Get shipping mark (category name)
                    $shippingMark = $contractMachine->machineCategory->name ?? 'N/A';
                @endphp
                <tr>
                    <td class="col-marks">{{ $shippingMark }}</td>
                    <td class="col-description">
                        <div class="item-description">
                            <div class="item-detail-line">{{ $mainDescription }}</div>
                            @if($contractMachine->wir)
                            <div class="item-detail-line">WIR {{ $contractMachine->wir->name }}</div>
                            @endif
                            @if($contractMachine->hsnCode)
                            <div class="item-detail-line">HSN Code: {{ $contractMachine->hsnCode->name }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="col-qty">{{ $piMachine->quantity }}</td>
                    <td class="col-amount">{{ number_format($lineTotal, 0) }}</td>
                </tr>
            @endforeach
            
            <!-- Summary Rows -->
            <tr class="summary-row">
                <td colspan="2" class="summary-label">Total PI Price</td>
                <td class="col-qty"></td>
                <td class="col-amount">{{ number_format($totalAmount, 0) }}</td>
            </tr>
            <tr class="summary-row">
                <td colspan="2" class="summary-label">Final Amount</td>
                <td class="col-qty"></td>
                <td class="col-amount">{{ $proformaInvoice->currency === 'INR' ? '₹' : 'USD' }} {{ number_format($proformaInvoice->total_amount, 2) }}</td>
            </tr>
            <tr class="summary-row final-amount-row">
                <td colspan="3" class="summary-label">Final Amount: {{ $proformaInvoice->currency === 'INR' ? '₹' : '$' }} {{ number_format($proformaInvoice->total_amount, 0) }}</td>
                <td class="col-amount"></td>
            </tr>
        </tbody>
    </table>

    <!-- Delivery Terms Section -->
    <div class="delivery-terms">
        <span class="section-number">1. DELIVERY TERMS:</span> 
        @if($proformaInvoice->contract->loading_terms)
            {{ strtoupper($proformaInvoice->contract->loading_terms) }}
        @else
            WITHIN 60 DAYS AFTER RECEIVING PAYMENT
        @endif
    </div>

    <!-- Bank Information Section -->
    @if($proformaInvoice->seller->bankDetails->count() > 0)
        @php
            $firstBank = $proformaInvoice->seller->bankDetails->first();
        @endphp
        <div class="bank-section">
            <div class="bank-info">
                <span class="section-number">2. BANK INFORMATION:</span>
            </div>
            <div class="bank-info">
                <span class="bank-label">BENEFICIARY NAME:</span> {{ $proformaInvoice->seller->seller_name }}
            </div>
            <div class="bank-info">
                <span class="bank-label">BENEFICIARY ADDRESS:</span> {{ $proformaInvoice->seller->address }}
            </div>
            <div class="bank-info">
                <span class="bank-label">BANK ACCOUNT NO.:</span> {{ $firstBank->account_number }}
            </div>
            @if($firstBank->bank_address)
            <div class="bank-info">
                <span class="bank-label">BANK ADDRESS:</span> {{ $firstBank->bank_address }}
            </div>
            @endif
            @if($firstBank->ifsc_code)
            <div class="bank-info">
                <span class="bank-label">SWIFT BIC:</span> {{ $firstBank->ifsc_code }}
            </div>
            @endif
        </div>
    @endif

    <!-- Footer Section -->
    <div class="footer-section">
        <div class="footer-left">
            @if($proformaInvoice->seller->logo)
            <div class="company-logo">
                <img src="{{ asset('storage/' . $proformaInvoice->seller->logo) }}" alt="Company Logo" style="max-height: 60px;">
            </div>
            @endif
            <div class="company-logo">
                {{ $proformaInvoice->seller->seller_name ?? 'Company Name' }}
            </div>
        </div>
        <div class="footer-right">
            <div class="signature-area">
                @if($proformaInvoice->seller->signature)
                <img src="{{ asset('storage/' . $proformaInvoice->seller->signature) }}" alt="Signature" style="max-height: 50px;">
                @endif
            </div>
        </div>
    </div>
</body>
</html>
