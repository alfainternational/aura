<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MessengerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check-role:admin,supervisor']);
    }

    /**
     * List all messengers
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'messenger');

        // Optional filtering
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $messengers = $query->paginate(20);
        
        return view('supervisor.messengers.index', compact('messengers'));
    }

    /**
     * Show create messenger form
     */
    public function create()
    {
        return view('supervisor.messengers.create');
    }

    /**
     * Store a new messenger
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

        $messenger = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'user_type' => 'messenger',
            'status' => $validatedData['status'],
            'password' => Hash::make($validatedData['password']),
            'zones' => json_encode($validatedData['zones'] ?? [])
        ]);

        return redirect()->route('supervisor.messengers.index')
            ->with('success', 'Messenger created successfully');
    }

    /**
     * Show edit messenger form
     */
    public function edit(User $messenger)
    {
        // Ensure the user is a messenger
        if ($messenger->user_type !== 'messenger') {
            abort(403, 'Unauthorized');
        }

        return view('supervisor.messengers.edit', compact('messenger'));
    }

    /**
     * Update messenger
     */
    public function update(Request $request, User $messenger)
    {
        // Ensure the user is a messenger
        if ($messenger->user_type !== 'messenger') {
            abort(403, 'Unauthorized');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $messenger->id,
            'phone' => 'required|string|unique:users,phone,' . $messenger->id,
            'password' => 'nullable|min:8|confirmed',
            'zones' => 'nullable|array',
            'status' => 'required|in:active,inactive'
        ]);

        $messenger->name = $validatedData['name'];
        $messenger->email = $validatedData['email'];
        $messenger->phone = $validatedData['phone'];
        $messenger->status = $validatedData['status'];
        $messenger->zones = json_encode($validatedData['zones'] ?? []);

        if (!empty($validatedData['password'])) {
            $messenger->password = Hash::make($validatedData['password']);
        }

        $messenger->save();

        return redirect()->route('supervisor.messengers.index')
            ->with('success', 'Messenger updated successfully');
    }

    /**
     * Show messenger performance details
     */
    public function performance(User $messenger)
    {
        // Ensure the user is a messenger
        if ($messenger->user_type !== 'messenger') {
            abort(403, 'Unauthorized');
        }

        // Fetch performance metrics
        $deliveries = $messenger->deliveries()->recent()->paginate(20);
        $performanceMetrics = $messenger->calculatePerformanceMetrics();

        return view('supervisor.messengers.performance', compact('messenger', 'deliveries', 'performanceMetrics'));
    }

    /**
     * Messenger Dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Fetch key metrics for messenger dashboard
        $activeDeliveries = $user->deliveries()->where('status', 'in_progress')->count();
        $completedDeliveries = $user->deliveries()->where('status', 'completed')->count();
        $earnings = $user->calculateTotalEarnings();
        $recentDeliveries = $user->deliveries()->recent()->take(5)->get();
        $performanceMetrics = $user->calculatePerformanceMetrics();

        return view('messenger.dashboard', compact(
            'user', 
            'activeDeliveries', 
            'completedDeliveries', 
            'earnings', 
            'recentDeliveries', 
            'performanceMetrics'
        ));
    }

    /**
     * Messenger Earnings
     */
    public function earnings()
    {
        $user = auth()->user();
        $totalEarnings = $user->calculateTotalEarnings();
        $monthlyEarnings = $user->calculateMonthlyEarnings();
        $pendingPayments = $user->getPendingPayments();

        return view('messenger.earnings.index', compact(
            'totalEarnings', 
            'monthlyEarnings', 
            'pendingPayments'
        ));
    }

    /**
     * Earnings History
     */
    public function earningsHistory(Request $request)
    {
        $user = auth()->user();
        
        $query = $user->payments();

        // Optional filtering
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $earningsHistory = $query->paginate(20);

        return view('messenger.earnings.history', compact('earningsHistory'));
    }

    /**
     * Withdraw Earnings
     */
    public function withdrawEarnings(Request $request)
    {
        $user = auth()->user();
        
        // Validate withdrawal request
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:50|max:' . $user->calculateAvailableEarnings(),
            'payment_method' => 'required|in:bank_transfer,mobile_money,cash'
        ]);

        try {
            $withdrawal = $user->createWithdrawalRequest($validatedData);
            
            return redirect()->route('messenger.earnings')
                ->with('success', 'Withdrawal request submitted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to process withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Messenger Performance Overview
     */
    public function performanceOverview()
    {
        $user = auth()->user();
        
        $performanceMetrics = $user->calculatePerformanceMetrics();
        $ratingBreakdown = $user->calculateRatingBreakdown();
        $performanceHistory = $user->getPerformanceHistory();

        return view('messenger.performance.index', compact(
            'performanceMetrics', 
            'ratingBreakdown', 
            'performanceHistory'
        ));
    }

    /**
     * Messenger Ratings
     */
    public function ratings()
    {
        $user = auth()->user();
        
        $ratings = $user->ratings()->with('customer')->paginate(20);
        $averageRating = $user->calculateAverageRating();

        return view('messenger.performance.ratings', compact(
            'ratings', 
            'averageRating'
        ));
    }

    /**
     * Messenger Performance Statistics
     */
    public function statistics()
    {
        $user = auth()->user();
        
        $monthlyStats = $user->calculateMonthlyPerformanceStats();
        $yearlyStats = $user->calculateYearlyPerformanceStats();

        return view('messenger.performance.statistics', compact(
            'monthlyStats', 
            'yearlyStats'
        ));
    }
}
