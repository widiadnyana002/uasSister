<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Products extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'image', 'price', 'author', 'category_id'
    ];

    public function writer(): BelongsTo {
        return $this->belongsTo(User::class, 'author', 'id');
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Categories::class, 'category_id', 'id');
    }
}
