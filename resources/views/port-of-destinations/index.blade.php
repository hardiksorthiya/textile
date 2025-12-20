<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Port of Destination Management</h1>
            <p class="text-muted mb-0">Manage ports of destination</p>
        </div>
    </div>

    <!-- Split Layout: 30% Form, 70% Table -->
    <div class="row g-4" x-data="{ 
        editingPort: null, 
        isEditing: false,
        editPort(port) {
            this.editingPort = port;
            this.isEditing = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editingPort = null;
            this.isEditing = false;
        }
    }">
        <!-- Left Side: Add/Edit Port Form (30%) -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-anchor'"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Port' : 'Add Port'"></h2>
                    </div>
                    
                    <!-- Add Form -->
                    <div x-show="!isEditing">
                        <form action="{{ route('port-of-destinations.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Port Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" required
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter port name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Port Code</label>
                                <input type="text" name="code"
                                       value="{{ old('code') }}"
                                       class="form-control @error('code') is-invalid @enderror"
                                       placeholder="Enter port code"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Description</label>
                                <textarea name="description" rows="3"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Enter description"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="fas fa-plus me-2"></i>Add Port
                            </button>
                        </form>
                    </div>

                    <!-- Edit Form -->
                    <div x-show="isEditing" x-cloak>
                        <template x-if="editingPort">
                            <form :action="`{{ url('admin/port-of-destinations') }}/${editingPort.id}`" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Port Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" required
                                           x-model="editingPort.name"
                                           class="form-control"
                                           placeholder="Enter port name"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Port Code</label>
                                    <input type="text" name="code"
                                           x-model="editingPort.code"
                                           class="form-control"
                                           placeholder="Enter port code"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Description</label>
                                    <textarea name="description" rows="3"
                                              x-model="editingPort.description"
                                              class="form-control"
                                              placeholder="Enter description"
                                              style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1 py-2 fw-semibold">
                                        <i class="fas fa-save me-2"></i>Update Port
                                    </button>
                                    <button type="button" @click="cancelEdit()" class="btn btn-outline-secondary py-2">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </form>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Ports Table (70%) -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                <i class="fas fa-list text-white"></i>
                            </div>
                            <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Ports of Destination</h2>
                            <span class="badge ms-3" style="background-color: color-mix(in srgb, #3b82f6 15%, #ffffff); color: #2563eb; font-size: 0.875rem; padding: 0.35rem 0.65rem;">{{ $ports->total() }} Total</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="sticky-top" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Port Name</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Port Code</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Description</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold text-center" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ports as $port)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold" style="color: #1f2937;">{{ $port->name }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div style="color: #6b7280;">{{ $port->code ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div style="color: #6b7280;">{{ Str::limit($port->description ?? 'N/A', 50) }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <button @click="editPort(@js($port))" class="action-btn action-btn-edit" title="Edit">
                                                    <i class="fas fa-edit" style="font-size: 14px;"></i>
                                                </button>
                                                <form action="{{ route('port-of-destinations.destroy', $port->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this port?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                                        <i class="fas fa-trash" style="font-size: 14px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-anchor fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                                <p class="mb-0">No ports of destination found.</p>
                                                <small class="text-muted mt-1">Add your first port to get started</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($ports->hasPages())
                    <div class="card-footer border-0 bg-transparent">
                        {{ $ports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border-radius: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
        border: 1px solid;
    }
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .action-btn-edit {
        border-color: #800020;
        color: #800020;
    }
    .action-btn-edit:hover {
        background-color: #800020;
        color: white;
    }
    .action-btn-delete {
        border-color: #dc2626;
        color: #dc2626;
    }
    .action-btn-delete:hover {
        background-color: #dc2626;
        color: white;
    }
</style>
