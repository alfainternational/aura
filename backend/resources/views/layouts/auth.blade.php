<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - منصة أورا</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        .login-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header img {
            width: 100px;
            margin-bottom: 15px;
        }
        .login-header h3 {
            color: #3c4b64;
            font-weight: 600;
        }
        .login-form .form-control {
            height: 50px;
            border-radius: 8px;
        }
        .login-btn {
            height: 50px;
            border-radius: 8px;
            background-color: #4650dd;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
        }
        .login-btn:hover {
            background-color: #3540c0;
            color: #fff;
        }
        .admin-login-container {
            background-color: #3c4b64;
            color: white;
        }
        .admin-login-container h3 {
            color: white;
        }
        .admin-login-container .form-control {
            background-color: rgba(255, 255, 255, 0.9);
        }
        .login-tabs {
            margin-bottom: 20px;
        }
        .login-tabs .nav-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .login-tabs .nav-link {
            color: rgba(255, 255, 255, 0.7);
            border: none;
            padding: 10px 15px;
            border-radius: 0;
            margin-right: 5px;
            font-weight: 500;
        }
        .login-tabs .nav-link:hover {
            color: #fff;
            border-color: transparent;
        }
        .login-tabs .nav-link.active {
            color: #fff;
            background-color: transparent;
            border-bottom: 2px solid #4650dd;
        }
        .biometric-icon {
            font-size: 48px;
            color: #4650dd;
            margin-bottom: 15px;
        }
        .biometric-btn {
            height: 50px;
            border-radius: 8px;
            background-color: #28a745;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
        }
        .biometric-btn:hover {
            background-color: #218838;
            color: #fff;
        }
        .toggle-password {
            background-color: rgba(255, 255, 255, 0.9);
            border-color: #ced4da;
        }
        .toggle-password:hover {
            background-color: rgba(255, 255, 255, 1);
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
