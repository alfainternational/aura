<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('auth');
        $this->paymentService = $paymentService;
    }

    /**
     * Universal Wallet Index
     */
    public function index()
    {
        $user = auth()->user();
        
        $walletBalance = $user->wallet_balance;
        $recentTransactions = $user->transactions()->recent()->take(10)->get();
        $depositMethods = $this->paymentService->getAvailableDepositMethods();
        $withdrawalMethods = $this->paymentService->getAvailableWithdrawalMethods();

        return view('wallet.index', compact(
            'walletBalance', 
            'recentTransactions', 
            'depositMethods', 
            'withdrawalMethods'
        ));
    }

    /**
     * Get Current Wallet Balance
     */
    public function balance()
    {
        $user = auth()->user();
        
        return response()->json([
            'balance' => $user->wallet_balance,
            'currency' => $user->preferred_currency ?? 'SDG'
        ]);
    }

    /**
     * Show Deposit Form
     */
    public function showDepositForm()
    {
        $user = auth()->user();
        
        $depositMethods = $this->paymentService->getAvailableDepositMethods();
        $minimumDeposit = $this->paymentService->getMinimumDepositAmount();

        return view('wallet.deposit', compact(
            'depositMethods', 
            'minimumDeposit'
        ));
    }

    /**
     * Process Deposit
     */
    public function deposit(Request $request)
    {
        $user = auth()->user();
        
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:10',
            'method' => 'required|in:bank_transfer,mobile_money,card',
            'reference' => 'nullable|string|max:255'
        ]);

        try {
            $transaction = $this->paymentService->processDeposit(
                $user, 
                $validatedData['amount'], 
                $validatedData['method'], 
                $validatedData['reference'] ?? null
            );

            return redirect()->route('wallet.transactions.details', $transaction)
                ->with('success', 'Deposit processed successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Deposit failed: ' . $e->getMessage());
        }
    }

    /**
     * Available Deposit Methods
     */
    public function depositMethods()
    {
        $methods = $this->paymentService->getAvailableDepositMethods();
        
        return response()->json($methods);
    }

    /**
     * Show Withdraw Form
     */
    public function showWithdrawForm()
    {
        $user = auth()->user();
        
        $withdrawalMethods = $this->paymentService->getAvailableWithdrawalMethods();
        $maximumWithdrawal = $this->paymentService->getMaximumWithdrawalAmount($user);
        $availableBalance = $user->wallet_balance;

        return view('wallet.withdraw', compact(
            'withdrawalMethods', 
            'maximumWithdrawal', 
            'availableBalance'
        ));
    }

    /**
     * Process Withdrawal
     */
    public function withdraw(Request $request)
    {
        $user = auth()->user();
        
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:50',
            'method' => 'required|in:bank_transfer,mobile_money,cash',
            'account_details' => 'required|array',
            'account_details.account_number' => 'required|string',
            'account_details.account_name' => 'required|string'
        ]);

        try {
            $transaction = $this->paymentService->processWithdrawal(
                $user, 
                $validatedData['amount'], 
                $validatedData['method'], 
                $validatedData['account_details']
            );

            return redirect()->route('wallet.transactions.details', $transaction)
                ->with('success', 'Withdrawal processed successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Withdrawal failed: ' . $e->getMessage());
        }
    }

    /**
     * Available Withdrawal Methods
     */
    public function withdrawalMethods()
    {
        $methods = $this->paymentService->getAvailableWithdrawalMethods();
        
        return response()->json($methods);
    }

    /**
     * List Transactions
     */
    public function transactions(Request $request)
    {
        $user = auth()->user();
        
        $query = $user->transactions();

        // Optional filtering
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        $transactions = $query->paginate(20);

        return view('wallet.transactions', compact('transactions'));
    }

    /**
     * Recent Transactions
     */
    public function recentTransactions()
    {
        $user = auth()->user();
        
        $recentTransactions = $user->transactions()
            ->recent()
            ->take(10)
            ->get();

        return response()->json($recentTransactions);
    }

    /**
     * Transaction Details
     */
    public function transactionDetails(Transaction $transaction)
    {
        // Ensure the transaction belongs to the authenticated user
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('wallet.transaction-details', compact('transaction'));
    }

    /**
     * Customer Wallet
     */
    public function customerWallet()
    {
        $user = auth()->user();
        
        // Ensure user is a customer
        if ($user->user_type !== 'customer') {
            abort(403, 'Unauthorized');
        }

        $walletBalance = $user->wallet_balance;
        $recentTransactions = $user->transactions()->recent()->take(10)->get();
        $linkedServices = $user->getLinkedPaymentServices();

        return view('wallet.customer', compact(
            'walletBalance', 
            'recentTransactions', 
            'linkedServices'
        ));
    }

    /**
     * Merchant Wallet
     */
    public function merchantWallet()
    {
        $user = auth()->user();
        
        // Ensure user is a merchant
        if ($user->user_type !== 'merchant') {
            abort(403, 'Unauthorized');
        }

        $walletBalance = $user->wallet_balance;
        $recentTransactions = $user->transactions()->recent()->take(10)->get();
        $salesSummary = $user->calculateMonthlySalesSummary();

        return view('wallet.merchant', compact(
            'walletBalance', 
            'recentTransactions', 
            'salesSummary'
        ));
    }

    /**
     * Agent Wallet
     */
    public function agentWallet()
    {
        $user = auth()->user();
        
        // Ensure user is an agent
        if ($user->user_type !== 'agent') {
            abort(403, 'Unauthorized');
        }

        $walletBalance = $user->wallet_balance;
        $recentTransactions = $user->transactions()->recent()->take(10)->get();
        $commissionSummary = $user->calculateCommissionSummary();

        return view('wallet.agent', compact(
            'walletBalance', 
            'recentTransactions', 
            'commissionSummary'
        ));
    }

    /**
     * Messenger Wallet
     */
    public function messengerWallet()
    {
        $user = auth()->user();
        
        // Ensure user is a messenger
        if ($user->user_type !== 'messenger') {
            abort(403, 'Unauthorized');
        }

        $walletBalance = $user->wallet_balance;
        $recentTransactions = $user->transactions()->recent()->take(10)->get();
        $deliveryEarnings = $user->calculateDeliveryEarnings();

        return view('wallet.messenger', compact(
            'walletBalance', 
            'recentTransactions', 
            'deliveryEarnings'
        ));
    }
}
