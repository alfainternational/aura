<div class="dropdown">
    <a class="nav-link position-relative" href="#" role="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        @if(auth()->user()->unreadNotificationsCount() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notifications-badge">
                {{ auth()->user()->unreadNotificationsCount() > 99 ? '99+' : auth()->user()->unreadNotificationsCount() }}
                <span class="visually-hidden">إشعارات غير مقروءة</span>
            </span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end notifications-dropdown" aria-labelledby="notificationsDropdown">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
            <span>الإشعارات</span>
            @if(auth()->user()->unreadNotificationsCount() > 0)
                <a href="{{ route('notifications.mark-all-read') }}" class="text-decoration-none" onclick="event.preventDefault(); markAllNotificationsAsRead();">
                    <small>تحديد الكل كمقروء</small>
                </a>
            @endif
        </div>
        <div class="notifications-container">
            @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                <a href="{{ $notification->action_url ?? route('notifications.index') }}" class="dropdown-item notification-item {{ $notification->read_at ? '' : 'unread' }}" onclick="{{ $notification->read_at ? '' : 'event.preventDefault(); markNotificationAsRead(' . $notification->id . ', \'' . ($notification->action_url ?? route('notifications.index')) . '\');' }}">
                    <div class="d-flex align-items-center">
                        <div class="notification-icon {{ $notification->type }} me-3">
                            <i class="fas fa-{{ $notification->icon ?? 'bell' }}"></i>
                        </div>
                        <div>
                            <div class="small fw-bold">{{ $notification->title }}</div>
                            <div class="text-truncate small" style="max-width: 200px;">{{ $notification->message }}</div>
                            <div class="small text-muted">{{ $notification->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="dropdown-item text-center py-3">
                    <span class="text-muted">لا توجد إشعارات</span>
                </div>
            @endforelse
        </div>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
            عرض جميع الإشعارات
        </a>
    </div>
</div>

@push('styles')
<style>
    .notifications-dropdown {
        width: 320px;
        max-height: 400px;
        padding: 0;
    }
    .notifications-container {
        max-height: 300px;
        overflow-y: auto;
    }
    .dropdown-header {
        padding: 0.5rem 1rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .notification-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f1f1;
        white-space: normal;
    }
    .notification-item:last-child {
        border-bottom: none;
    }
    .notification-item.unread {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    .notification-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0.875rem;
    }
    .notification-icon.info {
        background-color: rgba(var(--bs-info-rgb), 0.1);
        color: var(--bs-info);
    }
    .notification-icon.success {
        background-color: rgba(var(--bs-success-rgb), 0.1);
        color: var(--bs-success);
    }
    .notification-icon.warning {
        background-color: rgba(var(--bs-warning-rgb), 0.1);
        color: var(--bs-warning);
    }
    .notification-icon.danger {
        background-color: rgba(var(--bs-danger-rgb), 0.1);
        color: var(--bs-danger);
    }
</style>
@endpush

@push('scripts')
<script>
    function markNotificationAsRead(id, redirectUrl) {
        fetch('{{ route('notifications.mark-read', '') }}/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = redirectUrl;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function markAllNotificationsAsRead() {
        fetch('{{ route('notifications.mark-all-read') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar la interfaz de usuario
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });
                
                // Ocultar el contador de notificaciones
                const badge = document.querySelector('.notifications-badge');
                if (badge) {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Actualizar el contador de notificaciones cada minuto
    setInterval(function() {
        fetch('{{ route('notifications.unread-count') }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.notifications-badge');
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.style.display = 'block';
                } else {
                    // Crear el badge si no existe
                    const bell = document.querySelector('#notificationsDropdown');
                    if (bell) {
                        const newBadge = document.createElement('span');
                        newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notifications-badge';
                        newBadge.textContent = data.count > 99 ? '99+' : data.count;
                        const span = document.createElement('span');
                        span.className = 'visually-hidden';
                        span.textContent = 'إشعارات غير مقروءة';
                        newBadge.appendChild(span);
                        bell.appendChild(newBadge);
                    }
                }
            } else if (badge) {
                badge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }, 60000); // Cada minuto
</script>
@endpush
