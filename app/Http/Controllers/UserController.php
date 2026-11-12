<?php

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;


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
        $data = $request->validated();

        User::create([
            'email' => $data['email'],
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
            'active' => 'Y',
            'last_name' => $data['last_name'],
            'second_name' => $data['second_name'] ?? null,
            'role' => 'customer',
            'last_login' => now(),
        ]);

        return response(['message' => 'ok'], 200);
    }

    public function update(UserRequest $request, $id)
    {
        $currentUser = $request->user();
        $data = $request->validated();

        return DB::transaction(function () use ($id, $currentUser, $data) {
            $admins = isset($data['role']) ? $this->lockAdminIds() : null;
            $isAdmin = $admins ? $admins->contains($currentUser->id) : $currentUser->role === 'admin';

            if ((int) $id !== $currentUser->id && ! $isAdmin) {
                return response()->json(['error' => 'Admin check failed'], 403);
            }

            $user = User::lockForUpdate()->findOrFail($id);

            if ($isAdmin && ($data['role'] ?? null) === 'customer'
                && $user->role === 'admin' && $admins->count() === 1) {
                return response()->json(['message' => 'You are the only admin'], 409);
            }

            return $user->dataUpdate($data, $isAdmin ? 'admin' : 'customer');
        });
    }

    public function delete(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $admins = $this->lockAdminIds();
            $currentUserId = $request->user()->id;

            if ((int) $id !== $currentUserId && ! $admins->contains($currentUserId)) {
                return response()->json(['error' => 'Admin check failed'], 403);
            }

            $user = User::lockForUpdate()->findOrFail($id);
            if ($user->role === 'admin' && $admins->count() === 1) {
                return response()->json(['message' => 'You are the only admin'], 409);
            }

            $user->softDelete();

            return response()->json(['message' => 'deleted']);
        });
    }

    private function lockAdminIds(): Collection
    {
        return User::where('role', 'admin')->orderBy('id')->lockForUpdate()->pluck('id');
    }
}
