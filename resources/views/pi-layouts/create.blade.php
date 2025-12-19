<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Create PI Layout</h1>
            <p class="text-muted mb-0">Create a new proforma invoice layout template</p>
        </div>
        <a href="{{ route('pi-layouts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Layouts
        </a>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('pi-layouts.store') }}" method="POST">
                @csrf
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #374151;">Layout Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}" 
                               class="form-control" placeholder="Enter layout name" 
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #374151;">Description</label>
                        <input type="text" name="description" value="{{ old('description') }}" 
                               class="form-control" placeholder="Enter description" 
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active" style="color: #374151;">
                                Active
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default" style="color: #374151;">
                                Set as Default Layout
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" style="color: #374151;">Template HTML <span class="text-danger">*</span></label>
                    <p class="text-muted small mb-2">
                        Use Blade syntax. Available variables: <code>$proformaInvoice</code>, <code>$seller</code>, <code>$machines</code>
                    </p>
                    <textarea name="template_html" required rows="20" 
                              class="form-control font-monospace" 
                              style="border-radius: 8px; border: 1px solid #e5e7eb; font-family: 'Courier New', monospace;"
                              placeholder="Enter HTML template with Blade syntax">{{ old('template_html', $defaultTemplate ?? '') }}</textarea>
                    @error('template_html')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('pi-layouts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Layout
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
