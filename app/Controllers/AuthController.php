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

        $employeModel = new EmployerModel();
        $user = $employeModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email ou mot de passe incorrect');
        }

        // if (!password_verify($password, $user->password)) {
        //     return redirect()->back()->with('error', 'Email ou mot de passe incorrect');
        // }
        if ($password != $user['password']) {
            return redirect()->back()->with('error', 'Email ou mot de passe incorrect');
        }

        session()->set([
            'id' => $user['id'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'role' => $user['role'],
            'isLoggedIn' => true
        ]);
        
        $userRole = strtolower($user['role'] ?? 'employe');
        if ($userRole === 'responsable') {
            $userRole = 'rh';
        }
        
        if ($userRole === 'rh') {
            return redirect()->to('/rh/demandes');
        } elseif ($userRole === 'admin') {
            return redirect()->to('/admin/dashboard');
        }
        
        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
