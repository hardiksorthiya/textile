<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">PI Layout Preview</h1>
            <p class="text-muted mb-0">Layout: {{ $piLayout->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pi-layouts.edit', $piLayout) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('pi-layouts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Layouts
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <strong>Name:</strong> {{ $piLayout->name }}
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong> 
                    @if($piLayout->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                    @if($piLayout->is_default)
                        <span class="badge bg-primary ms-2">Default</span>
                    @endif
                </div>
                @if($piLayout->description)
                <div class="col-12">
                    <strong>Description:</strong> {{ $piLayout->description }}
                </div>
                @endif
                <div class="col-12">
                    <strong>Used By:</strong> {{ $piLayout->sellers()->count() }} seller(s)
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header">
            <h5 class="mb-0">Template HTML</h5>
        </div>
        <div class="card-body">
            <pre class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto;"><code>{{ $piLayout->template_html }}</code></pre>
        </div>
    </div>
</x-app-layout>
