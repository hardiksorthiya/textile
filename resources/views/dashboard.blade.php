<x-app-layout>
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ \App\Models\User::count() }}</h3>
            <p class="text-sm text-gray-500">Total Users</p>
        </div>

        <!-- Total Roles Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-tag text-green-600 text-xl"></i>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ \Spatie\Permission\Models\Role::count() }}</h3>
            <p class="text-sm text-gray-500">Total Roles</p>
        </div>

        <!-- Revenue Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-red-600 text-xl"></i>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">$45,236</h3>
            <p class="text-sm text-gray-500">Total Revenue</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Activity Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Activity Overview</h3>
                <select class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:outline-none focus:ring-1 focus:ring-purple-500">
                    <option>Show by months</option>
                    <option>Show by weeks</option>
                    <option>Show by days</option>
                </select>
            </div>
            <div class="h-64 flex items-end justify-between space-x-2">
                <!-- Simple bar chart representation -->
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-green-500 rounded-t" style="height: 60%"></div>
                    <span class="text-xs text-gray-500 mt-2">Jan</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-red-500 rounded-t" style="height: 80%"></div>
                    <span class="text-xs text-gray-500 mt-2">Feb</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-green-500 rounded-t" style="height: 45%"></div>
                    <span class="text-xs text-gray-500 mt-2">Mar</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-red-500 rounded-t" style="height: 70%"></div>
                    <span class="text-xs text-gray-500 mt-2">Apr</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-green-500 rounded-t" style="height: 90%"></div>
                    <span class="text-xs text-gray-500 mt-2">May</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-red-500 rounded-t" style="height: 55%"></div>
                    <span class="text-xs text-gray-500 mt-2">Jun</span>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">System Status</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-600">System Online</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">100%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-600">Active Users</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">{{ \App\Models\User::count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-600">Total Roles</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">{{ \Spatie\Permission\Models\Role::count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recent Users</h3>
                <a href="{{ route('users.index') }}" class="text-sm text-red-600 hover:text-red-700">View All</a>
            </div>
            <div class="space-y-4">
                @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 last:border-0">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-red-600 font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->phone ?? '-' }}</p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-1 bg-red-100 text-red-600 rounded">
                        {{ $user->roles->first()?->name ?? 'No Role' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Monthly Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">This Month</h3>
            </div>
            <div class="text-center mb-4">
                <p class="text-4xl font-bold text-red-600 mb-1">{{ \App\Models\User::whereMonth('created_at', now()->month)->count() }}</p>
                <p class="text-sm text-gray-500">New Users</p>
            </div>
            <div class="h-32 flex items-end justify-between space-x-1">
                <div class="flex-1 bg-red-200 rounded-t" style="height: 40%"></div>
                <div class="flex-1 bg-red-300 rounded-t" style="height: 60%"></div>
                <div class="flex-1 bg-red-400 rounded-t" style="height: 50%"></div>
                <div class="flex-1 bg-red-500 rounded-t" style="height: 80%"></div>
                <div class="flex-1 bg-red-600 rounded-t" style="height: 100%"></div>
                <div class="flex-1 bg-red-500 rounded-t" style="height: 70%"></div>
            </div>
            <p class="text-xs text-gray-500 text-center mt-2">Last 6 days</p>
        </div>
    </div>
</x-app-layout>



