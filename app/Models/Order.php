<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Order
 * @package App\Models
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory;

   public function items():BelongsToMany
   {
       return $this->belongsToMany(Product::class,'order_items')->withPivot(['qty']);
   }
}
