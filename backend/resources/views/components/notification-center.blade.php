<div class="dropdown">
    <button class="btn btn-link position-relative text-decoration-none" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-bell fs-5"></i>
        @if(isset($count) && $count > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $count > 99 ? '99+' : $count }}
                <span class="visually-hidden">إشعارات غير مقروءة</span>
            </span>
        @endif
    </button>
    
    <div class="dropdown-menu dropdown-menu-end p-0 shadow-sm" aria-labelledby="notificationDropdown" style="width: 300px; max-height: 500px; overflow-y: auto;">
        <div class="p-2 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0">الإشعارات</h6>
            @if(isset($markAllAsReadUrl))
                <a href="{{ $markAllAsReadUrl }}" class="text-decoration-none small">تعيين الكل كمقروء</a>
            @endif
        </div>
        
        <div class="notifications-list">
            @if(isset($empty) && $empty)
                <div class="text-center p-3 text-muted">
                    <i class="bi bi-bell-slash fs-4 d-block mb-2"></i>
                    <p class="m-0">لا توجد إشعارات جديدة</p>
                </div>
            @else
                {{ $slot }}
            @endif
        </div>
        
        @if(isset($footerUrl))
            <div class="p-2 bg-light border-top text-center">
                <a href="{{ $footerUrl }}" class="text-decoration-none small">عرض كل الإشعارات</a>
            </div>
        @endif
    </div>
</div>
