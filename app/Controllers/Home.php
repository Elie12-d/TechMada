<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function toFormLogin() {
        return view('auth/login');
    }
}
