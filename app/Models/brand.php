<?php

namespace App\Models;

use App\Models\Nomenclature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function nomenclature()
    {
        return $this->hasMany(Nomenclature::class);
    }
}
