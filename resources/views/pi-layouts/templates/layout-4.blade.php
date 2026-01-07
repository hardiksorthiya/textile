<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <title>Performa Invoice - {{ $proformaInvoice->proforma_invoice_number }}</title>
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
        .firm-name {
            font-size: 24px;
            font-weight: bold;
            color: #ff6600;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .firm-subtitle {
            font-size: 12px;
            color: #000;
            margin-bottom: 15px;
        }
        .invoice-meta-right {
            text-align: right;
            margin-top: -40px;
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
        }
        .recipient-name {
            margin: 5px 0;
        }
        .recipient-address {
            font-size: 11px;
            line-height: 1.4;
            margin-bottom: 5px;
        }
        .greeting {
            margin-top: 10px;
        }
        .subject-line {
            font-weight: bold;
            margin-top: 5px;
        }
        .items-section {
            margin: 20px 0;
        }
        .item-description {
            font-size: 11px;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .item-detail {
            margin-bottom: 3px;
        }
        .pricing-table {
            width: 100%;
            margin: 15px 0;
            font-size: 11px;
        }
        .pricing-row {
            display: table;
            width: 100%;
            margin: 5px 0;
        }
        .pricing-label {
            display: table-cell;
            width: 70%;
            padding-right: 10px;
        }
        .pricing-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-weight: bold;
        }
        .final-amount-row {
            border-top: 2px solid #000;
            padding-top: 10px;
            margin-top: 10px;
        }
        .amount-in-words {
            margin-top: 15px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        .terms-section {
            margin-top: 25px;
            font-size: 11px;
            line-height: 1.8;
        }
        .terms-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .bank-section {
            margin-top: 15px;
            font-size: 11px;
            line-height: 1.8;
        }
        .bank-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .bank-detail {
            margin: 3px 0;
        }
        .bank-label {
            font-weight: bold;
        }
        .signature-seal {
            margin-top: 30px;
            text-align: center;
        }
        .footer-section {
            margin-top: 50px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .seller-name {
            font-size: 20px;
            font-weight: bold;
            color: #ff6600;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .seller-address {
            font-size: 11px;
            margin-bottom: 5px;
            line-height: 1.5;
        }
        .seller-contact {
            font-size: 11px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header Section with Firm Logo/Name -->
    <div class="header-section">
        @php
            $businessFirm = $proformaInvoice->contract->businessFirm ?? null;
            $firmLogo = $businessFirm->logo ?? null;
            $firmName = $businessFirm->name ?? 'BUSINESS FIRM NAME';
        @endphp
        
        @if($firmLogo)
            <div style="margin-bottom: 10px;">
                <img src="{{ asset('storage/' . $firmLogo) }}" alt="{{ $firmName }}" style="max-height: 80px; max-width: 300px;">
            </div>
        @else
            <div class="firm-name">
                {{ $firmName }}
            </div>
        @endif
        
        @if($businessFirm && ($businessFirm->subtitle ?? false) && !$firmLogo)
        <div class="firm-subtitle">
            {{ $businessFirm->subtitle }}
        </div>
        @endif
    </div>

    <!-- Invoice Meta (Right-aligned) -->
    <div class="invoice-meta-right">
        <div class="invoice-meta-line">
            <strong>DATE:</strong> {{ $proformaInvoice->created_at->format('d-m-Y') }}
        </div>
        <div class="invoice-meta-line">
            <strong>Performa No.</strong> {{ $proformaInvoice->proforma_invoice_number }}
        </div>
    </div>

    <!-- Recipient Information -->
    <div class="recipient-section">
        <div class="recipient-label">To,</div>
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
        <div class="greeting">Dear Sir,</div>
        <div class="subject-line">Sub: Performa Invoice</div>
    </div>

    <!-- Item Details Section -->
    <div class="items-section">
        @foreach($proformaInvoice->proformaInvoiceMachines as $index => $piMachine)
            @php
                $contractMachine = $piMachine->contractMachine;
                $unitPrice = $piMachine->pi_price_plus_amc ?? ($piMachine->amount + ($piMachine->amc_price ?? 0));
                
                // Build item description
                $descriptionParts = [];
                if($contractMachine->brand) {
                    $descriptionParts[] = 'Brand: ' . $contractMachine->brand->name;
                }
                if($contractMachine->machineCategory) {
                    $descriptionParts[] = 'category: ' . $contractMachine->machineCategory->name;
                }
                if($contractMachine->machineModel) {
                    $descriptionParts[] = 'Model: ' . $contractMachine->machineModel->model_no;
                }
                if($contractMachine->machineSize) {
                    $descriptionParts[] = 'Size: ' . $contractMachine->machineSize->name;
                }
                if($contractMachine->feeder) {
                    $descriptionParts[] = 'Feeder: ' . $contractMachine->feeder->feeder;
                }
                if($contractMachine->machineSoftware) {
                    $descriptionParts[] = 'Software: ' . $contractMachine->machineSoftware->name;
                }
                if($contractMachine->machineNozzle) {
                    $descriptionParts[] = 'Nozzle: ' . $contractMachine->machineNozzle->name;
                }
                if($contractMachine->machineBeam) {
                    $descriptionParts[] = 'Beam: ' . $contractMachine->machineBeam->name;
                }
                if($contractMachine->machineClothRoller) {
                    $descriptionParts[] = 'Cloth Roller: ' . $contractMachine->machineClothRoller->name;
                }
                if($contractMachine->machineHealdWire) {
                    $descriptionParts[] = 'Heald Wires: ' . $contractMachine->machineHealdWire->name;
                }
                if($contractMachine->machineERead) {
                    $descriptionParts[] = 'E Read: ' . $contractMachine->machineERead->name;
                }
                
                $itemDescription = implode(', ', $descriptionParts);
                $qtyPrice = $unitPrice * $piMachine->quantity;
                $totalPIPrice = $qtyPrice;
                $gstPercentage = $proformaInvoice->gst_percentage ?? 18;
                $gstAmount = ($totalPIPrice * $gstPercentage) / 100;
                $finalAmount = $totalPIPrice + $gstAmount;
            @endphp
            
            <div class="item-description">
                <div class="item-detail">{{ $itemDescription }}</div>
                <div class="item-detail"><strong>Qty & Price :-</strong> {{ $piMachine->quantity }} * {{ number_format($unitPrice, 0) }}</div>
            </div>

            <!-- Pricing Breakdown -->
            <table class="pricing-table">
                <tr class="pricing-row">
                    <td class="pricing-label">Total PI Price</td>
                    <td class="pricing-value">{{ number_format($totalPIPrice, 0) }}</td>
                </tr>
                <tr class="pricing-row">
                    <td class="pricing-label">Final Total Price (85 * {{ number_format($totalPIPrice, 0) }})</td>
                    <td class="pricing-value">{{ number_format($totalPIPrice, 0) }}</td>
                </tr>
                <tr class="pricing-row">
                    <td class="pricing-label">GST</td>
                    <td class="pricing-value">{{ $gstPercentage }}%</td>
                </tr>
                <tr class="pricing-row">
                    <td class="pricing-label">GST Amount</td>
                    <td class="pricing-value">{{ number_format($gstAmount, 2) }}</td>
                </tr>
                <tr class="pricing-row final-amount-row">
                    <td class="pricing-label">Final Amount + GST Amount</td>
                    <td class="pricing-value">{{ number_format($finalAmount, 2) }} {{ $proformaInvoice->currency }}</td>
                </tr>
            </table>

            <!-- Amount in Words -->
            @php
                if (!function_exists('numberToWordsLayout4')) {
                    function numberToWordsLayout4($number) {
                        $ones = ["", "ONE", "TWO", "THREE", "FOUR", "FIVE", "SIX", "SEVEN", "EIGHT", "NINE", "TEN", 
                                 "ELEVEN", "TWELVE", "THIRTEEN", "FOURTEEN", "FIFTEEN", "SIXTEEN", "SEVENTEEN", "EIGHTEEN", "NINETEEN"];
                        $tens = ["", "", "TWENTY", "THIRTY", "FORTY", "FIFTY", "SIXTY", "SEVENTY", "EIGHTY", "NINETY"];
                        
                        $whole = floor($number);
                        $fraction = round(($number - $whole) * 10);
                        
                        if ($whole == 0) return "ZERO";
                        
                        $result = "";
                        if ($whole >= 1000) {
                            $thousands = floor($whole / 1000);
                            $result .= numberToWordsHelperLayout4($thousands) . " THOUSAND ";
                            $whole %= 1000;
                        }
                        if ($whole >= 100) {
                            $hundreds = floor($whole / 100);
                            $result .= $ones[$hundreds] . " HUNDRED ";
                            $whole %= 100;
                        }
                        if ($whole >= 20) {
                            $tens_digit = floor($whole / 10);
                            $result .= $tens[$tens_digit] . " ";
                            $whole %= 10;
                        }
                        if ($whole > 0) {
                            $result .= $ones[$whole] . " ";
                        }
                        
                        if ($fraction > 0) {
                            $result .= "POINT ";
                            $fractionStr = (string)$fraction;
                            for ($i = 0; $i < strlen($fractionStr); $i++) {
                                $digit = (int)$fractionStr[$i];
                                $result .= $ones[$digit] . " ";
                            }
                        }
                        
                        return trim($result);
                    }
                    
                    function numberToWordsHelperLayout4($num) {
                        $ones = ["", "ONE", "TWO", "THREE", "FOUR", "FIVE", "SIX", "SEVEN", "EIGHT", "NINE", "TEN", 
                                 "ELEVEN", "TWELVE", "THIRTEEN", "FOURTEEN", "FIFTEEN", "SIXTEEN", "SEVENTEEN", "EIGHTEEN", "NINETEEN"];
                        $tens = ["", "", "TWENTY", "THIRTY", "FORTY", "FIFTY", "SIXTY", "SEVENTY", "EIGHTY", "NINETY"];
                        
                        if ($num < 20) {
                            return $ones[$num];
                        } else {
                            return $tens[floor($num / 10)] . " " . $ones[$num % 10];
                        }
                    }
                }
                
                $amountInWords = numberToWordsLayout4($finalAmount);
            @endphp
            <div class="amount-in-words">
                Final Amount {{ number_format($finalAmount, 1) }} {{ strtoupper($amountInWords) }} {{ strtoupper($proformaInvoice->currency) }} ONLY
            </div>
        @endforeach
    </div>

    <!-- Terms and Conditions -->
    <div class="terms-section">
        <div class="terms-title">Terms and Conditions: -</div>
        <div>
            <strong>PAYMENT TERMS:</strong> 
            @if($proformaInvoice->contract->payment_terms)
                {{ strtoupper($proformaInvoice->contract->payment_terms) }}
            @else
                25% ADVANCE (PART PAYMENT ALLOWED) & 75% BALANCE BY T/T OR L/C BEFORE SHIPMENT
            @endif
        </div>
        <div>
            <strong>PARTIAL SHIPMENT:</strong> ALLOWED
        </div>
    </div>

    <!-- Bank Details -->
    @if($proformaInvoice->seller->bankDetails->count() > 0)
        @php
            $firstBank = $proformaInvoice->seller->bankDetails->first();
        @endphp
        <div class="bank-section">
            <div class="bank-title">Bank Detail:-</div>
            <div class="bank-detail">
                <span class="bank-label">Firm Name:</span> {{ $proformaInvoice->contract->businessFirm->name ?? $proformaInvoice->seller->seller_name }}
            </div>
            <div class="bank-detail">
                <span class="bank-label">A/c No:</span> {{ $firstBank->account_number }}
            </div>
            <div class="bank-detail">
                <span class="bank-label">Bank Name:</span> {{ $firstBank->bank_name }}
            </div>
            @if($firstBank->ifsc_code)
            <div class="bank-detail">
                <span class="bank-label">SWIFT Code:</span> {{ $firstBank->ifsc_code }}
            </div>
            @endif
        </div>
    @endif

    <!-- Signature/Seal Area -->
    <div class="signature-seal">
        @if($proformaInvoice->seller->signature)
        <img src="{{ asset('storage/' . $proformaInvoice->seller->signature) }}" alt="Company Seal" style="max-width: 120px; max-height: 120px;">
        @endif
    </div>

    <!-- Footer Section with Seller Name -->
    <div class="footer-section">
        <div class="seller-name">{{ $proformaInvoice->seller->seller_name ?? 'SELLER NAME' }}</div>
        @if($proformaInvoice->seller->address)
        <div class="seller-address">{{ $proformaInvoice->seller->address }}</div>
        @endif
        <div class="seller-contact">
            @if($proformaInvoice->seller->mobile)
            <strong>Mobile:</strong> {{ $proformaInvoice->seller->mobile }}
            @endif
            @if($proformaInvoice->seller->email)
            @if($proformaInvoice->seller->mobile) | @endif
            <strong>Email:</strong> {{ $proformaInvoice->seller->email }}
            @endif
        </div>
    </div>
</body>
</html>
