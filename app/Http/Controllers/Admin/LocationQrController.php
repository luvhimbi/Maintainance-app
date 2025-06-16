<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LocationQrController extends Controller
{
    public function index()
    {
        $buildings = Building::with(['floors' => function($query) {
            $query->orderBy('floor_number');
        }, 'floors.rooms' => function($query) {
            $query->orderBy('room_number');
        }])->get();

        return view('admin.locations.index', compact('buildings'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'building_name' => 'required|string|max:100',
            'floor_number' => 'required|string|max:20',
            'room_number' => 'required|string|max:20',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            // Check if building exists
            $building = Building::where('building_name', $validatedData['building_name'])->first();
            
            if (!$building) {
                // Create new building
                $building = Building::create([
                    'building_name' => $validatedData['building_name'],
                    'latitude' => $validatedData['latitude'],
                    'longitude' => $validatedData['longitude']
                ]);
            }

            // Check if floor exists
            $floor = Floor::where('building_id', $building->building_id)
                         ->where('floor_number', $validatedData['floor_number'])
                         ->first();

            if (!$floor) {
                // Create new floor
                $floor = Floor::create([
                    'building_id' => $building->building_id,
                    'floor_number' => $validatedData['floor_number']
                ]);
            }

            // Check if room exists
            $existingRoom = Room::where('floor_id', $floor->floor_id)
                              ->where('room_number', $validatedData['room_number'])
                              ->first();

            if ($existingRoom) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This room already exists in the specified floor.');
            }

            // Create new room
            Room::create([
                'floor_id' => $floor->floor_id,
                'room_number' => $validatedData['room_number']
            ]);

            return redirect()->route('admin.locations.index')
                ->with('success', 'Location added successfully!');

        } catch (\Exception $e) {
            Log::error('Error adding location: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add location. Please try again.');
        }
    }

    public function edit($id)
    {
        $building = Building::with(['floors.rooms'])->findOrFail($id);
        return view('admin.locations.edit', compact('building'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'building_name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        try {
            $building = Building::findOrFail($id);
            $building->update($validated);

            return redirect()->route('admin.locations.index')
                ->with('success', 'Building updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating building: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update building. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $building = Building::findOrFail($id);
            $building->delete();

            return redirect()->route('admin.locations.index')
                ->with('success', 'Building and all associated floors and rooms deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting building: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete building. Please try again.');
        }
    }

    // Building Management Methods
    public function buildingsIndex()
    {
        $buildings = Building::with(['floors' => function($query) {
            $query->orderBy('floor_number');
        }])->get();
        
        return view('admin.buildings.index', compact('buildings'));
    }

    public function createBuilding()
    {
        return view('admin.buildings.create');
    }

    public function storeBuilding(Request $request)
    {
        $validatedData = $request->validate([
            'building_name' => 'required|string|max:100|unique:buildings,building_name',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            Building::create($validatedData);

            return redirect()->route('admin.buildings.index')
                ->with('success', 'Building added successfully!');
        } catch (\Exception $e) {
            Log::error('Error adding building: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add building. Please try again.');
        }
    }

    public function editBuilding($id)
    {
        $building = Building::findOrFail($id);
        return view('admin.buildings.edit', compact('building'));
    }

    public function updateBuilding(Request $request, $id)
    {
        $validatedData = $request->validate([
            'building_name' => 'required|string|max:100|unique:buildings,building_name,' . $id . ',id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            $building = Building::findOrFail($id);
            $building->update($validatedData);

            return redirect()->route('admin.buildings.index')
                ->with('success', 'Building updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating building: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update building. Please try again.');
        }
    }

    public function destroyBuilding($id)
    {
        try {
            $building = Building::findOrFail($id);
            $building->delete();

            return redirect()->route('admin.buildings.index')
                ->with('success', 'Building deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting building: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete building. Please try again.');
        }
    }

    // Floor Management Methods
    public function floorsIndex()
    {
        $floors = Floor::with(['building', 'rooms'])->get();
        return view('admin.floors.index', compact('floors'));
    }

    public function createFloor()
    {
        $buildings = Building::all();
        return view('admin.floors.create', compact('buildings'));
    }

    public function storeFloor(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'floor_number' => 'required|string|max:255',
            'room_numbers' => 'array',
            'room_numbers.*' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $floor = Floor::create([
                'building_id' => $request->building_id,
                'floor_number' => $request->floor_number
            ]);

            if ($request->has('room_numbers')) {
                foreach ($request->room_numbers as $roomNumber) {
                    Room::create([
                        'floor_id' => $floor->id,
                        'room_number' => $roomNumber
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.floors.index')
                ->with('success', 'Floor created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating floor: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editFloor(Floor $floor)
    {
        $buildings = Building::all();
        return view('admin.floors.edit', compact('floor', 'buildings'));
    }

    public function updateFloor(Request $request, Floor $floor)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'floor_number' => 'required|string|max:255',
            'room_numbers' => 'array',
            'room_numbers.*' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $floor->update([
                'building_id' => $request->building_id,
                'floor_number' => $request->floor_number
            ]);

            // Delete existing rooms
            $floor->rooms()->delete();

            // Create new rooms if provided
            if ($request->has('room_numbers')) {
                foreach ($request->room_numbers as $roomNumber) {
                    Room::create([
                        'floor_id' => $floor->id,
                        'room_number' => $roomNumber
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.floors.index')
                ->with('success', 'Floor updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating floor: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroyFloor(Floor $floor)
    {
        try {
            DB::beginTransaction();
            
            // Delete all associated rooms first
            $floor->rooms()->delete();
            
            // Delete the floor
            $floor->delete();
            
            DB::commit();
            return redirect()->route('admin.floors.index')
                ->with('success', 'Floor deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting floor: ' . $e->getMessage());
        }
    }
}
