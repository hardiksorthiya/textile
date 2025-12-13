<x-app-layout> 
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Machine Hook Management</h1>
            <p class="text-muted mb-0">Manage machine hooks and their related categories</p>
        </div>
    </div>

    <div class="row g-4" x-data="machineHookApp()">

        <!-- LEFT FORM -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-link'"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Machine Hook' : 'Add Machine Hook'"></h2>
                    </div>

                    <!-- ADD FORM -->
                    <div x-show="!isEditing">
                        <form action="{{ route('machine-hooks.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Hook</label>
                                <input type="text" name="hook" class="form-control" required
                                       placeholder="Enter hook name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('hook')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Categories <small class="text-muted">(Multiple Select)</small></label>
                                <div class="position-relative" @click.away="categoryDropdownOpen = false">
                                    <button type="button" 
                                            @click="categoryDropdownOpen = !categoryDropdownOpen"
                                            class="form-control text-start d-flex justify-content-between align-items-center"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                        <span x-text="selectedCategories.length > 0 ? selectedCategories.length + ' category(ies) selected' : 'Select Categories'"></span>
                                        <i class="fas fa-chevron-down" :class="{ 'rotate-180': categoryDropdownOpen }" style="transition: transform 0.3s ease;"></i>
                                    </button>
                                    <div x-show="categoryDropdownOpen" 
                                         x-cloak
                                         class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                         style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                         @click.stop>
                                        @forelse($categories ?? [] as $category)
                                            <div class="d-flex align-items-center py-2 px-3" 
                                                 x-data="{ hovered: false }"
                                                 :class="isCategorySelected({{ $category->id }}) ? 'bg-purple-50' : ''"
                                                 :style="isCategorySelected({{ $category->id }}) || hovered ? 'background-color: #f3e8ff;' : 'background-color: white;'"
                                                 style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                 @mouseenter="hovered = true"
                                                 @mouseleave="hovered = false"
                                                 @click="toggleCategory({{ $category->id }})">
                                                <input class="form-check-input me-3" 
                                                       type="checkbox" 
                                                       :checked="isCategorySelected({{ $category->id }})"
                                                       style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                       @click.stop="toggleCategory({{ $category->id }})">
                                                <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                    {{ $category->name }}
                                                </label>
                                                <i class="fas fa-check text-primary ms-2" x-show="isCategorySelected({{ $category->id }})" style="font-size: 0.875rem;"></i>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center text-muted">
                                                <small>No categories available. Add categories first.</small>
                                            </div>
                                        @endforelse
                                    </div>
                                    <template x-for="categoryId in selectedCategories" :key="categoryId">
                                        <input type="hidden" :name="`categories[]`" :value="categoryId">
                                    </template>
                                </div>
                                @error('categories')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" style="border-radius: 8px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important; border: none;">
                                <i class="fas fa-plus me-2"></i>Add Machine Hook
                            </button>
                        </form>
                    </div>

                    <!-- EDIT FORM -->
                    <div x-show="isEditing" x-cloak>
                        <form :action="`{{ url('machine-hooks') }}/${editingHook.id}`" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Hook</label>
                                <input type="text" name="hook" x-model="editingHook.hook" class="form-control" required
                                       placeholder="Enter hook name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Categories <small class="text-muted">(Multiple Select)</small></label>
                                <div class="position-relative" @click.away="categoryDropdownOpen = false">
                                    <button type="button" 
                                            @click="categoryDropdownOpen = !categoryDropdownOpen"
                                            class="form-control text-start d-flex justify-content-between align-items-center"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                        <span x-text="selectedCategories.length > 0 ? selectedCategories.length + ' category(ies) selected' : 'Select Categories'"></span>
                                        <i class="fas fa-chevron-down" :class="{ 'rotate-180': categoryDropdownOpen }" style="transition: transform 0.3s ease;"></i>
                                    </button>
                                    <div x-show="categoryDropdownOpen" 
                                         x-cloak
                                         class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                         style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                         @click.stop>
                                        @forelse($categories ?? [] as $category)
                                            <div class="d-flex align-items-center py-2 px-3" 
                                                 x-data="{ hovered: false }"
                                                 :class="isCategorySelected({{ $category->id }}) ? 'bg-purple-50' : ''"
                                                 :style="isCategorySelected({{ $category->id }}) || hovered ? 'background-color: #f3e8ff;' : 'background-color: white;'"
                                                 style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                 @mouseenter="hovered = true"
                                                 @mouseleave="hovered = false"
                                                 @click="toggleCategory({{ $category->id }})">
                                                <input class="form-check-input me-3" 
                                                       type="checkbox" 
                                                       :checked="isCategorySelected({{ $category->id }})"
                                                       style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                       @click.stop="toggleCategory({{ $category->id }})">
                                                <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                    {{ $category->name }}
                                                </label>
                                                <i class="fas fa-check text-primary ms-2" x-show="isCategorySelected({{ $category->id }})" style="font-size: 0.875rem;"></i>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center text-muted">
                                                <small>No categories available</small>
                                            </div>
                                        @endforelse
                                    </div>
                                    <template x-for="categoryId in selectedCategories" :key="categoryId">
                                        <input type="hidden" :name="`categories[]`" :value="categoryId">
                                    </template>
                                </div>
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
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT TABLE -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Machine Hook List</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: calc(100vh - 350px); overflow-y: auto; overflow-x: hidden;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="sticky-top" style="background: linear-gradient(to right, #f3e8ff, #e9d5ff) !important;">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important; border-bottom: 1px solid #d8b4fe !important;">Hook</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important; border-bottom: 1px solid #d8b4fe !important;">Categories</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important; border-bottom: 1px solid #d8b4fe !important;">Created</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important; border-bottom: 1px solid #d8b4fe !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($machineHooks as $hook)
                                    <tr class="border-bottom" style="transition: all 0.2s ease;">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 shadow-sm" 
                                                     style="width: 45px; height: 45px; font-weight: 600; font-size: 16px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);">
                                                    {{ strtoupper(substr($hook->hook, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold" style="color: #1f2937; font-size: 0.95rem;">{{ $hook->hook }}</div>
                                                    <small class="text-muted" style="font-size: 0.75rem;">ID: {{ $hook->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($hook->machineCategories->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($hook->machineCategories as $category)
                                                        <span class="badge" style="background-color: #f3e8ff; color: #7c3aed; font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                            {{ $category->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <small class="text-muted">No categories</small>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar-alt me-2 text-muted" style="font-size: 0.75rem;"></i>
                                                <small class="text-muted" style="font-size: 0.8rem;">{{ $hook->created_at->format('M d, Y') }}</small>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex gap-2" role="group">
                                                <button type="button" 
                                                        @click="editHook({
                                                            id: {{ $hook->id }},
                                                            hook: '{{ addslashes($hook->hook) }}',
                                                            categories: @js($hook->machineCategories)
                                                        })"
                                                        class="btn btn-sm btn-outline-primary" 
                                                        title="Edit Machine Hook"
                                                        style="border-radius: 6px; border-color: #8b5cf6; color: #8b5cf6; transition: all 0.2s ease;"
                                                        onmouseover="this.style.background='#8b5cf6'; this.style.color='white';"
                                                        onmouseout="this.style.background='transparent'; this.style.color='#8b5cf6';">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('machine-hooks.destroy', $hook) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this machine hook?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Delete Machine Hook"
                                                            style="border-radius: 6px; border-color: #ef4444; color: #ef4444; transition: all 0.2s ease;"
                                                            onmouseover="this.style.background='#ef4444'; this.style.color='white';"
                                                            onmouseout="this.style.background='transparent'; this.style.color='#ef4444';">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-link fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                                <p class="mb-0" style="font-size: 0.9rem;">No machine hooks found.</p>
                                                <small class="text-muted mt-1">Add your first machine hook to get started</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($machineHooks->hasPages())
                    <div class="card-footer bg-transparent border-top" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="d-flex justify-content-center">
                            {{ $machineHooks->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <script>
            function machineHookApp() {
                return {
                    editingHook: null,
                    isEditing: false,
                    selectedCategories: [],
                    categoryDropdownOpen: false,
            
                    toggleCategory(id) {
                        id = String(id);
                        const index = this.selectedCategories.indexOf(id);
                        index > -1
                            ? this.selectedCategories.splice(index, 1)
                            : this.selectedCategories.push(id);
                    },
            
                    isCategorySelected(id) {
                        return this.selectedCategories.includes(String(id));
                    },
            
                    editHook(hook) {
                        this.editingHook = hook;
                        this.isEditing = true;
            
                        this.selectedCategories = Array.isArray(hook.categories)
                            ? hook.categories.map(c => String(c.id)).filter(Boolean)
                            : [];
            
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },
            
                    cancelEdit() {
                        this.editingHook = null;
                        this.isEditing = false;
                        this.selectedCategories = [];
                        this.categoryDropdownOpen = false;
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
            background-color: #f3e8ff !important;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        .card {
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>

    </div>
</x-app-layout>
