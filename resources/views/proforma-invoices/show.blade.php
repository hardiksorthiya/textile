@php
use Illuminate\Support\Facades\Storage;
@endphp
<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Proforma Invoice Details</h1>
            <p class="text-muted mb-0">Proforma Invoice: {{ $proformaInvoice->proforma_invoice_number }}</p>
        </div>
        <div class="d-flex gap-2">
            @can('view proforma invoices')
            <a href="{{ route('proforma-invoices.download-pdf', $proformaInvoice) }}" class="btn btn-success" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </a>
            @endcan
            @canany(['view contract approvals', 'convert contract'])
            <a href="{{ route('machine-statuses.create', ['proforma_invoice_id' => $proformaInvoice->id]) }}" class="btn btn-primary">
                <i class="fas fa-tasks me-2"></i>Status
            </a>
            @endcanany
            @can('edit proforma invoices')
            <a href="{{ route('proforma-invoices.edit', $proformaInvoice) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('proforma-invoices.delivery-details', $proformaInvoice) }}" class="btn btn-info">
                <i class="fas fa-truck me-2"></i>Delivery Details
            </a>
            @endcan
            @can('delete proforma invoices')
            <form action="{{ route('proforma-invoices.destroy', $proformaInvoice) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this proforma invoice?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
            </form>
            @endcan
            <a href="{{ route('proforma-invoices.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to PI List
            </a>
            <a href="{{ route('proforma-invoices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create New
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
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Seller</label>
                            <div style="color: #1f2937;">{{ $proformaInvoice->seller->seller_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="color: #6b7280; font-size: 0.875rem;">Total Amount</label>
                            <div class="fw-bold text-primary" style="font-size: 1.125rem;">
                                @php
                                    // Match frontend logic: recalculate USD amount from machines (before currency conversion)
                                    if (!$proformaInvoice->relationLoaded('proformaInvoiceMachines')) {
                                        $proformaInvoice->load('proformaInvoiceMachines');
                                    }
                                    
                                    $totalMachineAmount = 0;
                                    $totalCommissionAmount = 0;
                                    
                                    foreach ($proformaInvoice->proformaInvoiceMachines as $machine) {
                                        $unitAmount = $machine->amount ?? 0;
                                        $amcPrice = $machine->amc_price ?? 0;
                                        $piMachineAmount = $unitAmount * ($machine->quantity ?? 0);
                                        $piTotalAmount = $piMachineAmount + $amcPrice;
                                        $totalMachineAmount += $piTotalAmount;
                                        
                                        // Commission Amount (for High Seas only, per machine)
                                        if ($proformaInvoice->type_of_sale === 'high_seas' && $proformaInvoice->commission) {
                                            $commissionAmount = ($piTotalAmount * $proformaInvoice->commission) / 100;
                                            $totalCommissionAmount += $commissionAmount;
                                        }
                                    }
                                    
                                    // Add stored totals (calculated per-machine and summed)
                                    $overseasFreight = $proformaInvoice->overseas_freight ?? 0;
                                    $portExpensesClearing = $proformaInvoice->port_expenses_clearing ?? 0;
                                    $gstAmount = $proformaInvoice->gst_amount ?? 0;
                                    
                                    // Final amount in USD (before currency conversion) - matches frontend totalFinalAmountUSD
                                    $displayAmount = $totalMachineAmount + $totalCommissionAmount + $overseasFreight + $portExpensesClearing + $gstAmount;
                                    $currencySymbol = $proformaInvoice->currency === 'INR' ? '₹' : '$';
                                @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <span>{{ $currencySymbol }}{{ number_format($displayAmount, 2) }}</span>
                                    @if($proformaInvoice->type_of_sale === 'local' && $proformaInvoice->usd_rate)
                                        <!-- Show USD equivalent for Local -->
                                        <span class="text-success" style="font-size: 0.875rem;">
                                            (${{ number_format($displayAmount / $proformaInvoice->usd_rate, 2) }})
                                        </span>
                                    @elseif($proformaInvoice->type_of_sale === 'high_seas' && $proformaInvoice->usd_rate)
                                        <!-- Show INR equivalent for High Seas -->
                                        <span class="text-success" style="font-size: 0.875rem;">
                                            (₹{{ number_format($displayAmount * $proformaInvoice->usd_rate, 2) }})
                                        </span>
                                    @endif
                                </div>
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
                                        <td class="text-primary">
                                            @php
                                                // Use the same calculation as above
                                                if (!isset($displayAmount)) {
                                                    if (!$proformaInvoice->relationLoaded('proformaInvoiceMachines')) {
                                                        $proformaInvoice->load('proformaInvoiceMachines');
                                                    }
                                                    
                                                    $totalMachineAmount = 0;
                                                    $totalCommissionAmount = 0;
                                                    
                                                    foreach ($proformaInvoice->proformaInvoiceMachines as $machine) {
                                                        $unitAmount = $machine->amount ?? 0;
                                                        $amcPrice = $machine->amc_price ?? 0;
                                                        $piMachineAmount = $unitAmount * ($machine->quantity ?? 0);
                                                        $piTotalAmount = $piMachineAmount + $amcPrice;
                                                        $totalMachineAmount += $piTotalAmount;
                                                        
                                                        if ($proformaInvoice->type_of_sale === 'high_seas' && $proformaInvoice->commission) {
                                                            $commissionAmount = ($piTotalAmount * $proformaInvoice->commission) / 100;
                                                            $totalCommissionAmount += $commissionAmount;
                                                        }
                                                    }
                                                    
                                                    $overseasFreight = $proformaInvoice->overseas_freight ?? 0;
                                                    $portExpensesClearing = $proformaInvoice->port_expenses_clearing ?? 0;
                                                    $gstAmount = $proformaInvoice->gst_amount ?? 0;
                                                    
                                                    $displayAmount = $totalMachineAmount + $totalCommissionAmount + $overseasFreight + $portExpensesClearing + $gstAmount;
                                                }
                                                $currencySymbol = $proformaInvoice->currency === 'INR' ? '₹' : '$';
                                            @endphp
                                            {{ $currencySymbol }}{{ number_format($displayAmount, 2) }}
                                        </td>
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

    <!-- Delivery Details Section -->
    @if($proformaInvoice->deliveryDetails && $proformaInvoice->deliveryDetails->count() > 0)
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                <i class="fas fa-truck text-white"></i>
                            </div>
                            <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Delivery Details</h2>
                        </div>
                        @can('edit proforma invoices')
                        <a href="{{ route('proforma-invoices.delivery-details', $proformaInvoice) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Edit Delivery Details
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                <tr>
                                    <th style="width: 5%;" class="text-center">Status</th>
                                    <th style="width: 5%;" class="text-center">S.No</th>
                                    <th style="width: 25%;">Document Name</th>
                                    <th style="width: 18%;">Date</th>
                                    <th style="width: 22%;">Document Number</th>
                                    <th style="width: 15%;">No. of Copies</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proformaInvoice->deliveryDetails->sortBy('sort_order') as $index => $detail)
                                <tr>
                                    <td class="text-center">
                                        @if($detail->is_received)
                                            <span class="badge bg-success" title="Received">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-secondary" title="Pending">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $detail->document_name }}</td>
                                    <td>{{ $detail->date ? $detail->date->format('d-m-Y') : '-' }}</td>
                                    <td>{{ $detail->document_number ?? '-' }}</td>
                                    <td>{{ $detail->no_of_copies ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Uploaded Images Section -->
                    @if($proformaInvoice->documents && $proformaInvoice->documents->count() > 0)
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-bold mb-3" style="color: #1f2937;">
                            <i class="fas fa-images me-2"></i>Uploaded Images
                        </h5>
                        <div class="row g-3">
                            @foreach($proformaInvoice->documents as $image)
                            <div class="col-md-3">
                                <div class="card border position-relative">
                                    <img src="{{ Storage::url($image->file_path) }}" 
                                         class="card-img-top" 
                                         style="height: 150px; object-fit: cover; cursor: pointer;" 
                                         alt="{{ $image->file_name }}"
                                         onclick="window.open('{{ Storage::url($image->file_path) }}', '_blank')"
                                         onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                    <div class="card-body p-2">
                                        <small class="text-muted d-block text-truncate" title="{{ $image->file_name }}">
                                            {{ $image->file_name }}
                                        </small>
                                        <small class="text-muted">{{ number_format($image->file_size / 1024, 2) }} KB</small>
                                    </div>
                                    <a href="{{ Storage::url($image->file_path) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary position-absolute top-0 end-0 m-2" 
                                       title="View Full Size">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>