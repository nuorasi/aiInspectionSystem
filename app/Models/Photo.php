<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = [
        'disk',
        'path',
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
        'score'
    ];

    protected $casts = [
        'exif' => 'array',
    ];

    // Convenience accessor
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}

