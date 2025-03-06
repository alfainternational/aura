<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AURA - النظام البيئي الرقمي المتكامل الذي يجمع بين المدفوعات الإلكترونية، التواصل الاجتماعي، التجارة الإلكترونية والخدمات اللوجستية">
    <meta name="keywords" content="AURA, محفظة إلكترونية, تجارة إلكترونية, مراسلة, توصيل, نظام بيئي رقمي">
    <meta name="author" content="AURA">
    <title>@yield('title', 'AURA - النظام البيئي الرقمي المتكامل')</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    
    <!-- Google Fonts - Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Custom Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/icons.css') }}">
    
    <!-- Themes CSS - Debe ir antes del style.css -->
    <link rel="stylesheet" href="{{ asset('assets/css/themes.css') }}">
    
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    
    <!-- Additional Styles -->
    @stack('styles')
</head>
<body>
    <!-- الشريط العلوي للموقع -->
    @include('components.header-new')
    
    <!-- القائمة الجانبية (مرئية فقط على الأجهزة المحمولة) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
        <div class="offcanvas-body p-0">
            @include('components.sidebar')
        </div>
    </div>
    
    <!-- المحتوى الرئيسي -->
    <main class="site-main">
        @yield('content')
    </main>
    
    <!-- تذييل الموقع -->
    @include('components.footer')
    
    <!-- زر للرجوع إلى الأعلى -->
    <button id="backToTop" class="btn btn-primary rounded-circle shadow-sm back-to-top" title="العودة إلى الأعلى">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- محدد النمط والألوان -->
    @include('components.theme-selector')
    
    <!-- ملفات JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/theme-switcher.js') }}"></script>
    
    <!-- مخصص للصفحات التي تحتاج إلى سكريبتات إضافية -->
    @stack('scripts')
    
    <!-- سكريبت لزر العودة إلى الأعلى -->
    <script>
        // إظهار وإخفاء زر العودة إلى الأعلى
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $('#backToTop').fadeIn();
            } else {
                $('#backToTop').fadeOut();
            }
        });
        
        // العودة إلى أعلى الصفحة عند النقر على الزر
        $('#backToTop').click(function() {
            $('html, body').animate({scrollTop: 0}, 800);
            return false;
        });
        
        // فتح السايدبار في الأجهزة المحمولة
        $(document).ready(function() {
            $('.navbar-toggler').click(function() {
                $('#sidebar').offcanvas('show');
            });
        });
    </script>
</body>
</html>
