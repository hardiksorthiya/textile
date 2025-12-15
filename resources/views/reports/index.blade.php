<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Reports</h3>
                    <p>This page requires 'view reports' permission.</p>
                    
                    @can('export reports')
                        <div class="mt-4">
                            <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                Export Report
                            </button>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



