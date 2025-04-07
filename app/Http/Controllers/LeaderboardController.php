<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $activityType = $request->query('type'); // quiz_completed, poll_voted, etc.
        $days = $request->query('days'); // e.g., 7 for last 7 days

        $query = UserActivity::select('user_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id')
            ->orderByDesc('total_points');

        if ($activityType) {
            $query->where('activity_type', $activityType);
        }

        if ($days) {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        $leaderboard = $query->take(10)->get()->map(function ($row) {
            $user = User::find($row->user_id);

            return [
                'user_id' => $user->id,
                'name' => $user->name ?? 'Anonymous',
                'points' => $row->total_points,
            ];
        });

        return response()->json([
            'leaderboard' => $leaderboard,
        ]);
    }
}

