<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Constructor del controlador para aplicar middlewares globales
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Registrar actividad de acceso al dashboard
        $this->middleware(function ($request, $next) {
            Log::info('Dashboard access', [
                'user_id' => Auth::id(),
                'user_type' => Auth::user()->user_type,
                'route' => $request->route()->getName()
            ]);
            return $next($request);
        });
        $this->middleware('checkrole:customer')->only('customerDashboard', 'completeCustomerProfile');
        $this->middleware('checkrole:merchant')->only('merchantDashboard', 'completeMerchantProfile');
        $this->middleware('checkrole:agent')->only('agentDashboard', 'completeAgentProfile');
        $this->middleware('checkrole:messenger')->only('messengerDashboard', 'completeMessengerProfile');
        $this->middleware('checkrole:admin')->only('adminDashboard');
    }

    /**
     * عرض لوحة تحكم العميل
     * Muestra el panel de control del cliente con sus notificaciones
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function customerDashboard()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $data = [
            'user' => $user,
            'notifications' => $notifications,
            'page_title' => 'لوحة تحكم العميل',
        ];
        
        return view('customer.dashboard', $data);
    }

    /**
     * عرض لوحة تحكم التاجر
     * Muestra el panel de control del comerciante
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function merchantDashboard()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $data = [
            'user' => $user,
            'notifications' => $notifications,
            'page_title' => 'لوحة تحكم التاجر',
        ];
        
        return view('merchant.dashboard', $data);
    }

    /**
     * عرض لوحة تحكم الوكيل
     * Muestra el panel de control del agente
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function agentDashboard()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $data = [
            'user' => $user,
            'notifications' => $notifications,
            'page_title' => 'لوحة تحكم الوكيل',
        ];
        
        return view('agent.dashboard', $data);
    }

    /**
     * عرض لوحة تحكم المندوب
     * Muestra el panel de control del mensajero
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function messengerDashboard()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $data = [
            'user' => $user,
            'notifications' => $notifications,
            'page_title' => 'لوحة تحكم المندوب',
        ];
        
        return view('messenger.dashboard', $data);
    }

    /**
     * عرض لوحة تحكم المشرف
     * Muestra el panel de control del administrador
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function adminDashboard()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Obtener estadísticas básicas para el administrador
        $userCounts = [
            'total' => User::count(),
            'customers' => User::where('user_type', 'customer')->count(),
            'merchants' => User::where('user_type', 'merchant')->count(),
            'agents' => User::where('user_type', 'agent')->count(),
            'messengers' => User::where('user_type', 'messenger')->count(),
        ];
            
        $data = [
            'user' => $user,
            'notifications' => $notifications,
            'userCounts' => $userCounts,
            'page_title' => 'لوحة تحكم المشرف',
        ];
        
        return view('admin.dashboard', $data);
    }

    /**
     * استكمال بيانات العميل
     * Formulario para completar perfil de cliente
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function completeCustomerProfile()
    {
        return view('customer.complete-profile', [
            'user' => Auth::user(),
            'page_title' => 'استكمال البيانات'
        ]);
    }

    /**
     * استكمال بيانات التاجر
     * Formulario para completar perfil de comerciante
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function completeMerchantProfile()
    {
        return view('merchant.complete-profile', [
            'user' => Auth::user(),
            'page_title' => 'استكمال البيانات'
        ]);
    }

    /**
     * استكمال بيانات الوكيل
     * Formulario para completar perfil de agente
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function completeAgentProfile()
    {
        return view('agent.complete-profile', [
            'user' => Auth::user(),
            'page_title' => 'استكمال البيانات'
        ]);
    }

    /**
     * استكمال بيانات المندوب
     * Formulario para completar perfil de mensajero
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function completeMessengerProfile()
    {
        return view('messenger.complete-profile', [
            'user' => Auth::user(),
            'page_title' => 'استكمال البيانات'
        ]);
    }
    
    /**
     * Redirecciona al usuario a su dashboard según su tipo
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToDashboard()
    {
        $user = Auth::user();
        
        switch ($user->user_type) {
            case 'customer':
                return redirect()->route('customer.dashboard');
            case 'merchant':
                return redirect()->route('merchant.dashboard');
            case 'agent':
                return redirect()->route('agent.dashboard');
            case 'messenger':
                return redirect()->route('messenger.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('home');
        }
    }
}
