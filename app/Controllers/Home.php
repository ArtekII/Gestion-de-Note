<?php

namespace App\Controllers;

use App\Models\Etudiant;

class Home extends BaseController
{
    public function index()
    {
        return redirect()->to(site_url('dashboard'));
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

    public function list(): string
    {
        $etudiantModel = new Etudiant();
        $etudiants = $etudiantModel->orderBy('id', 'DESC')->findAll();

        return view('list.html', [
            'pageTitle' => 'Liste des etudiants',
            'topbarTitle' => 'Gestion des etudiants',
            'activeMenu' => 'list',
            'totalEtudiants' => count($etudiants),
            'etudiants' => $etudiants,
        ]);
    }

    public function notesEtudiant(string $nom): string
    {
        $etudiantModel = new Etudiant();
        $nom = urldecode($nom);
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

        return view('notes_etudiant.html', [
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
