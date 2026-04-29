<?php

namespace App\Controllers;

use App\Models\Etudiant;
use CodeIgniter\Exceptions\PageNotFoundException;

class EtudiantController extends BaseController
{
    public function index(): string
    {
        return $this->list();
    }

    public function list(): string
    {
        $db = db_connect();
        $etudiants = $db->table('etudiant e')
            ->select(
                'e.id, e.nom, e.id_semestre, e.id_option, e.id_matiere, e.note, e.credit, e.resultat, ' .
                's.nom AS semestre_nom, o.nom AS option_nom, m.code_matiere, lm.Nom_matiere AS matiere_nom'
            )
            ->join('semestre s', 's.id = e.id_semestre', 'left')
            ->join('options o', 'o.id = e.id_option', 'left')
            ->join('matiere m', 'm.id = e.id_matiere', 'left')
            ->join('liste_matiere lm', 'lm.code_matiere = m.code_matiere', 'left')
            ->orderBy('e.id', 'DESC')
            ->get()
            ->getResultArray();

        return view('list', [
            'pageTitle' => 'Liste des etudiants',
            'topbarTitle' => 'Gestion des etudiants',
            'activeMenu' => 'list',
            'totalEtudiants' => count($etudiants),
            'etudiants' => $etudiants,
        ]);
    }

    public function show(int $id): string
    {
        $etudiantModel = new Etudiant();
        $etudiantRef = $etudiantModel->find($id);
        if ($etudiantRef === null) {
            throw PageNotFoundException::forPageNotFound('Etudiant introuvable.');
        }

        $nom = $etudiantRef['nom'];
        $selectedOption = $this->request->getGet('option');
        $db = db_connect();

        $options = $db->table('etudiant e')
            ->select('e.id_option, o.nom AS option_nom')
            ->join('options o', 'o.id = e.id_option', 'left')
            ->where('e.nom', $nom)
            ->groupBy('e.id_option, o.nom')
            ->orderBy('e.id_option', 'ASC')
            ->get()
            ->getResultArray();

        $notesQuery = $db->table('etudiant e')
            ->select(
                'e.id, e.id_semestre, e.id_option, e.id_matiere, e.note, e.credit, e.resultat, ' .
                's.nom AS semestre_nom, o.nom AS option_nom, m.code_matiere, lm.Nom_matiere AS matiere_nom'
            )
            ->join('semestre s', 's.id = e.id_semestre', 'left')
            ->join('options o', 'o.id = e.id_option', 'left')
            ->join('matiere m', 'm.id = e.id_matiere', 'left')
            ->join('liste_matiere lm', 'lm.code_matiere = m.code_matiere', 'left')
            ->where('e.nom', $nom)
            ->orderBy('e.id_semestre', 'ASC')
            ->orderBy('e.id_matiere', 'ASC');

        if ($selectedOption !== null && $selectedOption !== '' && ctype_digit((string) $selectedOption)) {
            $notesQuery->where('e.id_option', (int) $selectedOption);
        } else {
            $selectedOption = '';
        }

        $notes = $notesQuery->get()->getResultArray();
        $moyenne = 0;
        if (! empty($notes)) {
            $total = array_sum(array_map(static fn ($note) => (float) $note['note'], $notes));
            $moyenne = round($total / count($notes), 2);
        }

        return view('notes_etudiant', [
            'pageTitle' => 'Notes etudiant',
            'topbarTitle' => 'Detail etudiant',
            'activeMenu' => 'list',
            'nomEtudiant' => $nom,
            'totalEtudiants' => (new Etudiant())->countAllResults(),
            'options' => $options,
            'selectedOption' => (string) $selectedOption,
            'notes' => $notes,
            'moyenne' => $moyenne,
        ]);
    }
}
