<?php

namespace App\Models;

use App\Models\Nomenclature;
use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Brand extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function nomenclature()
    {
        return $this->hasMany(Nomenclature::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }
}
