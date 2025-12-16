<x-app-layout> 
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Delivery Term Management</h1>
            <p class="text-muted mb-0">Manage delivery terms</p>
        </div>
    </div>

    <div class="row g-4" x-data="deliveryTermApp()">

        <!-- LEFT FORM -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-shipping-fast'"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Delivery Term' : 'Add Delivery Term'"></h2>
                    </div>

                    <!-- ADD FORM -->
                    <div x-show="!isEditing">
                        <form action="{{ route('delivery-terms.store') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Delivery Term Name</label>
                                <input type="text" name="name" class="form-control" required
                                       placeholder="Enter delivery term name (e.g., FOB, CIF)"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="fas fa-plus me-2"></i>Add Delivery Term
                            </button>
                        </form>
                    </div>

                    <!-- EDIT FORM -->
                    <div x-show="isEditing" x-cloak>
                        <form :action="`{{ url('delivery-terms') }}/${editingTerm.id}`" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Delivery Term Name</label>
                                <input type="text" name="name" x-model="editingTerm.name" class="form-control" required
                                       placeholder="Enter delivery term name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill py-2 fw-semibold">
                                    <i class="fas fa-save me-2"></i>Update
                                </button>
                                <button type="button" @click="cancelEdit()" class="btn btn-outline-secondary flex-fill py-2 fw-semibold">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT TABLE -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h5 fw-bold mb-0" style="color: #1f2937;">Delivery Terms List</h3>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="color: #374151; font-weight: 600;">#</th>
                                    <th style="color: #374151; font-weight: 600;">Name</th>
                                    <th style="color: #374151; font-weight: 600;" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deliveryTerms as $index => $deliveryTerm)
                                    <tr>
                                        <td>{{ $deliveryTerms->firstItem() + $index }}</td>
                                        <td class="fw-semibold" style="color: #1f2937;">{{ $deliveryTerm->name }}</td>
                                        <td class="text-end">
                                            <button @click="editTerm({{ $deliveryTerm->id }}, '{{ addslashes($deliveryTerm->name) }}')" 
                                                    class="btn btn-sm btn-outline-primary me-2" 
                                                    style="border-radius: 6px;">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('delivery-terms.destroy', $deliveryTerm) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this delivery term?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="mb-0">No delivery terms found. Add your first delivery term.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($deliveryTerms->hasPages())
                        <div class="mt-4">
                            {{ $deliveryTerms->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function deliveryTermApp() {
            return {
                isEditing: false,
                editingTerm: {
                    id: null,
                    name: ''
                },

                editTerm(id, name) {
                    this.isEditing = true;
                    this.editingTerm = {
                        id: id,
                        name: name
                    };
                    // Scroll to top of form
                    document.querySelector('.col-lg-4').scrollIntoView({ behavior: 'smooth', block: 'start' });
                },

                cancelEdit() {
                    this.isEditing = false;
                    this.editingTerm = {
                        id: null,
                        name: ''
                    };
                }
            }
        }
    </script>
</x-app-layout>
