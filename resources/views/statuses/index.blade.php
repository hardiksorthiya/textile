<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Status Management</h1>
            <p class="text-muted mb-0">Manage status text fields</p>
        </div>
    </div>

    <div class="row g-4" x-data="{ 
        editingStatus: null, 
        isEditing: false,
        editStatus(status) {
            this.editingStatus = status;
            this.isEditing = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editingStatus = null;
            this.isEditing = false;
        }
    }">
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-info-circle'"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Status' : 'Add Status'"></h2>
                    </div>
                    
                    <div x-show="!isEditing">
                        <form action="{{ route('statuses.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Status Name</label>
                                <input type="text" name="name" required
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter status name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <div class="card p-3" style="background-color: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 8px;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label class="form-label fw-semibold mb-0" style="color: #374151;">
                                            <i class="fas fa-calendar-alt me-2"></i>Requires Scheduling
                                        </label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="requires_scheduling_add"
                                                   name="requires_scheduling"
                                                   value="1"
                                                   {{ old('requires_scheduling') ? 'checked' : '' }}
                                                   style="cursor: pointer; width: 3rem; height: 1.5rem;">
                                            <label class="form-check-label ms-2" for="requires_scheduling_add" style="cursor: pointer;">
                                                <span id="scheduling_label_add">Disabled</span>
                                            </label>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        When enabled, leads with this status will show date/time scheduling fields.
                                    </small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="fas fa-plus me-2"></i>Add Status
                            </button>
                        </form>
                    </div>

                    <div x-show="isEditing" x-cloak>
                        <template x-if="editingStatus">
                            <form :action="`{{ url('statuses') }}/${editingStatus.id}`" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Status Name</label>
                                    <input type="text" name="name" required
                                           x-model="editingStatus.name"
                                           class="form-control"
                                           placeholder="Enter status name"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-4">
                                    <div class="card p-3" style="background-color: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 8px;">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label class="form-label fw-semibold mb-0" style="color: #374151;">
                                                <i class="fas fa-calendar-alt me-2"></i>Requires Scheduling
                                            </label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="requires_scheduling_edit"
                                                       name="requires_scheduling"
                                                       value="1"
                                                       :checked="editingStatus.requires_scheduling == 1"
                                                       style="cursor: pointer; width: 3rem; height: 1.5rem;">
                                                <label class="form-check-label ms-2" for="requires_scheduling_edit" style="cursor: pointer;">
                                                    <span x-text="editingStatus.requires_scheduling == 1 ? 'Enabled' : 'Disabled'"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            When enabled, leads with this status will show date/time scheduling fields.
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" @click="cancelEdit()" class="btn btn-outline-secondary flex-grow-1" style="border-radius: 8px;">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-save me-2"></i>Update
                                    </button>
                                </div>
                            </form>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Status List</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: calc(100vh - 350px); overflow-y: auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="sticky-top" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Status Name</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Scheduling</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Created</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($statuses as $status)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold" style="color: #1f2937;">{{ $status->name }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($status->requires_scheduling)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-calendar-check me-1"></i>Enabled
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-calendar-times me-1"></i>Disabled
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <small class="text-muted">{{ $status->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex gap-2">
                                                <button type="button" 
                                                        @click="editStatus({ id: {{ $status->id }}, name: '{{ addslashes($status->name) }}', requires_scheduling: {{ $status->requires_scheduling ? 1 : 0 }} })"
                                                        class="btn btn-sm btn-outline-primary" 
                                                        style="border-radius: 6px;">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('statuses.destroy', $status) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">No statuses found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($statuses->hasPages())
                    <div class="card-footer bg-transparent border-top">
                        <div class="d-flex justify-content-center">
                            {{ $statuses->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Update scheduling label on toggle change
        document.addEventListener('DOMContentLoaded', function() {
            const toggleAdd = document.getElementById('requires_scheduling_add');
            const labelAdd = document.getElementById('scheduling_label_add');
            
            if (toggleAdd && labelAdd) {
                toggleAdd.addEventListener('change', function() {
                    labelAdd.textContent = this.checked ? 'Enabled' : 'Disabled';
                });
            }
        });
    </script>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
</x-app-layout>




