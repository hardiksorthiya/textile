<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Area Management</h1>
            <p class="text-muted mb-0">Manage areas related to cities</p>
        </div>
    </div>

    <div class="row g-4" x-data="{ 
        editingArea: null, 
        isEditing: false,
        editArea(area) {
            this.editingArea = area;
            this.isEditing = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editingArea = null;
            this.isEditing = false;
        }
    }">
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-map-marker-alt'"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Area' : 'Add Area'"></h2>
                    </div>
                    
                    <div x-show="!isEditing">
                        <form action="{{ route('areas.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">City</label>
                                <select name="city_id" required class="form-select @error('city_id') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="">Select City</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }} ({{ $city->state->name }})</option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Area Name</label>
                                <input type="text" name="name" required
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter area name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" style="border-radius: 8px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important; border: none;">
                                <i class="fas fa-plus me-2"></i>Add Area
                            </button>
                        </form>
                    </div>

                    <div x-show="isEditing" x-cloak>
                        <template x-if="editingArea">
                            <form :action="`{{ url('areas') }}/${editingArea.id}`" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="color: #374151;">City</label>
                                    <select name="city_id" required class="form-select" style="border-radius: 8px;" x-model="editingArea.city_id">
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }} ({{ $city->state->name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Area Name</label>
                                    <input type="text" name="name" required
                                           x-model="editingArea.name"
                                           class="form-control"
                                           placeholder="Enter area name"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" @click="cancelEdit()" class="btn btn-outline-secondary flex-grow-1" style="border-radius: 8px;">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important; border: none;">
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
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Area List</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: calc(100vh - 350px); overflow-y: auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="sticky-top" style="background: linear-gradient(to right, #f3e8ff, #e9d5ff) !important;">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Area Name</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">City</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">State</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Created</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($areas as $area)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold" style="color: #1f2937;">{{ $area->name }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge bg-purple-100 text-purple-800">{{ $area->city->name }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge bg-purple-200 text-purple-900">{{ $area->city->state->name }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <small class="text-muted">{{ $area->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex gap-2">
                                                <button type="button" 
                                                        @click="editArea({ id: {{ $area->id }}, name: '{{ addslashes($area->name) }}', city_id: {{ $area->city_id }} })"
                                                        class="btn btn-sm btn-outline-primary" 
                                                        style="border-radius: 6px;">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('areas.destroy', $area) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
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
                                        <td colspan="5" class="text-center text-muted py-5">No areas found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($areas->hasPages())
                    <div class="card-footer bg-transparent border-top">
                        <div class="d-flex justify-content-center">
                            {{ $areas->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

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
