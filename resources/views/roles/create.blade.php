<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Create Role</h1>
            <p class="text-muted mb-0">Create a new role and assign permissions</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i>Back to Team
        </a>
    </div>

    <!-- Split Layout: 70% Form, 30% Role List -->
    <div class="row g-4">
        <!-- Left Side: Role Form with Permissions (70%) -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-user-tag text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Create New Role</h2>
                    </div>
                    
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        
                        <!-- Role Name -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #374151;">Role Name</label>
                            <input type="text" name="name" required
                                   value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g., Editor, Viewer, etc."
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Permissions -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-3" style="color: #374151;">Permissions</label>
                            
                            @php
                                $permissionGroups = [
                                    'User Management' => ['view users', 'create users', 'edit users', 'delete users'],
                                    'Role & Permission Management' => ['view roles', 'create roles', 'edit roles', 'delete roles', 'assign roles'],
                                    'Reports' => ['view reports', 'export reports'],
                                    'Settings' => ['view settings', 'edit settings'],
                                ];
                            @endphp

                            <div class="overflow-y-auto overflow-x-hidden" style="max-height: calc(100vh - 450px);">
                                <div class="row g-3">
                                    @foreach($permissionGroups as $groupName => $permissionNames)
                                        <div class="col-12">
                                            <div class="border rounded p-3" style="border-color: rgba(139, 92, 246, 0.2) !important; background: white;">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="fw-semibold mb-0" style="color: #1f2937;">{{ $groupName }}</h6>
                                                    <button type="button" 
                                                            onclick="toggleGroup('group-{{ $loop->index }}')"
                                                            class="btn btn-sm text-primary p-0" style="font-size: 0.75rem;">
                                                        <i class="fas fa-check-double me-1"></i>Select All
                                                    </button>
                                                </div>
                                                <div id="group-{{ $loop->index }}" class="row g-2 m-0">
                                                    @foreach($permissions as $permission)
                                                        @if(in_array($permission->name, $permissionNames))
                                                            <div class="col-md-6 col-lg-4 p-1">
                                                                <label for="permission-{{ $permission->id }}" class="d-block m-0" style="cursor: pointer;">
                                                                    <div class="permission-box p-3 rounded border position-relative" 
                                                                         style="background: rgba(139, 92, 246, 0.05); border: 2px solid rgba(139, 92, 246, 0.2) !important; transition: all 0.3s ease; min-height: 60px;"
                                                                         onmouseover="this.style.background='rgba(139, 92, 246, 0.1)'; this.style.borderColor='rgba(139, 92, 246, 0.4)'"
                                                                         onmouseout="this.style.background='rgba(139, 92, 246, 0.05)'; this.style.borderColor='rgba(139, 92, 246, 0.2)'">
                                                                        <div class="d-flex align-items-center">
                                                                            <input class="form-check-input me-3" 
                                                                                   type="checkbox" 
                                                                                   name="permissions[]" 
                                                                                   value="{{ $permission->id }}"
                                                                                   id="permission-{{ $permission->id }}"
                                                                                   {{ old('permissions') && in_array($permission->id, old('permissions')) ? 'checked' : '' }}
                                                                                   onchange="updateBoxStyle(this)"
                                                                                   style="width: 18px; height: 18px; border-color: #8b5cf6; cursor: pointer; margin-top: 0;">
                                                                            <span class="flex-grow-1" style="font-size: 0.875rem; color: #4b5563; font-weight: 500;">
                                                                                {{ $permission->name }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @error('permissions')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" style="border-radius: 8px;">
                                <i class="fas fa-save me-2"></i>Create Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side: Role List (30%) -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%);">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Role List</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($roles as $role)
                            <div class="list-group-item border-0 py-3 px-4" style="border-bottom: 1px solid rgba(139, 92, 246, 0.1) !important;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1" style="color: #1f2937;">
                                            {{ $role->name }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $role->permissions->count() }} permission(s)
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge 
                                            @if($role->name == 'Super Admin') bg-danger
                                            @elseif($role->name == 'Admin') bg-primary
                                            @elseif($role->name == 'Manager') bg-info
                                            @elseif($role->name == 'Staff') bg-success
                                            @else bg-secondary
                                            @endif" style="padding: 0.4em 0.8em; border-radius: 6px;">
                                            {{ $role->name }}
                                        </span>
                                        <div class="d-flex gap-2" role="group">
                                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary" title="Edit Role">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($role->name !== 'Super Admin')
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Role">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($role->permissions->count() > 0)
                                    <div class="mt-2">
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($role->permissions->take(3) as $permission)
                                                <span class="badge bg-light text-dark" style="font-size: 0.7rem; padding: 0.25em 0.5em; border-radius: 4px;">
                                                    {{ $permission->name }}
                                                </span>
                                            @endforeach
                                            @if($role->permissions->count() > 3)
                                                <span class="badge bg-light text-dark" style="font-size: 0.7rem; padding: 0.25em 0.5em; border-radius: 4px;">
                                                    +{{ $role->permissions->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary" style="font-size: 0.7rem;">No permissions</span>
                                @endif
                            </div>
                        @empty
                            <div class="list-group-item border-0 text-center py-5">
                                <i class="fas fa-user-tag fa-2x mb-3 d-block" style="color: #d1d5db;"></i>
                                <p class="text-muted mb-0">No roles found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px; animation: slideIn 0.3s ease;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px; animation: slideIn 0.3s ease;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                <span class="fw-semibold">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <script>
        function toggleGroup(groupId) {
            const group = document.getElementById(groupId);
            const checkboxes = group.querySelectorAll('input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
                updateBoxStyle(checkbox);
            });
        }
        
        function updateBoxStyle(checkbox) {
            const box = checkbox.closest('.permission-box');
            if (checkbox.checked) {
                box.style.background = 'rgba(139, 92, 246, 0.15)';
                box.style.borderColor = '#8b5cf6';
                box.style.boxShadow = '0 2px 8px rgba(139, 92, 246, 0.2)';
            } else {
                box.style.background = 'rgba(139, 92, 246, 0.05)';
                box.style.borderColor = 'rgba(139, 92, 246, 0.2)';
                box.style.boxShadow = 'none';
            }
        }
        
        // Initialize box styles on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(checkbox => {
                updateBoxStyle(checkbox);
            });
        });
    </script>
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
</x-app-layout>
