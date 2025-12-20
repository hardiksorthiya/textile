<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Purchase Order Management</h1>
            <p class="text-muted mb-0">View and manage all purchase orders</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Create Purchase Order
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 pb-0" style="background: transparent;">
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-shopping-cart text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">All Purchase Orders</h2>
                    <span class="badge ms-3" style="background-color: color-mix(in srgb, #3b82f6 15%, #ffffff); color: #2563eb; font-size: 0.875rem; padding: 0.35rem 0.65rem;">{{ $purchaseOrders->total() }} Total</span>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: calc(100vh - 400px); overflow-y: auto;">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="sticky-top" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                        <tr>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">PO Number</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">PI Number</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Buyer Name</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Port of Destination</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Created By</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Created At</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold text-center" style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <div class="fw-semibold" style="color: #1f2937;">{{ $po->purchase_order_number }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div style="color: #6b7280;">{{ $po->proformaInvoice->proforma_invoice_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold" style="color: #1f2937;">{{ $po->buyer_name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div style="color: #6b7280;">{{ $po->portOfDestination->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <small class="text-muted">{{ $po->creator->name ?? 'N/A' }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    <small class="text-muted">{{ $po->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('purchase-orders.show', $po->id) }}" class="action-btn action-btn-view" title="View">
                                            <i class="fas fa-eye" style="font-size: 14px;"></i>
                                        </a>
                                        <a href="{{ route('purchase-orders.edit', $po->id) }}" class="action-btn action-btn-edit" title="Edit">
                                            <i class="fas fa-edit" style="font-size: 14px;"></i>
                                        </a>
                                        <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this purchase order?');">
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
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-shopping-cart fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                        <p class="mb-0">No purchase orders found.</p>
                                        <small class="text-muted mt-1">Create your first purchase order to get started</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($purchaseOrders->hasPages())
            <div class="card-footer border-0 bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $purchaseOrders->firstItem() ?? 0 }} to {{ $purchaseOrders->lastItem() ?? 0 }} of {{ $purchaseOrders->total() }} purchase orders
                    </div>
                    <div>
                        {{ $purchaseOrders->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer border-0 bg-transparent">
                <div class="text-muted small text-center">
                    Showing {{ $purchaseOrders->count() }} of {{ $purchaseOrders->total() }} purchase orders
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

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
        border: 1px solid;
    }
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .action-btn-view {
        border-color: #06b6d4;
        color: #06b6d4;
    }
    .action-btn-view:hover {
        background-color: #06b6d4;
        color: white;
    }
    .action-btn-edit {
        border-color: #800020;
        color: #800020;
    }
    .action-btn-edit:hover {
        background-color: #800020;
        color: white;
    }
    .action-btn-delete {
        border-color: #dc2626;
        color: #dc2626;
    }
    .action-btn-delete:hover {
        background-color: #dc2626;
        color: white;
    }
</style>
