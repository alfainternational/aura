<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check-role:admin']);
    }

    /**
     * List all supervisors
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'supervisor');

        // Optional filtering
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $supervisors = $query->paginate(20);
        
        return view('admin.supervisors.index', compact('supervisors'));
    }

    /**
     * Show create supervisor form
     */
    public function create()
    {
        return view('admin.supervisors.create');
    }

    /**
     * Store a new supervisor
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|min:8|confirmed',
            'zones' => 'nullable|array',
            'status' => 'required|in:active,inactive'
        ]);

        $supervisor = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'user_type' => 'supervisor',
            'status' => $validatedData['status'],
            'password' => Hash::make($validatedData['password']),
            'zones' => json_encode($validatedData['zones'] ?? [])
        ]);

        return redirect()->route('admin.supervisors.index')
            ->with('success', 'Supervisor created successfully');
    }

    /**
     * Show edit supervisor form
     */
    public function edit(User $supervisor)
    {
        // Ensure the user is a supervisor
        if ($supervisor->user_type !== 'supervisor') {
            abort(403, 'Unauthorized');
        }

        return view('admin.supervisors.edit', compact('supervisor'));
    }

    /**
     * Update supervisor
     */
    public function update(Request $request, User $supervisor)
    {
        // Ensure the user is a supervisor
        if ($supervisor->user_type !== 'supervisor') {
            abort(403, 'Unauthorized');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $supervisor->id,
            'phone' => 'required|string|unique:users,phone,' . $supervisor->id,
            'password' => 'nullable|min:8|confirmed',
            'zones' => 'nullable|array',
            'status' => 'required|in:active,inactive'
        ]);

        $supervisor->name = $validatedData['name'];
        $supervisor->email = $validatedData['email'];
        $supervisor->phone = $validatedData['phone'];
        $supervisor->status = $validatedData['status'];
        $supervisor->zones = json_encode($validatedData['zones'] ?? []);

        if (!empty($validatedData['password'])) {
            $supervisor->password = Hash::make($validatedData['password']);
        }

        $supervisor->save();

        return redirect()->route('admin.supervisors.index')
            ->with('success', 'Supervisor updated successfully');
    }

    /**
     * Supervisor Dashboard
     */
    public function dashboard()
    {
        // Fetch key metrics for supervisor dashboard
        $totalMessengers = User::where('user_type', 'messenger')->count();
        $activeMessengers = User::where('user_type', 'messenger')->where('status', 'active')->count();
        $recentDeliveries = Delivery::with('messenger')->latest()->take(10)->get();
        $performanceMetrics = $this->calculateOverallPerformance();

        return view('supervisor.dashboard', compact(
            'totalMessengers', 
            'activeMessengers', 
            'recentDeliveries', 
            'performanceMetrics'
        ));
    }

    /**
     * Generate performance reports
     */
    public function reports(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth());
        $endDate = $request->input('end_date', now());

        $messengerPerformance = User::where('user_type', 'messenger')
            ->withCount(['deliveries' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderByDesc('deliveries_count')
            ->paginate(20);

        return view('supervisor.reports.index', compact('messengerPerformance', 'startDate', 'endDate'));
    }

    /**
     * Generate detailed messenger reports
     */
    public function messengerReports(Request $request)
    {
        $query = User::where('user_type', 'messenger');

        // Filter by performance metrics
        if ($request->has('min_performance')) {
            $query->where('performance_score', '>=', $request->min_performance);
        }

        $messengers = $query->with(['deliveries' => function ($q) {
            $q->recent()->withCount('completedDeliveries');
        }])
        ->paginate(20);

        return view('supervisor.reports.messengers', compact('messengers'));
    }

    /**
     * Generate delivery reports
     */
    public function deliveryReports(Request $request)
    {
        $query = Delivery::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by messenger
        if ($request->has('messenger_id')) {
            $query->where('messenger_id', $request->messenger_id);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        $deliveries = $query->with('messenger')
            ->withCount('completedDeliveries')
            ->paginate(20);

        return view('supervisor.reports.deliveries', compact('deliveries'));
    }

    /**
     * Calculate overall performance metrics
     */
    private function calculateOverallPerformance()
    {
        // Implement complex performance calculation logic
        return [
            'total_messengers' => User::where('user_type', 'messenger')->count(),
            'active_messengers' => User::where('user_type', 'messenger')->where('status', 'active')->count(),
            'total_deliveries' => Delivery::count(),
            'completed_deliveries' => Delivery::where('status', 'completed')->count(),
            'average_delivery_time' => Delivery::avg('delivery_duration'),
        ];
    }
}
