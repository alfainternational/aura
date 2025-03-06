<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحقق من البريد الإلكتروني - منصة أورا</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        .verify-container {
            max-width: 600px;
            margin: 80px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .verify-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .verify-header img {
            width: 100px;
            margin-bottom: 15px;
        }
        .verify-header h3 {
            color: #3c4b64;
            font-weight: 600;
        }
        .verify-icon {
            font-size: 60px;
            color: #4650dd;
            margin-bottom: 20px;
        }
        .verify-btn {
            height: 50px;
            border-radius: 8px;
            background-color: #4650dd;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
        }
        .verify-btn:hover {
            background-color: #3540c0;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verify-container">
            <div class="verify-header">
                <img src="/images/logo.png" alt="أورا">
                <h3>تحقق من بريدك الإلكتروني</h3>
            </div>
            
            <div class="text-center mb-4">
                <div class="verify-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <p class="lead">شكراً لتسجيلك في منصة أورا!</p>
                <p>قبل أن تبدأ، يرجى التحقق من عنوان بريدك الإلكتروني عن طريق النقر على الرابط الذي أرسلناه إلى بريدك الإلكتروني.</p>
                <p>إذا لم تستلم البريد الإلكتروني، فسنكون سعداء بإرسال رابط آخر إليك.</p>
            </div>
            
            @if(session('status') == 'verification-link-sent')
                <div class="alert alert-success text-center">
                    تم إرسال رابط تحقق جديد إلى عنوان البريد الإلكتروني المسجل لديك.
                </div>
            @endif
            
            <div class="d-flex justify-content-between">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn verify-btn">إعادة إرسال البريد</button>
                </form>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">تسجيل الخروج</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
