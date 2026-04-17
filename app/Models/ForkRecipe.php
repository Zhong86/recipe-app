<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForkRecipe extends Model
{
    use HasFactory;

    protected $table = 'fork_recipes';

    protected $fillable = [
        'original_recipe_id',
        'forked_recipe_id',
    ];

    public function original(): BelongsTo {
        return $this->belongsTo(Recipe::class, 'original_recipe_id');
    }

    public function forkedRecipe(): BelongsTo {
        return $this->belongsTo(Recipe::class, 'forked_recipe_id');
    }
}
