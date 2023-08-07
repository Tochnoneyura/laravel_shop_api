<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if( $user['role'] === 'admin') {

            $users = User::all();
            dd($users);

            return response(['message' => 'ok', 200]);
        }

        return response(['message' => 'not allowed']);
        
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

     public function update($id, UpdateUserRequest $request)
    {   
        $user = User::findOrFail($id);
        $currentUser =Auth::user();
        $data = $request->all();

        switch(true) {

            case(($id !== $currentUser['id']) AND ($currentUser['role'] !== 'admin')):
                return response(['message' => 'not allowed']);
            
            case(($id === $currentUser['id']) AND ($currentUser['role'] === 'customer')):
                $user->update([
                    'password' =>  Hash::make($data['password']),
                    'name' => $data['name'],
                    'last_name' => $data['last_name'],
                    'second_name' => $data['second_name'],
                ]);
                return response(['message' => 'ok', 200]);

            case($currentUser['role'] === 'admin'):
                $user->update([
                    'password' =>  Hash::make($data['password']),
                    'name' => $data['name'],
                    'last_name' => $data['last_name'],
                    'second_name' => $data['second_name'],
                    'role' => $data['role'],
                ]);
                return response(['message' => 'ok, admin', 200]);

        }
    }

    public function delete($id)
    {
        
    }
}
