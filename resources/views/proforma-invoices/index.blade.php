<x-app-layout>
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Proforma Invoices Management</h1>
                <p class="text-muted mb-0">View and manage all proforma invoices with their details</p>
            </div>
            @can('create proforma invoices')
            <a href="{{ route('proforma-invoices.create') }}" class="btn btn-success">
                <i class="fas fa-file-invoice me-2"></i>Create Proforma Invoice
            </a>
            @endcan
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 pb-0" style="background: transparent;">
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-file-invoice text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">PI List</h2>
                    <span class="badge ms-3" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.875rem; padding: 0.35rem 0.65rem;">{{ $proformaInvoices->total() }} Total</span>
                </div>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="card shadow-sm border-0 mb-3" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('proforma-invoices.index') }}" class="d-flex align-items-center gap-2">
                    <div class="flex-grow-1 position-relative">
                        <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; z-index: 10;"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="form-control ps-5" 
                               placeholder="Search by PI number, contract number, customer name, seller..." 
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                    </div>
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 40px; height: 40px;" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request()->hasAny(['search', 'pi_number', 'contract_number', 'customer_name', 'seller_id']))
                        <a href="{{ route('proforma-invoices.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 40px; height: 40px;" title="Clear Filters">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: calc(100vh - 400px); overflow-y: auto;">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="sticky-top" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                        <tr>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">PI Number</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Contract Number</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Customer Name</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Seller</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Total Amount</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Type of Sale</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Created</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proformaInvoices as $pi)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <div class="fw-semibold" style="color: #1f2937;">{{ $pi->proforma_invoice_number }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div style="color: #6b7280;">{{ $pi->contract->contract_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold" style="color: #1f2937;">{{ $pi->buyer_company_name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div style="color: #6b7280;">{{ $pi->seller->seller_name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-bold" style="color: var(--primary-color);">
                                        {{ $pi->currency === 'INR' ? '₹' : '$' }}{{ number_format($pi->total_amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-info text-capitalize">{{ $pi->type_of_sale }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <small class="text-muted">{{ $pi->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex gap-2" role="group">
                                        @can('view proforma invoices')
                                        <a href="{{ route('proforma-invoices.show', $pi) }}" class="btn btn-sm btn-outline-info" title="View PI Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('edit proforma invoices')
                                        <a href="{{ route('proforma-invoices.edit', $pi) }}" class="btn btn-sm btn-outline-secondary" title="Edit PI">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete proforma invoices')
                                        <form action="{{ route('proforma-invoices.destroy', $pi) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this proforma invoice?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete PI">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-file-invoice fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                        <p class="mb-0">No proforma invoices found.</p>
                                        <small class="text-muted mt-1">Create your first proforma invoice to get started</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($proformaInvoices->hasPages())
            <div class="card-footer border-0 bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $proformaInvoices->firstItem() ?? 0 }} to {{ $proformaInvoices->lastItem() ?? 0 }} of {{ $proformaInvoices->total() }} proforma invoices
                    </div>
                    <div>
                        {{ $proformaInvoices->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer border-0 bg-transparent">
                <div class="text-muted small text-center">
                    Showing {{ $proformaInvoices->count() }} of {{ $proformaInvoices->total() }} proforma invoices
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
