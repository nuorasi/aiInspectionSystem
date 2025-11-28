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
}

