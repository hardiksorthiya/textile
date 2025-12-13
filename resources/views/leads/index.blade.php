<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Leads Management</h1>
                    <p class="text-muted mb-0">View and manage all generated leads</p>
                </div>
                <a href="{{ route('leads.create') }}" class="btn btn-primary" style="border-radius: 8px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important; border: none;">
                    <i class="fas fa-plus me-2"></i>Create New Lead
                </a>
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
                <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="GET" action="{{ route('leads.index') }}" id="filterForm">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="color: #374151;">Lead Type</label>
                    <select name="type" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Types</option>
                        <option value="new" {{ request('type') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="old" {{ request('type') == 'old' ? 'selected' : '' }}>Old</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="color: #374151;">Status</label>
                    <select name="status_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

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
                        @if(request('state_id'))
                            @php
                                $filterCities = \App\Models\City::where('state_id', request('state_id'))->orderBy('name')->get();
                            @endphp
                            @foreach($filterCities as $city)
                                <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="color: #374151;">Business</label>
                    <select name="business_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Businesses</option>
                        @foreach($businesses as $business)
                            <option value="{{ $business->id }}" {{ request('business_id') == $business->id ? 'selected' : '' }}>{{ $business->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="color: #374151;">Brand</label>
                    <select name="brand_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important; border: none;">
                        <i class="fas fa-check me-2"></i>Apply
                    </button>
                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

        <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
        <div class="card-header border-0 pb-0" style="background: transparent;">
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                        <i class="fas fa-list text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">All Leads</h2>
                    <span class="badge bg-purple-100 text-purple-800 ms-3">{{ $leads->total() }} Total</span>
                </div>
 <!-- Search and Filter Bar -->
 <div class="card shadow-sm border-0 mb-3" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-3">
            <form method="GET" action="{{ route('leads.index') }}" class="flex-grow-1 d-flex align-items-center gap-2">
                <div class="flex-grow-1 position-relative">
                    <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; z-index: 10;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="form-control ps-5" 
                           placeholder="Search by name, phone, business, location..." 
                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                    @if(request()->hasAny(['type', 'status_id', 'state_id', 'city_id', 'business_id', 'brand_id']))
                        @foreach(request()->only(['type', 'status_id', 'state_id', 'city_id', 'business_id', 'brand_id']) as $key => $value)
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
            @if(request()->hasAny(['search', 'type', 'status_id', 'state_id', 'city_id', 'business_id', 'brand_id']))
                <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 40px; height: 40px;" title="Clear Filters">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>
    </div>
</div>
                
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: calc(100vh - 400px); overflow-y: auto;">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="sticky-top" style="background: linear-gradient(to right, #f3e8ff, #e9d5ff) !important;">
                        <tr>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Sr.no</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Name</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Phone</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Location</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Business/Brand</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Categories</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Status</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Created</th>
                            <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <span class="fw-semibold" style="color: #1f2937;">{{ $leads->firstItem() + $loop->index }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold" style="color: #1f2937;">{{ $lead->name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <small>{{ $lead->phone_number }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    <small>{{ $lead->area->name }}, {{ $lead->city->name }}, {{ $lead->state->name }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    @if($lead->type === 'new' && $lead->business)
                                        <span class="badge bg-purple-100 text-purple-800">{{ $lead->business->name }}</span>
                                    @elseif($lead->type === 'old' && $lead->brand)
                                        <span class="badge bg-blue-100 text-blue-800">{{ $lead->brand->name }}</span>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($lead->machineCategories->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($lead->machineCategories->take(2) as $category)
                                                <span class="badge" style="background-color: #f3e8ff; color: #7c3aed; font-size: 0.75rem;">
                                                    {{ $category->name }}
                                                </span>
                                            @endforeach
                                            @if($lead->machineCategories->count() > 2)
                                                <span class="badge bg-secondary" style="font-size: 0.75rem;">+{{ $lead->machineCategories->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <small class="text-muted">No categories</small>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-info text-white">{{ $lead->status->name }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <small class="text-muted">{{ $lead->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-outline-info" style="border-radius: 6px;" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('leads.convert-to-contract', $lead) }}" class="btn btn-sm btn-outline-success" style="border-radius: 6px;" title="Convert to Contract">
                                            <i class="fas fa-user-check"></i>
                                        </a>
                                        <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-user-friends fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                        <p class="mb-0">No leads found.</p>
                                        <small class="text-muted mt-1">Create your first lead to get started</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($leads->hasPages())
            <div class="card-footer bg-transparent border-top" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $leads->firstItem() }} to {{ $leads->lastItem() }} of {{ $leads->total() }} leads
                    </div>
                    <div>
                        {{ $leads->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer bg-transparent border-top" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                <div class="text-muted text-center">
                    Showing {{ $leads->count() }} of {{ $leads->total() }} leads
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
    </div>
</x-app-layout>
