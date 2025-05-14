<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SubscriptionRenewal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class UserController extends Controller
{
    /**
     * Register a new user.
     */

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number',
            'plan' => 'required|string|in:Daily,Weekly,Monthly',
        ]);

        //dd($request->all());
        // Determine expiration date based on plan
        $subscriptionDuration = [
            'Daily' => Carbon::now()->addDay(),
            'Weekly' => Carbon::now()->addWeek(),
            'Monthly' => Carbon::now()->addMonth(),
        ];

        // Generate a token if the user is on WiFi
        $token = $request->has('wifi') ? bin2hex(random_bytes(5)) : null;

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number, // Add this line
            'plan' => $request->plan,
            'subscribed_at' => Carbon::now(),
            'expires_at' => $subscriptionDuration[$request->plan],
            'token' => $token,
        ]);

        return response()->json([
            'message' => 'User registered successfully. Please log in.',
            'phone_number' => $user->phone_number,
            'token' => $token ?? 'Not required (using mobile data)'
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|exists:users,phone_number',
            'token' => 'nullable|string'
        ]);
    
        // Fetch user
        $user = User::where('phone_number', $request->phone_number)->first();

          // Check if user's subscription has expired
    if ($user->expires_at && now()->greaterThan($user->expires_at)) {
        return response()->json([
            'message' => 'Your subscription has expired. Please renew your data plan to continue.',
            'expired' => true
        ], 403);
    }
    
        // Token verification (WiFi use)
        if ($request->has('token') && $user->token !== $request->token) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
    
        // Prevent login if already logged in on another device
        if ($user->tokens()->count() > 0) {
            return response()->json([
                'message' => 'Already logged in on another device. Please log out first.'
            ], 403);
        }
    
        // Generate personal access token (for API use)
        $token = $user->createToken('auth_token')->plainTextToken;
    
         // Check if it's an API request
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'user' => $user,
                'token' => $token,
                'redirect' => route('dashboard')
            ]);
        }
        // 🔥 FIX: Laravel Sanctum doesn’t auto-login to session, so if you want session-based login:
        auth()->login($user); // create Laravel session
    
        // Now redirect to dashboard (HTML)
        return redirect()->route('dashboard')->with('auth_token', $token);
    }

    public function updateUserPreference(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'team_id' => 'required|integer',
        ]);
    
        $userId = auth()->id();
    
        if ($userId) {

            DB::enableQueryLog();

            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'name' => $request->username,
                    'team_id' => $request->team_id,
                    'updated_at' => now(),
                ]);

                  // Get the executed queries
        $queries = DB::getQueryLog();
        
        // Log the queries to Laravel's log file
        Log::info('User preference update query:', $queries);
    
                return redirect()->route('dashboard')->with('success', 'Fan profile updated successfully!');
        }
    
        return redirect()->back()->with('error', 'User not authenticated.');
    }
    /**
     * Logout the user.
     */    

