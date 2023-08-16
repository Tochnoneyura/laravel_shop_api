<?php

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\User as UserResource;


class UserController extends Controller
{
    public function index(UserFilter $filters, Request $request)
    {
        if($request->get('per_page') === 0) {
            $per_page = User::count();
        } else {
            $per_page = $request->get('per_page') ?? 20;
        }


        return User::withTrashed()
        ->filter($filters)
        ->OrderBy('users.id')
        ->distinct('users.id')
        ->paginate($per_page);
        
    }

    public function create(UserRequest $request)
    {
        $data = $request->all();

        try {
            User::create([
                'email' => $data['email'],
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'active' => 'Y',
                'last_name' => $data['last_name'],
                'second_name' => $data['second_name'],
                'role' => 'admin',
                'last_login' => now(),
            ]);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
        return response(['message' => 'ok'], 200);
        
        
    }

     public function update(UserRequest $request, $id)
    {   
        $user = User::findOrFail($id);
        $currentUser = Auth::user();
        $data = $request->all();

        switch(true) {

            case(((int) $id !== $currentUser['id']) && ($currentUser['role'] !== 'admin')):
                return response()->json(['error' => 'Admin check failed'], 401);
            
            case(((int) $id === $currentUser['id']) && ($currentUser['role'] === 'customer')
                || ($currentUser['role'] === 'admin')):
                
                
                    $role = $currentUser['role'];
                    $result = $user->dataUpdate($data, $role);
                    return response($result);
                    
        }
    }
    
    public function delete($id)
    {
        $currentUser =Auth::user();
        $num = User::where('role', '=', 'admin')->count();
        $user = User::findOrFail($id);

        switch(true) {

            case(((int) $id !== $currentUser['id']) && ($currentUser['role'] !== 'admin')):
                return response()->json(['error' => 'Admin check failed'], 401);
            
            case(((int) $id === $currentUser['id']) && ($currentUser['role'] === 'customer') 
                || ((int) $id !== $currentUser['id']) && ($currentUser['role'] === 'admin') 
                || ((int) $id === $currentUser['id']) && ($currentUser['role'] === 'admin') && ($num > 1)):
    
                $result = $user->softDelete();
                return repsonse($result);
                

            case(((int) $id === $currentUser['id']) && ($currentUser['role'] === 'admin') && ($num <= 1)):
                return response(['message' => 'You are the only admin'], 401);    
        }
    }
}
