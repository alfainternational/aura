<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agent;

class AgentsController extends Controller
{
    /**
     * Display the agents services page
     */
    public function index()
    {
        return view('services.agents.index');
    }

    /**
     * List available agents
     */
    public function list(Request $request)
    {
        $query = User::where('user_type', 'agent');

        // Filter by specialty
        if ($request->has('specialty')) {
            $query->whereHas('agent', function($q) use ($request) {
                $q->where('specialty', $request->specialty);
            });
        }

        // Filter by location
        if ($request->has('location')) {
            $query->whereHas('agent', function($q) use ($request) {
                $q->where('location', $request->location);
            });
        }

        $agents = $query->paginate(20);

        return view('services.agents.list', compact('agents'));
    }

    /**
     * Display agent performance metrics
     */
    public function performance()
    {
        $user = auth()->user();
        
        if ($user->user_type !== 'agent') {
            return redirect()->back()->withErrors('Access denied');
        }

        $agent = Agent::where('user_id', $user->id)->first();
        
        if (!$agent) {
            return redirect()->back()->withErrors('Agent profile not found');
        }

        $performanceMetrics = [
            'total_sales' => $agent->total_sales,
            'commission_rate' => $agent->commission_rate,
            'total_commission' => $agent->total_commission,
            'active_clients' => $agent->active_clients_count,
            'performance_rating' => $agent->performance_rating
        ];

        return view('services.agents.performance', compact('performanceMetrics'));
    }
}
