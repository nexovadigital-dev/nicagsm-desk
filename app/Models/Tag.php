<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['organization_id', 'name', 'color'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'tag_ticket');
    }
}
