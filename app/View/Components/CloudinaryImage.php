<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Exception;

class CloudinaryImage extends Component
{
    public $publicId;
    public $alt;
    public $class;
    public $style;
    public $width;
    public $height;

    public function __construct($publicId, $alt = '', $class = '', $style = '', $width = null, $height = null)
    {
        $this->publicId = $publicId;
        $this->alt = $alt;
        $this->class = $class;
        $this->style = $style;
        $this->width = $width;
        $this->height = $height;

        // Initialize Cloudinary configuration using filesystems config
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('filesystems.disks.cloudinary.cloud'),
                'api_key' => config('filesystems.disks.cloudinary.key'),
                'api_secret' => config('filesystems.disks.cloudinary.secret'),
            ],
            'url' => [
                'secure' => config('filesystems.disks.cloudinary.secure', true),
            ],
        ]);
    }

    public function render()
    {
        try {
            $cloudinary = new Cloudinary();
            
            // Build the image URL manually if the SDK method fails
            $cloudName = config('filesystems.disks.cloudinary.cloud');
            
            // Use the public ID directly without adding 'assets/' prefix
            $publicId = $this->publicId;
            
            $imageUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$publicId}";
            
            // Try to get the URL from the SDK first
            try {
                $imageUrl = $cloudinary->image($publicId)->toUrl();
            } catch (Exception $e) {
                // If SDK fails, use the manually constructed URL
                \Log::warning('Cloudinary SDK URL generation failed, using fallback URL: ' . $e->getMessage());
            }

            return view('components.cloudinary-image', [
                'imageUrl' => $imageUrl,
                'alt' => $this->alt,
                'class' => $this->class,
                'style' => $this->style,
                'width' => $this->width,
                'height' => $this->height,
            ]);
        } catch (Exception $e) {
            \Log::error('Cloudinary image component error: ' . $e->getMessage());
            
            // Return a fallback view or empty image if everything fails
            return view('components.cloudinary-image', [
                'imageUrl' => asset('images/images.png'), // Fallback to local image
                'alt' => $this->alt,
                'class' => $this->class,
                'style' => $this->style,
                'width' => $this->width,
                'height' => $this->height,
            ]);
        }
    }
} 