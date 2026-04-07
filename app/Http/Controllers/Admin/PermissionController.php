<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();

        return view('pages.admin.permissions.permissions_list', [
            'permissions' => $permissions,
        ]);
    }

    public function edit(Request $request)
    {
        $permission = $request->id ? Permission::find($request->id) : new Permission();

        return view('pages.admin.permissions.permission_edit', [
            'permission' => $permission,
        ]);
    }

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $permission = $request->id ? Permission::find($request->id) : new Permission();
        $permission->name = $request->name;
        $permission->description = $request->description;
        $permission->save();

        return redirect()->route('admin.permissions')->with('success', 'Permission saved successfully');
    }
}
