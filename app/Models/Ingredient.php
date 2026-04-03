<?php

namespace App\Models;

use App\Enums\Units;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'ingredients';

    protected $fillable = [
        'recipe_id',
        'name',
        'quantity',
        'unit'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit' => Units::class
    ];
}