public function logout(Request $request)
{
    $request->user()->tokens()->delete(); // API token logout

    auth()->logout(); // session logout
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}

 /**
     * Check if the user's subscription is still active.
     */
    public function checkSubscription(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if subscription is active
        $isActive = $user->expires_at && Carbon::now()->lessThan($user->expires_at);

        return response()->json([
            'subscription_active' => $isActive,
            'plan' => $user->plan,
            'subscribed_at' => $user->subscribed_at,
            'expires_at' => $user->expires_at,
            'days_remaining' => $isActive ? Carbon::now()->diffInDays($user->expires_at) : 0,
        ]);
    }

     /**
     * Renew the user's subscription using a data plan purchase code.
     */
    public function renewSubscription(Request $request)
    {
        $request->validate([
            'purchase_code' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validate the purchase code with an external API or database
        $isValid = $this->validatePurchaseCode($request->purchase_code);

        if (!$isValid) {
            return response()->json(['message' => 'Invalid or expired purchase code'], 400);
        }

           // Validate the purchase code
         $newPlan = $this->validatePurchaseCode($request->purchase_code);

        if (!$newPlan) {
             return response()->json(['message' => 'Invalid or expired purchase code'], 400);
        }

            // Store the previous expiry date before renewal
            $previousExpiresAt = $user->expires_at ?? Carbon::now();

        // Extend subscription based on current plan
            $subscriptionDuration = [
            'Daily' => Carbon::now()->addDay(),
            'Weekly' => Carbon::now()->addWeek(),
            'Monthly' => Carbon::now()->addMonth(),
        ];

        $user->plan = $newPlan;
        $user->subscribed_at = Carbon::now();
        $user->expires_at = $subscriptionDuration[$user->plan];
        // Using update() instead of save()
        $user->subscribed_at = Carbon::now();
        $user->expires_at = $subscriptionDuration[$user->plan] ?? Carbon::now()->addDay();
        $user->save();


            // Log the renewal in the subscription_renewals table
            SubscriptionRenewal::create([
                'user_id' => $user->id,
                'purchase_code' => $request->purchase_code,
                'previous_expires_at' => $previousExpiresAt,
                'new_expires_at' => $user->expires_at
            ]);

                return response()->json([
                    'message' => 'Subscription renewed successfully',
                    'new_expires_at' => $user->expires_at
                ], 200);
    }

    /**
     * Validate the purchase code (mocking an external API call).
     */
    private function validatePurchaseCode($code)
{
    // Simulated API response with valid purchase codes
    $validCodes = [
        'Daily' => 'Daily',
        'Weekly' => 'Weekly',
        'Monthly' => 'Monthly',
    ];

    return $validCodes[$code] ?? false; // Return plan name or false if invalid
}


public function toggleAutoRenew(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $user->auto_renew = !$user->auto_renew; // Toggle the auto-renewal status
    $user->save();

    return response()->json([
        'message' => $user->auto_renew ? 'Auto-renewal enabled' : 'Auto-renewal disabled',
        'auto_renew' => $user->auto_renew
    ]);
}

public function getRenewalLogs()
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $logs = $user->subscriptionRenewalLogs()->orderBy('created_at', 'desc')->get();
   // $logs = User::with('subscriptionRenewalLogs')->orderBy('created_at', 'desc')->get();

    return response()->json($logs);
}

public function stopAutoRenewal()
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Disable auto-renewal
    $user->auto_renew = false;
    auth()->$user->save();

    return response()->json([
        'message' => 'Auto-renewal has been disabled successfully.',
        'auto_renew' => $user->auto_renew
    ]);
}

public function saveUserTeam(Request $request)
{
    $request->validate([
        'team_id' => 'required|integer', // Ensure team_id is provided
    ]);

    $user = Auth::user();
    $user->team_id = $request->team_id;
    $user->save();

    return response()->json(['message' => 'Team selection saved successfully!']);
}


public function fullReport(Request $request)
{
    $users = User::with(['team', 'subscriptionRenewalLogs'])
        ->withCount([
            'activities as quiz_count' => fn ($q) => $q->where('activity_type', 'quiz_completed'),
            'activities as trivia_count' => fn ($q) => $q->where('activity_type', 'trivia_completed'),
            'pollVotes'
        ])
          ->when($request->plan, fn($q) => $q->where('plan', $request->plan))
        ->when($request->team, fn($q) => $q->where('team', $request->team))
        ->when($request->status, function ($q) use ($request) {
            $now = now();
            return $request->status === 'active'
                ? $q->where('expires_at', '>', $now)
                : $q->where('expires_at', '<=', $now);
        })
    ->when($request->search, function ($q) use ($request) {
        $term = $request->search;
        $q->where(function ($query) use ($term) {
            $query->where('name', 'like', "%$term%")
                  ->orWhere('phone_number', 'like', "%$term%");
        });
    })
    ->latest()
    ->paginate(25);
    
    return view('admin.reports.users', compact('users'));
}



}




