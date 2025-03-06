@extends('landing')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-right">
            <h1 class="display-4 mb-4">اتصل بنا</h1>
            
            <div class="contact-content">
                <p class="lead text-muted mb-4">
                    نحن هنا للمساعدة. يسعدنا استقبال استفساراتكم واقتراحاتكم
                </p>
                
                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name">الاسم الكامل</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="email">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="message">رسالتك</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">إرسال الرسالة</button>
                </form>
                
                <div class="supported-regions mt-5">
                    <h4 class="mb-3">المناطق المدعومة</h4>
                    <div class="region-badges">
                        @foreach($supportedCountries as $country)
                            <span class="badge badge-secondary mx-1">{{ $country }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
