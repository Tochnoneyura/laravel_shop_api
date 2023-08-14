<?php

namespace App\Models;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nomenclature extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    public function brand()
    {
      return $this->belongsTo(Brand::class);
    }
}
