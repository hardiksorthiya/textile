<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Delivery Details & Documents</h1>
            <p class="text-muted mb-0">View and manage delivery details for all Proforma Invoices</p>
        </div>
        <div>
            <a href="{{ route('proforma-invoices.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list me-2"></i>View All PIs
            </a>
        </div>
    </div>

    <!-- Search/Filter Section -->
    <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 pb-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-search text-white"></i>
                </div>
                <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Search Delivery Details</h2>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('proforma-invoices.delivery-details-index') }}" id="searchForm">
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
                            <a href="{{ route('proforma-invoices.delivery-details-index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Section -->
    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-body p-4">
            @if($proformaInvoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                            <tr>
                                <th>S.No</th>
                                <th>PI Number</th>
                                <th>Customer Name</th>
                                <th>Sales Manager</th>
                                <th>Total Amount</th>
                                <th>Delivery Details Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proformaInvoices as $index => $pi)
                            <tr>
                                <td>{{ ($proformaInvoices->currentPage() - 1) * $proformaInvoices->perPage() + $index + 1 }}</td>
                                <td class="fw-semibold">{{ $pi->proforma_invoice_number }}</td>
                                <td>{{ $pi->buyer_company_name }}</td>
                                <td>{{ $pi->contract->creator->name ?? ($pi->creator->name ?? 'N/A') }}</td>
                                <td>${{ number_format($pi->total_amount ?? 0, 2) }}</td>
                                <td>
                                    @php
                                        $checkedCount = $pi->deliveryDetails ? $pi->deliveryDetails->where('is_received', true)->count() : 0;
                                        $totalCount = $pi->deliveryDetails ? $pi->deliveryDetails->count() : 0;
                                    @endphp
                                    @if($checkedCount > 0)
                                        <span class="badge bg-success">{{ $checkedCount }} Received</span>
                                        @if($totalCount > $checkedCount)
                                            <span class="badge bg-secondary ms-1">{{ $totalCount - $checkedCount }} Pending</span>
                                        @endif
                                    @elseif($totalCount > 0)
                                        <span class="badge bg-warning">{{ $totalCount }} Pending</span>
                                    @else
                                        <span class="badge bg-secondary">No Details</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('proforma-invoices.show', $pi) }}" class="btn btn-sm btn-outline-primary" title="View PI">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('edit proforma invoices')
                                        <a href="{{ route('proforma-invoices.delivery-details', $pi) }}" class="btn btn-sm {{ $pi->deliveryDetails && $pi->deliveryDetails->count() > 0 ? 'btn-info' : 'btn-success' }}" title="{{ $pi->deliveryDetails && $pi->deliveryDetails->count() > 0 ? 'Edit' : 'Add' }} Delivery Details">
                                            <i class="fas fa-truck"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $proformaInvoices->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                    <p class="text-muted mb-2">No Proforma Invoices found matching your search criteria.</p>
                    <p class="text-muted small">Try adjusting your filters or <a href="{{ route('proforma-invoices.index') }}">view all PIs</a> to add delivery details.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

