<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Models\Message;
use App\Models\KycVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check-role:agent']);
    }

    /**
     * Agent Dashboard
     */
    public function dashboard()
    {
        $agent = auth()->user();
        
        // Fetch key metrics for agent dashboard
        $totalCustomers = $agent->customers()->count();
        $newCustomers = $agent->customers()->recent()->count();
        $kycPendingCustomers = $agent->customers()->whereHas('kycVerification', function ($query) {
            $query->where('status', 'pending');
        })->count();
        $totalCommissions = $agent->calculateTotalCommissions();
        $recentCustomers = $agent->customers()->recent()->take(5)->get();
        $performanceMetrics = $agent->calculatePerformanceMetrics();

        return view('agent.dashboard', compact(
            'agent',
            'totalCustomers',
            'newCustomers',
            'kycPendingCustomers',
            'totalCommissions',
            'recentCustomers',
            'performanceMetrics'
        ));
    }

    /**
     * List Customers
     */
    public function customers(Request $request)
    {
        $agent = auth()->user();
        
        $query = $agent->customers();

        // Optional filtering
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('kyc_status')) {
            $query->whereHas('kycVerification', function ($q) use ($request) {
                $q->where('status', $request->kyc_status);
            });
        }

        $customers = $query->paginate(20);

        return view('agent.customers.index', compact('customers'));
    }

    /**
     * Show Customer Creation Form
     */
    public function createCustomer()
    {
        return view('agent.customers.create');
    }

    /**
     * Store New Customer
     */
    public function storeCustomer(Request $request)
    {
        $agent = auth()->user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'location' => 'required|array',
            'location.country' => 'required|string',
            'location.city' => 'required|string',
            'location.address' => 'nullable|string',
            'preferred_language' => 'nullable|string',
            'referral_source' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $customer = User::create([
                'name' => $validatedData['name'],
                'phone' => $validatedData['phone'],
                'email' => $validatedData['email'] ?? null,
                'user_type' => 'customer',
                'status' => 'active',
                'password' => Hash::make(substr($validatedData['phone'], -6)), // Temporary password
                'location' => json_encode($validatedData['location']),
                'preferred_language' => $validatedData['preferred_language'] ?? null,
                'referred_by' => $agent->id
            ]);

            // Create initial KYC verification record
            KycVerification::create([
                'user_id' => $customer->id,
                'status' => 'pending',
                'agent_id' => $agent->id
            ]);

            DB::commit();

            return redirect()->route('agent.customers.onboarding', $customer)
                ->with('success', 'Customer created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Unable to create customer: ' . $e->getMessage());
        }
    }

    /**
     * Customer Details
     */
    public function customerDetails(User $customer)
    {
        // Ensure the customer belongs to the current agent
        if ($customer->referred_by !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $customer->load('kycVerification', 'wallet', 'orders');

        return view('agent.customers.details', compact('customer'));
    }

    /**
     * Customer Onboarding
     */
    public function customerOnboarding(User $customer)
    {
        // Ensure the customer belongs to the current agent
        if ($customer->referred_by !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('agent.customers.onboarding', compact('customer'));
    }

    /**
     * KYC Dashboard
     */
    public function kycDashboard()
    {
        $agent = auth()->user();
        
        $kycStats = [
            'total' => KycVerification::where('agent_id', $agent->id)->count(),
            'pending' => KycVerification::where('agent_id', $agent->id)
                ->where('status', 'pending')->count(),
            'approved' => KycVerification::where('agent_id', $agent->id)
                ->where('status', 'approved')->count(),
            'rejected' => KycVerification::where('agent_id', $agent->id)
                ->where('status', 'rejected')->count()
        ];

        return view('agent.kyc.dashboard', compact('kycStats'));
    }

    /**
     * Pending KYC Verifications
     */
    public function pendingKyc()
    {
        $agent = auth()->user();
        
        $pendingKyc = KycVerification::where('agent_id', $agent->id)
            ->where('status', 'pending')
            ->with('user')
            ->paginate(20);

        return view('agent.kyc.pending', compact('pendingKyc'));
    }

    /**
     * Verify Customer
     */
    public function verifyCustomer(User $customer)
    {
        // Ensure the customer belongs to the current agent
        if ($customer->referred_by !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $kycVerification = $customer->kycVerification;

        return view('agent.kyc.verify', compact('customer', 'kycVerification'));
    }

    /**
     * Submit KYC Verification
     */
    public function submitKycVerification(Request $request, User $customer)
    {
        // Ensure the customer belongs to the current agent
        if ($customer->referred_by !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validatedData = $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:500',
            'documents' => 'nullable|array'
        ]);

        try {
            $kycVerification = $customer->kycVerification;
            $kycVerification->update([
                'status' => $validatedData['status'],
                'verified_by' => auth()->id(),
                'notes' => $validatedData['notes'] ?? null,
                'verified_at' => now()
            ]);

            // Store verified documents if any
            if (!empty($validatedData['documents'])) {
                $kycVerification->documents()->createMany($validatedData['documents']);
            }

            return redirect()->route('agent.kyc')
                ->with('success', 'KYC verification submitted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to submit KYC verification: ' . $e->getMessage());
        }
    }

    /**
     * Reports Dashboard
     */
    public function reports()
    {
        $agent = auth()->user();
        
        $reportData = [
            'customerGrowth' => $agent->calculateCustomerGrowth(),
            'kycConversionRate' => $agent->calculateKycConversionRate(),
            'commissionSummary' => $agent->calculateCommissionSummary()
        ];

        return view('agent.reports.index', compact('reportData'));
    }

    /**
     * Customer Reports
     */
    public function customerReports(Request $request)
    {
        $agent = auth()->user();
        
        $query = $agent->customers();

        // Filtering options
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        $customerReports = $query->withCount('orders')
            ->withSum('orders', 'total_price')
            ->paginate(20);

        return view('agent.reports.customers', compact('customerReports'));
    }

    /**
     * Sales Reports
     */
    public function salesReports(Request $request)
    {
        $agent = auth()->user();
        
        $query = $agent->customers()->join('orders', 'users.id', '=', 'orders.user_id');

        // Filtering options
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('orders.created_at', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        $salesReports = $query->select(
            'users.id', 
            'users.name', 
            DB::raw('COUNT(orders.id) as total_orders'),
            DB::raw('SUM(orders.total_price) as total_sales')
        )
        ->groupBy('users.id', 'users.name')
        ->orderByDesc('total_sales')
        ->paginate(20);

        return view('agent.reports.sales', compact('salesReports'));
    }

    /**
     * Performance Reports
     */
    public function performanceReports()
    {
        $agent = auth()->user();
        
        $performanceData = [
            'customerAcquisition' => $agent->calculateCustomerAcquisitionRate(),
            'kycVerificationRate' => $agent->calculateKycVerificationRate(),
            'commissionEarnings' => $agent->calculateCommissionEarnings()
        ];

        return view('agent.reports.performance', compact('performanceData'));
    }

    /**
     * Messages
     */
    public function messages()
    {
        $agent = auth()->user();
        
        $conversations = $agent->conversations()
            ->with('lastMessage')
            ->paginate(20);

        return view('agent.messages.index', compact('conversations'));
    }

    /**
     * Customer Messages
     */
    public function customerMessages(Request $request)
    {
        $agent = auth()->user();
        
        $query = Message::whereHas('conversation', function ($q) use ($agent) {
            $q->where('agent_id', $agent->id);
        });

        // Optional filtering
        if ($request->has('customer_id')) {
            $query->where('sender_id', $request->customer_id)
                 ->orWhere('receiver_id', $request->customer_id);
        }

        $messages = $query->with('sender', 'receiver')
            ->latest()
            ->paginate(20);

        return view('agent.messages.customers', compact('messages'));
    }

    /**
     * Send Message
     */
    public function sendMessage(Request $request)
    {
        $agent = auth()->user();
        
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'type' => 'nullable|in:text,image'
        ]);

        try {
            $message = Message::create([
                'sender_id' => $agent->id,
                'receiver_id' => $validatedData['customer_id'],
                'message' => $validatedData['message'],
                'type' => $validatedData['type'] ?? 'text'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to send message: ' . $e->getMessage()
            ], 500);
        }
    }
}
