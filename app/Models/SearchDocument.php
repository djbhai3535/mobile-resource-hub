<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SearchDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'searchable_type',
        'searchable_id',
        'content_type',
        'title',
        'slug',
        'excerpt',
        'normalized_content',
        'searchable_url',
        'model_code',
        'brand_name',
        'publication_status',
        'published_at',
        'source_updated_at',
        'indexed_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'source_updated_at' => 'datetime',
        'indexed_at' => 'datetime',
    ];

    public function searchable(): MorphTo
    {
        return $this->morphTo();
    }

    public function terms(): HasMany
    {
        return $this->hasMany(SearchDocumentTerm::class, 'search_document_id');
    }

    public function scopePublished($query)
    {
        return $query->where('publication_status', 'published');
    }

    public function scopeForType($query, ?string $type)
    {
        if ($type && $type !== 'all') {
            return $query->where('content_type', $type);
        }

        return $query;
    }
}
