<!-- Logo Section -->
<div class="p-6 border-b border-gray-200">
    <div class="flex items-center">
        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
            <i class="fas fa-tshirt text-white text-xl"></i>
        </div>
        <h1 class="ml-3 text-xl font-bold text-gray-800">Textile ERP</h1>
    </div>
</div>

<!-- Navigation Menu -->
<nav class="flex-1 p-4 space-y-2 overflow-y-auto">
    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('dashboard') ? 'bg-purple-50 text-purple-600' : '' }}">
        <i class="fas fa-chart-line w-5 mr-3"></i>
        <span>Dashboard</span>
    </a>

    @hasrole('Admin|Super Admin')
    <div x-data="{ open: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'true' : 'false' }} }" class="space-y-2">
        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'bg-purple-50 text-purple-600' : '' }}">
            <div class="flex items-center">
                <i class="fas fa-users w-5 mr-3"></i>
                <span>Team</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse class="ml-8 space-y-1">
            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('users.*') && !request()->routeIs('roles.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-list w-4 mr-2"></i>
                <span>Team List</span>
            </a>
            <a href="{{ route('roles.create') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('roles.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-user-tag w-4 mr-2"></i>
                <span>Role Create</span>
            </a>
        </div>
    </div>
    @endhasrole

    @hasrole('Admin|Super Admin')
    <div x-data="{ open: {{ request()->routeIs('machine-categories.*') || request()->routeIs('sellers.*') || request()->routeIs('countries.*') || request()->routeIs('brands.*') || request()->routeIs('machine-models.*') || request()->routeIs('machine-sizes.*') || request()->routeIs('flange-sizes.*') || request()->routeIs('feeders.*') || request()->routeIs('machine-hooks.*') || request()->routeIs('colors.*') || request()->routeIs('machine-nozzles.*') || request()->routeIs('machine-dropins.*') || request()->routeIs('machine-beams.*') || request()->routeIs('machine-cloth-rollers.*') || request()->routeIs('machine-softwares.*') || request()->routeIs('hsn-codes.*') || request()->routeIs('wirs.*') || request()->routeIs('machine-shafts.*') || request()->routeIs('machine-levers.*') || request()->routeIs('machine-chains.*') || request()->routeIs('machine-heald-wires.*') || request()->routeIs('machine-e-reads.*') ? 'true' : 'false' }} }" class="space-y-2">
        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-categories.*') || request()->routeIs('sellers.*') || request()->routeIs('countries.*') || request()->routeIs('brands.*') || request()->routeIs('machine-models.*') || request()->routeIs('machine-sizes.*') || request()->routeIs('flange-sizes.*') || request()->routeIs('feeders.*') || request()->routeIs('machine-hooks.*') || request()->routeIs('colors.*') || request()->routeIs('machine-nozzles.*') || request()->routeIs('machine-dropins.*') || request()->routeIs('machine-beams.*') || request()->routeIs('machine-cloth-rollers.*') || request()->routeIs('machine-softwares.*') || request()->routeIs('hsn-codes.*') || request()->routeIs('wirs.*') || request()->routeIs('machine-shafts.*') || request()->routeIs('machine-levers.*') || request()->routeIs('machine-chains.*') || request()->routeIs('machine-heald-wires.*') || request()->routeIs('machine-e-reads.*') ? 'bg-purple-50 text-purple-600' : '' }}">
            <div class="flex items-center">
                <i class="fas fa-cog w-5 mr-3"></i>
                <span>Machine</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse class="ml-8 space-y-1">
            <a href="{{ route('machine-categories.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-categories.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-list w-4 mr-2"></i>
                <span>Category</span>
            </a>
            <a href="{{ route('brands.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('brands.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-tags w-4 mr-2"></i>
                <span>Brand</span>
            </a>
            <a href="{{ route('machine-models.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-models.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-cog w-4 mr-2"></i>
                <span>Machine Model</span>
            </a>
            <a href="{{ route('machine-sizes.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-sizes.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-ruler w-4 mr-2"></i>
                <span>Machine Size</span>
            </a>
            <a href="{{ route('flange-sizes.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('flange-sizes.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-compress-arrows-alt w-4 mr-2"></i>
                <span>Flange Size</span>
            </a>
            <a href="{{ route('feeders.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('feeders.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-box w-4 mr-2"></i>
                <span>Feeder</span>
            </a>
            <a href="{{ route('machine-hooks.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-hooks.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-link w-4 mr-2"></i>
                <span>Machine Hook</span>
            </a>
            <a href="{{ route('colors.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('colors.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-palette w-4 mr-2"></i>
                <span>Color</span>
            </a>
            <a href="{{ route('machine-nozzles.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-nozzles.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-spray-can w-4 mr-2"></i>
                <span>Machine Nozzle</span>
            </a>
            <a href="{{ route('machine-dropins.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-dropins.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-tint-droplet w-4 mr-2"></i>
                <span>Machine Dropin</span>
            </a>
            <a href="{{ route('machine-beams.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-beams.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-chart-line w-4 mr-2"></i>
                <span>Machine Beam</span>
            </a>
            <a href="{{ route('machine-cloth-rollers.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-cloth-rollers.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-rotate w-4 mr-2"></i>
                <span>Machine Cloth Roller</span>
            </a>
            <a href="{{ route('machine-softwares.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-softwares.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-code w-4 mr-2"></i>
                <span>Machine Software</span>
            </a>
            <a href="{{ route('hsn-codes.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('hsn-codes.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-hashtag w-4 mr-2"></i>
                <span>HSN Code</span>
            </a>
            <a href="{{ route('wirs.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('wirs.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-file-invoice w-4 mr-2"></i>
                <span>WIR</span>
            </a>
            <a href="{{ route('machine-shafts.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-shafts.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-circle-notch w-4 mr-2"></i>
                <span>Machine Shaft</span>
            </a>
            <a href="{{ route('machine-levers.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-levers.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-toggle-on w-4 mr-2"></i>
                <span>Machine Lever</span>
            </a>
            <a href="{{ route('machine-chains.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-chains.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-link w-4 mr-2"></i>
                <span>Machine Chain</span>
            </a>
            <a href="{{ route('machine-heald-wires.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-heald-wires.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-slash w-4 mr-2"></i>
                <span>Machine Heald Wire</span>
            </a>
            <a href="{{ route('machine-e-reads.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('machine-e-reads.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-book-reader w-4 mr-2"></i>
                <span>Machine E-Read</span>
            </a>
            <a href="{{ route('sellers.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('sellers.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-user-tie w-4 mr-2"></i>
                <span>Seller</span>
            </a>
            <a href="{{ route('countries.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('countries.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-globe w-4 mr-2"></i>
                <span>Seller country</span>
            </a>
        </div>
    </div>
    @endhasrole

    @hasrole('Admin|Super Admin')
    <div x-data="{ open: {{ request()->routeIs('leads.*') || request()->routeIs('businesses.*') || request()->routeIs('states.*') || request()->routeIs('cities.*') || request()->routeIs('areas.*') || request()->routeIs('statuses.*') ? 'true' : 'false' }} }" class="space-y-2">
        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('leads.*') || request()->routeIs('businesses.*') || request()->routeIs('states.*') || request()->routeIs('cities.*') || request()->routeIs('areas.*') || request()->routeIs('statuses.*') ? 'bg-purple-50 text-purple-600' : '' }}">
            <div class="flex items-center">
                <i class="fas fa-user-friends w-5 mr-3"></i>
                <span>Leads</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse class="ml-8 space-y-1">
            <a href="{{ route('leads.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('leads.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-list w-4 mr-2"></i>
                <span>List Leads</span>
            </a>
            <a href="{{ route('businesses.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('businesses.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-building w-4 mr-2"></i>
                <span>Business</span>
            </a>
            <a href="{{ route('states.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('states.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-map w-4 mr-2"></i>
                <span>State</span>
            </a>
            <a href="{{ route('cities.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('cities.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-city w-4 mr-2"></i>
                <span>City</span>
            </a>
            <a href="{{ route('areas.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('areas.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-map-marker-alt w-4 mr-2"></i>
                <span>Area</span>
            </a>
            <a href="{{ route('statuses.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('statuses.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-info-circle w-4 mr-2"></i>
                <span>Status</span>
            </a>
        </div>
    </div>
    @endhasrole

    @can('view reports')
    <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('reports.*') ? 'bg-purple-50 text-purple-600' : '' }}">
        <i class="fas fa-chart-bar w-5 mr-3"></i>
        <span>Reports</span>
    </a>
    @endcan

    @hasrole('Admin|Super Admin')
    <div x-data="{ open: {{ request()->routeIs('admin.*') || request()->routeIs('business-firms.*') ? 'true' : 'false' }} }" class="space-y-2">
        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('admin.*') || request()->routeIs('business-firms.*') ? 'bg-purple-50 text-purple-600' : '' }}">
            <div class="flex items-center">
                <i class="fas fa-cog w-5 mr-3"></i>
                <span>Settings</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse class="ml-8 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('admin.*') && !request()->routeIs('business-firms.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-cog w-4 mr-2"></i>
                <span>Admin Settings</span>
            </a>
            <a href="{{ route('business-firms.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors {{ request()->routeIs('business-firms.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-briefcase w-4 mr-2"></i>
                <span>Business Firm</span>
            </a>
        </div>
    </div>
    @endhasrole
</nav>

<!-- Mobile App Section -->
<div class="p-4 border-t border-gray-200 bg-gray-50 hidden lg:block">
    <div class="text-center mb-3">
        <i class="fas fa-mobile-alt text-4xl text-purple-600 mb-2"></i>
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

