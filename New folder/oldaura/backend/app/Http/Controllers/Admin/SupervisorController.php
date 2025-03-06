<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MessengerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SupervisorController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('checkrole:admin,supervisor');
    }
    
    /**
     * Display the supervisor dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        try {
            $supervisor = Auth::user();
            Log::info('Supervisor accessed dashboard', ['supervisor_id' => $supervisor->id]);
            
            // Get messengers supervised by this supervisor with eager loading
            $messengers = User::where('user_type', 'messenger')
                ->with('messengerProfile') // Eager load profile
                ->whereHas('messengerProfile', function($query) use ($supervisor) {
                    $query->where('supervisor_id', $supervisor->id);
                })->get();
                
            // Calculate statistics
            $stats = [
                'total_messengers' => $messengers->count(),
                'online_messengers' => $messengers->where('messengerProfile.is_online', true)->count(),
                'total_deliveries' => $messengers->sum('messengerProfile.completed_deliveries'),
                'average_rating' => $messengers->avg('messengerProfile.rating') ?? 0,
                'deliveries_today' => $this->getDeliveriesToday($supervisor->id),
                'pending_issues' => $this->getPendingIssues($supervisor->id),
            ];
            
            // Get active messengers with location data for tracking
            $activeMessengers = User::where('user_type', 'messenger')
                ->with(['messengerProfile', 'notifications' => function($query) {
                    $query->latest()->take(3);
                }])
                ->whereHas('messengerProfile', function($query) use ($supervisor) {
                    $query->where('supervisor_id', $supervisor->id)
                          ->where('is_online', true);
                })
                ->get();
                
            return view('supervisor.dashboard', compact('stats', 'activeMessengers'));
        } catch (\Exception $e) {
            Log::error('Error displaying supervisor dashboard', [
                'error' => $e->getMessage(),
                'supervisor_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while loading the dashboard. Please try again.');
        }
    }

    /**
     * Get the number of deliveries completed today
     * 
     * @param int $supervisorId
     * @return int
     */
    private function getDeliveriesToday($supervisorId)
    {
        // This is a placeholder - implement actual logic based on your database schema
        return DB::table('users')
            ->join('messenger_profiles', 'users.id', '=', 'messenger_profiles.user_id')
            ->join('deliveries', 'users.id', '=', 'deliveries.messenger_id')
            ->where('messenger_profiles.supervisor_id', $supervisorId)
            ->whereDate('deliveries.completed_at', Carbon::today())
            ->count();
    }
    
    /**
     * Get the number of pending issues for messengers under this supervisor
     * 
     * @param int $supervisorId
     * @return int
     */
    private function getPendingIssues($supervisorId)
    {
        // This is a placeholder - implement actual logic based on your database schema
        return DB::table('users')
            ->join('messenger_profiles', 'users.id', '=', 'messenger_profiles.user_id')
            ->join('delivery_issues', 'users.id', '=', 'delivery_issues.messenger_id')
            ->where('messenger_profiles.supervisor_id', $supervisorId)
            ->where('delivery_issues.status', 'pending')
            ->count();
    }

    /**
     * Display a list of messengers under supervision
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function messengers(Request $request)
    {
        try {
            $supervisor = Auth::user();
            
            $query = User::where('user_type', 'messenger')
                ->with('messengerProfile')
                ->whereHas('messengerProfile', function($query) use ($supervisor) {
                    $query->where('supervisor_id', $supervisor->id);
                });
                
            // Filter by status
            if ($request->has('status') && in_array($request->status, ['online', 'offline'])) {
                $isOnline = $request->status === 'online';
                $query->whereHas('messengerProfile', function($q) use ($isOnline) {
                    $q->where('is_online', $isOnline);
                });
            }
            
            // Filter by performance
            if ($request->has('performance') && in_array($request->performance, ['high', 'medium', 'low'])) {
                $query->whereHas('messengerProfile', function($q) use ($request) {
                    switch($request->performance) {
                        case 'high':
                            $q->where('rating', '>=', 4.5);
                            break;
                        case 'medium':
                            $q->whereBetween('rating', [3.0, 4.5]);
                            break;
                        case 'low':
                            $q->where('rating', '<', 3.0);
                            break;
                    }
                });
            }
            
            // Search by name, email or ID
            if ($request->has('search') && $request->search) {
                $search = '%' . $request->search . '%';
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', $search)
                      ->orWhere('email', 'like', $search)
                      ->orWhereHas('messengerProfile', function($sq) use ($search) {
                          $sq->where('messenger_id', 'like', $search);
                      });
                });
            }
            
            // Sort options
            if ($request->has('sort') && in_array($request->sort, ['name', 'rating', 'deliveries'])) {
                switch($request->sort) {
                    case 'name':
                        $query->orderBy('name', $request->order ?? 'asc');
                        break;
                    case 'rating':
                        $query->join('messenger_profiles', 'users.id', '=', 'messenger_profiles.user_id')
                              ->orderBy('messenger_profiles.rating', $request->order ?? 'desc')
                              ->select('users.*');
                        break;
                    case 'deliveries':
                        $query->join('messenger_profiles', 'users.id', '=', 'messenger_profiles.user_id')
                              ->orderBy('messenger_profiles.completed_deliveries', $request->order ?? 'desc')
                              ->select('users.*');
                        break;
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            $messengers = $query->paginate(20)->appends($request->query());
            
            return view('supervisor.messengers.index', compact('messengers'));
        } catch (\Exception $e) {
            Log::error('Error displaying messengers list', [
                'error' => $e->getMessage(),
                'supervisor_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while loading messengers. Please try again.');
        }
    }

    /**
     * Show details of a specific messenger
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showMessenger($id)
    {
        try {
            $supervisor = Auth::user();
            
            $messenger = User::where('user_type', 'messenger')
                ->with(['messengerProfile', 'notifications' => function($query) {
                    $query->latest()->take(10);
                }])
                ->whereHas('messengerProfile', function($query) use ($supervisor) {
                    $query->where('supervisor_id', $supervisor->id);
                })
                ->findOrFail($id);
                
            // Get delivery history with pagination
            $deliveries = DB::table('deliveries')
                ->where('messenger_id', $messenger->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            // Performance metrics
            $performanceMetrics = [
                'avg_delivery_time' => $this->calculateAvgDeliveryTime($messenger->id),
                'completion_rate' => $this->calculateCompletionRate($messenger->id),
                'customer_satisfaction' => $messenger->messengerProfile->rating ?? 0,
                'total_distance' => $this->calculateTotalDistance($messenger->id)
            ];
                
            return view('supervisor.messengers.show', compact('messenger', 'deliveries', 'performanceMetrics'));
        } catch (\Exception $e) {
            Log::error('Error showing messenger details', [
                'error' => $e->getMessage(),
                'messenger_id' => $id,
                'supervisor_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while loading messenger details. Please try again.');
        }
    }

    /**
     * Calculate average delivery time for a messenger
     * 
     * @param int $messengerId
     * @return float|int
     */
    private function calculateAvgDeliveryTime($messengerId)
    {
        // Placeholder function - implement with actual database schema
        return 25; // minutes
    }
    
    /**
     * Calculate completion rate for a messenger
     * 
     * @param int $messengerId
     * @return float
     */
    private function calculateCompletionRate($messengerId)
    {
        // Placeholder function - implement with actual database schema
        return 0.95; // 95%
    }
    
    /**
     * Calculate total distance covered by a messenger
     * 
     * @param int $messengerId
     * @return float
     */
    private function calculateTotalDistance($messengerId)
    {
        // Placeholder function - implement with actual database schema
        return 120.5; // km
    }

    /**
     * Assign a work zone to a messenger
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignZone(Request $request, $id)
    {
        try {
            $supervisor = Auth::user();
            
            $messenger = User::where('user_type', 'messenger')
                ->whereHas('messengerProfile', function($query) use ($supervisor) {
                    $query->where('supervisor_id', $supervisor->id);
                })
                ->with('messengerProfile')
                ->findOrFail($id);
                
            $request->validate([
                'zone' => 'required|string|max:100',
                'notes' => 'nullable|string|max:255'
            ]);
            
            $messenger->messengerProfile->zone = $request->zone;
            $messenger->messengerProfile->zone_notes = $request->notes;
            $messenger->messengerProfile->zone_assigned_at = now();
            $messenger->messengerProfile->save();
            
            // Log zone assignment for audit trail
            Log::info('Messenger zone assigned', [
                'messenger_id' => $messenger->id,
                'zone' => $request->zone,
                'supervisor_id' => $supervisor->id
            ]);
            
            return back()->with('success', 'Messenger work zone updated successfully');
        } catch (\Exception $e) {
            Log::error('Error assigning zone to messenger', [
                'error' => $e->getMessage(),
                'messenger_id' => $id,
                'supervisor_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while assigning zone. Please try again.');
        }
    }

    /**
     * Show messenger tracking map
     * 
     * @return \Illuminate\View\View
     */
    public function trackMessengers()
    {
        try {
            $supervisor = Auth::user();
            
            $messengers = User::where('user_type', 'messenger')
                ->with(['messengerProfile' => function($query) {
                    $query->select('user_id', 'supervisor_id', 'is_online', 'last_active_at', 
                                  'current_latitude', 'current_longitude', 'messenger_id', 'zone');
                }])
                ->whereHas('messengerProfile', function($query) use ($supervisor) {
                    $query->where('supervisor_id', $supervisor->id)
                          ->where('is_online', true);
                })
                ->select('id', 'name', 'email', 'phone_number', 'profile_image')
                ->get();
                
            // Fetch active deliveries for tracking
            $activeDeliveries = DB::table('deliveries')
                ->whereIn('messenger_id', $messengers->pluck('id'))
                ->where('status', 'in_progress')
                ->get();
                
            return view('supervisor.track', compact('messengers', 'activeDeliveries'));
        } catch (\Exception $e) {
            Log::error('Error loading messenger tracking', [
                'error' => $e->getMessage(),
                'supervisor_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while loading tracking map. Please try again.');
        }
    }

    /**
     * Show performance reports for messengers
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function reports(Request $request)
    {
        try {
            $supervisor = Auth::user();
            
            // Date range filtering
            $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
            
            // Get messengers supervised by this supervisor
            $messengers = User::where('user_type', 'messenger')
                ->with('messengerProfile')
                ->whereHas('messengerProfile', function($query) use ($supervisor) {
                    $query->where('supervisor_id', $supervisor->id);
                })
                ->get();
                
            // Generate overall statistics
            $stats = [
                'total_messengers' => $messengers->count(),
                'total_deliveries' => $messengers->sum('messengerProfile.completed_deliveries'),
                'average_rating' => $messengers->avg('messengerProfile.rating') ?? 0,
                'total_revenue' => $this->calculateTotalRevenue($messengers->pluck('id'), $startDate, $endDate),
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate
                ]
            ];
            
            // Generate individual messenger stats
            $messengerStats = $messengers->map(function($messenger) use ($startDate, $endDate) {
                return [
                    'id' => $messenger->id,
                    'name' => $messenger->name,
                    'messenger_id' => $messenger->messengerProfile->messenger_id,
                    'completed_deliveries' => $messenger->messengerProfile->completed_deliveries,
                    'period_deliveries' => $this->getPeriodDeliveries($messenger->id, $startDate, $endDate),
                    'rating' => $messenger->messengerProfile->rating ?? 0,
                    'last_active' => $messenger->messengerProfile->last_active_at,
                    'is_online' => $messenger->messengerProfile->is_online,
                    'performance_score' => $this->calculatePerformanceScore($messenger),
                    'avg_response_time' => $this->getResponseTime($messenger->id)
                ];
            })->sortByDesc('performance_score')->values();
            
            return view('supervisor.reports', compact('stats', 'messengerStats', 'startDate', 'endDate'));
        } catch (\Exception $e) {
            Log::error('Error generating messenger reports', [
                'error' => $e->getMessage(),
                'supervisor_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while generating reports. Please try again.');
        }
    }
    
    /**
     * Calculate total revenue generated by messengers
     * 
     * @param array $messengerIds
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    private function calculateTotalRevenue($messengerIds, $startDate, $endDate)
    {
        // Placeholder function - implement based on your database schema
        return 12500.00;
    }
    
    /**
     * Get deliveries in a specific period
     * 
     * @param int $messengerId
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    private function getPeriodDeliveries($messengerId, $startDate, $endDate)
    {
        // Placeholder function - implement based on your database schema
        return rand(10, 50); // Random value for example
    }
    
    /**
     * Calculate performance score for a messenger
     * 
     * @param User $messenger
     * @return float
     */
    private function calculatePerformanceScore($messenger)
    {
        // Simple algorithm to calculate performance
        $rating = $messenger->messengerProfile->rating ?? 0;
        $completionRate = $this->calculateCompletionRate($messenger->id);
        $responseTime = $this->getResponseTime($messenger->id);
        
        // Normalize response time (lower is better)
        $normalizedResponseTime = max(0, 1 - ($responseTime / 30));
        
        // Calculate weighted score (customize weights as needed)
        $score = ($rating * 0.4) + ($completionRate * 0.4) + ($normalizedResponseTime * 0.2);
        
        return round($score * 10, 1); // Scale to 0-10
    }
    
    /**
     * Get average response time for a messenger
     * 
     * @param int $messengerId
     * @return float
     */
    private function getResponseTime($messengerId)
    {
        // Placeholder function - implement based on your database schema
        return rand(3, 25); // Random value for example (minutes)
    }
}
