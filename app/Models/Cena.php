<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cena extends Model
{
    use HasFactory;

    protected $table = 'cenas';

    protected $fillable = [
        'user_id',
        'title',
        'datetime',
        'guests_max',
        'guests_current',
        'price',
        'menu',
        'location',
        'latitude',
        'longitude',
        'cover_image',
        'gallery_images',
        'status',
        'is_active',
        'special_requirements',
        'cancellation_policy'
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'price' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'gallery_images' => 'array',
        'is_active' => 'boolean',
        'guests_max' => 'integer',
        'guests_current' => 'integer'
    ];

    // Relaciones
    public function chef(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('datetime', '>', now());
    }

    // Accessors
    public function getFormattedDateAttribute()
    {
        return $this->datetime->format('d/m/Y H:i');
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 0, ',', '.');
    }

    public function getAvailableSpotsAttribute()
    {
        return $this->guests_max - $this->guests_current;
    }

    public function getIsFullAttribute()
    {
        return $this->guests_current >= $this->guests_max;
    }

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return null;
    }

    public function getGalleryImageUrlsAttribute()
    {
        if ($this->gallery_images && is_array($this->gallery_images)) {
            return collect($this->gallery_images)->map(function ($image) {
                return asset('storage/' . $image);
            });
        }
        return collect();
    }
}