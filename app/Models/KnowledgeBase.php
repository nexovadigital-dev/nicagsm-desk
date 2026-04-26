<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'widget_id',
        'channel',
        'title',
        'content',
        'source',
        'reference_id',
        'is_active',
    ];

    public function widget(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChatWidget::class, 'widget_id');
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];
}