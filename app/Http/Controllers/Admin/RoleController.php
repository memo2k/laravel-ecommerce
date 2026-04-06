<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return view('pages.admin.roles.roles_list');
    }

    public function edit()
    {
        return view('pages.admin.roles.role_edit');
    }
}
