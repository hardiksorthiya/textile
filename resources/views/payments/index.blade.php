<x-app-layout>
    <style>
        .action-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border-radius: 6px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .action-btn-view {
            border: 1px solid #06b6d4;
            color: #06b6d4;
        }
        .action-btn-view:hover {
            background-color: #06b6d4;
            color: white;
        }
        .action-btn-edit {
            border: 1px solid #800020;
            color: #800020;
        }
        .action-btn-edit:hover {
            background-color: #800020;
            color: white;
        }
        .action-btn-pdf {
            border: 1px solid #10b981;
            color: #10b981;
        }
        .action-btn-pdf:hover {
            background-color: #10b981;
            color: white;
        }
        .action-btn-delete {
            border: 1px solid #dc2626;
            color: #dc2626;
        }
        .action-btn-delete:hover {
            background-color: #dc2626;
            color: white;
        }
    </style>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Payment Management</h1>
            <p class="text-muted mb-0">View and manage all collected and returned payments</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('payments.collect-payment') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Add Payment
            </a>
            <a href="{{ route('payments.return-payment') }}" class="btn btn-danger">
                <i class="fas fa-undo me-2"></i>Return Payment
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 pb-0" style="background: transparent;">
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-money-bill-wave text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">All Payments</h2>
                    <span class="badge ms-3" style="background-color: color-mix(in srgb, #3b82f6 15%, #ffffff); color: #2563eb; font-size: 0.875rem; padding: 0.35rem 0.65rem;">{{ $payments->total() }} Total</span>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="card shadow-sm border-0 mb-3" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('payments.index') }}">
                    <!-- Filter Fields Row -->
                    <div class="row g-3 mb-3">
                        <!-- Payment Type Filter -->
                        <div class="col-md-3">
                            <label for="type" class="form-label fw-semibold" style="color: #374151; font-size: 0.875rem; margin-bottom: 0.5rem;">Payment Type</label>
                            <select name="type" id="type" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb; background-color: #ffffff;">
                                <option value="">All Payments</option>
                                <option value="collect" {{ request('type') == 'collect' ? 'selected' : '' }}>Collect Payment</option>
                                <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return Payment</option>
                            </select>
                        </div>

                        <!-- Sales Manager Filter -->
                        <div class="col-md-3">
                            <label for="sales_manager" class="form-label fw-semibold" style="color: #374151; font-size: 0.875rem; margin-bottom: 0.5rem;">Sales Manager</label>
                            <select name="sales_manager" id="sales_manager" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb; background-color: #ffffff;">
                                <option value="">All Sales Managers</option>
                                @foreach($salesManagers as $manager)
                                    <option value="{{ $manager->id }}" {{ request('sales_manager') == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Contract Number Filter -->
                        <div class="col-md-3">
                            <label for="contract_number" class="form-label fw-semibold" style="color: #374151; font-size: 0.875rem; margin-bottom: 0.5rem;">Contract Number</label>
                            <input type="text" name="contract_number" id="contract_number" 
                                   value="{{ request('contract_number') }}" 
                                   class="form-control" 
                                   placeholder="Enter contract number" 
                                   style="border-radius: 8px; border: 1px solid #e5e7eb; background-color: #ffffff;">
                        </div>

                        <!-- Customer Name Filter -->
                        <div class="col-md-3">
                            <label for="customer_name" class="form-label fw-semibold" style="color: #374151; font-size: 0.875rem; margin-bottom: 0.5rem;">Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" 
                                   value="{{ request('customer_name') }}" 
                                   class="form-control" 
                                   placeholder="Enter customer name" 
                                   style="border-radius: 8px; border: 1px solid #e5e7eb; background-color: #ffffff;">
                        </div>
                    </div>

                    <!-- Action Buttons Row -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn d-flex align-items-center gap-2" style="background: linear-gradient(to bottom, #dc2626, #b91c1c); color: #ffffff; border: none; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="fas fa-search"></i>
                            <span>Search</span>
                        </button>
                        <a href="{{ route('payments.index') }}" class="btn d-flex align-items-center gap-2" style="background-color: #ffffff; color: #374151; border: 1px solid #e5e7eb; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                            <i class="fas fa-redo"></i>
                            <span>Reset</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: calc(100vh - 400px); overflow-y: auto;">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="sticky-top" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                        <tr>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Type</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Payment Date</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Contract Number</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">PI Number</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Customer Name</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Amount</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Payment Method</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Created By</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold text-center" style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    @if($payment->type === 'collect')
                                        <span class="badge bg-success">Collect</span>
                                    @else
                                        <span class="badge bg-danger">Return</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold" style="color: #1f2937;">{{ $payment->payment_date->format('M d, Y') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div style="color: #6b7280;">
                                        {{ $payment->contract->contract_number ?? ($payment->proformaInvoice->contract->contract_number ?? 'N/A') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div style="color: #6b7280;">{{ $payment->proformaInvoice->proforma_invoice_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold" style="color: #1f2937;">
                                        {{ $payment->contract->buyer_name ?? ($payment->proformaInvoice->buyer_company_name ?? 'N/A') }}
                                        @if($payment->contract && $payment->contract->company_name)
                                            <br><small class="text-muted">({{ $payment->contract->company_name }})</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-bold" style="{{ $payment->type === 'collect' ? 'color: #10b981;' : 'color: #dc2626;' }}">
                                        ${{ number_format($payment->amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-info text-capitalize">{{ $payment->payment_method ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <small class="text-muted">{{ $payment->creator->name ?? 'N/A' }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('payments.show', $payment->id) }}" class="action-btn action-btn-view" title="View">
                                            <i class="fas fa-eye" style="font-size: 14px;"></i>
                                        </a>
                                        <a href="{{ route('payments.edit', $payment->id) }}" class="action-btn action-btn-edit" title="Edit">
                                            <i class="fas fa-edit" style="font-size: 14px;"></i>
                                        </a>
                                        <a href="{{ route('payments.download-pdf', $payment->id) }}" class="action-btn action-btn-pdf" title="Download PDF">
                                            <i class="fas fa-file-pdf" style="font-size: 14px;"></i>
                                        </a>
                                        <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                                <i class="fas fa-trash" style="font-size: 14px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-money-bill-wave fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                        <p class="mb-0">No payments found.</p>
                                        <small class="text-muted mt-1">Add your first payment to get started</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($payments->hasPages())
            <div class="card-footer border-0 bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} payments
                    </div>
                    <div>
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer border-0 bg-transparent">
                <div class="text-muted small text-center">
                    Showing {{ $payments->count() }} of {{ $payments->total() }} payments
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
