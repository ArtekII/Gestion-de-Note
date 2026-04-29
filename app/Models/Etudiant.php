<?php

namespace App\Models;

use CodeIgniter\Model;

class Etudiant extends Model
{
    protected $table            = 'etudiant';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "nom",
        "id_matiere",
        "id_semestre",
        "id_option",
        "note",
        "credit",
        "resultat"
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        "nom" => "required|at_least[3]",
        "id_matiere" => "required|integer",
        "id_semestre" => "required|integer",
        "id_option" => "permit_empty|integer",
        "note" => "required|numeric|less_than_equal_to[20]",
        "credit" => "required|integer|greater_than[0]",
        "resultat" => "required|in_list[B,AB,P,Aj,Comp.]"
    ];
    protected $validationMessages   = [
        "nom" => [
            "required" => "Le nom de l'étudiant est requis.",
            "at_least" => "Le nom doit comporter au moins 3 caractères."
        ],
        "id_matiere" => [
            "required" => "L'identifiant de la matière est requis.",
            "integer" => "L'identifiant de la matière doit être un nombre entier."
        ],
        "id_semestre" => [
            "required" => "L'identifiant du semestre est requis.",
            "integer" => "L'identifiant du semestre doit être un nombre entier."
        ],
        "id_option" => [
            "required" => "L'identifiant de l'option est requis.",
            "integer" => "L'identifiant de l'option doit être un nombre entier."
        ],
        "note" => [
            "required" => "La note est requise.",
            "numeric" => "La note doit être un nombre.",
            "less_than_equal_to" => "La note doit être inférieure ou égale à 20."
        ],
        "credit" => [
            "required" => "Le crédit est requis.",
            "integer" => "Le crédit doit être un nombre entier.",
            "greater_than" => "Le crédit doit être supérieur à 0."
        ],
        "resultat" => [
            "required" => "Le résultat est requis.",
            "in_list" => "Le résultat doit être l'un des suivants : B, AB, P, Aj."
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
