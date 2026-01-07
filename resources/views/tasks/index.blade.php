<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">My Tasks</h1>
            <p class="text-muted mb-0">Manage your tasks and reminders</p>
        </div>
    </div>

    <!-- Split Layout: 30% Form, 70% Table -->
    <div class="row g-4" x-data="{ 
        editingTask: null, 
        isEditing: false,
        editTask(task) {
            this.editingTask = task;
            this.isEditing = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editingTask = null;
            this.isEditing = false;
        }
    }">
        <!-- Left Side: Add/Edit Task Form (30%) -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-tasks'"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Task' : 'Add Task'"></h2>
                    </div>
                    
                    <!-- Add Form -->
                    <div x-show="!isEditing">
                        <form action="{{ route('tasks.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Title</label>
                                <input type="text" name="title" required
                                       value="{{ old('title') }}"
                                       class="form-control @error('title') is-invalid @enderror"
                                       placeholder="Enter task title"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Description</label>
                                <textarea name="description" rows="3"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Enter task description (optional)"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Status</label>
                                <select name="status" required
                                        class="form-select @error('status') is-invalid @enderror"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #374151;">Priority</label>
                                <select name="priority" required
                                        class="form-select @error('priority') is-invalid @enderror"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <option value="0" {{ old('priority', '0') == '0' ? 'selected' : '' }}>Low</option>
                                    <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>Medium</option>
                                    <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Due Date</label>
                                <input type="date" name="due_date"
                                       value="{{ old('due_date') }}"
                                       class="form-control @error('due_date') is-invalid @enderror"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="fas fa-plus me-2"></i>Add Task
                            </button>
                        </form>
                    </div>

                    <!-- Edit Form -->
                    <div x-show="isEditing" x-cloak>
                        <template x-if="editingTask">
                            <form :action="`{{ url('tasks') }}/${editingTask.id}`" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="color: #374151;">Title</label>
                                    <input type="text" name="title" required
                                           x-model="editingTask.title"
                                           class="form-control"
                                           placeholder="Enter task title"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="color: #374151;">Description</label>
                                    <textarea name="description" rows="3"
                                              x-model="editingTask.description"
                                              class="form-control"
                                              placeholder="Enter task description (optional)"
                                              style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="color: #374151;">Status</label>
                                    <select name="status" required
                                            x-model="editingTask.status"
                                            class="form-select"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="color: #374151;">Priority</label>
                                    <select name="priority" required
                                            x-model="editingTask.priority"
                                            class="form-select"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="0">Low</option>
                                        <option value="1">Medium</option>
                                        <option value="2">High</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Due Date</label>
                                    <input type="date" name="due_date"
                                           :value="editingTask.due_date"
                                           class="form-control"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" @click="cancelEdit()" class="btn btn-outline-secondary flex-grow-1">
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

        <!-- Right Side: Task List Table (70%) -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-list-check text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Task List</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: calc(100vh - 350px); overflow-y: auto; overflow-x: hidden;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="sticky-top table-light">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #6b7280;">Task</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #6b7280;">Status</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #6b7280;">Priority</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #6b7280;">Due Date</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #6b7280;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 shadow-sm" 
                                                     style="width: 45px; height: 45px; font-weight: 600; font-size: 16px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                                    {{ strtoupper(substr($task->title, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold" style="color: #1f2937;">{{ $task->title }}</div>
                                                    @if($task->description)
                                                        <small class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($task->description, 40) }}</small>
                                                    @else
                                                        <small class="text-muted" style="font-size: 0.75rem;">ID: {{ $task->id }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($task->status === 'completed')
                                                <span class="badge me-1" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                    Completed
                                                </span>
                                            @elseif($task->status === 'in_progress')
                                                <span class="badge me-1" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                    In Progress
                                                </span>
                                            @else
                                                <span class="badge me-1" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($task->priority == 2)
                                                <span class="badge me-1" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                    High
                                                </span>
                                            @elseif($task->priority == 1)
                                                <span class="badge me-1" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                    Medium
                                                </span>
                                            @else
                                                <span class="badge me-1" style="background: #6b7280; color: white; padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                    Low
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($task->due_date)
                                                <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger fw-semibold' : '' }}" style="color: #4b5563;">
                                                    {{ $task->due_date->format('M d, Y') }}
                                                </span>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex gap-2" role="group">
                                                <button type="button" 
                                                        @click="editTask({
                                                            id: {{ $task->id }},
                                                            title: '{{ addslashes($task->title) }}',
                                                            description: '{{ addslashes($task->description ?? '') }}',
                                                            status: '{{ $task->status }}',
                                                            priority: {{ $task->priority }},
                                                            due_date: '{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}'
                                                        })"
                                                        class="btn btn-sm btn-outline-primary" 
                                                        title="Edit Task">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Task">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="fas fa-tasks fa-2x mb-3 d-block" style="color: #d1d5db;"></i>
                                            No tasks found. Create your first task to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($tasks->hasPages())
                    <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex justify-content-center">
                            {{ $tasks->links() }}
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
