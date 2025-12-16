<x-app-layout>
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Contracts Management</h1>
                <p class="text-muted mb-0">View and manage all contracts with their status</p>
            </div>
            <a href="{{ route('leads.index') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Contract from Lead
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-file-contract text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Contracts List</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: calc(100vh - 350px); overflow-y: auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="sticky-top" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Contract Number</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Buyer Name</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Company</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Total Amount</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Approval Status</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Created</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contracts as $contract)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold" style="color: #1f2937;">{{ $contract->contract_number }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold" style="color: #1f2937;">{{ $contract->buyer_name }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div style="color: #6b7280;">{{ $contract->company_name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="fw-bold" style="color: var(--primary-color);">${{ number_format($contract->total_amount ?? 0, 2) }}</div>
                                        </td>
                                        <td class="px-4 py-3">
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
                                            @elseif($contract->approval_status === 'rejected')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Rejected
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Draft</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <small class="text-muted">{{ $contract->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex gap-2" role="group">
                                                <a href="{{ route('contracts.download-pdf', $contract) }}" class="btn btn-sm btn-outline-success" title="Download PDF" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                @if(!$contract->customer_signature)
                                                    <a href="{{ route('contracts.signature', $contract) }}" class="btn btn-sm btn-outline-primary" title="Sign Contract">
                                                        <i class="fas fa-signature"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('contracts.signature', $contract) }}" class="btn btn-sm btn-outline-info" title="View Signature">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-outline-secondary" title="Edit Contract Details">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this contract?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Contract">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">No contracts found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($contracts->hasPages())
                    <div class="card-footer bg-transparent border-top">
                        <div class="d-flex justify-content-center">
                            {{ $contracts->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
</x-app-layout>
