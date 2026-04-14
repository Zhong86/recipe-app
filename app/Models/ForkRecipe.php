<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForkRecipe extends Model
{
    use HasFactory;

    protected $table = 'forked_recipes';

    protected $fillable = [
        'recipe_id',
        'forked_user_id'
    ];
}
