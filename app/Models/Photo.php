<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $fillable = [
        'disk',
        'path',
        'scaledPath',
        'thumbPath',
        'file_name',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'exif',
        'image',
        'product',
        'size',
        'type',
        'installationStatus',
        'confidence',
        'tensorStatus',
        'analysisStatus',
    ];
    protected $casts = [
        'exif' => 'array',
        'confidence' => 'decimal:2',
    ];


    // Convenience accessor
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }


    protected $appends = ['thumb_url', 'scaled_url', 'original_url'];

    public function getThumbUrlAttribute(): ?string
    {
        return $this->path_thumb
            ? Storage::disk($this->disk)->url($this->path_thumb)
            : null;
    }

    public function getScaledUrlAttribute(): ?string
    {
        return $this->path_scaled
            ? Storage::disk($this->disk)->url($this->path_scaled)
            : null;
    }

    public function getOriginalUrlAttribute(): ?string
    {
        return $this->path_original
            ? Storage::disk($this->disk)->url($this->path_original)
            : null;
    }
}

