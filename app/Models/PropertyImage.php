<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyImage extends Model
{
    protected $fillable = [
        'property_id',
        'filename',
        'path',
        'disk',
        'mime_type',
        'size',
        'is_primary',
        'order',
        'alt_text',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'size' => 'integer',
        'order' => 'integer',
    ];

    protected $appends = ['url', 'thumbnail_url'];

    /**
     * Get the property that owns this image.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the full URL of the image.
     */
    public function getUrlAttribute(): string
    {
        if ($this->disk === 'public') {
            return asset('storage/' . $this->path);
        }

        // For S3 or other cloud storage
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get thumbnail URL (same as URL for now, could be processed later).
     */
    public function getThumbnailUrlAttribute(): string
    {
        // In production, you might want to generate actual thumbnails
        return $this->url;
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope to get primary image.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope to order by position.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    /**
     * Delete the image file when model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            Storage::disk($image->disk)->delete($image->path);
        });
    }
}
