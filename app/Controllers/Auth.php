<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;

class Auth extends BaseController
{
    protected $utilisateurModel;

    public function __construct()
    {
        $this->utilisateurModel = new UtilisateurModel();
    }
    public function login()
    {
        return view('login');
    }
    public function loginProcess()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        try {
            $utilisateur = $this->utilisateurModel->findByEmail($email);
            if ($utilisateur !== null && $password === $utilisateur['mot_de_passe']) {
                session()->set([
                    'user_id' => $utilisateur['id'],
                    'email' => $utilisateur['email'],
                    'role' => $utilisateur['roles'],
                    'isLoggedIn' => true,
                ]);
                return redirect()->to('/dashboard');
            }



          
            if ($utilisateur === null) {
                $data = [
                    'email' => $email,
                    'mot_de_passe' => $password,
                    'roles' => 'etudiant', 
                ];

                $this->utilisateurModel->skipValidation(true);
                $insertId = $this->utilisateurModel->insert($data);
                $this->utilisateurModel->skipValidation(false);
                
                if ($insertId) {
                    $nouveau_user = $this->utilisateurModel->find($insertId);
                    
                    session()->set([
                        'user_id' => $nouveau_user['id'],
                        'email' => $nouveau_user['email'],
                        'role' => $nouveau_user['roles'],
                        'isLoggedIn' => true,
                    ]);
                    return redirect()->to('/dashboard')
                        ->with('succes', 'Compte cree et connexion reussie');
                } else {
                    $errors = $this->utilisateurModel->errors();
                    return redirect()->back();
                }
            }
            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back();
        }
    }






    public function register()
    {
        return view('auth/register');
    }

    public function registerProcess()
    {
        $data = [
            'email' => $this->request->getPost('email'),
            'mot_de_passe' => $this->request->getPost('password'),
            'roles' => 'etudiant', 
        ];

        if ($this->utilisateurModel->insert($data)) {
            return redirect()->to('/auth/login');
        }

        return redirect()->back();
    }

    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }
}
