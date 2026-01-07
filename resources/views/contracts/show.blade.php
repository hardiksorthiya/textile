<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Contract Details</h1>
            <p class="text-muted mb-0">Contract: {{ $contract->contract_number }}</p>
        </div>
        <div class="d-flex gap-2">
            @canany(['view contract approvals', 'convert contract'])
            <a href="{{ route('machine-statuses.create', ['contract_id' => $contract->id]) }}" class="btn btn-primary">
                <i class="fas fa-tasks me-2"></i>Status
            </a>
            @endcanany
            <a href="{{ route('contracts.download-pdf', $contract) }}" class="btn btn-success" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </a>
            <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Contracts
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Contract Information Card -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-file-contract text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Contract Information</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Contract Number</label>
                            <div class="fw-bold" style="color: #1f2937;">{{ $contract->contract_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Business Firm</label>
                            <div style="color: #1f2937;">{{ $contract->businessFirm->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Buyer Name</label>
                            <div class="fw-semibold" style="color: #1f2937;">{{ $contract->buyer_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Company Name</label>
                            <div style="color: #1f2937;">{{ $contract->company_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Phone Number</label>
                            <div style="color: #1f2937;">{{ $contract->phone_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Email</label>
                            <div style="color: #1f2937;">{{ $contract->email ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Location</label>
                            <div style="color: #1f2937;">{{ $contract->area->name ?? '' }}, {{ $contract->city->name ?? '' }}, {{ $contract->state->name ?? '' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Total Amount</label>
                            <div class="fw-bold text-primary" style="font-size: 1.125rem;">${{ number_format($contract->total_amount ?? 0, 2) }}</div>
                        </div>
                        @if($contract->token_amount)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Token Amount</label>
                            <div class="fw-bold" style="color: #059669; font-size: 1.125rem;">₹{{ number_format($contract->token_amount, 2) }}</div>
                        </div>
                        @endif
                        @if($contract->creator)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Created By</label>
                            <div style="color: #1f2937;">{{ $contract->creator->name }}</div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Approval Status</label>
                            <div>
                                @if($contract->approval_status === 'pending')
                                    @if($contract->customer_signature)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Pending Approval
                                        </span>
                                    @else
                                        <span class="badge bg-info">
                                            <i class="fas fa-pen me-1"></i>Awaiting Signature
                                        </span>
                                    @endif
                                @elseif($contract->approval_status === 'approved')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Approved
                                    </span>
                                    @if($contract->approver)
                                        <small class="text-muted ms-2">by {{ $contract->approver->name }}</small>
                                    @endif
                                @elseif($contract->approval_status === 'rejected')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>Rejected
                                    </span>
                                    @if($contract->approver)
                                        <small class="text-muted ms-2">by {{ $contract->approver->name }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Machine Details -->
                    @if($contract->contractMachines->count() > 0)
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-bold mb-3" style="color: #1f2937;">Machine Details</h5>
                        @foreach($contract->contractMachines as $index => $machine)
                            <div class="card mb-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0" style="color: #1f2937;">
                                            Machine #{{ $index + 1 }}
                                            @if($machine->machineCategory)
                                                - {{ $machine->machineCategory->name }}
                                            @endif
                                        </h6>
                                        <div class="fw-bold text-primary">${{ number_format($machine->quantity * $machine->amount, 2) }}</div>
                                    </div>
                                    <div class="row g-2 small">
                                        @if($machine->brand)
                                        <div class="col-md-6"><strong>Brand:</strong> {{ $machine->brand->name }}</div>
                                        @endif
                                        @if($machine->seller)
                                        <div class="col-md-6"><strong>Machine Seller:</strong> {{ $machine->seller->seller_name }}</div>
                                        @endif
                                        @if($machine->machineModel)
                                        <div class="col-md-6"><strong>Model:</strong> {{ $machine->machineModel->model_no }}</div>
                                        @endif
                                        @if($machine->feeder)
                                        <div class="col-md-6"><strong>Feeder:</strong> {{ $machine->feeder->feeder }}{{ $machine->feeder->feederBrand ? ' (' . $machine->feeder->feederBrand->name . ')' : '' }}</div>
                                        @endif
                                        @if($machine->color)
                                        <div class="col-md-6"><strong>Color Selector:</strong> {{ $machine->color->name }}</div>
                                        @endif
                                        @if($machine->machineDropin)
                                        <div class="col-md-6"><strong>Dropins:</strong> {{ $machine->machineDropin->name }}</div>
                                        @endif
                                        @if($machine->machineBeam)
                                        <div class="col-md-6"><strong>Beam:</strong> {{ $machine->machineBeam->name }}</div>
                                        @endif
                                        @if($machine->machineClothRoller)
                                        <div class="col-md-6"><strong>Cloth Roller:</strong> {{ $machine->machineClothRoller->name }}</div>
                                        @endif
                                        @if($machine->machineHook)
                                        <div class="col-md-6"><strong>Hooks:</strong> {{ $machine->machineHook->hook }}</div>
                                        @endif
                                        @if($machine->machineERead)
                                        <div class="col-md-6"><strong>E-Read:</strong> {{ $machine->machineERead->name }}</div>
                                        @endif
                                        @if($machine->machineNozzle)
                                        <div class="col-md-6"><strong>Nozzle:</strong> {{ $machine->machineNozzle->nozzle }}</div>
                                        @endif
                                        @if($machine->machineSoftware)
                                        <div class="col-md-6"><strong>Software:</strong> {{ $machine->machineSoftware->name }}</div>
                                        @endif
                                        @if($machine->hsnCode)
                                        <div class="col-md-6"><strong>HSN Code:</strong> {{ $machine->hsnCode->hsn_code }}</div>
                                        @endif
                                        @if($machine->wir)
                                        <div class="col-md-6"><strong>WIR:</strong> {{ $machine->wir->wir }}</div>
                                        @endif
                                        @if($machine->machineShaft)
                                        <div class="col-md-6"><strong>Shaft:</strong> {{ $machine->machineShaft->name }}</div>
                                        @endif
                                        @if($machine->machineLever)
                                        <div class="col-md-6"><strong>Lever:</strong> {{ $machine->machineLever->name }}</div>
                                        @endif
                                        @if($machine->machineChain)
                                        <div class="col-md-6"><strong>Chain:</strong> {{ $machine->machineChain->name }}</div>
                                        @endif
                                        @if($machine->machineHealdWire)
                                        <div class="col-md-6"><strong>Heald Wires:</strong> {{ $machine->machineHealdWire->name }}</div>
                                        @endif
                                        <div class="col-md-6"><strong>Quantity:</strong> {{ $machine->quantity }}</div>
                                        <div class="col-md-6"><strong>Price:</strong> ${{ number_format($machine->amount, 2) }}</div>
                                        @if($machine->deliveryTerm)
                                        <div class="col-md-6"><strong>Delivery Terms:</strong> {{ $machine->deliveryTerm->name }}</div>
                                        @endif
                                        @if($machine->description)
                                        <div class="col-md-12"><strong>Description:</strong> {{ $machine->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Other Buyer Expenses Details -->
                    @if($contract->other_buyer_expenses_in_print && ($contract->overseas_freight || $contract->demurrage_detention_cfs_charges || $contract->air_pipe_connection || $contract->custom_duty || $contract->port_expenses_transport || $contract->crane_foundation || $contract->humidification || $contract->damage || $contract->gst_custom_charges || $contract->compressor || $contract->optional_spares))
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-bold mb-3" style="color: #1f2937;">Other Buyer Expenses Details</h5>
                        <div class="row g-3">
                            @if($contract->overseas_freight)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Overseas Freight</label>
                                <div style="color: #1f2937;">{{ $contract->overseas_freight }}</div>
                            </div>
                            @endif
                            @if($contract->demurrage_detention_cfs_charges)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Demurrage / Detention / CFS Charges</label>
                                <div style="color: #1f2937;">{{ $contract->demurrage_detention_cfs_charges }}</div>
                            </div>
                            @endif
                            @if($contract->air_pipe_connection)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Air Pipe Connection</label>
                                <div style="color: #1f2937;">{{ $contract->air_pipe_connection }}</div>
                            </div>
                            @endif
                            @if($contract->custom_duty)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Custom Duty</label>
                                <div style="color: #1f2937;">{{ $contract->custom_duty }}</div>
                            </div>
                            @endif
                            @if($contract->port_expenses_transport)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Port Expenses & Transport</label>
                                <div style="color: #1f2937;">{{ $contract->port_expenses_transport }}</div>
                            </div>
                            @endif
                            @if($contract->crane_foundation)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Crane & Foundation</label>
                                <div style="color: #1f2937;">{{ $contract->crane_foundation }}</div>
                            </div>
                            @endif
                            @if($contract->humidification)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Humidification</label>
                                <div style="color: #1f2937;">{{ $contract->humidification }}</div>
                            </div>
                            @endif
                            @if($contract->damage)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Damage</label>
                                <div style="color: #1f2937;">{{ $contract->damage }}</div>
                            </div>
                            @endif
                            @if($contract->gst_custom_charges)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">GST & Custom Charges</label>
                                <div style="color: #1f2937;">{{ $contract->gst_custom_charges }}</div>
                            </div>
                            @endif
                            @if($contract->compressor)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Compressor</label>
                                <div style="color: #1f2937;">{{ $contract->compressor }}</div>
                            </div>
                            @endif
                            @if($contract->optional_spares)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Optional Spares</label>
                                <div style="color: #1f2937;">{{ $contract->optional_spares }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Difference of Specification -->
                    @if($contract->difference_specification_in_print && ($contract->cam_jacquard_chain_jacquard || $contract->hooks_5376_to_6144_jacquard || $contract->warp_beam || $contract->reed_space_380_to_420_cm || $contract->color_selector_8_to_12 || $contract->hooks_5376_to_2688_jacquard || $contract->extra_feeder))
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-bold mb-3" style="color: #1f2937;">Difference of Specification</h5>
                        <div class="row g-3">
                            @if($contract->cam_jacquard_chain_jacquard)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Cam Jacquard of Chain Jacquard</label>
                                <div style="color: #1f2937;">{{ $contract->cam_jacquard_chain_jacquard }}</div>
                            </div>
                            @endif
                            @if($contract->hooks_5376_to_6144_jacquard)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">5376 Hooks to 6144 Hooks Jacquard</label>
                                <div style="color: #1f2937;">{{ $contract->hooks_5376_to_6144_jacquard }}</div>
                            </div>
                            @endif
                            @if($contract->warp_beam)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Warp Beam</label>
                                <div style="color: #1f2937;">{{ $contract->warp_beam }}</div>
                            </div>
                            @endif
                            @if($contract->reed_space_380_to_420_cm)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">380 cm to 420 cm reed space</label>
                                <div style="color: #1f2937;">{{ $contract->reed_space_380_to_420_cm }}</div>
                            </div>
                            @endif
                            @if($contract->color_selector_8_to_12)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">8 to 12 Color Selector</label>
                                <div style="color: #1f2937;">{{ $contract->color_selector_8_to_12 }}</div>
                            </div>
                            @endif
                            @if($contract->hooks_5376_to_2688_jacquard)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">5376 Hooks to 2688 Hooks Jacquard</label>
                                <div style="color: #1f2937;">{{ $contract->hooks_5376_to_2688_jacquard }}</div>
                            </div>
                            @endif
                            @if($contract->extra_feeder)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Extra Feeder</label>
                                <div style="color: #1f2937;">{{ $contract->extra_feeder }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Other Details -->
                    @if($contract->payment_terms || $contract->quote_validity || $contract->loading_terms || $contract->warranty || $contract->complimentary_spares)
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-bold mb-3" style="color: #1f2937;">Other Details</h5>
                        <div class="row g-3">
                            @if($contract->payment_terms)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Payment Terms</label>
                                <div style="color: #1f2937;">{{ $contract->payment_terms }}</div>
                            </div>
                            @endif
                            @if($contract->quote_validity)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Quote Validity</label>
                                <div style="color: #1f2937;">{{ $contract->quote_validity }}</div>
                            </div>
                            @endif
                            @if($contract->loading_terms)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Loading Terms</label>
                                <div style="color: #1f2937;">{{ $contract->loading_terms }}</div>
                            </div>
                            @endif
                            @if($contract->warranty)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Warranty</label>
                                <div style="color: #1f2937;">{{ $contract->warranty }}</div>
                            </div>
                            @endif
                            @if($contract->complimentary_spares)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Complimentary Spares</label>
                                <div style="color: #1f2937;">{{ $contract->complimentary_spares }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Signatures Card -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-signature text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Signatures</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Creator Signature -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2" style="color: #374151;">
                            <i class="fas fa-user-plus me-2"></i>Created By Signature
                        </label>
                        @if($contract->creator && !empty($contract->creator->signature))
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: white; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                @php
                                    $signaturePath = 'storage/' . $contract->creator->signature;
                                    $signatureExists = file_exists(storage_path('app/public/' . $contract->creator->signature));
                                @endphp
                                @if($signatureExists)
                                    <img src="{{ asset($signaturePath) }}" 
                                         alt="Creator Signature" 
                                         class="img-fluid" 
                                         style="max-height: 120px; max-width: 100%; object-fit: contain;">
                                @else
                                    <div>
                                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                                        <p class="text-muted mt-2 mb-0 small">Signature file not found</p>
                                    </div>
                                @endif
                            </div>
                            <small class="text-muted">Created by: {{ $contract->creator->name }}</small>
                        @else
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: #f9fafb; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <div>
                                    <i class="fas fa-signature text-muted" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0 small">No signature available</p>
                                </div>
                            </div>
                            @if($contract->creator)
                                <small class="text-muted">Created by: {{ $contract->creator->name }}</small>
                            @endif
                        @endif
                    </div>

                    <!-- Approver Signature -->
                    @if($contract->approval_status === 'approved' && $contract->approver)
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2" style="color: #374151;">
                            <i class="fas fa-check-circle me-2"></i>Approved By Signature
                        </label>
                        @if($contract->approver->signature)
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: white; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ asset('storage/' . $contract->approver->signature) }}" 
                                     alt="Approver Signature" 
                                     class="img-fluid" 
                                     style="max-height: 120px; max-width: 100%; object-fit: contain;">
                            </div>
                            <small class="text-muted">
                                Approved by: {{ $contract->approver->name }}
                                @if($contract->approved_at)
                                    on {{ $contract->approved_at->format('M d, Y') }}
                                @endif
                            </small>
                        @else
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: #f9fafb; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <div>
                                    <i class="fas fa-signature text-muted" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0 small">No signature available</p>
                                </div>
                            </div>
                            <small class="text-muted">
                                Approved by: {{ $contract->approver->name }}
                                @if($contract->approved_at)
                                    on {{ $contract->approved_at->format('M d, Y') }}
                                @endif
                            </small>
                        @endif
                    </div>
                    @endif

                    <!-- Customer Signature -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2" style="color: #374151;">
                            <i class="fas fa-user me-2"></i>Customer Signature
                        </label>
                        @if($contract->customer_signature)
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: white; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ $contract->customer_signature }}" 
                                     alt="Customer Signature" 
                                     class="img-fluid" 
                                     style="max-height: 120px; max-width: 100%; object-fit: contain;">
                            </div>
                            <small class="text-muted">
                                @if($contract->approval_status === 'pending')
                                    <span class="badge bg-warning">Pending Approval</span>
                                @elseif($contract->approval_status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($contract->approval_status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </small>
                        @else
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: #f9fafb; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <div>
                                    <i class="fas fa-signature text-muted" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0 small">Not signed yet</p>
                                </div>
                            </div>
                            <a href="{{ route('contracts.signature', $contract) }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-pen me-1"></i>Add Customer Signature
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
