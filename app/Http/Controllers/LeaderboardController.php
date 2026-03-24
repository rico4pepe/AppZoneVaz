<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $activityType = $request->query('type');
        $days = $request->query('days');

        $query = UserActivity::query()
            ->select(
                'users.id as user_id',
                'users.name',
                DB::raw('SUM(user_activities.points) as total_points')
            )
            ->join('users', 'users.id', '=', 'user_activities.user_id')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_points');

        // 🔍 Filter by activity type
        if ($activityType) {
            $query->where('user_activities.activity_type', $activityType);
        }

        // 🔍 Filter by time range
        if ($days) {
            $query->where('user_activities.created_at', '>=', now()->subDays($days));
        }

        $leaders = $query->limit(10)->get();

        // 🏆 Attach rank
        $leaders = $leaders->values()->map(function ($user, $index) {
            return [
                'user_id' => $user->user_id,
                'name'    => $user->name ?? 'Anonymous',
                'points'  => (int) $user->total_points,
                'rank'    => $index + 1,
            ];
        });

        return response()->json([
            'leaderboard' => $leaders,
        ]);
    }

    public function myRank(Request $request)
    {
        $userId = auth()->id();

        $activityType = $request->query('type');
        $days = $request->query('days');

        // 🔹 Base query (same as leaderboard)
        $baseQuery = \App\Models\UserActivity::query()
            ->select(
                'users.id as user_id',
                DB::raw('SUM(user_activities.points) as total_points')
            )
            ->join('users', 'users.id', '=', 'user_activities.user_id')
            ->groupBy('users.id');

        if ($activityType) {
            $baseQuery->where('user_activities.activity_type', $activityType);
        }

        if ($days) {
            $baseQuery->where('user_activities.created_at', '>=', now()->subDays($days));
        }

        // 🔹 Get ranked list
        $users = $baseQuery
            ->orderByDesc('total_points')
            ->get();

        // 🔹 Find current user
        $index = $users->search(fn($u) => $u->user_id == $userId);

        if ($index === false) {
             return response()->json([
            'rank'        => null,
            'total_users' => $users->count(),
            'points'      => 0,
            'label'       => "You are not ranked yet",
        ]);
        }

       return response()->json([
        'rank'        => $index + 1,
        'total_users' => $users->count(),
        'points'      => (int) $users[$index]->total_points,
        'label'       => "You are ranked " . ($index + 1) . " out of " . $users->count(),
    ]);
    }
}

