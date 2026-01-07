<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Create Purchase Order</h1>
            <p class="text-muted mb-0">Create a purchase order from a proforma invoice</p>
        </div>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div x-data="purchaseOrderForm({{ $selectedProformaInvoiceId ?? 'null' }})" x-init="init()">
        <!-- Search Section -->
        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 pb-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-search text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Search Proforma Invoice</h2>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('purchase-orders.create') }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #374151;">Sales Manager</label>
                            <select name="sales_manager_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('searchForm').submit();">
                                <option value="">All Sales Managers</option>
                                @foreach($salesManagers as $manager)
                                    <option value="{{ $manager->id }}" {{ request('sales_manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #374151;">PI Number</label>
                            <input type="text" name="pi_number" value="{{ request('pi_number') }}" 
                                   class="form-control" placeholder="Enter PI number" 
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #374151;">Customer Name</label>
                            <input type="text" name="customer_name" value="{{ request('customer_name') }}" 
                                   class="form-control" placeholder="Enter customer name" 
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                                <a href="{{ route('purchase-orders.create') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Proforma Invoices List -->
        @if(request()->hasAny(['sales_manager_id', 'pi_number', 'customer_name']) || $proformaInvoices->count() > 0)
        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 pb-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-file-invoice text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Select Proforma Invoice</h2>
                </div>
            </div>
            <div class="card-body p-4">
                @forelse($proformaInvoices as $pi)
                    <div class="card mb-3" style="cursor: pointer; border: 2px solid #e5e7eb; transition: all 0.2s;" 
                         :class="{ 'border-primary': selectedProformaInvoiceId === {{ $pi->id }} }"
                         @click="selectProformaInvoice({{ $pi->id }})"
                         onmouseover="this.style.borderColor='var(--primary-color)'" 
                         onmouseout="this.style.borderColor='#e5e7eb'">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-bold mb-1" style="color: #1f2937;">{{ $pi->proforma_invoice_number }}</h5>
                                    <p class="mb-1" style="color: #6b7280;">
                                        <strong>Customer:</strong> {{ $pi->buyer_company_name }}
                                    </p>
                                    <p class="mb-0 small text-muted">
                                        <strong>Seller:</strong> {{ $pi->seller->seller_name ?? 'N/A' }} | 
                                        <strong>Amount:</strong> ${{ number_format($pi->total_amount ?? 0, 2) }}
                                    </p>
                                </div>
                                <div>
                                    <i class="fas fa-chevron-right text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-file-invoice fa-3x mb-3" style="opacity: 0.3;"></i>
                        <p>No proforma invoices found. Please adjust your search criteria.</p>
                    </div>
                @endforelse

                @if($proformaInvoices->hasPages())
                    <div class="mt-3">
                        {{ $proformaInvoices->links() }}
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Purchase Order Form (shown when PI is selected) -->
        @if($selectedProformaInvoiceId)
        @php
            $selectedPI = \App\Models\ProformaInvoice::with([
                'contract.creator',
                'seller',
                'proformaInvoiceMachines.machineCategory',
                'proformaInvoiceMachines.brand',
                'proformaInvoiceMachines.machineModel.brand',
                'proformaInvoiceMachines.feeder.feederBrand',
                'proformaInvoiceMachines.machineHook',
                'proformaInvoiceMachines.machineERead',
                'proformaInvoiceMachines.color',
                'proformaInvoiceMachines.machineNozzle',
                'proformaInvoiceMachines.machineDropin',
                'proformaInvoiceMachines.machineBeam',
                'proformaInvoiceMachines.machineClothRoller',
                'proformaInvoiceMachines.machineSoftware',
                'proformaInvoiceMachines.hsnCode',
                'proformaInvoiceMachines.wir',
                'proformaInvoiceMachines.machineShaft',
                'proformaInvoiceMachines.machineLever',
                'proformaInvoiceMachines.machineChain',
                'proformaInvoiceMachines.machineHealdWire',
                'proformaInvoiceMachines.deliveryTerm',
                'proformaInvoiceMachines.contractMachine'
            ])->find($selectedProformaInvoiceId);
        @endphp
        @if($selectedPI)
        <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 pb-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-shopping-bag text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Purchase Order Details</h2>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('purchase-orders.store') }}" method="POST" enctype="multipart/form-data" id="poForm">
                    @csrf
                    <input type="hidden" name="proforma_invoice_id" value="{{ $selectedPI->id }}">

                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h5 class="fw-bold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Basic Information</h5>
                        </div>

                        <div class="col-md-6">
                            <label for="purchase_order_number" class="form-label fw-semibold">PO Number <span class="text-danger">*</span></label>
                            <input type="text" name="purchase_order_number" id="purchase_order_number" 
                                   class="form-control @error('purchase_order_number') is-invalid @enderror" 
                                   value="{{ old('purchase_order_number', 'PO-' . date('Ymd') . '-' . str_pad($selectedPI->id, 4, '0', STR_PAD_LEFT)) }}" 
                                   required
                                   readonly
                                   style="border-radius: 8px; border: 1px solid #e5e7eb; background-color: #f3f4f6;">
                            @error('purchase_order_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="buyer_name" class="form-label fw-semibold">Buyer Name <span class="text-danger">*</span></label>
                            <input type="text" name="buyer_name" id="buyer_name" 
                                   class="form-control @error('buyer_name') is-invalid @enderror" 
                                   value="{{ old('buyer_name', $selectedPI->buyer_company_name) }}" 
                                   required
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('buyer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label fw-semibold">Address</label>
                            <textarea name="address" id="address" rows="3" 
                                      class="form-control @error('address') is-invalid @enderror"
                                      placeholder="Enter address"
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('address', $selectedPI->billing_address ?? $selectedPI->shipping_address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Machine Details from PI -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-bold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Machine Details (from PI)</h5>
                        </div>

                        <div class="col-12">
                            <div class="table-responsive" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0; margin: 0;">
                                    <thead>
                                        <tr style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);">
                                            <th class="px-4 py-3 text-white fw-bold" style="border: none; min-width: 180px; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Machine Category</th>
                                            <th class="px-4 py-3 text-white fw-bold" style="border: none; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Specifications</th>
                                            <th class="px-4 py-3 text-white fw-bold text-center" style="border: none; min-width: 100px; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($selectedPI->proformaInvoiceMachines as $index => $piMachine)
                                            <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f8f9fa' }}; border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;">
                                                <td class="px-4 py-4" style="vertical-align: top; border: none;">
                                                    <div class="fw-bold" style="font-size: 1.05rem; color: var(--primary-color) !important; line-height: 1.4;">
                                                        <i class="fas fa-cog me-2" style="color: var(--primary-color);"></i>{{ $piMachine->machineCategory->name ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4" style="vertical-align: top; border: none;">
                                                    <div class="specifications-container" style="display: flex; flex-wrap: wrap; gap: 12px;">
                                                        @php
                                                            $specs = [];
                                                            if($piMachine->brand) $specs[] = ['label' => 'Brand', 'value' => $piMachine->brand->name];
                                                            if($piMachine->machineModel) $specs[] = ['label' => 'Model', 'value' => $piMachine->machineModel->model_no . ($piMachine->machineModel->brand ? ' (' . $piMachine->machineModel->brand->name . ')' : '')];
                                                            if($piMachine->feeder) $specs[] = ['label' => 'Feeder', 'value' => $piMachine->feeder->feeder . ($piMachine->feeder->feederBrand ? ' (' . $piMachine->feeder->feederBrand->name . ')' : '')];
                                                            if($piMachine->machineHook) $specs[] = ['label' => 'Hook', 'value' => $piMachine->machineHook->hook];
                                                            if($piMachine->machineERead) $specs[] = ['label' => 'E-Read', 'value' => $piMachine->machineERead->name];
                                                            if($piMachine->color) $specs[] = ['label' => 'Color', 'value' => $piMachine->color->name];
                                                            if($piMachine->machineNozzle) $specs[] = ['label' => 'Nozzle', 'value' => $piMachine->machineNozzle->nozzle];
                                                            if($piMachine->machineDropin) $specs[] = ['label' => 'Dropin', 'value' => $piMachine->machineDropin->name];
                                                            if($piMachine->machineBeam) $specs[] = ['label' => 'Beam', 'value' => $piMachine->machineBeam->name];
                                                            if($piMachine->machineClothRoller) $specs[] = ['label' => 'Cloth Roller', 'value' => $piMachine->machineClothRoller->name];
                                                            if($piMachine->machineSoftware) $specs[] = ['label' => 'Software', 'value' => $piMachine->machineSoftware->name];
                                                            if($piMachine->hsnCode) $specs[] = ['label' => 'HSN Code', 'value' => $piMachine->hsnCode->name];
                                                            if($piMachine->wir) $specs[] = ['label' => 'WIR', 'value' => $piMachine->wir->name];
                                                            if($piMachine->machineShaft) $specs[] = ['label' => 'Shaft', 'value' => $piMachine->machineShaft->name];
                                                            if($piMachine->machineLever) $specs[] = ['label' => 'Lever', 'value' => $piMachine->machineLever->name];
                                                            if($piMachine->machineChain) $specs[] = ['label' => 'Chain', 'value' => $piMachine->machineChain->name];
                                                            if($piMachine->machineHealdWire) $specs[] = ['label' => 'Heald Wire', 'value' => $piMachine->machineHealdWire->name];
                                                            if($piMachine->deliveryTerm) $specs[] = ['label' => 'Delivery Term', 'value' => $piMachine->deliveryTerm->name];
                                                        @endphp
                                                        @foreach($specs as $spec)
                                                            <div class="spec-badge" style="display: inline-flex; align-items: center; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 12px; margin: 0;">
                                                                <span class="text-muted" style="font-size: 0.75rem; font-weight: 600; margin-right: 6px; text-transform: uppercase; letter-spacing: 0.3px;">{{ $spec['label'] }}:</span>
                                                                <span class="fw-semibold" style="font-size: 0.875rem; color: #1f2937;">{{ $spec['value'] }}</span>
                                                            </div>
                                                        @endforeach
                                                        @if($piMachine->description)
                                                            <div class="spec-badge" style="display: inline-flex; align-items: start; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fbbf24; border-radius: 6px; padding: 8px 12px; margin: 0; width: 100%; margin-top: 8px;">
                                                                <span class="text-muted" style="font-size: 0.75rem; font-weight: 600; margin-right: 6px; text-transform: uppercase; letter-spacing: 0.3px;">Description:</span>
                                                                <span style="font-size: 0.875rem; color: #78350f; flex: 1;">{{ $piMachine->description }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-center" style="vertical-align: middle; border: none;">
                                                    <span class="badge rounded-pill px-3 py-2" style="font-size: 1rem; min-width: 50px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                        {{ $piMachine->quantity }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-5" style="border: none;">
                                                    <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.2; color: #9ca3af;"></i>
                                                    <div style="font-size: 1rem;">No machines found in this PI</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-bold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
                                Payment Details (First $ Transaction for this PI)
                                <button type="button" @click="loadPaymentDetails()" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-sync me-1"></i>Load Payment
                                </button>
                            </h5>
                        </div>

                        <div class="col-12" id="paymentDetailsSection" x-show="paymentDetails !== null" x-cloak>
                            <div class="alert alert-info">
                                <template x-if="paymentDetails">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <strong>Amount:</strong> <span x-text="paymentDetails.amount ? '$' + parseFloat(paymentDetails.amount).toFixed(2) : 'N/A'"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Payment Date:</strong> <span x-text="paymentDetails.payment_date || 'N/A'"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Payment Method:</strong> <span x-text="paymentDetails.payment_method || 'N/A'"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Transaction ID:</strong> <span x-text="paymentDetails.transaction_id || 'N/A'"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Payee Country:</strong> <span x-text="paymentDetails.payee_country || 'N/A'"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Payment To Seller:</strong> <span x-text="paymentDetails.payment_to_seller || 'N/A'"></span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="!paymentDetails" class="text-muted">
                                    No payment transaction found with $ currency for this PI
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Details -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-bold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Shipping Details</h5>
                        </div>

                        <div class="col-md-6">
                            <label for="no_of_bill" class="form-label fw-semibold">No of Bill</label>
                            <input type="number" name="no_of_bill" id="no_of_bill" 
                                   class="form-control @error('no_of_bill') is-invalid @enderror" 
                                   value="{{ old('no_of_bill') }}" 
                                   min="0"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('no_of_bill')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="no_of_container" class="form-label fw-semibold">No of Container</label>
                            <input type="number" name="no_of_container" id="no_of_container" 
                                   class="form-control @error('no_of_container') is-invalid @enderror" 
                                   value="{{ old('no_of_container') }}" 
                                   min="0"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('no_of_container')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="size_of_container" class="form-label fw-semibold">Size of Container</label>
                            <input type="text" name="size_of_container" id="size_of_container" 
                                   class="form-control @error('size_of_container') is-invalid @enderror" 
                                   value="{{ old('size_of_container') }}" 
                                   placeholder="e.g., 20ft, 40ft"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('size_of_container')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="port_of_destination_id" class="form-label fw-semibold">Port of Destination</label>
                            <select name="port_of_destination_id" id="port_of_destination_id" 
                                    class="form-select @error('port_of_destination_id') is-invalid @enderror"
                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                <option value="">Select Port of Destination</option>
                                @foreach($portOfDestinations as $port)
                                    <option value="{{ $port->id }}" {{ old('port_of_destination_id') == $port->id ? 'selected' : '' }}>
                                        {{ $port->name }} @if($port->code)({{ $port->code }})@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('port_of_destination_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Attachments -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-bold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Attachments</h5>
                        </div>

                        <div class="col-12">
                            <label for="attachments" class="form-label fw-semibold">Upload Files</label>
                            <input type="file" name="attachments[]" id="attachments" 
                                   class="form-control @error('attachments.*') is-invalid @enderror" 
                                   multiple
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <small class="text-muted">You can select multiple files. Accepted formats: PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR (Max 10MB per file)</small>
                            @error('attachments.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="col-12 mt-4">
                            <label for="notes" class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      class="form-control @error('notes') is-invalid @enderror"
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-12 mt-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Purchase Order
                                </button>
                                <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
        @endif
    </div>
</x-app-layout>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('purchaseOrderForm', (selectedPIId) => ({
        selectedProformaInvoiceId: selectedPIId,
        paymentDetails: null,
        
        init() {
            // Wait a bit for the DOM to be ready
            setTimeout(() => {
                if (this.selectedProformaInvoiceId) {
                    this.loadPaymentDetails();
                }
            }, 100);
        },
        
        selectProformaInvoice(piId) {
            this.selectedProformaInvoiceId = piId;
            this.paymentDetails = null;
            window.location.href = '{{ route('purchase-orders.create') }}?proforma_invoice_id=' + piId + 
                '&sales_manager_id={{ request('sales_manager_id') }}' +
                '&pi_number={{ request('pi_number') }}' +
                '&customer_name={{ request('customer_name') }}';
        },
        
        loadPaymentDetails() {
            if (!this.selectedProformaInvoiceId) {
                return;
            }
            
            // Construct the URL properly
            const baseUrl = '{{ url('/') }}';
            const url = baseUrl + '/purchase-orders/get-first-payment/' + this.selectedProformaInvoiceId;
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.payment) {
                        this.paymentDetails = data.payment;
                        const paymentSection = document.getElementById('paymentDetailsSection');
                        if (paymentSection) {
                            paymentSection.style.display = 'block';
                        }
                    } else {
                        this.paymentDetails = null;
                        const paymentSection = document.getElementById('paymentDetailsSection');
                        if (paymentSection) {
                            paymentSection.style.display = 'block';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading payment details:', error);
                    this.paymentDetails = null;
                });
        }
    }));
});
</script>
