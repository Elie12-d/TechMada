<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return redirect()->to('/login');
    }
    public function toFormLogin() {
        return view('auth/login');
    }
}
