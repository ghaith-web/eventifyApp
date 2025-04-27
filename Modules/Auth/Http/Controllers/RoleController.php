<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Http\Requests\RoleRequest;
use Modules\Auth\Models\Role;
use Modules\Auth\Services\RoleService;
use Modules\Auth\Transformers\RoleResource;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = $this->roleService->all();
        return RoleResource::collection($roles);
    }

    public function store(RoleRequest $request)
    {
        $role = $this->roleService->create($request->validated());
        return new RoleResource($role);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role = $this->roleService->update($role, $request->validated());
        return new RoleResource($role);
    }

    public function destroy(Role $role)
    {
        $this->roleService->delete($role);
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
