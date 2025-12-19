<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">PI Layouts</h1>
            <p class="text-muted mb-0">Manage proforma invoice layouts for different sellers</p>
        </div>
        <a href="{{ route('pi-layouts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Layout
        </a>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                        <tr>
                            <th class="text-center align-middle" style="border-color: rgba(255,255,255,0.3);">Name</th>
                            <th class="text-center align-middle" style="border-color: rgba(255,255,255,0.3);">Description</th>
                            <th class="text-center align-middle" style="border-color: rgba(255,255,255,0.3);">Status</th>
                            <th class="text-center align-middle" style="border-color: rgba(255,255,255,0.3);">Default</th>
                            <th class="text-center align-middle" style="border-color: rgba(255,255,255,0.3);">Used By</th>
                            <th class="text-center align-middle" style="border-color: rgba(255,255,255,0.3);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($layouts as $layout)
                            <tr>
                                <td class="text-center align-middle">
                                    <span class="fw-semibold">{{ $layout->name }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    {{ $layout->description ?? 'N/A' }}
                                </td>
                                <td class="text-center align-middle">
                                    @if($layout->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @if($layout->is_default)
                                        <span class="badge bg-primary">Default</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    {{ $layout->sellers()->count() }} seller(s)
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('pi-layouts.show', $layout) }}" class="btn btn-sm btn-info" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('pi-layouts.edit', $layout) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($layout->sellers()->count() == 0)
                                        <form action="{{ route('pi-layouts.destroy', $layout) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this layout?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-file-invoice fa-3x mb-3" style="opacity: 0.3;"></i>
                                        <p class="mb-0">No layouts found.</p>
                                        <a href="{{ route('pi-layouts.create') }}" class="btn btn-primary mt-3">
                                            <i class="fas fa-plus me-2"></i>Create First Layout
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
