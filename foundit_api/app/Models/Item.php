<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'title',
        'description',
        'location',
        'location_detail',
        'latitude',
        'longitude',
        'date_time',
        'storage_info',
        'status',
    ];

    protected $casts = [
        'date_time' => 'datetime',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     * Format tanpa timezone agar tidak di-convert oleh client
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get the user who reported this item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of this item.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get photos for this item.
     */
    public function photos()
    {
        return $this->hasMany(ItemPhoto::class);
    }

    /**
     * Get claims for this item.
     */
    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    /**
     * Scope for lost items.
     */
    public function scopeLost($query)
    {
        return $query->where('type', 'lost');
    }

    /**
     * Scope for found items.
     */
    public function scopeFound($query)
    {
        return $query->where('type', 'found');
    }

    /**
     * Scope for active items.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
