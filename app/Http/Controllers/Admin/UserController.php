<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('pages.admin.users.users_list', [
            'users' => $users,
        ]);
    }

    public function edit(Request $request)
    {
        $user = $request->id ? User::find($request->id) : new User();
        $roles = Role::all();
        
        return view('pages.admin.users.user_edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function save(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'role' => 'required|exists:roles,id',
        ];

        if (!$request->id) {
            $validationRules['password'] = 'required|string|min:8|confirmed';
        } else {
            $validationRules['password'] = 'nullable|string|min:8|confirmed';
        }

        $validation = Validator::make($request->all(), $validationRules);


        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $user = $request->id ? User::find($request->id) : new User();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->roles()->sync([$request->role]);

        return redirect()->route('admin.users')->with('success', 'User saved successfully');
    }
}
