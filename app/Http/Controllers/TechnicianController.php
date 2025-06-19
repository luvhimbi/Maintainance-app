<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Models\Issue;
use App\Models\TaskUpdate;
use App\Models\Comment;
use Illuminate\Support\Facades\Http;

class TechnicianController extends Controller
{

    public function dashboard()
{
    // Get the authenticated user's ID
    $userId = Auth::id();

    // Fetch tasks assigned to the logged-in technician
    $tasks = Task::where('assignee_id', $userId)->get();

    // Count tasks by status
    $pendingTasks = $tasks->where('issue_status', 'Pending')->count();
    $inProgressTasks = $tasks->where('issue_status', 'In Progress')->count();
    $completedTasks = $tasks->where('issue_status', 'Completed')->count();

    // Get overdue tasks (where expected completion date is past and status isn't Completed)
    $overdueTasks = $tasks->filter(function($task) {
        return $task->expected_completion->isPast() && $task->issue_status != 'Completed';
    });

    // Pass data to the view
    return view('technician.dashboard', [
        'tasks' => $tasks,
        'pendingTasks' => $pendingTasks,
        'inProgressTasks' => $inProgressTasks,
        'completedTasks' => $completedTasks,
        'overdueTasks' => $overdueTasks,
        'overdueCount' => $overdueTasks->count()
    ]);
}


public function directions()
    {
        $defaultLocation = [
            'lat' => -25.53978422415537,
            'lng' => 28.098271679102634
        ];

        return view('technician.directions', [
            'mapboxAccessToken' => config('services.mapbox.access_token'),
            'mapboxStyle' => config('services.mapbox.style', 'mapbox://styles/mapbox/streets-v11'),
            'defaultLocation' => $defaultLocation
        ]);
    }

    public function getRoute(Request $request)
    {
        try {
            $request->validate([
                'origin' => 'required|string',
                'destination' => 'required|string',
                'profile' => 'required|in:walking,driving,cycling'
            ]);

            // Parse coordinates
            $origin = explode(',', $request->origin);
            $destination = explode(',', $request->destination);

            // Calculate direct distance between points
            $originPoint = ['lng' => floatval($origin[0]), 'lat' => floatval($origin[1])];
            $destPoint = ['lng' => floatval($destination[0]), 'lat' => floatval($destination[1])];

            // Calculate distance using Haversine formula
            $distance = $this->calculateDistance($originPoint, $destPoint);

            // If distance is too large, return error
            if ($distance > 100) { // 100km limit
                return response()->json([
                    'error' => 'Route too long',
                    'message' => 'The selected route exceeds the maximum distance limit of 100km. Please select points closer together.',
                    'distance' => $distance
                ], 400);
            }

            // Make request to Mapbox Directions API with SSL verification disabled
            $response = Http::withOptions([
                'verify' => false
            ])->get("https://api.mapbox.com/directions/v5/mapbox/{$request->profile}/{$origin[0]},{$origin[1]};{$destination[0]},{$destination[1]}", [
                'geometries' => 'geojson',
                'steps' => 'true',
                'overview' => 'full',
                'access_token' => config('services.mapbox.access_token'),
                'alternatives' => 'true', // Get alternative routes
                'annotations' => 'distance,duration', // Get detailed distance and duration
                'language' => 'en' // English instructions
            ]);

            if (!$response->successful()) {
                \Log::error('Mapbox API Error:', [
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);

                $errorData = $response->json();
                $errorMessage = 'Failed to get route from Mapbox';

                if (isset($errorData['message'])) {
                    if (strpos($errorData['message'], 'maximum distance') !== false) {
                        $errorMessage = 'The selected route is too long. Please select points closer together.';
                    } elseif (strpos($errorData['message'], 'No route found') !== false) {
                        $errorMessage = 'No route found between the selected points. Please try different locations.';
                    }
                }

                return response()->json([
                    'error' => $errorMessage,
                    'details' => $errorData
                ], $response->status());
            }

            return $response->json();

        } catch (\Exception $e) {
            \Log::error('Route Calculation Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to calculate route',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($point1, $point2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $lat1 = deg2rad($point1['lat']);
        $lon1 = deg2rad($point1['lng']);
        $lat2 = deg2rad($point2['lat']);
        $lon2 = deg2rad($point2['lng']);

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius; // Distance in km
    }


    public function viewTaskDetails($task_id)
    {

        $task = Task::with([
                'issue.location', // Include issue and its location
                'issue.attachments', // Include issue attachments
                'assignee'

            ])
            ->findOrFail($task_id);

        // Ensure the authenticated user is the assignee
        if ($task->assignee_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('technician.task_details', compact('task'));
    }
}
