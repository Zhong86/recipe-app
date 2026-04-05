<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;

    protected $table = 'recipes';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'cook_time',
        'serving',
        'category',
        'image_url',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function ingredients(): HasMany {
        return $this->hasMany(Ingredient::class);
    }

    public function steps(): HasMany {
        return $this->hasMany(Step::class);
    }

    public function reviews(): HasMany {
        return $this->hasMany(Review::class);
    }

    public function tags(): BelongsToMany {
        return $this->belongsToMany(Tag::class, 'recipe_tag');
    }
}
