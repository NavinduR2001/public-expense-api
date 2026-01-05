<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiKey;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    // Show all API keys for authenticated user
    public function index()
    {
        $apiKeys = ApiKey::where('user_id', auth()->id())
            ->latest()
            ->get();
        
        $totalKeys = $apiKeys->count();
        $activeKeys = $apiKeys->where('is_active', true)->count();

        // Add category count
        $categories = Category::count();

        // Add Expense Data
        $totalExpenses = Expense::count();

        return view('dashboard', compact('apiKeys', 'totalKeys', 'activeKeys', 'categories', 'totalExpenses'));
    }

    // Generate new API key
    public function store(Request $request)
    {
        // Generate a random string for the API key
        $plainKey = 'pk_live_' . Str::random(32);
        
        // Hash it before storing
        $hashedKey = hash('sha256', $plainKey);
        
        // Store the hashed version
        $apiKey = ApiKey::create([
            'user_id' => auth()->id(),
            'key' => $hashedKey,
            'is_active' => true,
        ]);

        // Return the plain key ONCE (user must save it)
        return redirect()->route('dashboard')
            ->with('success', 'API Key generated successfully!')
            ->with('api_key', $plainKey); // Show plain key only once
    }

    // Revoke API key
    public function destroy($id)
    {
        $apiKey = ApiKey::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $apiKey->update(['is_active' => false]);

        return redirect()->route('dashboard')
            ->with('success', 'API Key revoked successfully!');
    }
}
