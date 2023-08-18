<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Order_item extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    protected $primaryKey = null;
    public $incrementing = false;

    public function order()
    {
      return $this->belongsTo(Order::class);
    }
}
