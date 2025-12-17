<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Proforma Invoice Details</h1>
            <p class="text-muted mb-0">Proforma Invoice: {{ $proformaInvoice->proforma_invoice_number }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('proforma-invoices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create New
            </a>
            <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Contracts
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Proforma Invoice Information Card -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-file-invoice text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Proforma Invoice Information</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Proforma Invoice Number</label>
                            <div class="fw-bold" style="color: #1f2937;">{{ $proformaInvoice->proforma_invoice_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Contract Number</label>
                            <div style="color: #1f2937;">{{ $proformaInvoice->contract->contract_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Customer Name</label>
                            <div class="fw-semibold" style="color: #1f2937;">{{ $proformaInvoice->contract->buyer_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Company Name</label>
                            <div style="color: #1f2937;">{{ $proformaInvoice->contract->company_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Created By</label>
                            <div style="color: #1f2937;">{{ $proformaInvoice->creator->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Total Amount</label>
                            <div class="fw-bold text-primary" style="font-size: 1.125rem;">
                                {{ $proformaInvoice->currency === 'INR' ? '₹' : '$' }}{{ number_format($proformaInvoice->total_amount, 2) }}
                            </div>
                        </div>
                        @if($proformaInvoice->billing_address)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Billing Address</label>
                            <div style="color: #1f2937; white-space: pre-line;">{{ $proformaInvoice->billing_address }}</div>
                        </div>
                        @endif
                        @if($proformaInvoice->shipping_address)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Shipping Address</label>
                            <div style="color: #1f2937; white-space: pre-line;">{{ $proformaInvoice->shipping_address }}</div>
                        </div>
                        @endif
                        @if($proformaInvoice->notes)
                        <div class="col-12">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Notes</label>
                            <div style="color: #1f2937;">{{ $proformaInvoice->notes }}</div>
                        </div>
                        @endif
                    </div>

                    <!-- Machine Details -->
                    @if($proformaInvoice->proformaInvoiceMachines->count() > 0)
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-bold mb-3" style="color: #1f2937;">Machine Details</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proformaInvoice->proformaInvoiceMachines as $piMachine)
                                        @php
                                            $contractMachine = $piMachine->contractMachine;
                                        @endphp
                                        <tr>
                                            <td>{{ $contractMachine->machineCategory->name ?? 'N/A' }}</td>
                                            <td>{{ $contractMachine->brand->name ?? 'N/A' }}</td>
                                            <td>{{ $contractMachine->machineModel->model_no ?? 'N/A' }}</td>
                                            <td>{{ $piMachine->quantity }}</td>
                                            <td>${{ number_format($contractMachine->amount, 2) }}</td>
                                            <td class="fw-bold text-primary">${{ number_format($piMachine->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="5" class="text-end">Total:</td>
                                        <td class="text-primary">${{ number_format($proformaInvoice->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contract Information Sidebar -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Contract Information</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Contract Number</label>
                        <div style="color: #1f2937;">{{ $proformaInvoice->contract->contract_number }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Business Firm</label>
                        <div style="color: #1f2937;">{{ $proformaInvoice->contract->businessFirm->name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Location</label>
                        <div style="color: #1f2937;">
                            {{ $proformaInvoice->contract->area->name ?? '' }}, 
                            {{ $proformaInvoice->contract->city->name ?? '' }}, 
                            {{ $proformaInvoice->contract->state->name ?? '' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Total Contract Amount</label>
                        <div class="fw-bold text-primary">${{ number_format($proformaInvoice->contract->total_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('contracts.show', $proformaInvoice->contract) }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-eye me-2"></i>View Contract
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>