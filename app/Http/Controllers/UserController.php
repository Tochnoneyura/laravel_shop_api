<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

            $users = User::all();
            dd($users);

            return response(['message' => 'ok', 200]);
        
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
        return response(['message' => 'ok', 200]);
        
        
    }

     public function update(UpdateUserRequest $request, $id)
    {   
        $user = User::findOrFail($id);
        $currentUser =Auth::user();
        $data = $request->all();

        switch(true) {

            case(((int) $id !== $currentUser['id']) AND ($currentUser['role'] !== 'admin')):
                return response()->json(['error' => 'Admin check failed'], 401);
            
            case(((int) $id === $currentUser['id']) AND ($currentUser['role'] === 'customer')):
                    $user->password =  Hash::make($data['password']) ?? $user->password;
                    $user->name = $data['name']?? $user->name;
                    $user->last_name = $data['last_name'] ?? $user->last_name;
                    $user->second_name = $data['second_name'] ?? $user->second_name;
                    $user->save();

                return response(['message' => 'ok', 200]);

            case($currentUser['role'] === 'admin'):
                    $user->password =  Hash::make($data['password']) ?? $user->password;
                    $user->name = $data['name'] ?? $user->name;
                    $user->last_name = $data['last_name'] ?? $user->last_name;
                    $user->second_name = $data['second_name'] ?? $user->second_name;
                    $user->role = $data['role'] ?? $user->role;
                    $user->save();
                return response(['message' => 'ok, admin', 200]);

        }
    }

    public function delete($id)
    {
        $currentUser =Auth::user();
        $num = DB::table('users')->where('role', '=', 'admin')->count();
        $user = User::findOrFail($id);

        switch(true) {

            case(((int) $id !== $currentUser['id']) AND ($currentUser['role'] !== 'admin')):
                return response()->json(['error' => 'Admin check failed'], 401);
            
            case(((int) $id === $currentUser['id']) AND ($currentUser['role'] === 'customer')):
    
                DB::transaction(function () use($user){
                    $user->active = 'N';
                    $user->delete();
                });
                return response(['message' => 'ok', 200]);


            case(((int) $id !== $currentUser['id']) AND ($currentUser['role'] === 'admin')):
                
                DB::transaction(function () use($user) {
                    $user->active = 'N';
                    $user->save();
                    $user->delete();
                });    
                return response(['message' => 'ok, admin', 200]);
            
            
            case(((int) $id === $currentUser['id']) AND ($currentUser['role'] === 'admin') AND ($num > 1)):

                DB::transaction(function () use($user) {
                    $user->active = 'N';
                    $user->save();
                    $user->delete();
                });
                return response(['message' => 'ok, admin', 200]);

            case(((int) $id === $currentUser['id']) AND ($currentUser['role'] === 'admin') AND ($num <= 1)):
                return response(['message' => 'You are the only admin', 401]);    
        }
    }
}
