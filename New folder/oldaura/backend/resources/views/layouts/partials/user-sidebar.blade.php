<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="app-brand">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Aura" class="brand-logo" width="120">
            </a>
        </div>
    </div>

    <div class="sidebar-user">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('assets/images/avatar-placeholder.jpg') }}" 
                    alt="{{ auth()->user()->name }}" 
                    class="user-avatar rounded-circle" 
                    width="40" height="40">
                
                @if(auth()->user()->isKycVerified())
                    <span class="verified-badge" title="تم التحقق من الهوية">
                        <i class="bi bi-patch-check-fill text-primary"></i>
                    </span>
                @endif
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                <span class="user-role text-muted">{{ __('user.role') }}</span>
            </div>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <a href="{{ route('user.dashboard') }}" class="nav-link">
                <i class="bi bi-house-door nav-icon"></i>
                <span class="nav-text">لوحة التحكم</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('user.profile') ? 'active' : '' }}">
            <a href="{{ route('user.profile') }}" class="nav-link">
                <i class="bi bi-person nav-icon"></i>
                <span class="nav-text">الملف الشخصي</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('user.notifications') ? 'active' : '' }}">
            <a href="{{ route('user.notifications') }}" class="nav-link">
                <i class="bi bi-bell nav-icon"></i>
                <span class="nav-text">الإشعارات</span>
                @if(auth()->user()->unreadNotificationsCount() > 0)
                    <span class="badge bg-danger rounded-pill ms-auto">{{ auth()->user()->unreadNotificationsCount() }}</span>
                @endif
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('user.kyc') ? 'active' : '' }}">
            <a href="{{ route('user.kyc') }}" class="nav-link">
                <i class="bi bi-shield-check nav-icon"></i>
                <span class="nav-text">التحقق من الهوية</span>
                @if(auth()->user()->kyc_status !== 'approved')
                    <span class="ms-auto">
                        @if(auth()->user()->kyc_status === 'pending')
                            <i class="bi bi-hourglass-split text-warning"></i>
                        @elseif(auth()->user()->kyc_status === 'rejected')
                            <i class="bi bi-x-circle text-danger"></i>
                        @else
                            <i class="bi bi-exclamation-circle text-muted"></i>
                        @endif
                    </span>
                @endif
            </a>
        </li>

        <!-- القسم الذي يتطلب التحقق من الهوية -->
        @if(auth()->user()->isKycVerified())
        <li class="nav-section">
            <span class="nav-section-title">الخدمات</span>
        </li>

        <li class="nav-item {{ request()->routeIs('user.statistics') ? 'active' : '' }}">
            <a href="{{ route('user.statistics') }}" class="nav-link">
                <i class="bi bi-graph-up nav-icon"></i>
                <span class="nav-text">الإحصائيات</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('user.settings*') ? 'active' : '' }}">
            <a href="{{ route('user.settings') }}" class="nav-link">
                <i class="bi bi-gear nav-icon"></i>
                <span class="nav-text">الإعدادات</span>
            </a>
        </li>
        @endif

        <li class="nav-section">
            <span class="nav-section-title">الأمان</span>
        </li>

        <li class="nav-item {{ request()->routeIs('user.security') ? 'active' : '' }}">
            <a href="{{ route('user.security') }}" class="nav-link">
                <i class="bi bi-lock nav-icon"></i>
                <span class="nav-text">الأمان</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('logout') }}" class="nav-link" 
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right nav-icon"></i>
                <span class="nav-text">تسجيل الخروج</span>
            </a>
        </li>
    </ul>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<style>
.verified-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    background-color: white;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
