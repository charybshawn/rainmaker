<?php

namespace App\Models;

use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes, TracksActivity;
    protected $fillable = [
        'name',
        'ticker_symbol',
        'sector',
        'industry',
        'market_cap',
        'description',
        'reports_financial_data_in',
        'website_url',
        'headquarters',
        'employees',
        'founded_date',
        'created_by',
    ];

    protected $casts = [
        'market_cap' => 'decimal:2',
        'founded_date' => 'date',
        'employees' => 'integer',
    ];

    public function researchItems(): HasMany
    {
        return $this->hasMany(ResearchItem::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function blogPosts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class);
    }

    public function getMarketCapFormattedAttribute(): string
    {
        if (!$this->market_cap) return 'N/A';

        $value = $this->market_cap;
        if ($value >= 1000000000000) {
            return '$' . number_format($value / 1000000000000, 1) . 'T';
        } elseif ($value >= 1000000000) {
            return '$' . number_format($value / 1000000000, 1) . 'B';
        } else {
            return '$' . number_format($value / 1000000, 1) . 'M';
        }
    }
}
