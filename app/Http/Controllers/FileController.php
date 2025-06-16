<?php

namespace App\Http\Controllers;

use App\Models\IssueAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function view($id)
    {
        $attachment = IssueAttachment::findOrFail($id);
        
        // Check if user has permission to view this file
        if (!$this->canAccessFile($attachment)) {
            abort(403, 'Unauthorized access to file.');
        }

        // Get the file path and check if it exists
        $path = $attachment->file_path;
        if (!Storage::disk($attachment->storage_disk)->exists($path)) {
            abort(404, 'File not found.');
        }

        // Get the file contents
        $file = Storage::disk($attachment->storage_disk)->get($path);
        
        // Return the file with appropriate headers
        return response($file, 200)
            ->header('Content-Type', $attachment->mime_type)
            ->header('Content-Disposition', 'inline; filename="' . $attachment->original_name . '"');
    }

    public function download($id)
    {
        $attachment = IssueAttachment::findOrFail($id);
        
        // Check if user has permission to download this file
        if (!$this->canAccessFile($attachment)) {
            abort(403, 'Unauthorized access to file.');
        }

        // Get the file path and check if it exists
        $path = $attachment->file_path;
        if (!Storage::disk($attachment->storage_disk)->exists($path)) {
            abort(404, 'File not found.');
        }

        // Return the file download response
        return Storage::disk($attachment->storage_disk)->download(
            $path,
            $attachment->original_name
        );
    }

    private function canAccessFile($attachment)
    {
        $user = Auth::user();
        
        // Admins can access all files
        if ($user->user_role === 'Admin') {
            return true;
        }

        // Get the issue associated with this attachment
        $issue = $attachment->issue;

        // If user is the reporter of the issue, they can access the file
        if ($issue->reporter_id === $user->user_id) {
            return true;
        }

        // If user is a technician assigned to the issue's task, they can access the file
        if ($user->user_role === 'Technician' && $issue->task && $issue->task->assignee_id === $user->user_id) {
            return true;
        }

        return false;
    }
} 