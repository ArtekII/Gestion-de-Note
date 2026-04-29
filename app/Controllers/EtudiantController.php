<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Etudiant;
use App\Models\Option;
class EtudiantController extends BaseController
{
    public function index()
    {
        $etudiantsModel= new Etudiant();
        $etudiantsModel= $etudiantsModel->findAll();
        return view('listeEtudiant', [
            'etudiants' => $etudiantsModel,
        ]);
    }
}
