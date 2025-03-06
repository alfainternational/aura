<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

class UserActivityController extends Controller
{
    /**
     * Registrar una nueva actividad del usuario
     */
    public function logActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activity_type' => 'required|string|max:50',
            'activity_details' => 'nullable|array',
            'metadata' => 'nullable|array',
            'duration' => 'nullable|integer',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Obtener información del dispositivo y navegador
        $agent = new Agent();
        $agent->setUserAgent($request->header('User-Agent'));
        
        $deviceInfo = [
            'device' => $agent->device(),
            'platform' => $agent->platform(),
            'browser' => $agent->browser(),
            'is_mobile' => $agent->isMobile(),
            'is_tablet' => $agent->isTablet(),
            'is_desktop' => $agent->isDesktop(),
        ];
        
        // Intentar obtener datos de ubicación desde la solicitud
        $locationData = null;
        if ($request->has('location')) {
            $locationData = $request->input('location');
        }
        
        // Crear el registro de actividad
        $activity = UserActivity::create([
            'user_id' => $request->user()->id,
            'activity_type' => $request->input('activity_type'),
            'activity_details' => $request->input('activity_details'),
            'metadata' => $request->input('metadata'),
            'ip_address' => $request->ip(),
            'device_info' => $deviceInfo,
            'location_data' => $locationData,
            'session_id' => $request->session()->getId(),
            'duration' => $request->input('duration'),
            'referrer' => $request->header('referer'),
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Actividad registrada correctamente',
            'data' => $activity
        ]);
    }

    /**
     * Obtener las actividades recientes del usuario
     */
    public function getUserActivities(Request $request)
    {
        $userId = $request->user()->id;
        
        $activities = UserActivity::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->take(50)
                    ->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Actividades obtenidas correctamente',
            'data' => $activities
        ]);
    }

    /**
     * Obtener estadísticas de las actividades del usuario
     */
    public function getUserStats(Request $request)
    {
        $userId = $request->user()->id;
        
        // Obtener estadísticas de actividad
        $stats = [
            'total_activities' => UserActivity::where('user_id', $userId)->count(),
            'activity_types' => UserActivity::where('user_id', $userId)
                ->select('activity_type')
                ->selectRaw('count(*) as count')
                ->groupBy('activity_type')
                ->get(),
            'daily_activities' => UserActivity::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays(7))
                ->selectRaw('DATE(created_at) as date')
                ->selectRaw('count(*) as count')
                ->groupBy('date')
                ->get(),
        ];
        
        return response()->json([
            'status' => 'success',
            'message' => 'Estadísticas obtenidas correctamente',
            'data' => $stats
        ]);
    }

    /**
     * Obtener recomendaciones personalizadas basadas en la actividad del usuario
     */
    public function getRecommendations(Request $request)
    {
        $userId = $request->user()->id;
        
        // Obtener categorías más visitadas
        $topCategories = UserActivity::where('user_id', $userId)
                        ->where('activity_type', 'view_category')
                        ->whereJsonContains('activity_details->category_id', '!=', null)
                        ->selectRaw('JSON_EXTRACT(activity_details, "$.category_id") as category_id')
                        ->selectRaw('count(*) as count')
                        ->groupBy('category_id')
                        ->orderBy('count', 'desc')
                        ->take(5)
                        ->get()
                        ->pluck('category_id');
        
        // Obtener productos más vistos
        $topProducts = UserActivity::where('user_id', $userId)
                    ->where('activity_type', 'view_product')
                    ->whereJsonContains('activity_details->product_id', '!=', null)
                    ->selectRaw('JSON_EXTRACT(activity_details, "$.product_id") as product_id')
                    ->selectRaw('count(*) as count')
                    ->groupBy('product_id')
                    ->orderBy('count', 'desc')
                    ->take(10)
                    ->get()
                    ->pluck('product_id');
        
        // Aquí se implementaría la lógica para obtener productos recomendados
        // basados en los datos anteriores
        
        return response()->json([
            'status' => 'success',
            'message' => 'Recomendaciones obtenidas correctamente',
            'data' => [
                'top_categories' => $topCategories,
                'top_products' => $topProducts,
                // Aquí irían los productos recomendados
            ]
        ]);
    }
}
