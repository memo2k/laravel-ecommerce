<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        
        return view('pages.admin.roles.roles_list', [
            'roles' => $roles,
        ]);
    }

    public function edit(Request $request)
    {
        $role = $request->id ? Role::find($request->id) : new Role();
        $permissions = Permission::all();
        
        return view('pages.admin.roles.role_edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);
        
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $role = $request->id ? Role::find($request->id) : new Role();
        $role->name = $request->name;
        $role->is_active = $request->is_active == 'on' ? 1 : 0;
        $role->save();

        return redirect()->route('admin.roles')->with('success', 'Role saved successfully');
    }
}
