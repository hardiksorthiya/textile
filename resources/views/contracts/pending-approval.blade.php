<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Pending Contract Approvals</h1>
            <p class="text-muted mb-0">Review and approve contracts signed by customers</p>
        </div>
        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>All Contracts
        </a>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Contracts Awaiting Approval</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: calc(100vh - 350px); overflow-y: auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="sticky-top" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Contract Number</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Buyer Name</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Total Amount</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Signed Date</th>
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
                                            <small class="text-muted">{{ $contract->company_name ?? '' }}</small>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="fw-bold" style="color: var(--primary-color);">${{ number_format($contract->total_amount ?? 0, 2) }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <small class="text-muted">{{ $contract->updated_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex gap-2">
                                                <button type="button" 
                                                        class="btn btn-sm btn-success" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#approveModal{{ $contract->id }}"
                                                        style="border-radius: 6px;">
                                                    <i class="fas fa-check me-1"></i>Approve
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal{{ $contract->id }}"
                                                        style="border-radius: 6px;">
                                                    <i class="fas fa-times me-1"></i>Reject
                                                </button>
                                                <a href="{{ route('contracts.signature', $contract) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;" title="View Contract & Signature">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px;" title="Edit Contract Details">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal{{ $contract->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Approve Contract</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('contracts.approve', $contract) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to approve contract <strong>{{ $contract->contract_number }}</strong>?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Buyer: <strong>{{ $contract->buyer_name }}</strong></label>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Total Amount: <strong>${{ number_format($contract->total_amount ?? 0, 2) }}</strong></label>
                                                        </div>
                                                        @if($contract->customer_signature)
                                                            <div class="mb-3">
                                                                <label class="form-label">Customer Signature:</label>
                                                                <div class="border rounded p-2" style="background: white;">
                                                                    <img src="{{ $contract->customer_signature }}" alt="Signature" style="max-width: 100%; height: auto; max-height: 150px;">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="mb-3">
                                                            <label class="form-label">Approval Notes (Optional)</label>
                                                            <textarea name="approval_notes" class="form-control" rows="3" placeholder="Add any notes about this approval..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-check me-1"></i>Approve Contract
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $contract->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Contract</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('contracts.reject', $contract) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to reject contract <strong>{{ $contract->contract_number }}</strong>?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Buyer: <strong>{{ $contract->buyer_name }}</strong></label>
                                                        </div>
                                                        @if($contract->customer_signature)
                                                            <div class="mb-3">
                                                                <label class="form-label">Customer Signature:</label>
                                                                <div class="border rounded p-2" style="background: white;">
                                                                    <img src="{{ $contract->customer_signature }}" alt="Signature" style="max-width: 100%; height: auto; max-height: 150px;">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="mb-3">
                                                            <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                                            <textarea name="approval_notes" class="form-control @error('approval_notes') is-invalid @enderror" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                                                            @error('approval_notes')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-times me-1"></i>Reject Contract
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="fas fa-check-circle fa-3x mb-3" style="color: #d1d5db;"></i>
                                            <p class="mb-0">No contracts pending approval.</p>
                                        </td>
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
</x-app-layout>
