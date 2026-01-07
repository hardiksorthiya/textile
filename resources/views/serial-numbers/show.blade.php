<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Serial Numbers & Khata Numbers</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
            <p class="text-muted mb-0">Customer: {{ $proformaInvoice->buyer_company_name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('serial-numbers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-2"></i>View PI
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 pb-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-hashtag text-white"></i>
                </div>
                <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Machine Categories & Serial Numbers</h2>
            </div>
        </div>
        <div class="card-body p-4">
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

            @if($machinesByCategory->count() > 0)
            <form method="POST" action="{{ route('serial-numbers.store', $proformaInvoice) }}">
                @csrf
                
                @foreach($machinesByCategory as $categoryData)
                <div class="mb-4">
                    <h5 class="fw-bold mb-3" style="color: var(--primary-color);">
                        <i class="fas fa-tag me-2"></i>{{ $categoryData['category']->name }}
                        <span class="badge bg-secondary ms-2">Quantity: {{ $categoryData['total_quantity'] }}</span>
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                <tr>
                                    <th style="width: 25%;">Category</th>
                                    <th style="width: 15%;">Brand</th>
                                    <th style="width: 15%;">Model</th>
                                    <th style="width: 10%;">Quantity</th>
                                    <th style="width: 17.5%;">Serial Number</th>
                                    <th style="width: 17.5%;">Khata Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryData['machines'] as $machineData)
                                    @php
                                        $machine = $machineData['machine'];
                                        $quantity = $machine->quantity;
                                        $existingSerials = $machineData['serial_numbers'] ?? [];
                                    @endphp
                                    
                                    @for($i = 0; $i < $quantity; $i++)
                                    <tr>
                                        @if($i === 0)
                                        <td rowspan="{{ $quantity }}" class="align-middle fw-semibold" style="color: #374151;">
                                            {{ $categoryData['category']->name }}
                                        </td>
                                        <td rowspan="{{ $quantity }}" class="align-middle">
                                            {{ $machine->brand->name ?? 'N/A' }}
                                        </td>
                                        <td rowspan="{{ $quantity }}" class="align-middle">
                                            {{ $machine->machineModel->model_no ?? 'N/A' }}
                                        </td>
                                        <td rowspan="{{ $quantity }}" class="align-middle text-center">
                                            <span class="badge bg-primary">{{ $quantity }}</span>
                                        </td>
                                        @endif
                                        <td>
                                            <input type="text" 
                                                   name="serial_numbers[{{ $machine->id }}][{{ $i }}][serial_number]" 
                                                   value="{{ isset($existingSerials[$i]) ? $existingSerials[$i]->serial_number : '' }}"
                                                   placeholder="Enter Serial Number" 
                                                   class="form-control form-control-sm" 
                                                   style="border-radius: 6px; border: 1px solid #e5e7eb;">
                                            <input type="hidden" name="serial_numbers[{{ $machine->id }}][{{ $i }}][machine_id]" value="{{ $machine->id }}">
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   name="serial_numbers[{{ $machine->id }}][{{ $i }}][khata_number]" 
                                                   value="{{ isset($existingSerials[$i]) ? $existingSerials[$i]->khata_number : '' }}"
                                                   placeholder="Enter Khata Number" 
                                                   class="form-control form-control-sm" 
                                                   style="border-radius: 6px; border: 1px solid #e5e7eb;">
                                        </td>
                                    </tr>
                                    @endfor
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('serial-numbers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Serial Numbers
                    </button>
                </div>
            </form>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                <p class="text-muted mb-0">No machines found in this Proforma Invoice.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

