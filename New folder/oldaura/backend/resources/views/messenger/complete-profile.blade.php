@extends('layouts.app')

@section('title', 'استكمال بيانات المندوب')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">استكمال بيانات المندوب</h4>
                </div>
                
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ملاحظة هامة:</strong> يرجى ملء جميع المعلومات المطلوبة بدقة لضمان الموافقة على طلب التسجيل كمندوب
                    </div>
                    
                    <form method="POST" action="{{ route('messenger.store-profile') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 text-primary"><i class="fas fa-id-card me-2"></i> معلومات الهوية</h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="national_id" class="form-label">رقم الهوية الوطنية <span class="text-danger">*</span></label>
                                <input type="text" id="national_id" name="national_id" class="form-control @error('national_id') is-invalid @enderror" value="{{ old('national_id') }}" required>
                                @error('national_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="id_document" class="form-label">صورة الهوية <span class="text-danger">*</span></label>
                                <input type="file" id="id_document" name="id_document" class="form-control @error('id_document') is-invalid @enderror" required>
                                <div class="form-text">يرجى إرفاق نسخة واضحة من الهوية الوطنية (JPG, PNG, PDF فقط)</div>
                                @error('id_document')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 text-primary"><i class="fas fa-id-badge me-2"></i> معلومات الرخصة</h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="driving_license" class="form-label">رقم رخصة القيادة <span class="text-danger">*</span></label>
                                <input type="text" id="driving_license" name="driving_license" class="form-control @error('driving_license') is-invalid @enderror" value="{{ old('driving_license') }}" required>
                                @error('driving_license')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="license_document" class="form-label">صورة الرخصة <span class="text-danger">*</span></label>
                                <input type="file" id="license_document" name="license_document" class="form-control @error('license_document') is-invalid @enderror" required>
                                <div class="form-text">يرجى إرفاق نسخة واضحة من رخصة القيادة (JPG, PNG, PDF فقط)</div>
                                @error('license_document')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 text-primary"><i class="fas fa-car me-2"></i> معلومات المركبة</h5>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="vehicle_type" class="form-label">نوع المركبة <span class="text-danger">*</span></label>
                                <select id="vehicle_type" name="vehicle_type" class="form-select @error('vehicle_type') is-invalid @enderror" required>
                                    <option value="" selected disabled>اختر نوع المركبة</option>
                                    <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>سيارة</option>
                                    <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>دراجة نارية</option>
                                    <option value="bicycle" {{ old('vehicle_type') == 'bicycle' ? 'selected' : '' }}>دراجة هوائية</option>
                                    <option value="van" {{ old('vehicle_type') == 'van' ? 'selected' : '' }}>شاحنة صغيرة</option>
                                </select>
                                @error('vehicle_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="vehicle_model" class="form-label">موديل المركبة <span class="text-danger">*</span></label>
                                <input type="text" id="vehicle_model" name="vehicle_model" class="form-control @error('vehicle_model') is-invalid @enderror" value="{{ old('vehicle_model') }}" required>
                                @error('vehicle_model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="vehicle_year" class="form-label">سنة الصنع <span class="text-danger">*</span></label>
                                <input type="number" id="vehicle_year" name="vehicle_year" class="form-control @error('vehicle_year') is-invalid @enderror" value="{{ old('vehicle_year') }}" min="1990" max="{{ date('Y') + 1 }}" required>
                                @error('vehicle_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="vehicle_color" class="form-label">لون المركبة <span class="text-danger">*</span></label>
                                <input type="text" id="vehicle_color" name="vehicle_color" class="form-control @error('vehicle_color') is-invalid @enderror" value="{{ old('vehicle_color') }}" required>
                                @error('vehicle_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="plate_number" class="form-label">رقم اللوحة <span class="text-danger">*</span></label>
                                <input type="text" id="plate_number" name="plate_number" class="form-control @error('plate_number') is-invalid @enderror" value="{{ old('plate_number') }}" required>
                                @error('plate_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="vehicle_image" class="form-label">صورة المركبة <span class="text-danger">*</span></label>
                                <input type="file" id="vehicle_image" name="vehicle_image" class="form-control @error('vehicle_image') is-invalid @enderror" required>
                                @error('vehicle_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 text-primary"><i class="fas fa-map-marker-alt me-2"></i> معلومات العمل</h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="zone_id" class="form-label">منطقة العمل</label>
                                <select id="zone_id" name="zone_id" class="form-select @error('zone_id') is-invalid @enderror">
                                    <option value="" selected>اختر منطقة العمل (اختياري)</option>
                                    @foreach(\App\Models\Zone::all() as $zone)
                                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">يمكنك اختيار منطقة العمل الآن أو يمكن تعيينها لاحقًا بواسطة المشرف</div>
                                @error('zone_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="delivery_preference" class="form-label">تفضيلات التوصيل <span class="text-danger">*</span></label>
                                <select id="delivery_preference" name="delivery_preference" class="form-select @error('delivery_preference') is-invalid @enderror" required>
                                    <option value="" selected disabled>اختر تفضيلاتك للتوصيل</option>
                                    <option value="food" {{ old('delivery_preference') == 'food' ? 'selected' : '' }}>طعام</option>
                                    <option value="goods" {{ old('delivery_preference') == 'goods' ? 'selected' : '' }}>بضائع</option>
                                    <option value="both" {{ old('delivery_preference') == 'both' ? 'selected' : '' }}>كلاهما</option>
                                </select>
                                @error('delivery_preference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="work_hours" class="form-label">ساعات العمل المفضلة <span class="text-danger">*</span></label>
                                <input type="text" id="work_hours" name="work_hours" class="form-control @error('work_hours') is-invalid @enderror" value="{{ old('work_hours') }}" placeholder="مثال: 9 صباحاً - 5 مساءً، أيام الأسبوع" required>
                                @error('work_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">المدينة <span class="text-danger">*</span></label>
                                <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input @error('agreement') is-invalid @enderror" id="agreement" name="agreement" required>
                            <label class="form-check-label" for="agreement">أوافق على <a href="#" target="_blank">شروط وأحكام العمل كمندوب</a></label>
                            @error('agreement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>تنبيه:</strong> سيتم مراجعة البيانات المقدمة من قبل فريق إدارة المنصة، وقد يستغرق ذلك من 24 إلى 48 ساعة عمل.
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">إرسال الطلب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
