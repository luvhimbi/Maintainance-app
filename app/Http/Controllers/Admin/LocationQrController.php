<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Validation\Rule;

class LocationQrController extends Controller
{

    public function index()
    {
        $locations = Location::paginate(5); // Add pagination with 10 items per page
        return view('admin.locations.index', compact('locations'));
    }
  
    /**
     * Store a newly created location
     */
  

public function store(Request $request)
{
    $validated = $request->validate([
        'building_name' => [
            'required',
            'string',
            'max:100',
            Rule::unique('location')->where(function ($query) use ($request) {
                return $query->where('floor_number', $request->floor_number)
                            ->where('room_number', $request->room_number);
            })
        ],
        'floor_number' => 'required|string|max:20',
        'room_number' => 'required|string|max:20',
        'description' => 'nullable|string'
    ], [
        'building_name.unique' => 'This location combination (building, floor, room) already exists.'
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
