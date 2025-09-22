<?php

namespace App\Models;

use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use TracksActivity;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function researchItems(): HasMany
    {
        return $this->hasMany(ResearchItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
