@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">{{ __('اتصل بنا') }}</h3>
                </div>
                <div class="card-body">
                    <p class="lead text-center mb-4">{{ __('نحن هنا للإجابة على جميع استفساراتك. يرجى ملء النموذج أدناه وسنرد عليك في أقرب وقت ممكن.') }}</p>
                    
                    <form action="#" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="name">{{ __('الاسم الكامل') }}</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="email">{{ __('البريد الإلكتروني') }}</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="phone">{{ __('رقم الهاتف') }}</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="subject">{{ __('الموضوع') }}</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="message">{{ __('الرسالة') }}</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('إرسال') }}</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                            <h5>{{ __('العنوان') }}</h5>
                            <p>{{ __('الخرطوم، السودان') }}<br>{{ __('شارع النيل، مبنى 123') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-phone-alt fa-3x text-primary mb-3"></i>
                            <h5>{{ __('الهاتف') }}</h5>
                            <p>{{ __('+249 123 456789') }}<br>{{ __('+249 987 654321') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                            <h5>{{ __('البريد الإلكتروني') }}</h5>
                            <p>info@aura.com<br>support@aura.com</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <h4 class="text-center mb-4">{{ __('موقعنا') }}</h4>
                <div class="embed-responsive embed-responsive-16by9" style="height: 400px;">
                    <iframe class="embed-responsive-item w-100 h-100" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d245662.89357611965!2d32.40859408117463!3d15.59343670000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x168e8f69bdd7fd49%3A0x71c9c95f1f33b2b8!2sKhartoum%2C%20Sudan!5e0!3m2!1sen!2sus!4v1646267784239!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
