<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ingredient
 * @package App\Models
 * @mixin \Eloquent
 */
class Ingredient extends Model
{
    use HasFactory;
    protected $fillable =[
        'email_sent'
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class,'products_ingredients')->withPivot(['recipe_amount']);
    }
}
