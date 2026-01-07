@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Machine Status</h1>
            <p class="text-muted mb-0">Track and manage machine status for contracts and proforma invoices</p>
        </div>
        <div>
            <a href="{{ route('contracts.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list me-2"></i>View Contracts
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
                <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Search Status</h2>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('machine-statuses.index') }}" id="searchForm">
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
                            <option value="">All PIs</option>
                            @foreach($pis as $pi)
                                <option value="{{ $pi->proforma_invoice_number }}" {{ request('pi_number') == $pi->proforma_invoice_number ? 'selected' : '' }}>{{ $pi->proforma_invoice_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="color: #374151;">Contract Number</label>
                        <select name="contract_number" id="contract_number" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <option value="">All Contracts</option>
                            @foreach($contractsForDropdown as $contract)
                                <option value="{{ $contract->contract_number }}" {{ request('contract_number') == $contract->contract_number ? 'selected' : '' }}>{{ $contract->contract_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                            <a href="{{ route('machine-statuses.index') }}" class="btn btn-outline-secondary">
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
            @if($paginator->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                            <tr>
                                <th>S.No</th>
                                <th>Contract Number</th>
                                <th>PI Number</th>
                                <th>Customer Name</th>
                                <th>Sales Manager</th>
                                <th>Status Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paginator as $index => $item)
                            @php
                                $contract = $item['contract'];
                                $pi = $item['proforma_invoice'];
                                $status = $item['machine_status'];
                                $contractNumber = $contract ? $contract->contract_number : ($pi && $pi->contract ? $pi->contract->contract_number : 'N/A');
                                $piNumber = $pi ? $pi->proforma_invoice_number : 'N/A';
                                $customerName = $contract ? $contract->buyer_name : ($pi ? $pi->buyer_company_name : 'N/A');
                                $salesManager = $contract ? ($contract->creator->name ?? 'N/A') : ($pi ? ($pi->contract->creator->name ?? ($pi->creator->name ?? 'N/A')) : 'N/A');
                                
                                $completedCount = 0;
                                $totalCount = 6;
                                if ($status) {
                                    if ($status->contract_date_completed) $completedCount++;
                                    if ($status->proforma_invoice_completed) $completedCount++;
                                    if ($status->china_payment_completed) $completedCount++;
                                    if ($status->actual_dispatch_completed) $completedCount++;
                                    if ($status->expected_arrival_completed) $completedCount++;
                                    if ($status->actual_arrival_completed) $completedCount++;
                                }
                                $percentage = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ ($paginator->currentPage() - 1) * $paginator->perPage() + $index + 1 }}</td>
                                <td class="fw-semibold">{{ $contractNumber }}</td>
                                <td class="fw-semibold">{{ $piNumber }}</td>
                                <td>{{ $customerName }}</td>
                                <td>{{ $salesManager }}</td>
                                <td>
                                    @if($status)
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                                                    {{ $completedCount }}/{{ $totalCount }}
                                                </div>
                                            </div>
                                            <span class="badge bg-success">{{ round($percentage) }}%</span>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">No Status</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item['type'] === 'contract')
                                        <a href="{{ route('machine-statuses.create', ['contract_id' => $contract->id]) }}" 
                                           class="btn btn-sm {{ $status ? 'btn-primary' : 'btn-success' }}" 
                                           title="{{ $status ? 'Edit' : 'Add' }} Status">
                                            <i class="fas fa-{{ $status ? 'edit' : 'plus' }}"></i> {{ $status ? 'Edit' : 'Add' }} Status
                                        </a>
                                    @else
                                        <a href="{{ route('machine-statuses.create', ['proforma_invoice_id' => $pi->id]) }}" 
                                           class="btn btn-sm {{ $status ? 'btn-primary' : 'btn-success' }}" 
                                           title="{{ $status ? 'Edit' : 'Add' }} Status">
                                            <i class="fas fa-{{ $status ? 'edit' : 'plus' }}"></i> {{ $status ? 'Edit' : 'Add' }} Status
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $paginator->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                    <p class="text-muted mb-2">No Contracts or Proforma Invoices found matching your search criteria.</p>
                    <p class="text-muted small">Try adjusting your filters or <a href="{{ route('contracts.index') }}">view all contracts</a> to add machine status.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const salesManagerSelect = document.getElementById('sales_manager_id');
            const piSelect = document.getElementById('pi_number');
            const contractSelect = document.getElementById('contract_number');

            salesManagerSelect.addEventListener('change', function() {
                const salesManagerId = this.value;
                
                // Reset dropdowns
                piSelect.innerHTML = '<option value="">All PIs</option>';
                contractSelect.innerHTML = '<option value="">All Contracts</option>';
                
                if (salesManagerId) {
                    // Fetch PIs
                    fetch(`{{ route('machine-statuses.get-pis') }}?sales_manager_id=${salesManagerId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(pi => {
                                const option = document.createElement('option');
                                option.value = pi.proforma_invoice_number;
                                option.textContent = pi.proforma_invoice_number;
                                // Preserve selected value if it matches
                                if ('{{ request("pi_number") }}' === pi.proforma_invoice_number) {
                                    option.selected = true;
                                }
                                piSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching PIs:', error);
                        });
                    
                    // Fetch Contracts
                    fetch(`{{ route('machine-statuses.get-contracts') }}?sales_manager_id=${salesManagerId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(contract => {
                                const option = document.createElement('option');
                                option.value = contract.contract_number;
                                option.textContent = contract.contract_number;
                                // Preserve selected value if it matches
                                if ('{{ request("contract_number") }}' === contract.contract_number) {
                                    option.selected = true;
                                }
                                contractSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching Contracts:', error);
                        });
                }
            });
        });
    </script>
</x-app-layout>

