<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <title>Contract - {{ $contract->contract_number }}</title>
    <style>
        @page {
            margin-top: 5mm;
            margin-bottom: 5mm;
            margin-left: 10mm;
            margin-right: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 5px;
        }
        .firm-logo {
            max-width: 200px;
            max-height: 80px;
            margin: 0 auto 10px;
            display: block;
        }
        .company-name {
            font-size: 14px;
            color: #000;
            margin-bottom: 10px;
        }
        .company-address {
            font-size: 12px;
            color: #212121;
            margin-bottom: 10px;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #dc2626;
            margin: 20px 0;
        }
        .customer-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .customer-info td {
            padding: 8px;
            border: 1px solid #5e5e5e;
        }
        .customer-info td:nth-child(odd) {
            font-weight: bold;
            background-color: #dbd9d6;
            width: 20%;
        }
        .customer-info td:nth-child(even) {
            background-color: #ffffff;
            width: 30%;
        }
        .machine-section {
            margin-top: 0;
            page-break-inside: avoid;
            background: #f9f9f9;
            
        }
        .machine-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            padding: 10px 0;
        }
        .machine-title-main {
            color: #dc2626;
        }
        .machine-title-category {
            text-transform: uppercase;
            color: #dc2626;
        }
        .machine-table {
            width: 100%;
            border-collapse: collapse;
        }
        .machine-table td {
            padding: 6px 8px;
            border: 1px solid #5e5e5e;
        }
        .machine-table td:nth-child(odd) {
            font-weight: bold;
            background-color: #dbd9d6;
            width: 20%;
        }
        .machine-table td:nth-child(even) {
            background-color: #ffffff;
            width: 30%;
        }
        .machine-total-section {
            margin-top: 20px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            padding: 10px;
            border-top: 2px solid #5e5e5e;
        }
        .machine-total-label {
            display: inline-block;
            margin-right: 20px;
        }
        .machine-total-amount {
            font-size: 16px;
            color: #dc2626;
        }
        .total-section {
            margin-top: 30px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }
        .rupee-symbol {
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
        .other-details-section {
            margin-top: 0;
            margin-bottom: 0;
            page-break-inside: avoid;
            background: #f9f9f9;
        }
        .other-details-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            padding: 8px 0 5px 0;
            color: #dc2626;
            text-transform: uppercase;
            margin: 0;
        }
        .other-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .other-details-table td {
            padding: 6px 8px;
            border: 1px solid #5e5e5e;
            font-size: 12px;
        }
        .other-details-table td:nth-child(odd) {
            font-weight: bold;
            background-color: #dbd9d6;
            width: 30%;
        }
        .other-details-table td:nth-child(even) {
            background-color: #ffffff;
            width: 20%;
        }
        /* Increase width for OTHER BUYER EXPENSES DETAILS table odd columns to ~60% total */
        .expenses-details-table td:nth-child(odd) {
            width: 30%;
        }
        .expenses-details-table td:nth-child(even) {
            width: 20%;
        }
    </style>
