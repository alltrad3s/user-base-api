<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

class AdminController extends Controller
{
    //
    public function getStatistics(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'period' => 'required|string|in:daily,weekly,monthly,custom',
                'date' => 'required_if:period,daily|date',
                'start_date' => 'required_if:period,custom|date',
                'end_date' => 'required_if:period,custom|date|after_or_equal:start_date',
            ]);

            $query = User::query();

            switch ($request->period) {
                case 'daily':
                    $date = $request->date ? Carbon::parse($request->date) : today();
                    $stats = $this->getDailyStats($date);
                    break;

                case 'weekly':
                    $stats = $this->getWeeklyStats();
                    break;

                case 'monthly':
                    $stats = $this->getMonthlyStats();
                    break;

                case 'custom':
                    $stats = $this->getCustomPeriodStats(
                        Carbon::parse($request->start_date),
                        Carbon::parse($request->end_date)
                    );
                    break;
            }

            return response()->json($stats);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Failed to fetch statistics: {$e->getMessage()}"
            ], 500);
        }
    }

    private function getDailyStats($date)
    {
        // Get hourly breakdown for the specified day
        $hourlyBreakdown = User::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->whereDate('created_at', $date)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Fill in missing hours with zero counts
        $completeHourlyBreakdown = collect(range(0, 23))->map(function ($hour) use ($hourlyBreakdown) {
            $hourData = $hourlyBreakdown->firstWhere('hour', $hour);
            return [
                'hour' => $hour,
                'count' => $hourData ? $hourData->count : 0
            ];
        });

        return [
            'period' => 'daily',
            'date' => $date->toDateString(),
            'total_users' => $hourlyBreakdown->sum('count'),
            'hourly_breakdown' => $completeHourlyBreakdown
        ];
    }

    private function getWeeklyStats()
    {
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $dailyBreakdown = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates with zero counts
        $completeDailyBreakdown = collect(range(0, 6))->map(function ($daysAgo) use ($dailyBreakdown) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            $dayData = $dailyBreakdown->firstWhere('date', $date);
            return [
                'date' => $date,
                'count' => $dayData ? $dayData->count : 0
            ];
        })->sortBy('date')->values();

        return [
            'period' => 'weekly',
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_users' => $dailyBreakdown->sum('count'),
            'daily_breakdown' => $completeDailyBreakdown
        ];
    }

    private function getMonthlyStats()
    {
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        $dailyBreakdown = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates with zero counts
        $completeDailyBreakdown = collect(range(0, 29))->map(function ($daysAgo) use ($dailyBreakdown) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            $dayData = $dailyBreakdown->firstWhere('date', $date);
            return [
                'date' => $date,
                'count' => $dayData ? $dayData->count : 0
            ];
        })->sortBy('date')->values();

        return [
            'period' => 'monthly',
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_users' => $dailyBreakdown->sum('count'),
            'daily_breakdown' => $completeDailyBreakdown
        ];
    }

    private function getCustomPeriodStats($startDate, $endDate)
    {
        $dailyBreakdown = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates
        $days = $startDate->diffInDays($endDate);
        $completeDailyBreakdown = collect(range(0, $days))->map(function ($day) use ($startDate, $dailyBreakdown) {
            $date = $startDate->copy()->addDays($day)->format('Y-m-d');
            $dayData = $dailyBreakdown->firstWhere('date', $date);
            return [
                'date' => $date,
                'count' => $dayData ? $dayData->count : 0
            ];
        });

        return [
            'period' => 'custom',
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_users' => $dailyBreakdown->sum('count'),
            'daily_breakdown' => $completeDailyBreakdown
        ];
    }

    // List all users
    public function index()
    {
        try {
            $users = User::orderBy('created_at', 'desc')->paginate(10);
            return response()->json($users);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Failed to fetch users: {$e->getMessage()}"
            ], 500);
        }
    }

    // Create user (admin)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|string|in:admin,user,moderator'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role
            ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Failed to create user: {$e->getMessage()}"
            ], 500);
        }
    }

    // Update user
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users,email,'.$id,
                'role' => 'string|in:admin,user,moderator',
                'password' => 'string|min:8'
            ]);

            $user->update($request->all());

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Failed to update user: {$e->getMessage()}"
            ], 500);
        }
    }

    // Delete user
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Failed to delete user: {$e->getMessage()}"
            ], 500);
        }
    }
}
