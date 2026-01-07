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
        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .firm-logo {
            margin-bottom: 10px;
        }
        .firm-logo img {
            max-height: 80px;
            max-width: 400px;
        }
        .firm-name {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .firm-address {
            font-size: 11px;
            margin-bottom: 15px;
        }
        .invoice-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .invoice-details-section {
            margin: 20px 0;
        }
        .seller-buyer-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .seller-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-meta-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .info-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-value {
            margin-bottom: 8px;
        }
        .invoice-meta-line {
            margin: 5px 0;
            text-decoration: underline;
        }
        .from-to-section {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        .from-info {
            display: table-cell;
            width: 50%;
        }
        .to-info {
            display: table-cell;
            width: 50%;
            text-align: right;
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
        .items-table .col-description {
            width: 60%;
        }
        .items-table .col-qty {
            width: 15%;
            text-align: center;
        }
        .items-table .col-amount {
            width: 25%;
            text-align: right;
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
            text-align: right;
        }
        .summary-label {
            text-align: left !important;
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
        .bank-section {
            margin-top: 20px;
            font-size: 11px;
            line-height: 1.8;
        }
        .bank-title {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .bank-info {
            margin-bottom: 5px;
        }
        .bank-label {
            font-weight: bold;
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
        }
        .footer-right {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
            text-align: right;
        }
        .company-logo-footer {
            margin-bottom: 10px;
        }
        .seller-name-footer {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .signature-area {
            margin-top: 40px;
            min-height: 60px;
        }
    </style>
</head>
<body>
    <!-- Header Section with Firm Logo/Name -->
    <div class="header-section">
        @php
            $businessFirm = $proformaInvoice->contract->businessFirm ?? null;
            $firmLogo = $businessFirm->logo ?? null;
            $firmName = $businessFirm->name ?? ($proformaInvoice->seller->seller_name ?? 'COMPANY NAME');
            $firmAddress = $businessFirm->address ?? ($proformaInvoice->seller->address ?? '');
        @endphp
        
        @if($firmLogo)
            <div class="firm-logo">
                <img src="{{ asset('storage/' . $firmLogo) }}" alt="{{ $firmName }}">
            </div>
        @else
            <div class="firm-name">
                {{ $firmName }}
            </div>
        @endif
        
        @if($firmAddress)
        <div class="firm-address">{{ $firmAddress }}</div>
        @endif
    </div>

    <!-- Invoice Title -->
    <div class="invoice-title">PROFORMA INVOICE</div>

    <!-- Invoice Details Section -->
    <div class="invoice-details-section">
        <div class="seller-buyer-info">
            <div class="seller-info">
                <div class="info-label">The Seller:</div>
                <div class="info-value">{{ $firmName }}</div>
                @if($firmAddress)
                <div class="info-value" style="font-size: 10px;">{{ $firmAddress }}</div>
                @endif
            </div>
            <div class="invoice-meta-right">
                <div class="invoice-meta-line">
                    <strong>PI No:</strong> {{ $proformaInvoice->proforma_invoice_number }}
                </div>
                <div class="invoice-meta-line">
                    <strong>Date:</strong> {{ $proformaInvoice->created_at->format('d-m-Y') }}
                </div>
            </div>
        </div>

        <div class="seller-info" style="margin-top: 15px;">
            <div class="info-label">The Buyer:</div>
            <div class="info-value">{{ $proformaInvoice->buyer_company_name }}</div>
            <div class="info-value" style="font-size: 10px;">
                @if($proformaInvoice->billing_address)
                    {{ $proformaInvoice->billing_address }}
                @elseif($proformaInvoice->shipping_address)
                    {{ $proformaInvoice->shipping_address }}
                @else
                    {{ $proformaInvoice->contract->contact_address ?? 'Address not provided' }}
                @endif
            </div>
        </div>

        <div class="from-to-section">
            <div class="from-info">
                <strong>From:</strong> {{ $firmName }}
            </div>
            <div class="to-info">
                <strong>To:</strong> {{ $proformaInvoice->buyer_company_name }}
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-description">Description</th>
                <th class="col-qty">Quantity</th>
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
                    
                    // Build detailed description
                    $descriptionParts = [];
                    if($contractMachine->machineCategory) {
                        $descriptionParts[] = 'category: ' . $contractMachine->machineCategory->name;
                    }
                    if($contractMachine->machineModel) {
                        $descriptionParts[] = 'Model: ' . $contractMachine->machineModel->model_no;
                    }
                    
                    $firstLine = implode(', ', $descriptionParts);
                    
                    $secondLineParts = [];
                    if($contractMachine->machineHook) {
                        $hookValue = is_numeric($contractMachine->machineHook->name) ? $contractMachine->machineHook->name : ($contractMachine->machineHook->name ?? '');
                        if($hookValue) {
                            $secondLineParts[] = 'Hooks: ' . $hookValue;
                        }
                    }
                    if($contractMachine->machineBeam) {
                        $beamName = $contractMachine->machineBeam->name ?? '';
                        $secondLineParts[] = 'Beam: ' . $beamName . ' : ' . $piMachine->quantity . ' pieces';
                    }
                    if($contractMachine->machineClothRoller) {
                        $secondLineParts[] = 'Cloth Roller: ' . $piMachine->quantity . ' pieces';
                    }
                    if($contractMachine->machineChain) {
                        $chainName = $contractMachine->machineChain->name ?? 'CAM';
                        $secondLineParts[] = 'CAM-Chain: ' . $chainName;
                    }
                    if($contractMachine->machineHealdWire) {
                        $healdValue = is_numeric($contractMachine->machineHealdWire->name) ? $contractMachine->machineHealdWire->name : ($contractMachine->machineHealdWire->name ?? '');
                        if($healdValue) {
                            $secondLineParts[] = 'Heald Wires: ' . $healdValue;
                        }
                    }
                    if($contractMachine->machineERead) {
                        $secondLineParts[] = 'E Read: ' . $piMachine->quantity . ' pieces';
                    }
                    
                    $secondLine = implode(', ', $secondLineParts);
                @endphp
                <tr>
                    <td class="col-description">
                        <div class="item-description">
                            @if($firstLine)
                            <div class="item-detail-line">{{ $firstLine }},</div>
                            @endif
                            @if($secondLine)
                            <div class="item-detail-line">{{ $secondLine }}</div>
                            @endif
                            @if($contractMachine->wir)
                            <div class="item-detail-line">WIR {{ $contractMachine->wir->name }}</div>
                            @endif
                            @if($contractMachine->hsnCode)
                            <div class="item-detail-line">HSN {{ $contractMachine->hsnCode->name }}</div>
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
                <td class="col-amount">{{ number_format($totalAmount, 0) }}</td>
            </tr>
            <tr class="summary-row">
                <td colspan="2" class="summary-label">Final Amount</td>
                <td class="col-amount">{{ number_format($proformaInvoice->total_amount, 2) }}</td>
            </tr>
            <tr class="summary-row final-amount-row">
                <td colspan="2" class="summary-label">Final Amount</td>
                <td class="col-amount">{{ $proformaInvoice->currency === 'INR' ? '₹' : '$' }} {{ number_format($proformaInvoice->total_amount, 0) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Payment Terms -->
    <div class="payment-terms">
        PAYMENT TERMS: 
        @if($proformaInvoice->contract->payment_terms)
            {{ strtoupper($proformaInvoice->contract->payment_terms) }}
        @else
            25% ADVANCE (PART PAYMENT ALLOWED) & 75% BALANCE BY T/T OR L/C BEFORE SHIPMENT
        @endif
    </div>

    <!-- Beneficiary Bank Information -->
    @if($proformaInvoice->seller->bankDetails->count() > 0)
        @php
            $firstBank = $proformaInvoice->seller->bankDetails->first();
        @endphp
        <div class="bank-section">
            <div class="bank-title">*. Beneficiary Bank Information:</div>
            <div class="bank-info">
                {{ $firmName }}.
            </div>
            <div class="bank-info">
                <span class="bank-label">ADDRESS:</span> {{ $firmAddress ?: ($proformaInvoice->seller->address ?? '') }}
            </div>
            <div class="bank-info">
                <span class="bank-label">BANK NAME:</span> {{ $firstBank->bank_name }}
                @if($firstBank->ifsc_code)
                    (SWIFT: {{ $firstBank->ifsc_code }})
                @endif
            </div>
            @if($firstBank->ifsc_code)
            <div class="bank-info">
                <span class="bank-label">BIC:</span> {{ $firstBank->ifsc_code }}
            </div>
            @endif
            @if($firstBank->bank_address)
            <div class="bank-info">
                {{ $firstBank->bank_address }}
            </div>
            @endif
            <div class="bank-info">
                <span class="bank-label">A/C NO.:</span> {{ $firstBank->account_number }}
            </div>
        </div>
    @endif

    <!-- Footer Section -->
    <div class="footer-section">
        <div class="footer-left">
            @if($firmLogo)
            <div class="company-logo-footer">
                <img src="{{ asset('storage/' . $firmLogo) }}" alt="Company Logo" style="max-height: 60px;">
            </div>
            @endif
            <div class="seller-name-footer">
                {{ $firmName }}
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

    <!-- Seller Name at Bottom -->
    <div style="margin-top: 20px; text-align: center; font-weight: bold; text-transform: uppercase;">
        {{ $proformaInvoice->seller->seller_name ?? 'SELLER NAME' }}
    </div>
</body>
</html>