</head>
<body>
    <!-- Header with Firm Logo and Address -->
    <div class="header">
        @if($contract->businessFirm && $contract->businessFirm->logo)
            @php
                $logoPath = storage_path('app/public/' . $contract->businessFirm->logo);
                $logoExists = file_exists($logoPath);
            @endphp
            @if($logoExists)
                <img src="{{ $logoPath }}" alt="{{ $contract->businessFirm->name }}" class="firm-logo">
            @else
                <div class="company-name">{{ $contract->businessFirm->name }}</div>
            @endif
            <div class="company-address">{{ $contract->businessFirm->address ?? 'Signature House, Behind HP Petrol Pump, Bhatar Char Rasta, U.M. Road, Surat-395 007 (Guj.), India' }}</div>
        @elseif($contract->businessFirm)
            <div class="company-name">{{ $contract->businessFirm->name }}</div>
            <div class="company-address">{{ $contract->businessFirm->address ?? 'Signature House, Behind HP Petrol Pump, Bhatar Char Rasta, U.M. Road, Surat-395 007 (Guj.), India' }}</div>
        @else
            <div class="company-address">Signature House, Behind HP Petrol Pump, Bhatar Char Rasta, U.M. Road, Surat-395 007 (Guj.), India</div>
        @endif
    </div>

    <!-- Purchase Contract Title -->
    <div class="title">PURCHASE CONTRACT</div>

    <!-- Customer Information -->
    <table class="customer-info">
        <tr>
            <td>Name</td>
            <td>{{ $contract->buyer_name }}</td>
            <td>Email</td>
            <td>{{ $contract->email ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Company Name</td>
            <td>{{ $contract->company_name ?? 'N/A' }}</td>
            <td>Mob</td>
            <td>{{ $contract->phone_number }}</td>
        </tr>
    </table>

    <!-- Machine Details -->
    @php
        $grandTotal = 0;
    @endphp
    @foreach($contract->contractMachines as $index => $machine)
        @php
            $machineTotal = $machine->quantity * $machine->amount;
            $grandTotal += $machineTotal;
        @endphp
        <div class="machine-section">
            <div class="machine-title">
                <span class="machine-title-main">MACHINE SPECIFICATION</span>
                @if($machine->machineCategory)
                    <span class="machine-title-category"> - {{ $machine->machineCategory->name }}</span>
                @endif
            </div>
            <table class="machine-table">
                @php
                    $fields = [];
                    if($machine->brand) $fields[] = ['label' => 'Brand', 'value' => $machine->brand->name];
                    if($machine->machineModel) $fields[] = ['label' => 'Model', 'value' => $machine->machineModel->model_no];
                    if($machine->feeder) $fields[] = ['label' => 'Feeder', 'value' => $machine->feeder->feeder];
                    if($machine->color) $fields[] = ['label' => 'Color Selector', 'value' => $machine->color->name];
                    if($machine->machineDropin) $fields[] = ['label' => 'Dropins', 'value' => $machine->machineDropin->name];
                    if($machine->machineBeam) $fields[] = ['label' => 'Beam', 'value' => $machine->machineBeam->name];
                    if($machine->machineClothRoller) $fields[] = ['label' => 'Cloth Roller', 'value' => $machine->machineClothRoller->name];
                    if($machine->machineHook) $fields[] = ['label' => 'Hooks', 'value' => $machine->machineHook->hook];
                    if($machine->machineERead) $fields[] = ['label' => 'E-Read', 'value' => $machine->machineERead->name];
                    if($machine->machineNozzle) $fields[] = ['label' => 'Nozzle', 'value' => $machine->machineNozzle->nozzle];
                    if($machine->machineSoftware) $fields[] = ['label' => 'Software', 'value' => $machine->machineSoftware->name];
                    if($machine->machineShaft) $fields[] = ['label' => 'Shaft', 'value' => $machine->machineShaft->name];
                    if($machine->machineLever) $fields[] = ['label' => 'Lever', 'value' => $machine->machineLever->name];
                    if($machine->machineChain) $fields[] = ['label' => 'Chain', 'value' => $machine->machineChain->name];
                    if($machine->machineHealdWire) $fields[] = ['label' => 'Heald Wires', 'value' => $machine->machineHealdWire->name];
                @endphp
                
                @for($i = 0; $i < count($fields); $i += 2)
                    @if(isset($fields[$i]))
                        <tr>
                            <td>{{ $fields[$i]['label'] }}</td>
                            <td>{{ $fields[$i]['value'] }}</td>
                            @if(isset($fields[$i + 1]))
                                <td>{{ $fields[$i + 1]['label'] }}</td>
                                <td>{{ $fields[$i + 1]['value'] }}</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    @endif
                @endfor
                
                <tr>
                    <td>Quantity</td>
                    <td>{{ $machine->quantity }}</td>
                    <td>Price</td>
                    <td>${{ number_format($machine->amount, 2) }}</td>
                </tr>
                @if($machine->deliveryTerm)
                <tr>
                    <td>Total Price</td>
                    <td>${{ number_format($machineTotal, 2) }}</td>
                    <td>Delivery Terms</td>
                    <td>{{ $machine->deliveryTerm->name }}</td>
                </tr>
                @else
                <tr>
                    <td>Total Price</td>
                    <td colspan="3">${{ number_format($machineTotal, 2) }}</td>
                </tr>
                @endif
            </table>
        </div>
    @endforeach
    
    <!-- Other Buyer Expenses Details -->
    @if($contract->other_buyer_expenses_in_print && ($contract->overseas_freight || $contract->demurrage_detention_cfs_charges || $contract->air_pipe_connection || $contract->custom_duty || $contract->port_expenses_transport || $contract->crane_foundation || $contract->humidification || $contract->damage || $contract->gst_custom_charges || $contract->compressor || $contract->optional_spares))
    <div class="other-details-section">
        <div class="other-details-title">OTHER BUYER EXPENSES DETAILS</div>
        <table class="other-details-table expenses-details-table">
            @php
                $expenseFields = [];
                if($contract->overseas_freight) $expenseFields[] = ['label' => 'Overseas Freight', 'value' => $contract->overseas_freight];
                if($contract->demurrage_detention_cfs_charges) $expenseFields[] = ['label' => 'Demurrage / Detention / CFS Charges', 'value' => $contract->demurrage_detention_cfs_charges];
                if($contract->air_pipe_connection) $expenseFields[] = ['label' => 'Air Pipe Connection', 'value' => $contract->air_pipe_connection];
                if($contract->custom_duty) $expenseFields[] = ['label' => 'Custom Duty', 'value' => $contract->custom_duty];
                if($contract->port_expenses_transport) $expenseFields[] = ['label' => 'Port Expenses & Transport', 'value' => $contract->port_expenses_transport];
                if($contract->crane_foundation) $expenseFields[] = ['label' => 'Crane & Foundation', 'value' => $contract->crane_foundation];
                if($contract->humidification) $expenseFields[] = ['label' => 'Humidification', 'value' => $contract->humidification];
                if($contract->damage) $expenseFields[] = ['label' => 'Damage', 'value' => $contract->damage];
                if($contract->gst_custom_charges) $expenseFields[] = ['label' => 'GST & Custom Charges', 'value' => $contract->gst_custom_charges];
                if($contract->compressor) $expenseFields[] = ['label' => 'Compressor', 'value' => $contract->compressor];
                if($contract->optional_spares) $expenseFields[] = ['label' => 'Optional Spares', 'value' => $contract->optional_spares];
            @endphp
            
            @for($i = 0; $i < count($expenseFields); $i += 2)
                @if(isset($expenseFields[$i]))
                    <tr>
                        <td>{{ $expenseFields[$i]['label'] }}</td>
                        @if(isset($expenseFields[$i + 1]))
                            <td>{{ $expenseFields[$i]['value'] }}</td>
                            <td>{{ $expenseFields[$i + 1]['label'] }}</td>
                            <td>{{ $expenseFields[$i + 1]['value'] }}</td>
                        @else
                            <td colspan="3">{{ $expenseFields[$i]['value'] }}</td>
                        @endif
                    </tr>
                @endif
            @endfor
        </table>
    </div>
    @endif

    <!-- Other Details -->
    @if($contract->other_details_in_print && ($contract->payment_terms || $contract->quote_validity || $contract->loading_terms || $contract->warranty || $contract->complimentary_spares))
    <div class="other-details-section">
        <div class="other-details-title">OTHER DETAILS</div>
        <table class="other-details-table">
            @php
                $otherDetailFields = [];
                if($contract->payment_terms) $otherDetailFields[] = ['label' => 'Payment Terms', 'value' => $contract->payment_terms];
                if($contract->quote_validity) $otherDetailFields[] = ['label' => 'Quote Validity', 'value' => $contract->quote_validity];
                if($contract->loading_terms) $otherDetailFields[] = ['label' => 'Loading Terms', 'value' => $contract->loading_terms];
                if($contract->warranty) $otherDetailFields[] = ['label' => 'Warranty', 'value' => $contract->warranty];
                if($contract->complimentary_spares) $otherDetailFields[] = ['label' => 'Complimentary Spares', 'value' => $contract->complimentary_spares];
            @endphp
            
            @for($i = 0; $i < count($otherDetailFields); $i += 2)
                @if(isset($otherDetailFields[$i]))
                    <tr>
                        <td>{{ $otherDetailFields[$i]['label'] }}</td>
                        @if(isset($otherDetailFields[$i + 1]))
                            <td>{{ $otherDetailFields[$i]['value'] }}</td>
                            <td>{{ $otherDetailFields[$i + 1]['label'] }}</td>
                            <td>{{ $otherDetailFields[$i + 1]['value'] }}</td>
                        @else
                            <td colspan="3">{{ $otherDetailFields[$i]['value'] }}</td>
                        @endif
                    </tr>
                @endif
            @endfor
        </table>
    </div>
    @endif

    <!-- Difference of Specification -->
    @if($contract->difference_specification_in_print && ($contract->cam_jacquard_chain_jacquard || $contract->hooks_5376_to_6144_jacquard || $contract->warp_beam || $contract->reed_space_380_to_420_cm || $contract->color_selector_8_to_12 || $contract->hooks_5376_to_2688_jacquard || $contract->extra_feeder))
    <div class="other-details-section">
        <div class="other-details-title">DIFFERENCE OF SPECIFICATION</div>
        <table class="other-details-table">
            @php
                $differenceFields = [];
                if($contract->cam_jacquard_chain_jacquard) $differenceFields[] = ['label' => 'Cam Jacquard of Chain Jacquard', 'value' => $contract->cam_jacquard_chain_jacquard];
                if($contract->hooks_5376_to_6144_jacquard) $differenceFields[] = ['label' => '5376 Hooks to 6144 Hooks Jacquard', 'value' => $contract->hooks_5376_to_6144_jacquard];
                if($contract->warp_beam) $differenceFields[] = ['label' => 'Warp Beam', 'value' => $contract->warp_beam];
                if($contract->reed_space_380_to_420_cm) $differenceFields[] = ['label' => '380 cm to 420 cm reed space', 'value' => $contract->reed_space_380_to_420_cm];
                if($contract->color_selector_8_to_12) $differenceFields[] = ['label' => '8 to 12 Color Selector', 'value' => $contract->color_selector_8_to_12];
                if($contract->hooks_5376_to_2688_jacquard) $differenceFields[] = ['label' => '5376 Hooks to 2688 Hooks Jacquard', 'value' => $contract->hooks_5376_to_2688_jacquard];
                if($contract->extra_feeder) $differenceFields[] = ['label' => 'Extra Feeder', 'value' => $contract->extra_feeder];
            @endphp
            
            @for($i = 0; $i < count($differenceFields); $i += 2)
                @if(isset($differenceFields[$i]))
                    <tr>
                        <td>{{ $differenceFields[$i]['label'] }}</td>
                        @if(isset($differenceFields[$i + 1]))
                            <td>{{ $differenceFields[$i]['value'] }}</td>
                            <td>{{ $differenceFields[$i + 1]['label'] }}</td>
                            <td>{{ $differenceFields[$i + 1]['value'] }}</td>
                        @else
                            <td colspan="3">{{ $differenceFields[$i]['value'] }}</td>
                        @endif
                    </tr>
                @endif
            @endfor
        </table>
    </div>
    @endif

    <!-- Total Amount and Token Amount -->
    <div class="total-section">
        <div style="margin-bottom: 10px;">
            <strong>Total Contract Amount: ${{ number_format($contract->total_amount ?? 0, 2) }}</strong>
        </div>
        @if($contract->token_amount)
        <div>
            <strong>Token Amount: <span class="rupee-symbol">&#8377;</span>{{ number_format($contract->token_amount, 2) }}</strong>
        </div>
        @endif
    </div>

    <!-- Signatures Section -->
    <div style="margin-top: 40px; page-break-inside: avoid;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 30px;">
            <tr>
                <!-- Creator Signature (User who created the contract) -->
                <td style="width: {{ $contract->approval_status === 'approved' && $contract->approver ? '33.33' : '50' }}%; padding: 20px; vertical-align: top; border-top: 2px solid #5e5e5e;">
                    <div style="text-align: center;">
                        <div style="font-weight: bold; margin-bottom: 10px; font-size: 14px;">Created By</div>
                        @php
                            $creator = $contract->creator;
                            $creatorSignatureImg = '';
                            if ($creator) {
                                // Check if signature exists and is not empty
                                if (!empty($creator->signature)) {
                                    $signaturePath = storage_path('app/public/' . $creator->signature);
                                    // Check if file exists
                                    if (file_exists($signaturePath) && is_readable($signaturePath)) {
                                        try {
                                            $signatureData = file_get_contents($signaturePath);
                                            if ($signatureData !== false && !empty($signatureData)) {
                                                $signatureBase64 = base64_encode($signatureData);
                                                $signatureMime = mime_content_type($signaturePath);
                                                if (!$signatureMime) {
                                                    // Try to detect from extension
                                                    $ext = strtolower(pathinfo($signaturePath, PATHINFO_EXTENSION));
                                                    $signatureMime = $ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : ($ext === 'png' ? 'image/png' : ($ext === 'gif' ? 'image/gif' : 'image/png'));
                                                }
                                                $creatorSignatureImg = 'data:' . $signatureMime . ';base64,' . $signatureBase64;
                                            }
                                        } catch (\Exception $e) {
                                            // Signature file exists but couldn't be read
                                        }
                                    }
                                }
                            }
                        @endphp
                        @if(!empty($creatorSignatureImg))
                            <img src="{{ $creatorSignatureImg }}" alt="Creator Signature" style="max-height: 100px; max-width: 100%; object-fit: contain; margin-bottom: 10px;">
                        @endif
                        <div style="border-top: 1px solid #5e5e5e; padding-top: 5px; margin-top: 10px; font-size: 12px;">
                            @if($creator)
                                {{ $creator->name }}
                            @else
                                Contract Creator
                            @endif
                        </div>
                    </div>
                </td>
                
                <!-- Approver Signature (User who approved the contract) - Only show if approved -->
                @if($contract->approval_status === 'approved' && $contract->approver)
                <td style="width: 33.33%; padding: 20px; vertical-align: top; border-top: 2px solid #5e5e5e;">
                    <div style="text-align: center;">
                        <div style="font-weight: bold; margin-bottom: 10px; font-size: 14px;">Approved By</div>
                        @php
                            $approverSignatureImg = '';
                            if ($contract->approver && $contract->approver->signature && !empty($contract->approver->signature)) {
                                $approverSignaturePath = storage_path('app/public/' . $contract->approver->signature);
                                if (file_exists($approverSignaturePath) && is_readable($approverSignaturePath)) {
                                    try {
                                        $approverSignatureData = file_get_contents($approverSignaturePath);
                                        if ($approverSignatureData !== false && !empty($approverSignatureData)) {
                                            $approverSignatureBase64 = base64_encode($approverSignatureData);
                                            $approverSignatureMime = mime_content_type($approverSignaturePath);
                                            if (!$approverSignatureMime) {
                                                $ext = strtolower(pathinfo($approverSignaturePath, PATHINFO_EXTENSION));
                                                $approverSignatureMime = $ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : ($ext === 'png' ? 'image/png' : ($ext === 'gif' ? 'image/gif' : 'image/png'));
                                            }
                                            $approverSignatureImg = 'data:' . $approverSignatureMime . ';base64,' . $approverSignatureBase64;
                                        }
                                    } catch (\Exception $e) {
                                        // Signature file exists but couldn't be read
                                    }
                                }
                            }
                        @endphp
                        @if(!empty($approverSignatureImg))
                            <img src="{{ $approverSignatureImg }}" alt="Approver Signature" style="max-height: 100px; max-width: 100%; object-fit: contain; margin-bottom: 10px;">
                        @endif
                        <div style="border-top: 1px solid #5e5e5e; padding-top: 5px; margin-top: 10px; font-size: 12px;">
                            {{ $contract->approver->name }}
                            @if($contract->approved_at)
                                <br><small style="font-size: 10px;">{{ $contract->approved_at->format('M d, Y') }}</small>
                            @endif
                        </div>
                    </div>
                </td>
                @endif
                
                <!-- Customer Signature -->
                <td style="width: {{ $contract->approval_status === 'approved' && $contract->approver ? '33.33' : '50' }}%; padding: 20px; vertical-align: top; border-top: 2px solid #5e5e5e;">
                    <div style="text-align: center;">
                        <div style="font-weight: bold; margin-bottom: 10px; font-size: 14px;">Customer Signature</div>
                        @if($contract->customer_signature)
                            <img src="{{ $contract->customer_signature }}" alt="Customer Signature" style="max-height: 100px; max-width: 100%; object-fit: contain; margin-bottom: 10px;">
                        @endif
                        <div style="border-top: 1px solid #5e5e5e; padding-top: 5px; margin-top: 10px; font-size: 12px;">
                            {{ $contract->buyer_name }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
