<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class LocationQrController extends Controller
{

    public function index()
    {
        $locations = Location::all();
        return view('admin.locations.index', compact('locations'));
    }
   /**
     * Show the form for creating a new location
     */
    public function create()
    {
        return view('admin.locations.create');
    }

    /**
     * Store a newly created location
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_name' => 'required|string|max:100',
            'floor_number' => 'required|string|max:20',
            'room_number' => 'required|string|max:20',
            'description' => 'nullable|string'
        ]);

        Location::create($validated);

        return redirect()->route('admin.locations.create')
            ->with('success', 'Location created successfully');
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
            'description' => 'nullable|string'
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
