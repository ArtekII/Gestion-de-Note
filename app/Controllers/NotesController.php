<?php

namespace App\Controllers;

use App\Models\Etudiant;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\Option;
class NotesController extends BaseController
{
    public function show(int $id): string
    {
        $etudiantModel = new Etudiant();
        $etudiantRef = $etudiantModel->find($id);
        $options = new Option();
        $allOptions = $options->findAll();
        if ($etudiantRef === null) {
            throw PageNotFoundException::forPageNotFound('Etudiant introuvable.');
        }

        $nom = $etudiantRef['nom'];
        $selectedOption = $this->request->getGet('option');

        $options = $etudiantModel
            ->select('id_option')
            ->where('nom', $nom)
            ->groupBy('id_option')
            ->orderBy('id_option', 'ASC')
            ->findAll();

        $notesQuery = $etudiantModel
            ->where('nom', $nom)
            ->orderBy('id_semestre', 'ASC')
            ->orderBy('id_matiere', 'ASC');

        if ($selectedOption !== null && $selectedOption !== '' && ctype_digit((string) $selectedOption)) {
            $notesQuery->where('id_option', (int) $selectedOption);
        } else {
            $selectedOption = '';
        }

        $notes = $notesQuery->findAll();
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
            'selectedOption' => (string) $selectedOption,
            'notes' => $notes,
            'moyenne' => $moyenne,
            'options' => $allOptions,
        ]);
    }
}
