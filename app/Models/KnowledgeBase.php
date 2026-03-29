<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'title',
        'content',
        'source',
        'reference_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}