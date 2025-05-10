<?php

namespace Modules\Auth\Services;

use Modules\Auth\Models\Role;
use Modules\Auth\Repositories\RoleRepository;

class RoleService
{
    protected RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function all()
    {
        return $this->roleRepository->all();
    }

    public function create(array $data)
    {
        return $this->roleRepository->create($data);
    }

    public function update(Role $role, array $data)
    {
        return $this->roleRepository->update($role, $data);
    }

    public function delete(Role $role)
    {
        return $this->roleRepository->delete($role);
    }
}
