<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Business Firm Management</h1>
            <p class="text-muted mb-0">Manage business firms with logo and address</p>
        </div>
    </div>

    <div class="row g-4" x-data="{ 
        editingFirm: null, 
        isEditing: false,
        editFirm(firm) {
            this.editingFirm = firm;
            this.isEditing = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editingFirm = null;
            this.isEditing = false;
        }
    }">
        <!-- LEFT FORM -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-building'"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Business Firm' : 'Add Business Firm'"></h2>
                    </div>
                    
                    <!-- Add Form -->
                    <div x-show="!isEditing">
                        <form action="{{ route('business-firms.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Business Firm Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" required
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter business firm name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Logo</label>
                                <input type="file" name="logo" accept="image/*"
                                       class="form-control @error('logo') is-invalid @enderror"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;"
                                       onchange="previewImage(this, 'add-preview')">
                                @error('logo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div id="add-preview" class="mt-2" style="display: none;">
                                    <img src="" alt="Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Address</label>
                                <textarea name="address" rows="3"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Enter address"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" style="border-radius: 8px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important; border: none;">
                                <i class="fas fa-plus me-2"></i>Add Business Firm
                            </button>
                        </form>
                    </div>

                    <!-- Edit Form -->
                    <div x-show="isEditing" x-cloak>
                        <template x-if="editingFirm">
                            <form :action="`{{ url('business-firms') }}/${editingFirm.id}`" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="color: #374151;">Business Firm Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" required
                                           x-model="editingFirm.name"
                                           class="form-control"
                                           placeholder="Enter business firm name"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="color: #374151;">Logo</label>
                                    <input type="file" name="logo" accept="image/*"
                                           class="form-control"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;"
                                           onchange="previewImage(this, 'edit-preview')">
                                    <div class="mt-2" x-show="editingFirm.logo">
                                        <small class="text-muted">Current logo:</small>
                                        <div>
                                            <img :src="`{{ asset('storage') }}/${editingFirm.logo}`" alt="Current Logo" class="img-thumbnail mt-1" style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                                        </div>
                                    </div>
                                    <div id="edit-preview" class="mt-2" style="display: none;">
                                        <small class="text-muted">New logo preview:</small>
                                        <div>
                                            <img src="" alt="Preview" class="img-thumbnail mt-1" style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Address</label>
                                    <textarea name="address" rows="3"
                                              x-model="editingFirm.address"
                                              class="form-control"
                                              placeholder="Enter address"
                                              style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
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

        <!-- RIGHT SIDE: Business Firm List -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Business Firm List</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: calc(100vh - 350px); overflow-y: auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="sticky-top" style="background: linear-gradient(to right, #f3e8ff, #e9d5ff) !important;">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Logo</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Name</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Address</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Created</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($businessFirms as $firm)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-3">
                                            @if($firm->logo)
                                                <img src="{{ asset('storage/' . $firm->logo) }}" alt="{{ $firm->name }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                            @else
                                                <div class="rounded-circle bg-purple-100 d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-building text-purple-600"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold" style="color: #1f2937;">{{ $firm->name }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <small class="text-muted">{{ Str::limit($firm->address, 50) }}</small>
                                        </td>
                                        <td class="px-4 py-3">
                                            <small class="text-muted">{{ $firm->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex gap-2">
                                                <button type="button" 
                                                        @click="editFirm({
                                                            id: {{ $firm->id }},
                                                            name: '{{ addslashes($firm->name) }}',
                                                            address: '{{ addslashes($firm->address ?? '') }}',
                                                            logo: '{{ $firm->logo }}'
                                                        })"
                                                        class="btn btn-sm btn-outline-primary" 
                                                        style="border-radius: 6px;" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('business-firms.destroy', $firm) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-building fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                                <p class="mb-0">No business firms found.</p>
                                                <small class="text-muted mt-1">Add your first business firm to get started</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($businessFirms->hasPages())
                    <div class="card-footer bg-transparent border-top">
                        <div class="d-flex justify-content-center">
                            {{ $businessFirms->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.style.display = 'block';
                    preview.querySelector('img').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
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
