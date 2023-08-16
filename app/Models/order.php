<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order_item;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];
    

    public function order_item()
    {
      return $this->hasMany(Order_item::class);
    }

    
}
