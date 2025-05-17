<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Http\Requests\RoleRequest;
use Modules\Auth\Models\Role;
use Modules\Auth\Services\RoleService;
use Modules\Auth\Transformers\RoleResource;
use App\Helpers\ApiResponseHelper;
use Throwable;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        try {
            $roles = $this->roleService->all();
            return ApiResponseHelper::success(RoleResource::collection($roles), 'Roles retrieved successfully');
        } catch (Throwable $e) {
            return ApiResponseHelper::error('Failed to retrieve roles', 500, null, $e);
        }
    }

    public function store(RoleRequest $request)
    {
        try {
            $role = $this->roleService->create($request->validated());
            return ApiResponseHelper::success(new RoleResource($role), 'Role created successfully', 201);
        } catch (Throwable $e) {
            return ApiResponseHelper::error('Failed to create role', 500, null, $e);
        }
    }

    public function update(RoleRequest $request, Role $role)
    {
        try {
            $role = $this->roleService->update($role, $request->validated());
            return ApiResponseHelper::success(new RoleResource($role), 'Role updated successfully');
        } catch (Throwable $e) {
            return ApiResponseHelper::error('Failed to update role', 500, null, $e);
        }
    }

    public function destroy(Role $role)
    {
        try {
            $this->roleService->delete($role);
            return ApiResponseHelper::success(null, 'Role deleted successfully', 204);
        } catch (Throwable $e) {
            return ApiResponseHelper::error('Failed to delete role', 500, null, $e);
        }
    }
}
