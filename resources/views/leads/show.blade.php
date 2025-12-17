<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">View Lead</h1>
            <p class="text-muted mb-0">Lead details and information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Leads
            </a>
            @can('edit leads')
            <a href="{{ route('leads.edit', $lead) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit Lead
            </a>
            @endcan
            @can('convert contract')
            @if(!$lead->contract)
            <a href="{{ route('leads.convert-to-contract', $lead) }}" class="btn btn-success">
                <i class="fas fa-user-check me-2"></i>Convert to Contract
            </a>
            @endif
            @endcan
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-user-friends text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Lead Information</h2>
                        <span class="badge ms-auto {{ $lead->type === 'new' ? 'bg-success' : 'bg-info' }}">
                            {{ ucfirst($lead->type) }} Lead
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3" style="color: #1f2937; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                <i class="fas fa-user me-2 text-primary"></i>Basic Information
                            </h5>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Name</label>
                                <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">{{ $lead->name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Phone Number</label>
                                <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">{{ $lead->phone_number }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Status</label>
                                <div>
                                    <span class="badge bg-info text-white" style="font-size: 0.875rem;">{{ $lead->status->name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3" style="color: #1f2937; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>Location
                            </h5>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">State</label>
                                <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">{{ $lead->state->name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">City</label>
                                <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">{{ $lead->city->name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Area</label>
                                <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">{{ $lead->area->name }}</div>
                            </div>
                        </div>

                        @if($lead->type === 'new')
                            <!-- New Lead Specific Information -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3" style="color: #1f2937; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                    <i class="fas fa-building me-2 text-primary"></i>Business Information
                                </h5>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Business</label>
                                    <div>
                                        @if($lead->business)
                                            <span class="badge" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.875rem; padding: 0.35rem 0.65rem;">{{ $lead->business->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Quantity</label>
                                    <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">{{ $lead->quantity }}</div>
                                </div>
                            </div>
                        @else
                            <!-- Old Lead Specific Information -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3" style="color: #1f2937; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                    <i class="fas fa-cog me-2 text-primary"></i>Machine Information
                                </h5>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Brand of Machine</label>
                                    <div>
                                        @if($lead->brand)
                                            <span class="badge" style="background-color: color-mix(in srgb, #3b82f6 15%, #ffffff); color: #2563eb; font-size: 0.875rem; padding: 0.35rem 0.65rem;">{{ $lead->brand->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Machine Quantity</label>
                                    <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">{{ $lead->machine_quantity ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Running Since</label>
                                    <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">{{ $lead->running_since ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Quantity</label>
                                    <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">{{ $lead->quantity }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Machine Categories -->
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3" style="color: #1f2937; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                <i class="fas fa-tags me-2 text-primary"></i>Machine Categories
                            </h5>
                            <div class="mb-3">
                                @if($lead->machineCategories->count() > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($lead->machineCategories as $category)
                                            <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-dark); font-size: 0.875rem; padding: 0.5rem 0.75rem;">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No categories assigned</span>
                                @endif
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-md-12">
                            <h5 class="fw-bold mb-3" style="color: #1f2937; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                <i class="fas fa-info-circle me-2 text-primary"></i>Additional Information
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Created At</label>
                                    <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">{{ $lead->created_at->format('M d, Y h:i A') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Updated At</label>
                                    <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">{{ $lead->updated_at->format('M d, Y h:i A') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 0.875rem;">Lead ID</label>
                                    <div class="fw-semibold" style="color: #1f2937; font-size: 1rem;">#{{ $lead->id }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>




