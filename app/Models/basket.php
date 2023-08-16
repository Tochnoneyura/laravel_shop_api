<?php

namespace App\Models;

use App\Models\User;
use App\Models\Nomenclature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Basket extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function nomenclature()
    {
      return $this->belongsTo(Nomenclature::class);
    }

    public function user()
    {
      return $this->belongsTo(User::class);
    }

}
