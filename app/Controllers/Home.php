<?php

namespace App\Controllers;

use App\Models\Etudiant;

class Home extends BaseController
{
    public function index()
    {
        // return redirect()->to(site_url('dashboard'));
        return redirect()->to(site_url('list'));
    }

    public function dashboard(): string
    {
        $etudiantModel = new Etudiant();
        $totalEtudiants = $etudiantModel->countAllResults();

        return view('dashboard_home', [
            'pageTitle' => 'Tableau de bord',
            'topbarTitle' => 'Tableau de bord',
            'activeMenu' => 'dashboard',
            'totalEtudiants' => $totalEtudiants,
        ]);
    }

}
