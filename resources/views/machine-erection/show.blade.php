<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Machine Erection Details</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
            <p class="text-muted mb-0">Customer: {{ $proformaInvoice->buyer_company_name }}</p>
            <p class="text-muted mb-0">Seller: {{ $proformaInvoice->seller->seller_name ?? 'N/A' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('machine-erection.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-2"></i>View PI
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($machineCategories->count() > 0)
        @foreach($machineCategories as $machineCategory)
            @php
                $categoryQuantity = $machineCategoriesWithQuantity->firstWhere('category.id', $machineCategory->id)['quantity'] ?? 0;
            @endphp
            <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-cogs text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">{{ $machineCategory->name }} ({{ $categoryQuantity }} Machine{{ $categoryQuantity != 1 ? 's' : '' }})</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Points to Follow Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover" id="erectionTable{{ $machineCategory->id }}" data-machine-count="{{ $categoryQuantity }}">
                            <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                <tr>
                                    <th style="width: 5%;" class="text-center">No</th>
                                    <th style="width: 25%;">Point To Follow</th>
                                    <th colspan="{{ $categoryQuantity }}" class="text-center">Machine</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    @for($i = 1; $i <= $categoryQuantity; $i++)
                                        <th class="text-center" style="font-weight: normal;">Machine {{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody id="pointsBody{{ $machineCategory->id }}">
                                @php
                                    // Get existing points for this category
                                    $categoryDetails = $existingDetails->filter(function($group) use ($machineCategory) {
                                        $first = $group->first();
                                        return $first && $first->machine_category_id == $machineCategory->id;
                                    });
                                    
                                    // Get unique points
                                    $points = $categoryDetails->keys()->map(function($key) {
                                        $parts = explode('_', $key, 2);
                                        return isset($parts[1]) ? $parts[1] : '';
                                    })->filter()->unique();
                                    
                                    // If no existing points, use default
                                    if ($points->isEmpty()) {
                                        $points = collect($defaultPointsToFollow);
                                    }
                                @endphp
                                
                                @foreach($points as $pointIndex => $pointToFollow)
                                    <tr data-point-row>
                                        <td class="text-center fw-semibold" style="vertical-align: middle;">
                                            {{ $pointIndex + 1 }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <input type="text" 
                                                   name="machine_erection_details[{{ $machineCategory->id }}][{{ $pointIndex }}][point_to_follow]" 
                                                   value="{{ $pointToFollow }}"
                                                   class="form-control form-control-sm point-input" 
                                                   style="border-radius: 6px; border: 1px solid #e5e7eb; background-color: #f9fafb;"
                                                   readonly
                                                   required>
                                            <input type="hidden" 
                                                   name="machine_erection_details[{{ $machineCategory->id }}][{{ $pointIndex }}][machine_category_id]" 
                                                   value="{{ $machineCategory->id }}">
                                        </td>
                                        @for($machineNum = 1; $machineNum <= $categoryQuantity; $machineNum++)
                                            @php
                                                $existingDate = null;
                                                foreach ($categoryDetails as $key => $group) {
                                                    if (str_contains($key, $pointToFollow)) {
                                                        $detail = $group->where('machine_number', $machineNum)->first();
                                                        if ($detail && $detail->date) {
                                                            $existingDate = $detail->date->format('d-m');
                                                        }
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            <td style="vertical-align: middle;">
                                                <div class="position-relative">
                                                    <input type="text" 
                                                           name="machine_erection_details[{{ $machineCategory->id }}][{{ $pointIndex }}][machine_dates][{{ $machineNum }}]" 
                                                           value="{{ $existingDate }}"
                                                           placeholder="dd-mm"
                                                           class="form-control form-control-sm date-input" 
                                                           style="border-radius: 6px; border: 1px solid #e5e7eb;"
                                                           data-machine-number="{{ $machineNum }}">
                                                    <i class="fas fa-calendar-alt position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #6b7280;"></i>
                                                </div>
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        @endforeach
        
        <form method="POST" action="{{ route('machine-erection.store', $proformaInvoice) }}" id="mainErectionForm">
            @csrf
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('machine-erection.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save All Machine Erection Details
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                <p class="text-muted">No machine categories found for this Proforma Invoice.</p>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date pickers
            const dateInputs = document.querySelectorAll('.date-input');
            dateInputs.forEach(input => {
                flatpickr(input, {
                                            dateFormat: "d-m",
                    allowInput: true,
                });
            });
        });
        
    </script>
</x-app-layout>

