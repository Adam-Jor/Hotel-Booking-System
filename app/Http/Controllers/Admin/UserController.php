<?php

namespace App\Http\Controllers\Admin;

class UserController
{
    public function index()
    {
        return view('admin.users.index');
    }
}
