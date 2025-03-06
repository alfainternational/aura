<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class AIAssistantController extends Controller
{
    /**
     * Display the AI Assistant services page
     */
    public function index()
    {
        return view('services.ai-assistant.index');
    }

    /**
     * Process AI query
     */
    public function query(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:500'
        ]);

        try {
            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant for the Aura platform.'],
                    ['role' => 'user', 'content' => $request->input('query')]
                ]
            ]);

            $response = $result->choices[0]->message->content;

            return response()->json([
                'query' => $request->input('query'),
                'response' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to process your request. Please try again later.'
            ], 500);
        }
    }
}
