<?php

namespace App\Controllers;

use App\Models\Etudiant;
use CodeIgniter\Exceptions\PageNotFoundException;

class EtudiantController extends BaseController
{
    private const OPTIONAL_SUBJECT_GROUPS = [
        ['MTH203', 'MTH206'],
    ];

    public function index(): string
    {
        return $this->list();
    }

    public function list(): string
    {
        $db = db_connect();
        $etudiants = $db->table('etudiant e')
            ->select(
                'MIN(e.id) AS id, e.nom, COUNT(*) AS nb_notes, ROUND(AVG(e.note), 2) AS moyenne_brute, ' .
                'GROUP_CONCAT(DISTINCT s.nom ORDER BY s.id SEPARATOR ", ") AS semestres, ' .
                'GROUP_CONCAT(DISTINCT o.nom ORDER BY o.id SEPARATOR ", ") AS options'
            )
            ->join('semestre s', 's.id = e.id_semestre', 'left')
            ->join('options o', 'o.id = e.id_option', 'left')
            ->groupBy('e.nom')
            ->orderBy('e.nom', 'ASC')
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
        $selectedOption = (string) ($this->request->getGet('option') ?? '');
        $selectedSemestre = (string) ($this->request->getGet('semestre') ?? '');
        $selectedAnnee = trim((string) ($this->request->getGet('annee') ?? ''));
        $db = db_connect();

        $hasAnneeColumn = in_array('annee', $db->getFieldNames('semestre'), true);
        $anneeSelect = $hasAnneeColumn ? 's.annee AS annee_nom' : '"L2" AS annee_nom';

        $notesRaw = $db->table('etudiant e')
            ->select(
                'e.id, e.id_semestre, e.id_option, e.id_matiere, e.note, e.credit, e.resultat, ' .
                's.nom AS semestre_nom, o.nom AS option_nom, m.code_matiere, m.coefficient, lm.Nom_matiere AS matiere_nom, ' .
                $anneeSelect
            )
            ->join('semestre s', 's.id = e.id_semestre', 'left')
            ->join('options o', 'o.id = e.id_option', 'left')
            ->join('matiere m', 'm.id = e.id_matiere', 'left')
            ->join('liste_matiere lm', 'lm.code_matiere = m.code_matiere', 'left')
            ->where('e.nom', $nom)
            ->orderBy('e.id_semestre', 'ASC')
            ->orderBy('e.id_matiere', 'ASC')
            ->get()
            ->getResultArray();

        $allOptions = $db->table('options')
            ->select('id, nom')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $options = [
            [
                'value' => 'none',
                'label' => 'Tronc commun (sans option)',
            ],
        ];
        foreach ($allOptions as $optionRow) {
            $options[] = [
                'value' => (string) $optionRow['id'],
                'label' => (string) $optionRow['nom'],
            ];
        }
        $semestres = $this->extractSemestres($notesRaw);
        $annees = $this->extractAnnees($notesRaw);

        $notesFiltered = $this->applySelectionFilters($notesRaw, $selectedOption, $selectedSemestre, $selectedAnnee);
        $notes = $this->applyBusinessRules($notesFiltered);

        usort($notes, static function (array $a, array $b): int {
            return [$a['id_semestre'] ?? 0, $a['id_matiere'] ?? 0, $a['id'] ?? 0]
                <=>
                [$b['id_semestre'] ?? 0, $b['id_matiere'] ?? 0, $b['id'] ?? 0];
        });

        $moyenne = 0;
        if (! empty($notes)) {
            $total = array_sum(array_map(static fn (array $note): float => (float) $note['note'], $notes));
            $moyenne = round($total / count($notes), 2);
        }

        return view('notes_etudiant', [
            'pageTitle' => 'Notes etudiant',
            'topbarTitle' => 'Detail etudiant',
            'activeMenu' => 'list',
            'nomEtudiant' => $nom,
            'etudiantId' => $id,
            'totalEtudiants' => (new Etudiant())->countAllResults(),
            'options' => $options,
            'semestres' => $semestres,
            'annees' => $annees,
            'selectedOption' => $selectedOption,
            'selectedSemestre' => $selectedSemestre,
            'selectedAnnee' => $selectedAnnee,
            'notes' => $notes,
            'moyenne' => $moyenne,
        ]);
    }

    private function extractSemestres(array $notes): array
    {
        $out = [];
        foreach ($notes as $note) {
            if (! isset($note['id_semestre'])) {
                continue;
            }

            $id = (int) $note['id_semestre'];
            $key = (string) $id;
            if (isset($out[$key])) {
                continue;
            }

            $out[$key] = [
                'value' => $key,
                'label' => (string) ($note['semestre_nom'] ?? ('Semestre #' . $id)),
            ];
        }

        return array_values($out);
    }

    private function extractAnnees(array $notes): array
    {
        $out = [];
        foreach ($notes as $note) {
            $annee = trim((string) ($note['annee_nom'] ?? ''));
            if ($annee === '' || isset($out[$annee])) {
                continue;
            }

            $out[$annee] = [
                'value' => $annee,
                'label' => $annee,
            ];
        }

        return array_values($out);
    }

    private function applySelectionFilters(array $notes, string $selectedOption, string $selectedSemestre, string $selectedAnnee): array
    {
        return array_values(array_filter($notes, static function (array $note) use ($selectedOption, $selectedSemestre, $selectedAnnee): bool {
            if ($selectedOption !== '') {
                if ($selectedOption === 'none') {
                    if (($note['id_option'] ?? null) !== null) {
                        return false;
                    }
                } elseif (ctype_digit($selectedOption)) {
                    if ((int) ($note['id_option'] ?? 0) !== (int) $selectedOption) {
                        return false;
                    }
                }
            }

            if ($selectedSemestre !== '' && ctype_digit($selectedSemestre)) {
                if ((int) ($note['id_semestre'] ?? 0) !== (int) $selectedSemestre) {
                    return false;
                }
            }

            if ($selectedAnnee !== '') {
                if ((string) ($note['annee_nom'] ?? '') !== $selectedAnnee) {
                    return false;
                }
            }

            return true;
        }));
    }

    private function applyBusinessRules(array $notes): array
    {
        if (empty($notes)) {
            return [];
        }

        // Regle 1: pour une matiere donnee, garder la note maximale.
        $bestBySubject = [];
        foreach ($notes as $note) {
            $subjectKey = (string) ($note['code_matiere'] ?? 'MAT#' . ($note['id_matiere'] ?? '0'));
            if (! isset($bestBySubject[$subjectKey]) || (float) $note['note'] > (float) $bestBySubject[$subjectKey]['note']) {
                $bestBySubject[$subjectKey] = $note;
            }
        }

        $filtered = array_values($bestBySubject);

        // Regle 2: pour les matieres optionnelles, garder la meilleure note du groupe.
        foreach (self::OPTIONAL_SUBJECT_GROUPS as $group) {
            $bestIndex = null;
            $bestNote = -INF;

            foreach ($filtered as $i => $note) {
                $code = (string) ($note['code_matiere'] ?? '');
                if (in_array($code, $group, true) && (float) $note['note'] > $bestNote) {
                    $bestNote = (float) $note['note'];
                    $bestIndex = $i;
                }
            }

            if ($bestIndex === null) {
                continue;
            }

            foreach ($filtered as $i => $note) {
                $code = (string) ($note['code_matiere'] ?? '');
                if ($i !== $bestIndex && in_array($code, $group, true)) {
                    unset($filtered[$i]);
                }
            }
        }

        return array_values($filtered);
    }
}
