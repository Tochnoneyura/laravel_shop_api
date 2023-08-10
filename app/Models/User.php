<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Facade\Ignition\QueryRecorder\Query;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }

    public function softDelete()
    {
        try{
            DB::beginTransaction();
                $this->active = 'N';
                $this->save();
                $this->delete();
            DB::commit();    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response(['message' => 'deleted', 200]);
    }

    public function dataUpdate($data, $role)
    {
        try {

            if (isset($data['password'])) {
                $this->password = Hash::make($data['password']);
            } else {
                $this->password;
            }
            $this->name = $data['name']?? $this->name;
            $this->last_name = $data['last_name'] ?? $this->last_name;
            
            isset($data['name']) && empty($data['name']) ? null : ($data['name'] ?? $this->second_name);

            if($role === 'admin') {
                $this->role = $data['role'] ?? $this->role;
            }
            $this->save();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
        return response(['message' => 'updated'], 200);
        
    }
}
