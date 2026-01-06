<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'page_key',
        'section_key',
        'title',
        'subtitle',
        'description',
        'content',
        'image_url',
        'button_text',
        'button_url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get contents for a specific page.
     */
    public function scopeForPage($query, $pageKey)
    {
        return $query->where('page_key', $pageKey)->where('is_active', true)->orderBy('order');
    }

    /**
     * Get a specific section.
     */
    public function scopeSection($query, $pageKey, $sectionKey)
    {
        return $query->where('page_key', $pageKey)->where('section_key', $sectionKey);
    }

    /**
     * Company relationship.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
