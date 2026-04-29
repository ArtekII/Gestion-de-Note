<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'email' => session()->get('email'),
            'role' => session()->get('role'),
        ];

        return view('dashboard', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')
            ->with('succes', 'Vous avez ete deconnecte');
    }
}
