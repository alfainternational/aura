<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check-role:admin,supervisor']);
    }

    /**
     * List all deliveries
     */
    public function index(Request $request)
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

        $deliveries = $query->with('messenger')->paginate(20);
        
        return view('supervisor.deliveries.index', compact('deliveries'));
    }

    /**
     * List active deliveries
     */
    public function activeDeliveries()
    {
        $deliveries = Delivery::where('status', 'in_progress')
            ->with('messenger')
            ->paginate(20);
        
        return view('supervisor.deliveries.active', compact('deliveries'));
    }

    /**
     * List completed deliveries
     */
    public function completedDeliveries()
    {
        $deliveries = Delivery::where('status', 'completed')
            ->with('messenger')
            ->paginate(20);
        
        return view('supervisor.deliveries.completed', compact('deliveries'));
    }

    /**
     * Show delivery details
     */
    public function show(Delivery $delivery)
    {
        $delivery->load('messenger', 'order');
        
        return view('supervisor.deliveries.details', compact('delivery'));
    }

    /**
     * Display delivery services page for users
     */
    public function servicesIndex()
    {
        return view('services.delivery.index');
    }

    /**
     * Provide delivery tracking information
     */
    public function servicesTracking(Request $request)
    {
        $trackingNumber = $request->input('tracking_number');
        
        if ($trackingNumber) {
            $delivery = Delivery::where('tracking_number', $trackingNumber)->first();
            
            if ($delivery) {
                return view('services.delivery.tracking', compact('delivery'));
            }
            
            return back()->withErrors(['tracking' => 'Tracking number not found']);
        }
        
        return view('services.delivery.tracking');
    }

    /**
     * Display delivery rates and pricing
     */
    public function servicesRates()
    {
        // In a real-world scenario, this would fetch rates from a delivery service API
        $rates = [
            ['type' => 'Standard', 'price' => 50, 'estimated_delivery' => '3-5 days'],
            ['type' => 'Express', 'price' => 100, 'estimated_delivery' => '1-2 days'],
            ['type' => 'Next Day', 'price' => 200, 'estimated_delivery' => 'Next business day'],
        ];
        
        return view('services.delivery.rates', compact('rates'));
    }
}
