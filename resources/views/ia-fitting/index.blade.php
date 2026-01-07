<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">IA Fitting</h1>
            <p class="text-muted mb-0">View and manage IA fitting details for all Proforma Invoices</p>
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
                <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Search IA Fitting Details</h2>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('ia-fitting.index') }}" id="searchForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="color: #374151;">Sales Manager</label>
                        <select name="sales_manager_id" id="sales_manager_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <option value="">All Sales Managers</option>
                            @foreach($salesManagers as $manager)
                                <option value="{{ $manager->id }}" {{ request('sales_manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="color: #374151;">PI Number</label>
                        <select name="pi_number" id="pi_number" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <option value="">All PI Numbers</option>
                            @if(request('sales_manager_id'))
                                @php
                                    $selectedPIs = \App\Models\ProformaInvoice::where(function($q) {
                                        $q->where('created_by', request('sales_manager_id'))
                                          ->orWhereHas('contract', function($subQ) {
                                              $subQ->where('created_by', request('sales_manager_id'));
                                          });
                                    })
                                    ->orderBy('proforma_invoice_number')
                                    ->get(['id', 'proforma_invoice_number']);
                                @endphp
                                @foreach($selectedPIs as $pi)
                                    <option value="{{ $pi->proforma_invoice_number }}" {{ request('pi_number') == $pi->proforma_invoice_number ? 'selected' : '' }}>{{ $pi->proforma_invoice_number }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="color: #374151;">Customer Name (Buyer)</label>
                        <select name="customer_name" id="customer_name" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <option value="">All Customers</option>
                            @if(request('sales_manager_id'))
                                @php
                                    $selectedCustomers = \App\Models\ProformaInvoice::where(function($q) {
                                        $q->where('created_by', request('sales_manager_id'))
                                          ->orWhereHas('contract', function($subQ) {
                                              $subQ->where('created_by', request('sales_manager_id'));
                                          });
                                    })
                                    ->select('buyer_company_name')
                                    ->distinct()
                                    ->whereNotNull('buyer_company_name')
                                    ->orderBy('buyer_company_name')
                                    ->get()
                                    ->pluck('buyer_company_name')
                                    ->unique()
                                    ->values();
                                @endphp
                                @foreach($selectedCustomers as $customer)
                                    <option value="{{ $customer }}" {{ request('customer_name') == $customer ? 'selected' : '' }}>{{ $customer }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                            <a href="{{ route('ia-fitting.index') }}" class="btn btn-outline-secondary">
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
                                <th>Customer Name (Buyer)</th>
                                <th>Sales Manager</th>
                                <th>Total Amount</th>
                                <th>IA Fitting Details Count</th>
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
                                        $detailCount = $pi->iaFittingDetails ? $pi->iaFittingDetails->count() : 0;
                                    @endphp
                                    @if($detailCount > 0)
                                        <span class="badge bg-success">{{ $detailCount }} Details</span>
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
                                        <a href="{{ route('ia-fitting.show', $pi) }}" class="btn btn-sm {{ $pi->iaFittingDetails && $pi->iaFittingDetails->count() > 0 ? 'btn-info' : 'btn-success' }}" title="{{ $pi->iaFittingDetails && $pi->iaFittingDetails->count() > 0 ? 'Edit' : 'Add' }} IA Fitting Details">
                                            <i class="fas fa-wrench"></i>
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
                    <p class="text-muted small">Try adjusting your filters or <a href="{{ route('proforma-invoices.index') }}">view all PIs</a> to add IA fitting details.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const salesManagerSelect = document.getElementById('sales_manager_id');
            const piNumberSelect = document.getElementById('pi_number');
            const customerNameSelect = document.getElementById('customer_name');

            salesManagerSelect.addEventListener('change', function() {
                const salesManagerId = this.value;
                
                // Store current selected values
                const selectedPINumber = piNumberSelect.value;
                const selectedCustomerName = customerNameSelect.value;
                
                // Reset PI and Customer dropdowns
                piNumberSelect.innerHTML = '<option value="">All PI Numbers</option>';
                customerNameSelect.innerHTML = '<option value="">All Customers</option>';
                
                if (salesManagerId) {
                    // Fetch PIs
                    fetch(`{{ route('ia-fitting.get-pis') }}?sales_manager_id=${salesManagerId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(pi => {
                                const option = document.createElement('option');
                                option.value = pi.proforma_invoice_number;
                                option.textContent = pi.proforma_invoice_number;
                                if (pi.proforma_invoice_number === selectedPINumber) {
                                    option.selected = true;
                                }
                                piNumberSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching PIs:', error);
                        });

                    // Fetch Customers
                    fetch(`{{ route('ia-fitting.get-customers') }}?sales_manager_id=${salesManagerId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(customer => {
                                const option = document.createElement('option');
                                option.value = customer;
                                option.textContent = customer;
                                if (customer === selectedCustomerName) {
                                    option.selected = true;
                                }
                                customerNameSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching customers:', error);
                        });
                }
            });
        });
    </script>
</x-app-layout>

