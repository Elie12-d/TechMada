<?php

namespace App\Controllers;

use App\Models\EmployerModel;

class AuthController extends BaseController
{
    public function login()
    {

        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $employeModel = new EmployeModel();
        $user = $employeModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email ou mot de passe incorrect');
        }

        if (!password_verify($password, $user->password)) {
            return redirect()->back()->with('error', 'Email ou mot de passe incorrect');
        }

        session()->set([
            'id' => $user['id'],
            'nom' => $user['nom'],
            'role' => $user['role'],<?php

namespace App\Controllers;

use App\Models\EmployerModel;

class AuthController extends BaseController
{
    public function login()
    {

        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $employeModel = new EmployeModel();
        $user = $employeModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email ou mot de passe incorrect');
        }

        if (!password_verify($password, $user->password)) {
            return redirect()->back()->with('error', 'Email ou mot de passe incorrect');
        }

        session()->set([
            'id' => $user['id'],
            'nom' => $user['nom'],
            'role' => $user['role'],
            'isLoggedIn' => true
        ]);
        return redirect()->to('/dashboard');
    }
}

            'isLoggedIn' => true
        ]);
        return redirect()->to('/dashboard');
    }
}
