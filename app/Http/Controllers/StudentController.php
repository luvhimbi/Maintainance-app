<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Issue;
use App\Models\Comment;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $userId = Auth::id();

        // Get search and status filters from request
        $search = $request->input('search');
        $statuses = (array) $request->input('status', ['Open', 'In Progress']);

        $issues = Issue::where('reporter_id', $userId)
            ->where(function($q) use ($statuses) {
                $q->whereIn('issue_status', $statuses);
            })
            ->when($search, function($q) use ($search) {
                $q->where(function($sub) use ($search) {
                    $sub->whereRaw('LOWER(issue_type) LIKE ?', ["%" . strtolower($search) . "%"])
                        ->orWhereRaw('LOWER(issue_description) LIKE ?', ["%" . strtolower($search) . "%"])
                        ->orWhereHas('room', function($room) use ($search) {
                            $room->whereHas('floor', function($floor) use ($search) {
                                $floor->whereHas('building', function($building) use ($search) {
                                    $building->whereRaw('LOWER(building_name) LIKE ?', ["%" . strtolower($search) . "%"]);
                                });
                            })
                            ->orWhere('room_number', 'like', "%$search%");
                        });
                });
            })
            ->with(['room.floor.building'])
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        // Keep query params in pagination links
        $issues->appends($request->all());

        return view('Student.dashboard', compact('issues'));
    }

}
