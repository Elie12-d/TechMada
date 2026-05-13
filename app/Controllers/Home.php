<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('login');
        return redirect()->to('/login');
    }
    public function toFormLogin() {
        return view('auth/login');
    }
}
