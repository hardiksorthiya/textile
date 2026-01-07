@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout> 
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Inventory Management</h1>
            <p class="text-muted mb-0">Manage inventory items and their related sellers</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('spares.download-template') }}" class="btn btn-outline-info">
                <i class="fas fa-download me-2"></i>Download CSV Template
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-csv me-2"></i>Import CSV
            </button>
        </div>
    </div>

    <div class="row g-4" x-data="spareApp()">

        <!-- LEFT FORM -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-cog'"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Inventory Item' : 'Add Inventory Item'"></h2>
                    </div>

                    <!-- ADD FORM -->
                    <div x-show="!isEditing">
                        <form action="{{ route('spares.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Image</label>
                                <input type="file" 
                                       name="image" 
                                       id="imageInput"
                                       accept="image/*"
                                       class="form-control" 
                                       @change="previewImage($event)"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                                </div>
                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required
                                       placeholder="Enter inventory item name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Description</label>
                                <textarea name="description" class="form-control" rows="3"
                                          placeholder="Enter description"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Spare Type <span class="text-danger">*</span></label>
                                <select name="spare_type" class="form-select" required
                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <option value="">Select Type</option>
                                    <option value="mechanical">Mechanical</option>
                                    <option value="electrical">Electrical</option>
                                </select>
                                @error('spare_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" required
                                       min="0" step="1" value="0"
                                       placeholder="Enter quantity"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('quantity')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Sellers <small class="text-muted">(Multiple Select)</small></label>
                                <div class="position-relative" @click.away="sellerDropdownOpen = false">
                                    <button type="button" 
                                            @click="sellerDropdownOpen = !sellerDropdownOpen"
                                            class="form-control text-start d-flex justify-content-between align-items-center"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                        <span x-text="selectedSellers.length > 0 ? selectedSellers.length + ' seller(s) selected' : 'Select Sellers'"></span>
                                        <i class="fas fa-chevron-down" :class="{ 'rotate-180': sellerDropdownOpen }" style="transition: transform 0.3s ease;"></i>
                                    </button>
                                    <div x-show="sellerDropdownOpen" 
                                         x-cloak
                                         class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                         style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                         @click.stop>
                                        @forelse($sellers ?? [] as $seller)
                                            <div class="d-flex align-items-center py-2 px-3" 
                                                 x-data="{ hovered: false }"
                                                 :class="isSellerSelected({{ $seller->id }}) ? 'bg-red-50' : ''"
                                                 :style="isSellerSelected({{ $seller->id }}) || hovered ? 'background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);' : 'background-color: white;'"
                                                 style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                 @mouseenter="hovered = true"
                                                 @mouseleave="hovered = false"
                                                 @click="toggleSeller({{ $seller->id }})">
                                                <input class="form-check-input me-3" 
                                                       type="checkbox" 
                                                       :checked="isSellerSelected({{ $seller->id }})"
                                                       style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                       @click.stop="toggleSeller({{ $seller->id }})">
                                                <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                    {{ $seller->seller_name }}
                                                </label>
                                                <i class="fas fa-check text-primary ms-2" x-show="isSellerSelected({{ $seller->id }})" style="font-size: 0.875rem;"></i>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center text-muted">
                                                <small>No sellers available. Add sellers first.</small>
                                            </div>
                                        @endforelse
                                    </div>
                                    <template x-for="sellerId in selectedSellers" :key="sellerId">
                                        <input type="hidden" :name="`sellers[]`" :value="sellerId">
                                    </template>
                                </div>
                                @error('sellers')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="fas fa-plus me-2"></i>Add Inventory Item
                            </button>
                        </form>
                    </div>

                    <!-- EDIT FORM -->
                    <div x-show="isEditing" x-cloak>
                        <form :action="`{{ url('spares') }}/${editingSpare.id}`" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Image</label>
                                <input type="file" 
                                       name="image" 
                                       id="editImageInput"
                                       accept="image/*"
                                       class="form-control" 
                                       @change="previewEditImage($event)"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                <div id="editImagePreview" class="mt-2">
                                    <img :src="editingSpare.image_url" alt="Current Image" class="img-thumbnail" style="max-width: 100%; max-height: 200px; border-radius: 8px;" x-show="editingSpare.image_url">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" x-model="editingSpare.name" class="form-control" required
                                       placeholder="Enter inventory item name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Description</label>
                                <textarea name="description" x-model="editingSpare.description" class="form-control" rows="3"
                                          placeholder="Enter description"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Spare Type <span class="text-danger">*</span></label>
                                <select name="spare_type" x-model="editingSpare.spare_type" class="form-select" required
                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <option value="">Select Type</option>
                                    <option value="mechanical">Mechanical</option>
                                    <option value="electrical">Electrical</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" x-model="editingSpare.quantity" class="form-control" required
                                       min="0" step="1"
                                       placeholder="Enter quantity"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Sellers <small class="text-muted">(Multiple Select)</small></label>
                                <div class="position-relative" @click.away="sellerDropdownOpen = false">
                                    <button type="button" 
                                            @click="sellerDropdownOpen = !sellerDropdownOpen"
                                            class="form-control text-start d-flex justify-content-between align-items-center"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                        <span x-text="selectedSellers.length > 0 ? selectedSellers.length + ' seller(s) selected' : 'Select Sellers'"></span>
                                        <i class="fas fa-chevron-down" :class="{ 'rotate-180': sellerDropdownOpen }" style="transition: transform 0.3s ease;"></i>
                                    </button>
                                    <div x-show="sellerDropdownOpen" 
                                         x-cloak
                                         class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                         style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                         @click.stop>
                                        @forelse($sellers ?? [] as $seller)
                                            <div class="d-flex align-items-center py-2 px-3" 
                                                 x-data="{ hovered: false }"
                                                 :class="isSellerSelected({{ $seller->id }}) ? 'bg-red-50' : ''"
                                                 :style="isSellerSelected({{ $seller->id }}) || hovered ? 'background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);' : 'background-color: white;'"
                                                 style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                 @mouseenter="hovered = true"
                                                 @mouseleave="hovered = false"
                                                 @click="toggleSeller({{ $seller->id }})">
                                                <input class="form-check-input me-3" 
                                                       type="checkbox" 
                                                       :checked="isSellerSelected({{ $seller->id }})"
                                                       style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                       @click.stop="toggleSeller({{ $seller->id }})">
                                                <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                    {{ $seller->seller_name }}
                                                </label>
                                                <i class="fas fa-check text-primary ms-2" x-show="isSellerSelected({{ $seller->id }})" style="font-size: 0.875rem;"></i>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center text-muted">
                                                <small>No sellers available</small>
                                            </div>
                                        @endforelse
                                    </div>
                                    <template x-for="sellerId in selectedSellers" :key="sellerId">
                                        <input type="hidden" :name="`sellers[]`" :value="sellerId">
                                    </template>
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
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT TABLE -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Inventory List</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: calc(100vh - 350px); overflow-y: auto; overflow-x: auto;">
                        <table class="table table-hover mb-0 align-middle" style="min-width: 1000px;">
                            <thead class="sticky-top" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; width: 80px;">Image</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; min-width: 200px;">Name</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; width: 120px;">Type</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; width: 100px;">Quantity</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; min-width: 180px;">Sellers</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; width: 130px;">Created</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; width: 120px; position: sticky; right: 0; background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($spares as $spare)
                                    <tr class="border-bottom" style="transition: all 0.2s ease; background-color: white;">
                                        <td class="px-4 py-3">
                                            @if($spare->image)
                                                <img src="{{ Storage::url($spare->image) }}" 
                                                     alt="{{ $spare->name }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                                     onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px; font-weight: 600; font-size: 20px;">
                                                    {{ strtoupper(substr($spare->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="fw-semibold" style="color: #1f2937; font-size: 0.95rem;">{{ $spare->name }}</div>
                                                @if($spare->description)
                                                <small class="text-muted" style="font-size: 0.75rem; display: block; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $spare->description }}">{{ $spare->description }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge" 
                                                  style="background-color: {{ $spare->spare_type == 'mechanical' ? '#3b82f6' : '#10b981' }}; color: white; font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                {{ ucfirst($spare->spare_type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge bg-info" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                {{ $spare->quantity ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($spare->sellers->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($spare->sellers as $seller)
                                                        <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-dark); font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                            {{ $seller->seller_name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <small class="text-muted">No sellers</small>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar-alt me-2 text-muted" style="font-size: 0.75rem;"></i>
                                                <small class="text-muted" style="font-size: 0.8rem;">{{ $spare->created_at->format('M d, Y') }}</small>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3" style="position: sticky; right: 0; background-color: white; z-index: 10; box-shadow: -2px 0 5px rgba(0,0,0,0.1);">
                                            <div class="d-flex gap-2" role="group">
                                                <button type="button" 
                                                        @click="editSpare({
                                                            id: {{ $spare->id }},
                                                            name: @js($spare->name),
                                                            description: @js($spare->description ?? ''),
                                                            spare_type: @js($spare->spare_type),
                                                            quantity: {{ $spare->quantity ?? 0 }},
                                                            image_url: @js($spare->image ? Storage::url($spare->image) : ''),
                                                            sellers: @js($spare->sellers)
                                                        })"
                                                        class="btn btn-sm btn-outline-primary" 
                                                        title="Edit Inventory Item">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('spares.destroy', $spare) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this inventory item?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Delete Inventory Item">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-cog fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                                <p class="mb-0" style="font-size: 0.9rem;">No inventory items found.</p>
                                                <small class="text-muted mt-1">Add your first inventory item to get started</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($spares->hasPages())
                    <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex justify-content-center">
                            {{ $spares->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <script>
            function spareApp() {
                return {
                    editingSpare: null,
                    isEditing: false,
                    selectedSellers: [],
                    sellerDropdownOpen: false,
            
                    toggleSeller(id) {
                        id = String(id);
                        const index = this.selectedSellers.indexOf(id);
                        index > -1
                            ? this.selectedSellers.splice(index, 1)
                            : this.selectedSellers.push(id);
                    },
            
                    isSellerSelected(id) {
                        return this.selectedSellers.includes(String(id));
                    },
            
                    editSpare(spare) {
                        this.editingSpare = spare;
                        this.isEditing = true;
            
                        this.selectedSellers = Array.isArray(spare.sellers)
                            ? spare.sellers.map(s => String(s.id)).filter(Boolean)
                            : [];
            
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },
            
                    cancelEdit() {
                        this.editingSpare = null;
                        this.isEditing = false;
                        this.selectedSellers = [];
                        this.sellerDropdownOpen = false;
                        // Reset image preview
                        document.getElementById('imagePreview').style.display = 'none';
                        document.getElementById('editImagePreview').innerHTML = '';
                    },

                    previewImage(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const preview = document.getElementById('imagePreview');
                                const img = document.getElementById('previewImg');
                                img.src = e.target.result;
                                preview.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        }
                    },

                    previewEditImage(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const preview = document.getElementById('editImagePreview');
                                preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 200px; border-radius: 8px;">`;
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                }
            }
        </script>
            
    <!-- Success/Error Message -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
        </div>
        <style>
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 bg-danger text-white px-4 py-3 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(45deg, #ef4444, #f87171) !important; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
            <div class="d-flex align-items-center">
                <i class="fas fa-times-circle me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <style>
        .rotate-180 {
            transform: rotate(180deg);
        }
        .table-hover tbody tr:hover {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        .table-hover tbody tr:hover td[style*="position: sticky"] {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
        }
        .card {
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>

    </div>

    <!-- CSV Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Inventory Items from CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('spares.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Select CSV File</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                            <small class="form-text text-muted">
                                CSV format: name, description, spare_type (mechanical/electrical), quantity, image (file path or URL), sellers (comma-separated)
                            </small>
                        </div>
                        <div class="alert alert-info">
                            <strong>CSV Format:</strong>
                            <ul class="mb-0 small">
                                <li><strong>name</strong> - Required: Inventory item name</li>
                                <li><strong>description</strong> - Optional: Description</li>
                                <li><strong>spare_type</strong> - Required: 'mechanical' or 'electrical'</li>
                                <li><strong>quantity</strong> - Required: Quantity (integer, minimum 0)</li>
                                <li><strong>image</strong> - Optional: Image file path (relative to storage/app/public/spares/) or image URL</li>
                                <li><strong>sellers</strong> - Optional: Comma-separated seller names (e.g., "Seller1,Seller2")</li>
                            </ul>
                            <div class="alert alert-warning mt-2 mb-0 small">
                                <strong>Note:</strong> For images, you can use:
                                <ul class="mb-0 mt-1">
                                    <li>File path: <code>image.jpg</code> (must be in storage/app/public/spares/)</li>
                                    <li>Full URL: <code>https://example.com/image.jpg</code></li>
                                </ul>
                            </div>
                        </div>
                        @if(session('import_errors'))
                            <div class="alert alert-warning">
                                <strong>Import Errors:</strong>
                                <ul class="mb-0 small">
                                    @foreach(session('import_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Import CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

