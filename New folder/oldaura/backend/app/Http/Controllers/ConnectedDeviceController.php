<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ConnectedDevice;
use App\Models\TwoFactorSession;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Agent;

class ConnectedDeviceController extends Controller
{
    /**
     * Constructor del controlador
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('2fa.verify');
    }

    /**
     * Mostrar la lista de dispositivos conectados
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener el dispositivo actual
        $currentDevice = $user->connectedDevices()
            ->where('is_current_device', true)
            ->first();
            
        // Obtener otros dispositivos
        $otherDevices = $user->connectedDevices()
            ->where('is_current_device', false)
            ->orderBy('last_active_at', 'desc')
            ->get();
            
        return view('dashboard.user.connected-devices', compact('currentDevice', 'otherDevices'));
    }
    
    /**
     * Cambiar el estado de confianza de un dispositivo
     */
    public function toggleTrust($id)
    {
        $user = Auth::user();
        $device = $user->connectedDevices()->findOrFail($id);
        
        // Alternar el estado de confianza
        $device->toggleTrust();
        
        // Si el dispositivo ya no es de confianza, eliminar cualquier sesión de 2FA asociada
        if (!$device->is_trusted) {
            $user->twoFactorSessions()
                ->where('ip_address', $device->ip_address)
                ->where('user_agent', $device->user_agent)
                ->delete();
                
            // Eliminar el dispositivo de la lista de dispositivos de confianza
            if ($device->device_token) {
                $user->removeTrustedDevice($device->device_token);
            }
        }
        
        $statusMessage = $device->is_trusted 
            ? 'تم تعيين الجهاز كجهاز موثوق بنجاح.' 
            : 'تم إلغاء الثقة في الجهاز بنجاح.';
            
        return redirect()->route('user.devices.index')->with('success', $statusMessage);
    }
    
    /**
     * Cerrar sesión en un dispositivo específico
     */
    public function logout($id)
    {
        $user = Auth::user();
        $device = $user->connectedDevices()->findOrFail($id);
        
        // Eliminar cualquier sesión de 2FA asociada
        $user->twoFactorSessions()
            ->where('ip_address', $device->ip_address)
            ->where('user_agent', $device->user_agent)
            ->delete();
            
        // Eliminar el dispositivo de la lista de dispositivos de confianza
        if ($device->device_token) {
            $user->removeTrustedDevice($device->device_token);
        }
        
        // Eliminar el dispositivo
        $device->delete();
        
        return redirect()->route('user.devices.index')->with('success', 'تم تسجيل الخروج من الجهاز بنجاح.');
    }
    
    /**
     * Cerrar sesión en todos los dispositivos excepto el actual
     */
    public function logoutAll()
    {
        $user = Auth::user();
        
        // Obtener el dispositivo actual
        $currentDevice = $user->connectedDevices()
            ->where('is_current_device', true)
            ->first();
            
        // Eliminar todos los dispositivos excepto el actual
        if ($currentDevice) {
            // Eliminar todas las sesiones de 2FA excepto la actual
            $user->twoFactorSessions()
                ->where(function($query) use ($currentDevice) {
                    $query->where('ip_address', '!=', $currentDevice->ip_address)
                        ->orWhere('user_agent', '!=', $currentDevice->user_agent);
                })
                ->delete();
                
            // Eliminar todos los dispositivos excepto el actual
            $user->connectedDevices()
                ->where('id', '!=', $currentDevice->id)
                ->delete();
                
            // Eliminar todos los dispositivos de confianza excepto el actual
            $user->removeAllTrustedDevices();
            
            // Si el dispositivo actual es de confianza, volver a agregarlo
            if ($currentDevice->is_trusted && $currentDevice->device_token) {
                $user->addTrustedDevice($currentDevice->device_token);
            }
        }
        
        return redirect()->route('user.devices.index')->with('success', 'تم تسجيل الخروج من جميع الأجهزة الأخرى بنجاح.');
    }
    
    /**
     * Registrar el dispositivo actual
     */
    public function registerCurrentDevice()
    {
        $user = Auth::user();
        $agent = new Agent();
        $request = request();
        
        // Generar un token único para el dispositivo
        $deviceToken = md5($request->userAgent() . $request->ip() . $user->id . uniqid());
        
        // Verificar si ya existe un dispositivo con la misma información
        $existingDevice = $user->connectedDevices()
            ->where('ip_address', $request->ip())
            ->where('user_agent', $request->userAgent())
            ->first();
            
        if ($existingDevice) {
            // Actualizar el dispositivo existente
            $existingDevice->update([
                'device_token' => $deviceToken,
                'last_active_at' => now(),
                'is_current_device' => true
            ]);
            
            // Desmarcar otros dispositivos como actuales
            $user->connectedDevices()
                ->where('id', '!=', $existingDevice->id)
                ->update(['is_current_device' => false]);
                
            return $existingDevice;
        }
        
        // Crear un nuevo registro de dispositivo
        $device = new ConnectedDevice();
        $device->user_id = $user->id;
        $device->device_name = $this->getDeviceName($agent);
        $device->device_type = $this->getDeviceType($agent);
        $device->browser = $agent->browser();
        $device->operating_system = $agent->platform();
        $device->ip_address = $request->ip();
        $device->location = $this->getLocationFromIp($request->ip());
        $device->user_agent = $request->userAgent();
        $device->device_token = $deviceToken;
        $device->last_active_at = now();
        $device->is_current_device = true;
        $device->save();
        
        // Desmarcar otros dispositivos como actuales
        $user->connectedDevices()
            ->where('id', '!=', $device->id)
            ->update(['is_current_device' => false]);
            
        return $device;
    }
    
    /**
     * Obtener el nombre del dispositivo
     */
    private function getDeviceName($agent)
    {
        $deviceType = $this->getDeviceType($agent);
        $browser = $agent->browser();
        $platform = $agent->platform();
        
        return "{$browser} على {$platform} ({$deviceType})";
    }
    
    /**
     * Obtener el tipo de dispositivo
     */
    private function getDeviceType($agent)
    {
        if ($agent->isPhone()) {
            return 'هاتف محمول';
        } elseif ($agent->isTablet()) {
            return 'جهاز لوحي';
        } else {
            return 'كمبيوتر';
        }
    }
    
    /**
     * Obtener la ubicación a partir de la dirección IP
     */
    private function getLocationFromIp($ip)
    {
        // Esta función podría implementarse con un servicio de geolocalización
        // Por ahora, devolvemos un valor predeterminado
        return 'موقع غير معروف';
    }
}
