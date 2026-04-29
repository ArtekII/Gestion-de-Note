<?php

namespace App\Models;

use CodeIgniter\Model;

class UtilisateurModel extends Model
{
    protected $table = 'utilisateurs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['email', 'mot_de_passe', 'roles'];
    protected $useTimestamps = false;
    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[utilisateurs.email,id,{id}]',
        'mot_de_passe' => 'required|min_length[3]',
        'roles' => 'in_list[etudiant,admin]',
    ];
    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email deja utilisé',
        ],
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
}