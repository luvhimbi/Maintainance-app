<?php

namespace App\Http\Controllers;

use App\Models\IssueAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    public function view($id)
    {
        $attachment = IssueAttachment::findOrFail($id);
        
        // Check if user has permission to view this file
        if (!$this->canAccessFile($attachment)) {
            abort(403, 'Unauthorized access to file.');
        }

        // Get the file path
        $path = $attachment->file_path;
        Log::info('Attempting to view file', [
            'id' => $id,
            'path' => $path,
            'disk' => $attachment->storage_disk,
            'is_url' => str_starts_with($path, 'http')
        ]);

        // If it's a Supabase URL, redirect to it directly
        if (str_starts_with($path, 'http')) {
            Log::info('Redirecting to Supabase URL', ['url' => $path]);
            return redirect($path);
        } else {
            $publicUrl = "{$this->supabaseUrl}/storage/v1/object/public/onlinebucket/{$path}";
            return redirect($publicUrl);
        }

        // For local files, use the storage disk
        if (!Storage::disk($attachment->storage_disk)->exists($path)) {
            Log::error('File not found in storage', [
                'id' => $id,
                'path' => $path,
                'disk' => $attachment->storage_disk,
                'storage_path' => Storage::disk($attachment->storage_disk)->path($path)
            ]);
            abort(404, 'File not found.');
        }

        // Get the file contents
        $file = Storage::disk($attachment->storage_disk)->get($path);
        
        // Return the file with appropriate headers
        return response($file, 200)
            ->header('Content-Type', $attachment->mime_type)
            ->header('Content-Disposition', 'inline; filename="' . $attachment->original_name . '"')
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    public function publicView($filename)
    {
        // Find the attachment by filename
        $attachment = IssueAttachment::where('file_path', 'like', '%' . $filename)->first();
        
        if (!$attachment) {
            Log::error('File not found in database', ['filename' => $filename]);
            abort(404, 'File not found.');
        }

        // Check if user has permission to view this file
        if (!$this->canAccessFile($attachment)) {
            abort(403, 'Unauthorized access to file.');
        }

        $path = $attachment->file_path;
        
        // If it's a Supabase URL, redirect to it directly
        if (str_starts_with($path, 'http')) {
            return redirect($path);
        } else {
            $publicUrl = "{$this->supabaseUrl}/storage/v1/object/public/onlinebucket/{$path}";
            return redirect($publicUrl);
        }

        // For local files, use the storage disk
        if (!Storage::disk($attachment->storage_disk)->exists($path)) {
            Log::error('File not found in storage', [
                'filename' => $filename,
                'path' => $path,
                'disk' => $attachment->storage_disk
            ]);
            abort(404, 'File not found.');
        }

        $file = Storage::disk($attachment->storage_disk)->get($path);
        
        return response($file, 200)
            ->header('Content-Type', $attachment->mime_type)
            ->header('Content-Disposition', 'inline; filename="' . $attachment->original_name . '"')
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    public function download($id)
    {
        $attachment = IssueAttachment::findOrFail($id);
        
        // Check if user has permission to download this file
        if (!$this->canAccessFile($attachment)) {
            abort(403, 'Unauthorized access to file.');
        }

        // Get the file path
        $path = $attachment->file_path;
        Log::info('Attempting to download file', [
            'id' => $id,
            'path' => $path,
            'disk' => $attachment->storage_disk,
            'is_url' => str_starts_with($path, 'http')
        ]);

        // If it's a Supabase URL, redirect to it directly
        if (str_starts_with($path, 'http')) {
            Log::info('Redirecting to Supabase URL for download', ['url' => $path]);
            // Use a different approach to force download
            return response()->streamDownload(function () use ($path) {
                echo file_get_contents($path);
            }, $attachment->original_name, [
                'Content-Type' => $attachment->mime_type,
                'Content-Disposition' => 'attachment; filename="' . $attachment->original_name . '"',
                'Cache-Control' => 'public, max-age=31536000'
            ]);
        } else {
            $publicUrl = "{$this->supabaseUrl}/storage/v1/object/public/onlinebucket/{$path}";
            return redirect($publicUrl);
        }

        // For local files, use the storage disk
        if (!Storage::disk($attachment->storage_disk)->exists($path)) {
            Log::error('File not found in storage for download', [
                'id' => $id,
                'path' => $path,
                'disk' => $attachment->storage_disk,
                'storage_path' => Storage::disk($attachment->storage_disk)->path($path)
            ]);
            abort(404, 'File not found.');
        }

        // Get the file contents
        $file = Storage::disk($attachment->storage_disk)->get($path);
        
        // Return the file with appropriate headers
        return response($file, 200)
            ->header('Content-Type', $attachment->mime_type)
            ->header('Content-Disposition', 'attachment; filename="' . $attachment->original_name . '"')
            ->header('Cache-Control', 'public, max-age=31536000');
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