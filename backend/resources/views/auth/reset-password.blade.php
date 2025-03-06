<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور - منصة أورا</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        .password-reset-container {
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .password-reset-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .password-reset-header img {
            width: 100px;
            margin-bottom: 15px;
        }
        .password-reset-header h3 {
            color: #3c4b64;
            font-weight: 600;
        }
        .password-reset-form .form-control {
            height: 50px;
            border-radius: 8px;
        }
        .reset-btn {
            height: 50px;
            border-radius: 8px;
            background-color: #4650dd;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
        }
        .reset-btn:hover {
            background-color: #3540c0;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="password-reset-container">
            <div class="password-reset-header">
                <img src="/images/logo.png" alt="أورا">
                <h3>إعادة تعيين كلمة المرور</h3>
                <p class="text-muted">أدخل كلمة المرور الجديدة للمتابعة</p>
            </div>
            
            <form class="password-reset-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                
                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور الجديدة</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required autofocus>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn reset-btn">إعادة تعيين كلمة المرور</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
