<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Floor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{


    public function getFloors(Building $building): JsonResponse
    {
        try {
            Log::info('Fetching floors for building: ' . $building->id);

            if (!$building) {
                return response()->json([
                    'success' => false,
                    'message' => 'Building not found'
                ], 404);
            }

            $floors = $building->floors()
                ->orderBy('floor_number')
                ->get(['id', 'floor_number']);

            Log::info('Found floors:', ['count' => $floors->count()]);

            return response()->json([
                'success' => true,
                'data' => $floors
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching floors: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch floors: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRooms(Floor $floor): JsonResponse
    {
        try {
            Log::info('Fetching rooms for floor: ' . $floor->id);

            if (!$floor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Floor not found'
                ], 404);
            }

            $rooms = $floor->rooms()
                ->orderBy('room_number')
                ->get(['id', 'room_number']);

            Log::info('Found rooms:', ['count' => $rooms->count()]);

            return response()->json([
                'success' => true,
                'data' => $rooms
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching rooms: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rooms: ' . $e->getMessage()
            ], 500);
        }
    }
}
