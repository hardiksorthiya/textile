<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Customers Management</h1>
                    <p class="text-muted mb-0">View all customers from approved contracts</p>
                </div>
            </div>
        </div>

        <!-- Filter Sidebar Overlay -->
        <div x-show="filterSidebarOpen" 
             x-cloak
             @click="filterSidebarOpen = false"
             class="position-fixed top-0 start-0 w-100 h-100 bg-dark"
             style="opacity: 0.5; z-index: 1040;"></div>

        <!-- Filter Sidebar -->
        <div x-show="filterSidebarOpen" 
             x-cloak
             class="position-fixed top-0 end-0 h-100 bg-white shadow-lg"
             style="width: 350px; z-index: 1050; overflow-y: auto; border-left: 1px solid #e5e7eb;"
             @click.stop>
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0" style="color: #1f2937;">
                    <i class="fas fa-filter me-2 text-primary"></i>Filters
                </h5>
                <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="GET" action="{{ route('customers.index') }}" id="filterForm">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="color: #374151;">State</label>
                    <select name="state_id" id="filter_state_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="loadFilterCities(this.value);">
                        <option value="">All States</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ request('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="color: #374151;">City</label>
                    <select name="city_id" id="filter_city_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="color: #374151;">Business Firm</label>
                    <select name="business_firm_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Business Firms</option>
                        @foreach($businessFirms as $firm)
                            <option value="{{ $firm->id }}" {{ request('business_firm_id') == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-check me-2"></i>Apply
                    </button>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 pb-0" style="background: transparent;">
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Customers List</h2>
                    <span class="badge ms-3" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.875rem; padding: 0.35rem 0.65rem;">{{ $customers->total() }} Total</span>
                </div>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="card shadow-sm border-0 mb-3" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <form method="GET" action="{{ route('customers.index') }}" class="flex-grow-1 d-flex align-items-center gap-2">
                        <div class="flex-grow-1 position-relative">
                            <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; z-index: 10;"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   class="form-control ps-5" 
                                   placeholder="Search by contract number, name, phone, email, location..." 
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @if(request()->hasAny(['state_id', 'city_id', 'business_firm_id']))
                                @foreach(request()->only(['state_id', 'city_id', 'business_firm_id']) as $key => $value)
                                    @if($value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 40px; height: 40px;" title="Search">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <button type="button" 
                            @click="filterSidebarOpen = !filterSidebarOpen"
                            class="btn btn-outline-primary d-flex align-items-center justify-content-center" 
                            style="border-radius: 8px; width: 40px; height: 40px;" title="Filter">
                        <i class="fas fa-filter"></i>
                    </button>
                    @if(request()->hasAny(['search', 'state_id', 'city_id', 'business_firm_id']))
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 40px; height: 40px;" title="Clear Filters">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: calc(100vh - 400px); overflow-y: auto;">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="sticky-top" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                        <tr>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Contract Number</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Customer Name</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Company Name</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Contact</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Location</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Contract Amount</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Approved Date</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <div class="fw-semibold" style="color: #1f2937;">{{ $customer->contract_number }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold" style="color: #1f2937;">{{ $customer->buyer_name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div style="color: #6b7280;">{{ $customer->company_name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div style="color: #6b7280;">
                                        <div><i class="fas fa-phone me-1"></i>{{ $customer->phone_number }}</div>
                                        @if($customer->phone_number_2)
                                            <div class="small"><i class="fas fa-phone me-1"></i>{{ $customer->phone_number_2 }}</div>
                                        @endif
                                        @if($customer->email)
                                            <div class="small"><i class="fas fa-envelope me-1"></i>{{ $customer->email }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div style="color: #6b7280;">
                                        @if($customer->area || $customer->city || $customer->state)
                                            {{ $customer->area->name ?? '' }}{{ $customer->area && ($customer->city || $customer->state) ? ', ' : '' }}{{ $customer->city->name ?? '' }}{{ $customer->city && $customer->state ? ', ' : '' }}{{ $customer->state->name ?? '' }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-bold" style="color: var(--primary-color);">${{ number_format($customer->total_amount ?? 0, 2) }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <small class="text-muted">
                                        @if($customer->approved_at)
                                            {{ \Carbon\Carbon::parse($customer->approved_at)->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </small>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex gap-2" role="group">
                                        <a href="{{ route('contracts.show', $customer) }}" class="btn btn-sm btn-outline-info" title="View Contract Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('contracts.download-pdf', $customer) }}" class="btn btn-sm btn-outline-success" title="Download PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @can('delete customers')
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer? This will delete the contract and all related data including machine details. This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Customer">
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
                                        <i class="fas fa-users fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                        <p class="mb-0">No customers found.</p>
                                        <small class="text-muted mt-1">Customers will appear here once contracts are approved</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($customers->hasPages())
            <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers
                    </div>
                    <div>
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="text-muted text-center">
                    Showing {{ $customers->count() }} of {{ $customers->total() }} customers
                </div>
            </div>
        @endif
    </div>

    <script>
        function loadFilterCities(stateId) {
            const citySelect = document.getElementById('filter_city_id');
            citySelect.innerHTML = '<option value="">All Cities</option>';
            
            if (stateId) {
                fetch(`/leads/cities/${stateId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name;
                            citySelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading cities:', error));
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

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
