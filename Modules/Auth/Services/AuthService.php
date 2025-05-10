<?php

namespace Modules\Auth\Services;

use Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Repositories\RoleRepository;

class AuthService
{

    protected RoleRepository $roleRepo;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        if (empty($data['role_id'])) {
            $defaultRole = $this->roleRepo->findByName('user');
            if (!$defaultRole) {
                $defaultRole = $this->roleRepo->create(['name' => 'user']);
            }

            $data['role_id'] = $defaultRole->id;
        }

        return User::create($data);
    }

    public function login(array $credentials)
    {
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return null;
        }
        return $token;
    }

    public function logout()
    {
        Auth::guard('api')->logout();
    }

    public function refresh()
    {
        return Auth::guard('api')->refresh();
    }

    public function profile()
    {
        return Auth::guard('api')->user();
    }
}
