@extends('layouts.app')

@section('title', 'لوحة التحكم - أورا')

@section('content')
<div class="home-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-md-3 sidebar">
                <div class="user-profile text-center mb-4">
                    <img src="{{ $user->profile_picture ?? asset('images/default-avatar.png') }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle avatar-lg mb-3">
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->user_type }}</p>
                </div>

                <nav class="nav flex-column dashboard-nav">
                    <a class="nav-link active" href="{{ route('messaging.index') }}">
                        <i class="fas fa-comments"></i> الرسائل
                        @if($unreadMessages->count() > 0)
                            <span class="badge badge-danger">{{ $unreadMessages->count() }}</span>
                        @endif
                    </a>
                    <a class="nav-link" href="{{ route('calls.index') }}">
                        <i class="fas fa-phone"></i> المكالمات
                        @if($pendingCalls->count() > 0)
                            <span class="badge badge-warning">{{ $pendingCalls->count() }}</span>
                        @endif
                    </a>
                    <a class="nav-link" href="{{ route('profile.index') }}">
                        <i class="fas fa-user"></i> الملف الشخصي
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 main-content">
                <div class="row">
                    <!-- Recent Contacts -->
                    <div class="col-md-4">
                        <div class="card recent-contacts">
                            <div class="card-header">
                                <h5 class="card-title">جهات الاتصال الأخيرة</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    @forelse($recentContacts as $contact)
                                        <li class="contact-item">
                                            <img src="{{ $contact->profile_picture ?? asset('images/default-avatar.png') }}" 
                                                 alt="{{ $contact->name }}" 
                                                 class="rounded-circle avatar-sm">
                                            <span>{{ $contact->name }}</span>
                                            <a href="{{ route('messaging.conversation', $contact->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-comment"></i>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="text-center text-muted">
                                            لا توجد جهات اتصال حديثة
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-md-8">
                        <div class="card quick-actions">
                            <div class="card-header">
                                <h5 class="card-title">الإجراءات السريعة</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <a href="{{ route('messaging.conversations') }}" 
                                           class="btn btn-block btn-outline-primary mb-3">
                                            <i class="fas fa-envelope"></i> المحادثات
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="{{ route('calls.initiate') }}" 
                                           class="btn btn-block btn-outline-success mb-3">
                                            <i class="fas fa-phone"></i> مكالمة جديدة
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="{{ route('profile.update') }}" 
                                           class="btn btn-block btn-outline-secondary mb-3">
                                            <i class="fas fa-user-edit"></i> تعديل الملف
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.home-dashboard {
    background-color: #f4f7f6;
    min-height: 100vh;
}

.sidebar {
    background-color: #ffffff;
    border-left: 1px solid #e0e0e0;
    padding-top: 30px;
}

.avatar-lg {
    width: 120px;
    height: 120px;
    object-fit: cover;
}

.avatar-sm {
    width: 40px;
    height: 40px;
    object-fit: cover;
    margin-left: 10px;
}

.dashboard-nav .nav-link {
    color: #2c3e50;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 5px;
    transition: all 0.3s ease;
}

.dashboard-nav .nav-link:hover,
.dashboard-nav .nav-link.active {
    background-color: #e9ecef;
}

.dashboard-nav .nav-link i {
    margin-left: 10px;
}

.contact-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 5px;
}

.quick-actions .btn {
    display: flex;
    align-items: center;
    justify-content: center;
}

.quick-actions .btn i {
    margin-left: 10px;
}
</style>
@endpush
