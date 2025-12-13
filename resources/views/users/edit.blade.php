<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Edit Team Member</h1>
            <p class="text-muted mb-0">Update team member information</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i>Back to Team
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-12 mx-auto">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas fa-user-edit text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Edit Team Member: {{ $user->name }}</h2>
                    </div>
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color: #374151;">Name</label>
                            <input type="text" name="name" required
                                   value="{{ old('name', $user->name) }}"
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
                                   value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="Enter email address"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color: #374151;">Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Enter new password (optional)"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                    class="form-select @error('role') is-invalid @enderror"
                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" 
                                        {{ (old('role', $user->roles->first()?->name) == $role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" style="border-radius: 8px;">
                                <i class="fas fa-save me-2"></i>Update Team Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Message -->
    @if(session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 bg-success text-white px-4 py-3 rounded shadow-lg" style="z-index: 1050; background: linear-gradient(45deg, #22c55e, #4ade80) !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
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
