<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Over Invoice List</h1>
            <p class="text-muted mb-0">Contracts where total Proforma Invoice amount exceeds Contract amount</p>
        </div>
        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Contracts
        </a>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 pb-0" style="background: transparent;">
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Over Invoice Contracts</h2>
                    <span class="badge ms-3" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.875rem; padding: 0.35rem 0.65rem;">{{ $overInvoices->total() }} Total</span>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="card shadow-sm border-0 mb-3" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('contracts.over-invoice') }}">
                    <!-- Filter Fields Row -->
                    <div class="row g-3 mb-3">
                        <!-- Sales Manager Filter -->
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label for="contract_number" class="form-label fw-semibold" style="color: #374151; font-size: 0.875rem; margin-bottom: 0.5rem;">Contract Number</label>
                            <input type="text" name="contract_number" id="contract_number" 
                                   value="{{ request('contract_number') }}" 
                                   class="form-control" 
                                   placeholder="Enter contract number" 
                                   style="border-radius: 8px; border: 1px solid #e5e7eb; background-color: #ffffff;">
                        </div>

                        <!-- Customer Name Filter -->
                        <div class="col-md-4">
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
                        <a href="{{ route('contracts.over-invoice') }}" class="btn d-flex align-items-center gap-2" style="background-color: #ffffff; color: #374151; border: 1px solid #e5e7eb; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
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
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Contract Number</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Buyer Name</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Total Contract Amount</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Total Proforma Amount</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Difference Amount (PI - Contract)</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overInvoices as $contract)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <div class="fw-semibold" style="color: #1f2937;">{{ $contract->contract_number }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold" style="color: #1f2937;">{{ $contract->buyer_name }}</div>
                                    @if($contract->company_name)
                                        <small class="text-muted">{{ $contract->company_name }}</small>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-bold" style="color: #059669;">
                                        ${{ number_format($contract->total_amount ?? 0, 2) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-bold" style="color: #dc2626;">
                                        ${{ number_format($contract->total_pi_amount ?? 0, 2) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-bold" style="color: #dc2626; font-size: 1.1rem;">
                                        ${{ number_format($contract->difference_amount ?? 0, 2) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex gap-2" role="group">
                                        <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-outline-info" title="View Contract">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('proforma-invoices.index') }}?contract_number={{ $contract->contract_number }}" class="btn btn-sm btn-outline-primary" title="View Proforma Invoices">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-check-circle fa-3x mb-3" style="color: #10b981; opacity: 0.5;"></i>
                                        <p class="mb-0">No over invoices found.</p>
                                        <small class="text-muted mt-1">All contracts are within their proforma invoice limits</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($overInvoices->hasPages())
            <div class="card-footer border-0 bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $overInvoices->firstItem() ?? 0 }} to {{ $overInvoices->lastItem() ?? 0 }} of {{ $overInvoices->total() }} over invoices
                    </div>
                    <div>
                        {{ $overInvoices->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer border-0 bg-transparent">
                <div class="text-muted small text-center">
                    Showing {{ $overInvoices->count() }} of {{ $overInvoices->total() }} over invoices
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
