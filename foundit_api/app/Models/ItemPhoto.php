<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'photo_url',
    ];

    /**
     * Get the item this photo belongs to.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
