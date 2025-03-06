<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق بخطوتين - منصة أورا</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        .two-factor-container {
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .two-factor-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .two-factor-header img {
            width: 100px;
            margin-bottom: 15px;
        }
        .two-factor-header h3 {
            color: #3c4b64;
            font-weight: 600;
        }
        .two-factor-form .form-control {
            height: 50px;
            border-radius: 8px;
            text-align: center;
            font-size: 24px;
            letter-spacing: 8px;
        }
        .two-factor-btn {
            height: 50px;
            border-radius: 8px;
            background-color: #4650dd;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
        }
        .two-factor-btn:hover {
            background-color: #3540c0;
            color: #fff;
        }
        .verification-icon {
            font-size: 60px;
            color: #4650dd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="two-factor-container">
            <div class="two-factor-header">
                <img src="/images/logo.png" alt="أورا">
                <h3>التحقق بخطوتين</h3>
            </div>
            
            <div class="text-center mb-4">
                <div class="verification-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <p>أدخل رمز التحقق المرسل إلى هاتفك</p>
            </div>
            
            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            
            <form class="two-factor-form" method="POST" action="{{ route('two-factor.verify') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="code" class="form-label">رمز التحقق</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" id="code" maxlength="6" required autofocus>
                    @error('code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn two-factor-btn">تحقق</button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none">تسجيل الخروج</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
