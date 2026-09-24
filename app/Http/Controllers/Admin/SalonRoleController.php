<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class SalonRoleController extends RoleController
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return parent::edit($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        return parent::update($request, $role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        return parent::destroy($role);
    }

    /**
     * Toggle the status of the specified role.
     */
    public function toggleStatus(Role $role)
    {
        return parent::toggleStatus($role);
    }

    /**
     * Get users assigned to a role.
     */
    public function getUsers(Role $role)
    {
        return parent::getUsers($role);
    }

    /**
     * Get available users for a role.
     */
    public function getAvailableUsers(Role $role)
    {
        return parent::getAvailableUsers($role);
    }

    /**
     * Add a user to a role.
     */
    public function addUser(Request $request, Role $role)
    {
        return parent::addUser($request, $role);
    }

    /**
     * Remove a user from a role.
     */
    public function removeUser(Role $role, User $user)
    {
        return parent::removeUser($role, $user);
    }
}
