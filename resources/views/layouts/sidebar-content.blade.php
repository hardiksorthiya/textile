<!-- Logo Section -->
<div class="p-6 border-b border-gray-200">
    <div class="flex items-center">
        @if(!empty($logoPath))
            <img src="{{ $logoPath }}" alt="Logo" class="rounded-lg object-contain bg-white">
        @else
            <div class="rounded-lg flex items-center justify-center" style="background-color: var(--primary-color);">
                <i class="fas fa-tshirt text-white text-xl"></i>
            </div>
        @endif
        {{-- <h1 class="ml-3 text-xl font-bold text-gray-800">{{ config('app.name', 'Textile ERP') }}</h1> --}}
    </div>
</div>

<!-- Navigation Menu -->
<nav class="flex-1 p-4 space-y-2 overflow-y-auto">
    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'sidebar-active' : 'sidebar-link' }}">
        <i class="fas fa-chart-line w-5 mr-3"></i>
        <span>Dashboard</span>
    </a>

    @canany(['view users', 'create users', 'edit users', 'delete users', 'view roles', 'create roles', 'edit roles', 'delete roles'])
    <div x-data="{ open: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'true' : 'false' }} }" class="space-y-2">
        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg sidebar-link transition-colors {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'sidebar-active' : '' }}">
            <div class="flex items-center">
                <i class="fas fa-users w-5 mr-3"></i>
                <span>Team</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse class="ml-8 space-y-1">
            @can('view users')
            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('users.*') && !request()->routeIs('roles.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-list w-4 mr-2"></i>
                <span>Team List</span>
            </a>
            @endcan
            @canany(['view roles', 'create roles', 'edit roles', 'delete roles'])
            <a href="{{ route('roles.create') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('roles.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-user-tag w-4 mr-2"></i>
                <span>Role Create</span>
            </a>
            @endcanany
        </div>
    </div>
    @endcanany

    @hasrole('Admin|Super Admin')
    <div x-data="{ open: {{ request()->routeIs('machine-categories.*') || request()->routeIs('sellers.*') || request()->routeIs('countries.*') || request()->routeIs('brands.*') || request()->routeIs('machine-models.*') || request()->routeIs('machine-sizes.*') || request()->routeIs('flange-sizes.*') || request()->routeIs('feeders.*') || request()->routeIs('machine-hooks.*') || request()->routeIs('colors.*') || request()->routeIs('machine-nozzles.*') || request()->routeIs('machine-dropins.*') || request()->routeIs('machine-beams.*') || request()->routeIs('machine-cloth-rollers.*') || request()->routeIs('machine-softwares.*') || request()->routeIs('hsn-codes.*') || request()->routeIs('wirs.*') || request()->routeIs('machine-shafts.*') || request()->routeIs('machine-levers.*') || request()->routeIs('machine-chains.*') || request()->routeIs('machine-heald-wires.*') || request()->routeIs('machine-e-reads.*') || request()->routeIs('delivery-terms.*') || request()->routeIs('settings.contract-details') || request()->routeIs('settings.update-contract-details') ? 'true' : 'false' }} }" class="space-y-2">
        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-categories.*') || request()->routeIs('sellers.*') || request()->routeIs('countries.*') || request()->routeIs('brands.*') || request()->routeIs('machine-models.*') || request()->routeIs('machine-sizes.*') || request()->routeIs('flange-sizes.*') || request()->routeIs('feeders.*') || request()->routeIs('machine-hooks.*') || request()->routeIs('colors.*') || request()->routeIs('machine-nozzles.*') || request()->routeIs('machine-dropins.*') || request()->routeIs('machine-beams.*') || request()->routeIs('machine-cloth-rollers.*') || request()->routeIs('machine-softwares.*') || request()->routeIs('hsn-codes.*') || request()->routeIs('wirs.*') || request()->routeIs('machine-shafts.*') || request()->routeIs('machine-levers.*') || request()->routeIs('machine-chains.*') || request()->routeIs('machine-heald-wires.*') || request()->routeIs('machine-e-reads.*') || request()->routeIs('delivery-terms.*') || request()->routeIs('settings.contract-details') || request()->routeIs('settings.update-contract-details') ? 'sidebar-active' : '' }}">
            <div class="flex items-center">
                <i class="fas fa-industry w-5 mr-3"></i>
                <span class="fw-semibold">Machine</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse class="ml-8 space-y-1">
            <a href="{{ route('machine-categories.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-categories.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-list w-4 mr-2"></i>
                <span>Category</span>
            </a>
            <a href="{{ route('brands.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('brands.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-tags w-4 mr-2"></i>
                <span>Brand</span>
            </a>
            <a href="{{ route('machine-models.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-models.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-cog w-4 mr-2"></i>
                <span>Machine Model</span>
            </a>
            <a href="{{ route('machine-sizes.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-sizes.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-ruler w-4 mr-2"></i>
                <span>Machine Size</span>
            </a>
            <a href="{{ route('flange-sizes.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('flange-sizes.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-compress-arrows-alt w-4 mr-2"></i>
                <span>Flange Size</span>
            </a>
            <a href="{{ route('feeders.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('feeders.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-box w-4 mr-2"></i>
                <span>Feeder</span>
            </a>
            <a href="{{ route('machine-hooks.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-hooks.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-link w-4 mr-2"></i>
                <span>Machine Hook</span>
            </a>
            <a href="{{ route('colors.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('colors.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-palette w-4 mr-2"></i>
                <span>Color</span>
            </a>
            <a href="{{ route('machine-nozzles.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-nozzles.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-spray-can w-4 mr-2"></i>
                <span>Machine Nozzle</span>
            </a>
            <a href="{{ route('machine-dropins.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-dropins.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-tint-droplet w-4 mr-2"></i>
                <span>Machine Dropin</span>
            </a>
            <a href="{{ route('machine-beams.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-beams.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-chart-line w-4 mr-2"></i>
                <span>Machine Beam</span>
            </a>
            <a href="{{ route('machine-cloth-rollers.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-cloth-rollers.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-rotate w-4 mr-2"></i>
                <span>Machine Cloth Roller</span>
            </a>
            <a href="{{ route('machine-softwares.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-softwares.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-code w-4 mr-2"></i>
                <span>Machine Software</span>
            </a>
            <a href="{{ route('hsn-codes.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('hsn-codes.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-hashtag w-4 mr-2"></i>
                <span>HSN Code</span>
            </a>
            <a href="{{ route('wirs.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('wirs.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-file-invoice w-4 mr-2"></i>
                <span>WIR</span>
            </a>
            <a href="{{ route('machine-shafts.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-shafts.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-circle-notch w-4 mr-2"></i>
                <span>Machine Shaft</span>
            </a>
            <a href="{{ route('machine-levers.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-levers.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-toggle-on w-4 mr-2"></i>
                <span>Machine Lever</span>
            </a>
            <a href="{{ route('machine-chains.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-chains.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-link w-4 mr-2"></i>
                <span>Machine Chain</span>
            </a>
            <a href="{{ route('machine-heald-wires.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-heald-wires.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-slash w-4 mr-2"></i>
                <span>Machine Heald Wire</span>
            </a>
            <a href="{{ route('machine-e-reads.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('machine-e-reads.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-book-reader w-4 mr-2"></i>
                <span>Machine E-Read</span>
            </a>
            <a href="{{ route('delivery-terms.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('delivery-terms.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-shipping-fast w-4 mr-2"></i>
                <span>Delivery Term</span>
            </a>
            <a href="{{ route('sellers.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('sellers.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-user-tie w-4 mr-2"></i>
                <span>Seller</span>
            </a>
            <a href="{{ route('countries.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('countries.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-globe w-4 mr-2"></i>
                <span>Seller country</span>
            </a>
            <a href="{{ route('settings.contract-details') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('settings.contract-details') || request()->routeIs('settings.update-contract-details') ? 'sidebar-active' : '' }}">
                <i class="fas fa-cog w-4 mr-2"></i>
                <span>Other Contract Details</span>
            </a>
        </div>
    </div>
    @endhasrole

    @canany(['view leads', 'create leads', 'edit leads', 'delete leads', 'convert contract'])
    <div x-data="{ open: {{ request()->routeIs('leads.*') || request()->routeIs('businesses.*') || request()->routeIs('states.*') || request()->routeIs('cities.*') || request()->routeIs('areas.*') || request()->routeIs('statuses.*') ? 'true' : 'false' }} }" class="space-y-2">
        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg sidebar-link transition-colors {{ request()->routeIs('leads.*') || request()->routeIs('businesses.*') || request()->routeIs('states.*') || request()->routeIs('cities.*') || request()->routeIs('areas.*') || request()->routeIs('statuses.*') ? 'sidebar-active' : '' }}">
            <div class="flex items-center">
                <i class="fas fa-user-friends w-5 mr-3"></i>
                <span>Leads</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse class="ml-8 space-y-1">
            @can('view leads')
            <a href="{{ route('leads.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('leads.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-list w-4 mr-2"></i>
                <span>List Leads</span>
            </a>
            @endcan
            @hasrole('Admin|Super Admin')
            <a href="{{ route('businesses.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('businesses.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-building w-4 mr-2"></i>
                <span>Business</span>
            </a>
            <a href="{{ route('states.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('states.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-map w-4 mr-2"></i>
                <span>State</span>
            </a>
            <a href="{{ route('cities.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('cities.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-city w-4 mr-2"></i>
                <span>City</span>
            </a>
            <a href="{{ route('areas.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('areas.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-map-marker-alt w-4 mr-2"></i>
                <span>Area</span>
            </a>
            <a href="{{ route('statuses.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('statuses.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-info-circle w-4 mr-2"></i>
                <span>Status</span>
            </a>
            @endhasrole
        </div>
    </div>
    @endcanany

    @canany(['view contract approvals', 'approve contracts', 'reject contracts'])
    <a href="{{ route('contracts.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg sidebar-link transition-colors {{ request()->routeIs('contracts.index') || request()->routeIs('contracts.edit') || request()->routeIs('contracts.signature') ? 'sidebar-active' : '' }}">
        <i class="fas fa-file-contract w-5 mr-3"></i>
        <span>Contracts</span>
    </a>
    @endcanany

    @canany(['view proforma invoices', 'create proforma invoices', 'edit proforma invoices', 'delete proforma invoices'])
    <a href="{{ route('proforma-invoices.create') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg sidebar-link transition-colors {{ request()->routeIs('proforma-invoices.*') ? 'sidebar-active' : '' }}">
        <i class="fas fa-file-invoice w-5 mr-3"></i>
        <span>Proforma Invoice</span>
    </a>
    @endcanany

    @can('view customers')
    <a href="{{ route('customers.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg sidebar-link transition-colors {{ request()->routeIs('customers.*') ? 'sidebar-active' : '' }}">
        <i class="fas fa-user-check w-5 mr-3"></i>
        <span>Customers</span>
    </a>
    @endcan

    @can('view reports')
    <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg sidebar-link transition-colors {{ request()->routeIs('reports.*') ? 'sidebar-active' : '' }}">
        <i class="fas fa-chart-bar w-5 mr-3"></i>
        <span>Reports</span>
    </a>
    @endcan

    @can('view contract approvals')
    <a href="{{ route('contracts.pending-approval') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg sidebar-link transition-colors {{ request()->routeIs('contracts.pending-approval') || request()->routeIs('contracts.approve') || request()->routeIs('contracts.reject') ? 'sidebar-active' : '' }}">
        <i class="fas fa-check-circle w-5 mr-3"></i>
        <span>Contract Approvals</span>
        @php
            $pendingCount = \App\Models\Contract::where('approval_status', 'pending')->whereNotNull('customer_signature')->count();
        @endphp
        @if($pendingCount > 0)
            <span class="badge bg-danger ms-2">{{ $pendingCount }}</span>
        @endif
    </a>
    @endcan

    @canany(['view settings', 'edit settings'])
    <div x-data="{ open: {{ request()->routeIs('settings.*') || request()->routeIs('admin.*') || request()->routeIs('business-firms.*') ? 'true' : 'false' }} }" class="space-y-2">
        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg sidebar-link transition-colors {{ request()->routeIs('settings.*') || request()->routeIs('admin.*') || request()->routeIs('business-firms.*') ? 'sidebar-active' : '' }}">
            <div class="flex items-center">
                <i class="fas fa-cog w-5 mr-3"></i>
                <span>Settings</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse class="ml-8 space-y-1">
            <a href="{{ route('settings.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('settings.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-cog w-4 mr-2"></i>
                <span>Admin Settings</span>
            </a>
            <a href="{{ route('business-firms.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg sidebar-link transition-colors {{ request()->routeIs('business-firms.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-briefcase w-4 mr-2"></i>
                <span>Business Firm</span>
            </a>
        </div>
    </div>
    @endcanany
</nav>

<!-- Mobile App Section -->
<div class="p-4 border-t border-gray-200 bg-gray-50 hidden lg:block">
    <div class="text-center mb-3">
        <i class="fas fa-mobile-alt text-4xl mb-2" style="color: var(--primary-color);"></i>
        <p class="text-sm font-semibold text-gray-700">Get Mobile App</p>
    </div>
    <div class="flex justify-center space-x-2">
        <a href="#" class="flex items-center justify-center w-24 h-8 bg-black text-white rounded text-xs">
            <i class="fab fa-google-play mr-1"></i>
            <span>Play</span>
        </a>
        <a href="#" class="flex items-center justify-center w-24 h-8 bg-black text-white rounded text-xs">
            <i class="fab fa-apple mr-1"></i>
            <span>App Store</span>
        </a>
    </div>
</div>


