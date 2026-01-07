<x-app-layout>
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ \App\Models\User::count() }}</h3>
            <p class="text-sm text-gray-500">Total Users</p>
        </div>

        <!-- Total Roles Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-tag text-green-600 text-xl"></i>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ \Spatie\Permission\Models\Role::count() }}</h3>
            <p class="text-sm text-gray-500">Total Roles</p>
        </div>

        <!-- Revenue Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-red-600 text-xl"></i>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">$45,236</h3>
            <p class="text-sm text-gray-500">Total Revenue</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Activity Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Activity Overview</h3>
                <select class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:outline-none focus:ring-1 focus:ring-purple-500">
                    <option>Show by months</option>
                    <option>Show by weeks</option>
                    <option>Show by days</option>
                </select>
            </div>
            <div class="h-64 flex items-end justify-between space-x-2">
                <!-- Simple bar chart representation -->
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-green-500 rounded-t" style="height: 60%"></div>
                    <span class="text-xs text-gray-500 mt-2">Jan</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-red-500 rounded-t" style="height: 80%"></div>
                    <span class="text-xs text-gray-500 mt-2">Feb</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-green-500 rounded-t" style="height: 45%"></div>
                    <span class="text-xs text-gray-500 mt-2">Mar</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-red-500 rounded-t" style="height: 70%"></div>
                    <span class="text-xs text-gray-500 mt-2">Apr</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-green-500 rounded-t" style="height: 90%"></div>
                    <span class="text-xs text-gray-500 mt-2">May</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-red-500 rounded-t" style="height: 55%"></div>
                    <span class="text-xs text-gray-500 mt-2">Jun</span>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">System Status</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-600">System Online</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">100%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-600">Active Users</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">{{ \App\Models\User::count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-600">Total Roles</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">{{ \Spatie\Permission\Models\Role::count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Calendar -->
    <div class="bg-white mb-6" style="border: 1px solid #60a5fa; border-radius: 6px; padding: 16px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h5 fw-bold mb-0" style="color: #1f2937;">My Tasks Calendar</h3>
            <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-list me-2"></i>View All Tasks
            </a>
        </div>
        <div class="calendar-container" x-data="calendarView()" style="position: relative;">
            <!-- Calendar Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button @click="previousMonth()" class="btn btn-sm btn-link text-decoration-none p-1" style="color: #6b7280; border: none; background: none;">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h4 class="h6 fw-semibold mb-0" style="color: #1f2937;" x-text="monthYear"></h4>
                <button @click="nextMonth()" class="btn btn-sm btn-link text-decoration-none p-1" style="color: #6b7280; border: none; background: none;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <!-- Calendar Grid -->
            <div class="calendar-grid" style="border: 1px solid #e5e7eb; border-radius: 4px; overflow: hidden;">
                <!-- Day Headers -->
                <div class="row g-0" style="border-bottom: 1px solid #e5e7eb;">
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-semibold" style="color: #6b7280; font-size: 0.8125rem;">Sun</span>
                    </div>
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-semibold" style="color: #6b7280; font-size: 0.8125rem;">Mon</span>
                    </div>
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-semibold" style="color: #6b7280; font-size: 0.8125rem;">Tue</span>
                    </div>
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-semibold" style="color: #6b7280; font-size: 0.8125rem;">Wed</span>
                    </div>
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-semibold" style="color: #6b7280; font-size: 0.8125rem;">Thu</span>
                    </div>
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-semibold" style="color: #6b7280; font-size: 0.8125rem;">Fri</span>
                    </div>
                    <div class="col text-center py-2">
                        <span class="small fw-semibold" style="color: #6b7280; font-size: 0.8125rem;">Sat</span>
                    </div>
                </div>
                
                <!-- Calendar Days -->
                <div>
                    <template x-for="(week, weekIndex) in calendarWeeks" :key="weekIndex">
                        <div class="row g-0" style="border-bottom: 1px solid #e5e7eb;">
                            <template x-for="day in week" :key="day.date">
                                <div 
                                    class="col calendar-day-cell"
                                    :class="{
                                        'calendar-day-other-month': !day.isCurrentMonth,
                                        'calendar-day-today': day.isToday
                                    }">
                                    <!-- Date Number -->
                                    <div class="mb-2">
                                        <span 
                                            class="d-inline-block text-center"
                                            style="
                                                width: 28px;
                                                height: 28px;
                                                line-height: 28px;
                                                font-size: 0.875rem;
                                                font-weight: 500;
                                                border-radius: 4px;
                                            "
                                            :style="!day.isCurrentMonth ? 'color: #d1d5db; background-color: transparent;' : (day.isToday ? 'background-color: #9333ea; color: white; font-weight: 600;' : 'color: #2563eb; background-color: transparent;')"
                                            x-text="day.dayNumber">
                                        </span>
                                    </div>
                                    
                                    <!-- Tasks for this day -->
                                    <div style="display: flex; flex-direction: column; gap: 4px; margin-top: 2px;">
                                        <template x-for="task in day.tasks" :key="task.id">
                                            <div 
                                                class="calendar-task-item rounded px-2 py-1"
                                                style="
                                                    font-size: 0.75rem;
                                                    font-weight: 500;
                                                    cursor: pointer;
                                                    transition: opacity 0.2s;
                                                    color: #374151;
                                                    border-radius: 6px;
                                                    position: relative;
                                                "
                                                :style="`background-color: ${getTaskColor(task.priority, task.status)};`"
                                                x-bind:data-task-id="task.id"
                                                x-bind:data-task-title="task.title"
                                                x-bind:data-task-event-type="task.event_type"
                                                x-bind:data-task-due-date="task.due_date_formatted"
                                                x-bind:data-task-scheduled-time="task.scheduled_time || ''"
                                                x-bind:data-task-description="task.description || ''"
                                                onmouseenter="showTaskPopup(this, event)"
                                                onmouseleave="hideTaskPopup()">
                                                <span x-text="task.title"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        
        <!-- Task Detail Popup (Vanilla JS) -->
        <div id="task-detail-popup" class="task-detail-popup" style="display: none;"></div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recent Users</h3>
                <a href="{{ route('users.index') }}" class="text-sm text-red-600 hover:text-red-700">View All</a>
            </div>
            <div class="space-y-4">
                @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 last:border-0">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-red-600 font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->phone ?? '-' }}</p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-1 bg-red-100 text-red-600 rounded">
                        {{ $user->roles->first()?->name ?? 'No Role' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Monthly Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">This Month</h3>
            </div>
            <div class="text-center mb-4">
                <p class="text-4xl font-bold text-red-600 mb-1">{{ \App\Models\User::whereMonth('created_at', now()->month)->count() }}</p>
                <p class="text-sm text-gray-500">New Users</p>
            </div>
            <div class="h-32 flex items-end justify-between space-x-1">
                <div class="flex-1 bg-red-200 rounded-t" style="height: 40%"></div>
                <div class="flex-1 bg-red-300 rounded-t" style="height: 60%"></div>
                <div class="flex-1 bg-red-400 rounded-t" style="height: 50%"></div>
                <div class="flex-1 bg-red-500 rounded-t" style="height: 80%"></div>
                <div class="flex-1 bg-red-600 rounded-t" style="height: 100%"></div>
                <div class="flex-1 bg-red-500 rounded-t" style="height: 70%"></div>
            </div>
            <p class="text-xs text-gray-500 text-center mt-2">Last 6 days</p>
        </div>
    </div>

    <style>
        .calendar-day-cell {
            /* aspect-ratio: 1 / 1; */
            min-height: 80px;
            padding: 8px 6px;
            border-right: 1px solid #e5e7eb;
            position: relative;
            background-color: white;
        }
        
        .calendar-day-other-month {
            background-color: #fafafa !important;
        }
        
        .calendar-day-today {
            border: 2px solid #9333ea !important;
            background-color: white !important;
        }
        
        .task-detail-popup {
            position: fixed;
            z-index: 99999;
            width: 360px;
            background-color: #bbf7d0;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            pointer-events: auto;
        }
        
        [x-cloak] {
            display: none !important;
        }
        
        .task-detail-popup .popup-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        
        .task-detail-popup .popup-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #059669;
            margin: 0;
            flex: 1;
        }
        
        .task-detail-popup .popup-actions {
            display: flex;
            gap: 8px;
        }
        
        .task-detail-popup .popup-actions button {
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            font-size: 0.875rem;
            padding: 4px;
            transition: color 0.2s;
        }
        
        .task-detail-popup .popup-actions button:hover {
            color: #1f2937;
        }
        
        .task-detail-popup .popup-content {
            background-color: #86efac;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
        }
        
        .task-detail-popup .popup-detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .task-detail-popup .popup-detail-row:last-child {
            margin-bottom: 0;
        }
        
        .task-detail-popup .popup-detail-label {
            font-weight: 600;
            color: #065f46;
            min-width: 80px;
            font-size: 0.875rem;
        }
        
        .task-detail-popup .popup-detail-value {
            color: #064e3b;
            font-size: 0.875rem;
            flex: 1;
        }
        
        .task-detail-popup .popup-description {
            color: #064e3b;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(6, 78, 59, 0.2);
        }
    </style>

    <script>
        function calendarView() {
            return {
                currentDate: new Date({{ $selectedDate->year }}, {{ $selectedDate->month - 1 }}, 1),
                tasks: @json($tasksForJs),
                
                get monthYear() {
                    return this.currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                },
                
                get calendarDays() {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth();
                    
                    // First day of the month
                    const firstDay = new Date(year, month, 1);
                    const startingDayOfWeek = firstDay.getDay();
                    
                    // Last day of the month
                    const lastDay = new Date(year, month + 1, 0);
                    const daysInMonth = lastDay.getDate();
                    
                    // Previous month's days to fill the first week
                    const prevMonth = new Date(year, month, 0);
                    const daysInPrevMonth = prevMonth.getDate();
                    
                    const days = [];
                    
                    // Previous month's trailing days
                    for (let i = startingDayOfWeek - 1; i >= 0; i--) {
                        const day = daysInPrevMonth - i;
                        const prevMonthNum = month === 0 ? 12 : month;
                        const prevYear = month === 0 ? year - 1 : year;
                        days.push({
                            dayNumber: day,
                            date: `${prevYear}-${String(prevMonthNum).padStart(2, '0')}-${String(day).padStart(2, '0')}`,
                            isCurrentMonth: false,
                            isToday: false,
                            tasks: []
                        });
                    }
                    
                    // Current month's days
                    const today = new Date();
                    for (let day = 1; day <= daysInMonth; day++) {
                        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        const isToday = year === today.getFullYear() && 
                                       month === today.getMonth() && 
                                       day === today.getDate();
                        
                        // Find tasks for this date
                        const dayTasks = this.tasks.find(t => t.date === dateStr);
                        
                        days.push({
                            dayNumber: day,
                            date: dateStr,
                            isCurrentMonth: true,
                            isToday: isToday,
                            tasks: dayTasks ? dayTasks.tasks : []
                        });
                    }
                    
                    // Next month's days to fill the last week (to make 6 weeks)
                    const totalDays = days.length;
                    const remainingDays = 42 - totalDays; // 6 weeks * 7 days
                    const nextMonthNum = month === 11 ? 1 : month + 2;
                    const nextYear = month === 11 ? year + 1 : year;
                    for (let day = 1; day <= remainingDays; day++) {
                        days.push({
                            dayNumber: day,
                            date: `${nextYear}-${String(nextMonthNum).padStart(2, '0')}-${String(day).padStart(2, '0')}`,
                            isCurrentMonth: false,
                            isToday: false,
                            tasks: []
                        });
                    }
                    
                    return days;
                },
                
                get calendarWeeks() {
                    const days = this.calendarDays;
                    const weeks = [];
                    for (let i = 0; i < days.length; i += 7) {
                        weeks.push(days.slice(i, i + 7));
                    }
                    return weeks;
                },
                
                getTaskColor(priority, status) {
                    if (status === 'completed') {
                        return '#e5e7eb'; // Light gray for completed
                    }
                    
                    // Soft pastel colors exactly matching the reference image
                    // Light backgrounds with dark text
                    const colors = [
                        '#bfdbfe', // Light blue (like "Ring Ceremony" - date 11)
                        '#fecdd3', // Light pink (like anniversaries - date 15, 17)
                        '#fed7aa', // Light orange/peach (like birthdays - date 16)
                        '#c7d2fe', // Light purple (like "Marriage Meeting" - date 24)
                        '#bbf7d0', // Light green (like Gujarati text - date 18)
                        '#fbcfe8'  // Light pink/magenta (alternative)
                    ];
                    
                    // Use priority to determine color, cycling for variety
                    switch(priority) {
                        case 2: return colors[2]; // Light orange for high priority
                        case 1: return colors[1]; // Light pink for medium priority
                        default: return colors[0]; // Light blue for low priority
                    }
                },
                
                previousMonth() {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
                    this.loadTasksForMonth();
                },
                
                nextMonth() {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
                    this.loadTasksForMonth();
                },
                
                loadTasksForMonth() {
                    // Reload tasks for the new month
                    const year = this.currentDate.getFullYear();
                    const month = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                    window.location.href = `/dashboard?month=${year}-${month}`;
                }
            }
        }
    </script>

    <!-- Vanilla JavaScript for Task Popup -->
    <script>
        let taskPopupTimer = null;
        const taskPopup = document.getElementById('task-detail-popup');
        
        function showTaskPopup(element, event) {
            // Clear any existing timer
            if (taskPopupTimer) {
                clearTimeout(taskPopupTimer);
                taskPopupTimer = null;
            }
            
            // Get task data from data attributes
            const taskId = element.getAttribute('data-task-id');
            const taskTitle = element.getAttribute('data-task-title');
            const taskEventType = element.getAttribute('data-task-event-type');
            const taskDueDate = element.getAttribute('data-task-due-date');
            const taskScheduledTime = element.getAttribute('data-task-scheduled-time');
            const taskDescription = element.getAttribute('data-task-description');
            
            // Calculate popup position
            const rect = element.getBoundingClientRect();
            let popupX = rect.left + (rect.width / 2) - 180;
            let popupY = rect.bottom + 10;
            
            // Adjust if popup would go off screen
            if (popupX < 10) popupX = 10;
            if (popupX + 360 > window.innerWidth) popupX = window.innerWidth - 370;
            if (popupY + 200 > window.innerHeight) {
                popupY = rect.top - 200;
            }
            
            // Build popup HTML
            let reminderHtml = '';
            if (taskEventType === 'Meeting' && taskScheduledTime) {
                reminderHtml = `
                    <div class="popup-detail-row">
                        <span class="popup-detail-label">Reminder:</span>
                        <span class="popup-detail-value">1 hour before (${taskScheduledTime})</span>
                    </div>
                `;
            }
            
            let descriptionHtml = '';
            if (taskDescription) {
                descriptionHtml = `<div class="popup-description">${taskDescription}</div>`;
            }
            
            taskPopup.innerHTML = `
                <div>
                    <!-- Popup Header -->
                    <div class="popup-header">
                        <h4 class="popup-title">${taskTitle}</h4>
                        <div class="popup-actions">
                            <a href="{{ url('tasks') }}/${taskId}/edit" class="btn btn-sm btn-link p-0 text-decoration-none" title="Edit" style="color: #6b7280;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" onclick="deleteTask(${taskId})" class="btn btn-sm btn-link p-0 text-decoration-none" title="Delete" style="color: #6b7280;">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button onclick="hideTaskPopup()" class="btn btn-sm btn-link p-0 text-decoration-none" title="Close" style="color: #6b7280;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Popup Content -->
                    <div class="popup-content">
                        <div class="popup-detail-row">
                            <span class="popup-detail-label">Event Type:</span>
                            <span class="popup-detail-value">${taskEventType}</span>
                        </div>
                        <div class="popup-detail-row">
                            <span class="popup-detail-label">Date:</span>
                            <span class="popup-detail-value">${taskDueDate}</span>
                        </div>
                        ${reminderHtml}
                    </div>
                    
                    <!-- Description -->
                    ${descriptionHtml}
                </div>
            `;
            
            // Position and show popup
            taskPopup.style.left = popupX + 'px';
            taskPopup.style.top = popupY + 'px';
            taskPopup.style.display = 'block';
            
            // Keep popup visible when hovering over it
            taskPopup.onmouseenter = function() {
                if (taskPopupTimer) {
                    clearTimeout(taskPopupTimer);
                    taskPopupTimer = null;
                }
            };
            
            taskPopup.onmouseleave = function() {
                hideTaskPopup();
            };
        }
        
        function hideTaskPopup() {
            taskPopupTimer = setTimeout(() => {
                if (taskPopup) {
                    taskPopup.style.display = 'none';
                }
            }, 200);
        }
        
        function deleteTask(taskId) {
            if (confirm('Are you sure you want to delete this task?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('tasks') }}/${taskId}`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                form.innerHTML = `
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="${csrfToken}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <!-- Vanilla JavaScript for Task Popup -->
    <script>
        let taskPopupTimer = null;
        let taskPopup = null;
        
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            taskPopup = document.getElementById('task-detail-popup');
        });
        
        function showTaskPopup(element, event) {
            if (!taskPopup) {
                taskPopup = document.getElementById('task-detail-popup');
            }
            
            // Clear any existing timer
            if (taskPopupTimer) {
                clearTimeout(taskPopupTimer);
                taskPopupTimer = null;
            }
            
            // Get task data from data attributes
            const taskId = element.getAttribute('data-task-id');
            const taskTitle = element.getAttribute('data-task-title');
            const taskEventType = element.getAttribute('data-task-event-type');
            const taskDueDate = element.getAttribute('data-task-due-date');
            const taskScheduledTime = element.getAttribute('data-task-scheduled-time');
            const taskDescription = element.getAttribute('data-task-description');
            
            // Calculate popup position
            const rect = element.getBoundingClientRect();
            let popupX = rect.left + (rect.width / 2) - 180;
            let popupY = rect.bottom + 10;
            
            // Adjust if popup would go off screen
            if (popupX < 10) popupX = 10;
            if (popupX + 360 > window.innerWidth) popupX = window.innerWidth - 370;
            if (popupY + 200 > window.innerHeight) {
                popupY = rect.top - 200;
            }
            
            // Build popup HTML
            let reminderHtml = '';
            if (taskEventType === 'Meeting' && taskScheduledTime) {
                reminderHtml = `
                    <div class="popup-detail-row">
                        <span class="popup-detail-label">Reminder:</span>
                        <span class="popup-detail-value">1 hour before (${taskScheduledTime})</span>
                    </div>
                `;
            }
            
            let descriptionHtml = '';
            if (taskDescription) {
                descriptionHtml = `<div class="popup-description">${escapeHtml(taskDescription)}</div>`;
            }
            
            taskPopup.innerHTML = `
                <div>
                    <!-- Popup Header -->
                    <div class="popup-header">
                        <h4 class="popup-title">${escapeHtml(taskTitle)}</h4>
                        <div class="popup-actions">
                            <a href="{{ url('tasks') }}/${taskId}/edit" class="btn btn-sm btn-link p-0 text-decoration-none" title="Edit" style="color: #6b7280;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" onclick="deleteTaskPopup(${taskId})" class="btn btn-sm btn-link p-0 text-decoration-none" title="Delete" style="color: #6b7280;">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button onclick="hideTaskPopup()" class="btn btn-sm btn-link p-0 text-decoration-none" title="Close" style="color: #6b7280;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Popup Content -->
                    <div class="popup-content">
                        <div class="popup-detail-row">
                            <span class="popup-detail-label">Event Type:</span>
                            <span class="popup-detail-value">${escapeHtml(taskEventType)}</span>
                        </div>
                        <div class="popup-detail-row">
                            <span class="popup-detail-label">Date:</span>
                            <span class="popup-detail-value">${escapeHtml(taskDueDate)}</span>
                        </div>
                        ${reminderHtml}
                    </div>
                    
                    <!-- Description -->
                    ${descriptionHtml}
                </div>
            `;
            
            // Position and show popup
            taskPopup.style.left = popupX + 'px';
            taskPopup.style.top = popupY + 'px';
            taskPopup.style.display = 'block';
            
            // Keep popup visible when hovering over it
            taskPopup.onmouseenter = function() {
                if (taskPopupTimer) {
                    clearTimeout(taskPopupTimer);
                    taskPopupTimer = null;
                }
            };
            
            taskPopup.onmouseleave = function() {
                hideTaskPopup();
            };
        }
        
        function hideTaskPopup() {
            taskPopupTimer = setTimeout(() => {
                if (taskPopup) {
                    taskPopup.style.display = 'none';
                }
            }, 200);
        }
        
        function deleteTaskPopup(taskId) {
            if (confirm('Are you sure you want to delete this task?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('tasks') }}/${taskId}`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                form.innerHTML = `
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="${csrfToken}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</x-app-layout>



