<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Issue;
use App\Models\Comment;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $userId = Auth::id();

        // Get search and status filters from request
        $search = $request->input('search');
        $statuses = (array) $request->input('status', ['open', 'in progress']);

        $issues = Issue::where('reporter_id', $userId)
            ->where(function($q) use ($statuses) {
                $q->whereIn(\DB::raw('LOWER(issue_status)'), array_map('strtolower', $statuses));
            })
            ->when($search, function($q) use ($search) {
                $q->where(function($sub) use ($search) {
                    $sub->where('issue_type', 'like', "%$search%")
                        ->orWhere('issue_description', 'like', "%$search%")
                        ->orWhereHas('location', function($loc) use ($search) {
                            $loc->where('building_name', 'like', "%$search%")
                                ->orWhere('room_number', 'like', "%$search%");
                        });
                });
            })
            ->with('location')
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        // Keep query params in pagination links
        $issues->appends($request->all());

        return view('Student.dashboard', compact('issues'));
    }

}
