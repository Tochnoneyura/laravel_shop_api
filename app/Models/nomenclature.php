<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Basket;
use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nomenclature extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $primaryKey = 'guid';
    
    public function brand()
    {
      return $this->belongsTo(Brand::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }

    public function basket()
    {
      return $this->hasMany(Basket::class);
    }

}
