<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AURA - لوحة التحكم">
    <meta name="author" content="AURA">
    <title>@yield('title', 'لوحة التحكم') | AURA</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    
    <!-- Google Fonts - Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Custom Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/icons.css') }}">
    
    <!-- Themes CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/themes.css') }}">
    
    <!-- Dashboard Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    
    <!-- KYC Verification CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/kyc-verification.css') }}">
    
    <!-- Additional Styles -->
    @stack('styles')
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            @if(auth()->user()->user_type === 'admin')
                @include('layouts.partials.admin-sidebar')
            @else
                @include('layouts.partials.user-sidebar')
            @endif
        </aside>
        
        <!-- Main Content -->
        <div class="dashboard-main">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-auto d-lg-none">
                            <button class="btn btn-link sidebar-toggle p-0" type="button">
                                <i class="bi bi-list fs-4"></i>
                            </button>
                        </div>
                        
                        <div class="col">
                            <h1 class="dashboard-title">@yield('page-title', 'لوحة التحكم')</h1>
                        </div>
                        
                        <div class="col-auto">
                            <div class="d-flex align-items-center">
                                <!-- Notifications -->
                                <div class="dropdown me-3">
                                    <button class="btn btn-link position-relative p-0" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-bell fs-5"></i>
                                        @if(auth()->user()->unreadNotificationsCount() > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                {{ auth()->user()->unreadNotificationsCount() }}
                                            </span>
                                        @endif
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end notifications-dropdown" aria-labelledby="notificationsDropdown">
                                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">الإشعارات</h6>
                                            <a href="{{ route('user.notifications') }}" class="text-muted small">عرض الكل</a>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                        <div class="notifications-container">
                                            @if(auth()->user()->notifications->count() > 0)
                                                @foreach(auth()->user()->notifications->take(5) as $notification)
                                                    <a href="#" class="dropdown-item notification-item {{ $notification->read_at ? '' : 'unread' }}">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <div class="notification-icon bg-{{ $notification->data['type'] ?? 'primary' }}">
                                                                    <i class="bi bi-{{ $notification->data['icon'] ?? 'bell' }}"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-1">{{ $notification->data['title'] ?? 'إشعار جديد' }}</h6>
                                                                <p class="mb-1 small text-muted">{{ $notification->data['message'] ?? '' }}</p>
                                                                <span class="small text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            @else
                                                <div class="text-center py-3">
                                                    <i class="bi bi-bell-slash text-muted mb-2 d-block"></i>
                                                    <p class="mb-0 text-muted">لا توجد إشعارات</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- User Profile -->
                                <div class="dropdown">
                                    <button class="btn btn-link p-0 d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('assets/images/avatar-placeholder.jpg') }}" 
                                            alt="{{ auth()->user()->name }}" 
                                            class="rounded-circle me-2" 
                                            width="32" height="32">
                                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                                        <i class="bi bi-chevron-down ms-1 small"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <li><a class="dropdown-item" href="{{ route('user.profile') }}"><i class="bi bi-person me-2"></i> الملف الشخصي</a></li>
                                        <li><a class="dropdown-item" href="{{ route('user.security') }}"><i class="bi bi-shield-lock me-2"></i> الأمان</a></li>
                                        <li><a class="dropdown-item" href="{{ route('user.kyc') }}"><i class="bi bi-shield-check me-2"></i> التحقق من الهوية</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('logout') }}" 
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <i class="bi bi-box-arrow-right me-2"></i> تسجيل الخروج
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="dashboard-content">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <footer class="dashboard-footer">
                <div class="container-fluid">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <div class="text-center text-md-start mb-2 mb-md-0">
                            <p class="mb-0">&copy; {{ date('Y') }} AURA. جميع الحقوق محفوظة.</p>
                        </div>
                        <div class="text-center text-md-end">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item"><a href="#">سياسة الخصوصية</a></li>
                                <li class="list-inline-item"><a href="#">شروط الاستخدام</a></li>
                                <li class="list-inline-item"><a href="#">مركز المساعدة</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Modal de KYC requerido -->
    @if(auth()->user()->user_type !== 'admin' && auth()->user()->kyc_status !== 'approved')
        @include('dashboard.user.partials.kyc-required-modal')
    @endif
    
    <!-- Formulario de logout -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    
    <!-- ملفات JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/kyc-verification.js') }}"></script>
    
    <script>
        // Toggle sidebar on mobile
        document.querySelector('.sidebar-toggle')?.addEventListener('click', function() {
            document.querySelector('.dashboard-container').classList.toggle('sidebar-open');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const container = document.querySelector('.dashboard-container');
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            
            if (container.classList.contains('sidebar-open') && 
                !event.target.closest('.dashboard-sidebar') && 
                event.target !== sidebarToggle && 
                !sidebarToggle.contains(event.target)) {
                container.classList.remove('sidebar-open');
            }
        });
    </script>
    
    <!-- Scripts adicionales -->
    @stack('scripts')
</body>
</html>
