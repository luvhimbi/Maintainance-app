<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Excel;
use App\Exports\FeedbacksExport;
class FeedbackController extends Controller
{
    public function store(Request $request, Issue $issue)
    {
        // Validate the request
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comments' => 'nullable|string|max:500'
        ]);

        // Ensure issue is resolved
        if ($issue->issue_status !== 'Resolved') {
            return back()->with('error', 'Feedback can only be submitted for resolved issues.');
        }

        // Ensure user hasn't already submitted feedback
        if ($issue->hasFeedbackFrom(Auth::user())) {
            return back()->with('error', 'You have already submitted feedback for this issue.');
        }

        // Create feedback
        Feedback::create([
            'issue_id' => $issue->issue_id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comments' => $request->comments
        ]);

        return back()->with('success', 'Thank you for your feedback!');
    }

       public function index(Request $request)
    {
      
        
        $query = Feedback::with(['user', 'issue'])
            ->orderBy('created_at', 'desc');

       

        $feedbacks = $query->paginate(15);

        return view('admin.feedbacks.index', compact('feedbacks'));
    }

    /**
     * Export feedback to Excel
     */
    public function export() 
    {
        // $this->authorize('export', Feedback::class);
        
        return Excel::download(new FeedbacksExport, 'feedbacks-'.now()->format('Y-m-d').'.xlsx');
    }

    /**
     * Show feedback statistics
     */
    public function stats()
    {
        // $this->authorize('viewStats', Feedback::class);
        
        $stats = [
            'total' => Feedback::count(),
            'average_rating' => round(Feedback::avg('rating'), 2),
            'rating_distribution' => Feedback::selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->orderBy('rating')
                ->get(),
            'latest_feedbacks' => Feedback::with(['user', 'issue'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];

        return view('admin.feedbacks.stats', compact('stats'));
    }
}
