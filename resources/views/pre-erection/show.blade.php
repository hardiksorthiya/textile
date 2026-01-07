<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Pre Erection Details</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
            <p class="text-muted mb-0">Customer: {{ $proformaInvoice->buyer_company_name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pre-erection.index') }}" class="btn btn-outline-secondary">
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
                    <i class="fas fa-tools text-white"></i>
                </div>
                <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Technical Specifications</h2>
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
            
            <form method="POST" action="{{ route('pre-erection.store', $proformaInvoice) }}">
                @csrf
                
                <!-- Pre Erection Details Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover">
                        <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                            <tr>
                                <th style="width: 8%;" class="text-center">Sno</th>
                                <th style="width: 32%;">Technical Specification</th>
                                <th style="width: 60%;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($technicalSpecifications as $index => $specification)
                                @php
                                    $existingDetail = $existingDetails->get($specification);
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center align-items-center" style="min-height: 38px;">
                                            <input type="checkbox" 
                                                   name="pre_erection_details[{{ $index }}][is_completed]" 
                                                   value="1"
                                                   {{ $existingDetail && $existingDetail->is_completed ? 'checked' : '' }}
                                                   class="form-check-input" 
                                                   style="width: 20px; height: 20px; cursor: pointer; margin: 0;">
                                        </div>
                                        <input type="hidden" name="pre_erection_details[{{ $index }}][technical_specification]" value="{{ $specification }}">
                                    </td>
                                    <td class="fw-semibold" style="color: #374151; vertical-align: middle;">
                                        {{ $specification }}
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <input type="text" 
                                               name="pre_erection_details[{{ $index }}][details]" 
                                               value="{{ $existingDetail ? $existingDetail->details : '' }}"
                                               placeholder="Enter Details" 
                                               class="form-control form-control-sm" 
                                               style="border-radius: 6px; border: 1px solid #e5e7eb;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('pre-erection.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Pre Erection Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

