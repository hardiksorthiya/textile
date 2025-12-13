<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Team Management</h1>
            <p class="text-muted mb-0">Manage team members and roles</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary d-flex align-items-center shadow-sm">
            <i class="fas fa-user-tag me-2"></i>
            Create Role
        </a>
    </div>

    <!-- Split Layout: 30% Form, 70% Table -->
    <div class="row g-4" x-data="{ 
        editingUser: null, 
        isEditing: false,
        editUser(user) {
            this.editingUser = user;
            this.isEditing = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editingUser = null;
            this.isEditing = false;
        }
    }">
        <!-- Left Side: Add/Edit Team Member Form (30%) -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-user-edit' : 'fa-user-plus'"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Team Member' : 'Add Team Member'"></h2>
                    </div>
                    
                    <!-- Add Form -->
                    <div x-show="!isEditing">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Name</label>
                                <input type="text" name="name" required
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter full name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Email</label>
                                <input type="email" name="email" required
                                       value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Enter email address"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Password</label>
                                <input type="password" name="password" required
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter password"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Confirm Password</label>
                                <input type="password" name="password_confirmation" required
                                       class="form-control"
                                       placeholder="Confirm password"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Role</label>
                                <select name="role" required
                                        class="form-select @error('role') is-invalid @enderror"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" style="border-radius: 8px;">
                                <i class="fas fa-plus me-2"></i>Add Team Member
                            </button>
                        </form>
                    </div>

                    <!-- Edit Form -->
                    <div x-show="isEditing" x-cloak>
                        <template x-if="editingUser">
                            <form :action="`{{ url('users') }}/${editingUser.id}`" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="color: #374151;">Name</label>
                                    <input type="text" name="name" required
                                           x-model="editingUser.name"
                                           class="form-control"
                                           placeholder="Enter full name"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="color: #374151;">Email</label>
                                    <input type="email" name="email" required
                                           x-model="editingUser.email"
                                           class="form-control"
                                           placeholder="Enter email address"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="color: #374151;">Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                                    <input type="password" name="password"
                                           class="form-control"
                                           placeholder="Enter new password (optional)"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="color: #374151;">Confirm Password</label>
                                    <input type="password" name="password_confirmation"
                                           class="form-control"
                                           placeholder="Confirm new password"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Role</label>
                                    <select name="role" required
                                            x-model="editingUser.current_role"
                                            class="form-select"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" @click="cancelEdit()" class="btn btn-outline-secondary flex-grow-1" style="border-radius: 8px;">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px;">
                                        <i class="fas fa-save me-2"></i>Update
                                    </button>
                                </div>
                            </form>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Team List Table (70%) -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%);">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Team List</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: calc(100vh - 350px); overflow-y: auto; overflow-x: hidden;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="sticky-top table-light">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #6b7280;">User</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #6b7280;">Email</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #6b7280;">Role</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #6b7280;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 shadow-sm" 
                                                     style="width: 45px; height: 45px; font-weight: 600; font-size: 16px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold" style="color: #1f2937;">{{ $user->name }}</div>
                                                    <small class="text-muted" style="font-size: 0.75rem;">ID: {{ $user->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div style="color: #4b5563;">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($user->roles->count() > 0)
                                                @foreach($user->roles as $role)
                                                    <span class="badge me-1" style="
                                                        @if($role->name == 'Super Admin') background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;
                                                        @elseif($role->name == 'Admin') background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;
                                                        @elseif($role->name == 'Manager') background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;
                                                        @elseif($role->name == 'Staff') background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;
                                                        @else background: #6b7280; color: white;
                                                        @endif
                                                        padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                                    ">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="badge bg-secondary" style="padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px;">No Role</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex gap-2" role="group">
                                                <button type="button" 
                                                        @click="editUser({
                                                            id: {{ $user->id }},
                                                            name: '{{ addslashes($user->name) }}',
                                                            email: '{{ addslashes($user->email) }}',
                                                            current_role: '{{ $user->roles->first()?->name ?? '' }}'
                                                        })"
                                                        class="btn btn-sm btn-outline-primary" 
                                                        title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="fas fa-users fa-2x mb-3 d-block" style="color: #d1d5db;"></i>
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($users->hasPages())
                    <div class="card-footer bg-transparent border-top" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="d-flex justify-content-center">
                            {{ $users->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Success/Error Message -->
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
             class="position-fixed bottom-0 end-0 m-4 bg-danger text-white px-4 py-3 rounded shadow-lg" style="z-index: 1050; background: linear-gradient(45deg, #ef4444, #f87171) !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-times-circle me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
</x-app-layout>
