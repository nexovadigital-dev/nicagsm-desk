<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BannedIp extends Model
{
    protected $fillable = ['organization_id', 'ip', 'reason'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
