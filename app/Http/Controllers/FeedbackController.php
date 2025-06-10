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
    public function store(Request $request, $issueId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();
        $issue = \App\Models\Issue::findOrFail($issueId);

        // Prevent duplicate feedback from the same user for the same issue
        $existing = \App\Models\Feedback::where('issue_id', $issueId)
            ->where('user_id', $user->user_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You have already submitted feedback for this issue.'
            ], 409);
        }

        $feedback = \App\Models\Feedback::create([
            'issue_id' => $issueId,
            'user_id' => $user->user_id,
            'rating' => $request->input('rating'),
            'comments' => $request->input('comments'),
        ]);

        return response()->json([
            'message' => 'Feedback submitted successfully.',
            'feedback' => $feedback
        ]);
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
