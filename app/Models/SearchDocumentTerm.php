<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchDocumentTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_document_id',
        'normalized_term',
        'field_name',
        'weight',
        'occurrence_count',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(SearchDocument::class, 'search_document_id');
    }
}
