<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class LocationQrController extends Controller
{

    public function index()
    {
        $locations = Location::paginate(5); // Add pagination with 10 items per page
        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create', [
            'mapboxAccessToken' => config('services.mapbox.access_token'),
            'mapboxStyle' => config('services.mapbox.style', 'mapbox://styles/mapbox/streets-v11'),
            'defaultLatitude' => -25.540672986478395,
            'defaultLongitude' => 28.097913893018625
        ]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'building_name' => 'required|string|max:100',
            'floor_number' => 'nullable|string|max:20',
            'room_number' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        try {
            // Check for existing location with the same combination of attributes
            $existingLocation = Location::where('building_name', $validatedData['building_name'])
                ->where('floor_number', $validatedData['floor_number'])
                ->where('room_number', $validatedData['room_number'])
                ->where('latitude', $validatedData['latitude'])
                ->where('longitude', $validatedData['longitude'])
                ->first();

            if ($existingLocation) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This location already exists in our system.');
            }

            // Create a new Location instance and fill it with validated data
            $location = new Location();
            $location->building_name = $validatedData['building_name'];
            $location->floor_number = $validatedData['floor_number'];
            $location->room_number = $validatedData['room_number'];
            $location->description = $validatedData['description'];
            $location->latitude = $validatedData['latitude'];
            $location->longitude = $validatedData['longitude'];
            $location->save();

            // Redirect back with a success message to the locations list
            return redirect()->route('admin.locations.index')->with('success', 'Location added successfully!');

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error adding location: ' . $e->getMessage());
            // Redirect back with an error message, preserving input
            return redirect()->back()->withInput()->with('error', 'Failed to add location. Please try again.');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error adding location: ' . $e->getMessage());
            // Redirect back with an error message, preserving input
            return redirect()->back()->withInput()->with('error', 'Failed to add location. Please try again.');
        }
    }
    /**
     * Show the form for editing a location
     */
    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    /**
     * Update the specified location
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'building_name' => 'required|string|max:100',
            'floor_number' => 'required|string|max:20',
            'room_number' => 'required|string|max:20',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        $location->update($validated);

        return redirect()->route('admin.locations.edit', $location->location_id)
            ->with('success', 'Location updated successfully');
    }

    /**
     * Remove the specified location
     */
    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully');
    }
}
