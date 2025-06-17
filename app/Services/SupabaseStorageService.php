<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseStorageService
{
    protected $supabaseUrl;
    protected $supabaseKey;
    protected $bucketName;

    public function __construct()
    {
        // Remove any trailing slashes and ensure we're using the project URL
        $this->supabaseUrl = rtrim(config('services.supabase.url'), '/');
        $this->supabaseKey = config('services.supabase.key');
        $this->bucketName = config('services.supabase.bucket');

        // Log configuration for debugging
        Log::info('Supabase Configuration:', [
            'url' => $this->supabaseUrl,
            'bucket' => $this->bucketName,
            'key_length' => strlen($this->supabaseKey)
        ]);
    }

    public function uploadFile($file, $path = '')
    {
        try {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $path ? $path . '/' . $fileName : $fileName;
            
            // Log file details for debugging
            Log::info('Attempting to upload file:', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $filePath
            ]);

            // Create a temporary file
            $tempFile = tmpfile();
            $tempFilePath = stream_get_meta_data($tempFile)['uri'];
            file_put_contents($tempFilePath, file_get_contents($file->getRealPath()));

            // Initialize cURL
            $ch = curl_init();
            
            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, "{$this->supabaseUrl}/storage/v1/object/onlinebucket/{$filePath}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->supabaseKey,
                'apikey: ' . $this->supabaseKey,
                'Content-Type: ' . $file->getMimeType()
            ]);

            // Set file data
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($tempFilePath));

            // Execute cURL request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Check for cURL errors
            if (curl_errno($ch)) {
                throw new \Exception('cURL Error: ' . curl_error($ch));
            }
            
            // Close cURL and clean up temp file
            curl_close($ch);
            fclose($tempFile);

            // Log the response for debugging
            Log::info('Supabase API Response:', [
                'status' => $httpCode,
                'body' => $response
            ]);

            if ($httpCode >= 200 && $httpCode < 300) {
                $publicUrl = "{$this->supabaseUrl}/storage/v1/object/public/onlinebucket/{$filePath}";
                return [
                    'success' => true,
                    'path' => $publicUrl,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ];
            }

            $errorMessage = 'Upload failed with status: ' . $httpCode . ' - ' . $response;
            Log::error('Supabase upload failed: ' . $errorMessage);
            return ['success' => false, 'error' => $errorMessage];
        } catch (\Exception $e) {
            $errorMessage = 'Supabase upload error: ' . $e->getMessage();
            Log::error($errorMessage);
            return ['success' => false, 'error' => $errorMessage];
        }
    }

    public function deleteFile($path)
    {
        try {
            // Extract the file path from the public URL
            $filePath = str_replace("{$this->supabaseUrl}/storage/v1/object/public/{$this->bucketName}/", '', $path);
            
            Log::info('Attempting to delete file:', ['path' => $filePath]);

            $response = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey
            ])->withOptions([
                'verify' => false // Disable SSL verification temporarily
            ])->delete(
                "{$this->supabaseUrl}/storage/v1/object/{$this->bucketName}/{$filePath}"
            );

            Log::info('Supabase delete response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Supabase delete error: ' . $e->getMessage());
            return false;
        }
    }

    public function getPublicUrl($path)
    {
        return "{$this->supabaseUrl}/storage/v1/object/public/{$this->bucketName}/{$path}";
    }
} 