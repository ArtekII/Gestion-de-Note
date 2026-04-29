<?php

namespace App\Controllers;

use App\Models\Etudiant;
use CodeIgniter\Exceptions\PageNotFoundException;

class EtudiantController extends BaseController
{
    private const OPTIONAL_SUBJECT_GROUPS_FALLBACK = [
        [
            'id_semestre' => 2,
            'id_option' => null,
            'codes' => ['MTH203', 'MTH206'],
        ],
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

    public function createNoteForm(): string
    {
        $db = db_connect();

        $semestres = $db->table('semestre')
            ->select('id, nom, annee')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $options = $db->table('options')
            ->select('id, nom')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $matieres = $db->table('matiere m')
            ->select('m.id, m.id_semestre, m.id_option, m.code_matiere, m.coefficient, lm.Nom_matiere AS matiere_nom, s.nom AS semestre_nom, o.nom AS option_nom')
            ->join('liste_matiere lm', 'lm.code_matiere = m.code_matiere', 'left')
            ->join('semestre s', 's.id = m.id_semestre', 'left')
            ->join('options o', 'o.id = m.id_option', 'left')
            ->orderBy('m.id', 'ASC')
            ->get()
            ->getResultArray();

        return view('form', [
            'pageTitle' => 'Ajouter des notes',
            'topbarTitle' => 'Saisie des notes',
            'activeMenu' => 'note_form',
            'totalEtudiants' => (new Etudiant())->countAllResults(),
            'semestres' => $semestres,
            'options' => $options,
            'matieres' => $matieres,
        ]);
    }

    public function storeNotes()
    {
        $rows = $this->request->getPost('rows');
        if (! is_array($rows) || $rows === []) {
            return redirect()->back()->with('error', 'Aucune ligne de note à enregistrer.');
        }

        $db = db_connect();
        $matieres = $db->table('matiere')->select('id, coefficient')->get()->getResultArray();
        $coeffByMatiere = [];
        foreach ($matieres as $matiere) {
            $coeffByMatiere[(int) $matiere['id']] = (int) $matiere['coefficient'];
        }

        $toInsert = [];
        foreach ($rows as $row) {
            $nom = trim((string) ($row['nom'] ?? ''));
            $idSemestre = (string) ($row['id_semestre'] ?? '');
            $idOption = (string) ($row['id_option'] ?? '');
            $idMatiere = (string) ($row['id_matiere'] ?? '');
            $note = (string) ($row['note'] ?? '');
            $credit = (string) ($row['credit'] ?? '');
            $resultat = trim((string) ($row['resultat'] ?? ''));

            // Skip empty rows created by UI.
            if ($nom === '' && $idMatiere === '' && $note === '') {
                continue;
            }

            if ($nom === '' || ! ctype_digit($idSemestre) || ! ctype_digit($idMatiere) || $note === '' || $resultat === '') {
                return redirect()->back()->withInput()->with('error', 'Une ou plusieurs lignes sont incomplètes.');
            }

            if (! is_numeric($note)) {
                return redirect()->back()->withInput()->with('error', 'La note doit être numérique.');
            }

            $noteFloat = (float) $note;
            if ($noteFloat < 0 || $noteFloat > 20) {
                return redirect()->back()->withInput()->with('error', 'La note doit être entre 0 et 20.');
            }

            $idMatiereInt = (int) $idMatiere;
            $creditInt = ctype_digit($credit) ? (int) $credit : ($coeffByMatiere[$idMatiereInt] ?? 0);
            if ($creditInt <= 0) {
                return redirect()->back()->withInput()->with('error', 'Crédit invalide.');
            }

            $toInsert[] = [
                'nom' => $nom,
                'id_semestre' => (int) $idSemestre,
                'id_option' => ctype_digit($idOption) ? (int) $idOption : null,
                'id_matiere' => $idMatiereInt,
                'note' => $noteFloat,
                'credit' => $creditInt,
                'resultat' => $resultat,
            ];
        }

        if ($toInsert === []) {
            return redirect()->back()->withInput()->with('error', 'Aucune ligne valide à enregistrer.');
        }

        $model = new Etudiant();
        if (! $model->insertBatch($toInsert)) {
            $error = implode(' | ', $model->errors() ?? []);
            return redirect()->back()->withInput()->with('error', $error !== '' ? $error : 'Erreur lors de l\'enregistrement.');
        }

        return redirect()->to(site_url('list'))->with('success', count($toInsert) . ' note(s) ajoutée(s).');
    }

    public function editNoteForm(int $id): string
    {
        $db = db_connect();
        $note = $db->table('etudiant')->where('id', $id)->get()->getRowArray();
        if ($note === null) {
            throw PageNotFoundException::forPageNotFound('Note introuvable.');
        }

        $semestres = $db->table('semestre')
            ->select('id, nom, annee')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $options = $db->table('options')
            ->select('id, nom')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $matieres = $db->table('matiere m')
            ->select('m.id, m.id_semestre, m.id_option, m.code_matiere, m.coefficient, lm.Nom_matiere AS matiere_nom, s.nom AS semestre_nom, o.nom AS option_nom')
            ->join('liste_matiere lm', 'lm.code_matiere = m.code_matiere', 'left')
            ->join('semestre s', 's.id = m.id_semestre', 'left')
            ->join('options o', 'o.id = m.id_option', 'left')
            ->orderBy('m.id', 'ASC')
            ->get()
            ->getResultArray();

        $returnQuery = (string) ($this->request->getGet('return_query') ?? '');

        return view('note_edit', [
            'pageTitle' => 'Modifier une note',
            'topbarTitle' => 'Edition de note',
            'activeMenu' => 'note_form',
            'totalEtudiants' => (new Etudiant())->countAllResults(),
            'note' => $note,
            'semestres' => $semestres,
            'options' => $options,
            'matieres' => $matieres,
            'returnQuery' => $returnQuery,
            'studentRefId' => $this->resolveStudentReferenceId((string) ($note['nom'] ?? '')) ?? (int) ($note['id'] ?? 0),
        ]);
    }

    public function updateNote(int $id)
    {
        $db = db_connect();
        $existing = $db->table('etudiant')->where('id', $id)->get()->getRowArray();
        if ($existing === null) {
            throw PageNotFoundException::forPageNotFound('Note introuvable.');
        }

        $nom = trim((string) $this->request->getPost('nom'));
        $idSemestre = (string) $this->request->getPost('id_semestre');
        $idOption = (string) $this->request->getPost('id_option');
        $idMatiere = (string) $this->request->getPost('id_matiere');
        $note = (string) $this->request->getPost('note');
        $credit = (string) $this->request->getPost('credit');
        $resultat = trim((string) $this->request->getPost('resultat'));
        $returnQuery = (string) $this->request->getPost('return_query');

        if ($nom === '' || ! ctype_digit($idSemestre) || ! ctype_digit($idMatiere) || $note === '' || $resultat === '') {
            return redirect()->back()->withInput()->with('error', 'Tous les champs obligatoires doivent etre remplis.');
        }

        if (! is_numeric($note)) {
            return redirect()->back()->withInput()->with('error', 'La note doit etre numerique.');
        }

        $noteFloat = (float) $note;
        if ($noteFloat < 0 || $noteFloat > 20) {
            return redirect()->back()->withInput()->with('error', 'La note doit etre entre 0 et 20.');
        }

        $creditInt = ctype_digit($credit) ? (int) $credit : 0;
        if ($creditInt <= 0) {
            return redirect()->back()->withInput()->with('error', 'Le credit doit etre superieur a 0.');
        }

        $payload = [
            'nom' => $nom,
            'id_semestre' => (int) $idSemestre,
            'id_option' => ctype_digit($idOption) ? (int) $idOption : null,
            'id_matiere' => (int) $idMatiere,
            'note' => $noteFloat,
            'credit' => $creditInt,
            'resultat' => $resultat,
        ];

        $model = new Etudiant();
        if (! $model->update($id, $payload)) {
            $error = implode(' | ', $model->errors() ?? []);
            return redirect()->back()->withInput()->with('error', $error !== '' ? $error : 'Erreur pendant la modification.');
        }

        $studentRefId = $this->resolveStudentReferenceId($nom) ?? (int) $id;

        return redirect()->to($this->buildStudentNotesUrl($studentRefId, $returnQuery))
            ->with('success', 'Note modifiee avec succes.');
    }

    public function deleteNote(int $id)
    {
        $db = db_connect();
        $note = $db->table('etudiant')->where('id', $id)->get()->getRowArray();
        if ($note === null) {
            throw PageNotFoundException::forPageNotFound('Note introuvable.');
        }

        $returnQuery = (string) $this->request->getPost('return_query');
        $model = new Etudiant();
        $nom = (string) ($note['nom'] ?? '');
        $model->delete($id);

        $studentRefId = $this->resolveStudentReferenceId($nom, $id);
        if ($studentRefId === null) {
            return redirect()->to(site_url('list'))->with('success', 'Note supprimee. Aucun enregistrement restant pour cet etudiant.');
        }

        return redirect()->to($this->buildStudentNotesUrl($studentRefId, $returnQuery))->with('success', 'Note supprimee.');
    }

    public function resetStudentNotes(int $id)
    {
        $db = db_connect();
        $etudiantRef = $db->table('etudiant')->where('id', $id)->get()->getRowArray();
        if ($etudiantRef === null) {
            throw PageNotFoundException::forPageNotFound('Etudiant introuvable.');
        }

        $nom = (string) ($etudiantRef['nom'] ?? '');
        if ($nom === '') {
            throw PageNotFoundException::forPageNotFound('Etudiant introuvable.');
        }

        $db->table('etudiant')->where('nom', $nom)->delete();

        return redirect()->to(site_url('list'))->with('success', 'Toutes les notes de ' . $nom . ' ont ete reinitialisees.');
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
        $optionalGroups = $this->loadOptionalSubjectGroups();
        foreach ($optionalGroups as $group) {
            $bestIndex = null;
            $bestNote = -INF;

            foreach ($filtered as $i => $note) {
                $code = (string) ($note['code_matiere'] ?? '');
                if (! in_array($code, $group['codes'], true)) {
                    continue;
                }

                if (! $this->noteMatchesGroupScope($note, $group)) {
                    continue;
                }

                if ((float) $note['note'] > $bestNote) {
                    $bestNote = (float) $note['note'];
                    $bestIndex = $i;
                }
            }

            if ($bestIndex === null) {
                continue;
            }

            foreach ($filtered as $i => $note) {
                $code = (string) ($note['code_matiere'] ?? '');
                if ($i === $bestIndex) {
                    continue;
                }
                if (! in_array($code, $group['codes'], true)) {
                    continue;
                }
                if (! $this->noteMatchesGroupScope($note, $group)) {
                    continue;
                }
                if ($i !== $bestIndex) {
                    unset($filtered[$i]);
                }
            }
        }

        return array_values($filtered);
    }

    private function loadOptionalSubjectGroups(): array
    {
        $db = db_connect();
        if (! $db->tableExists('groupe_optionnel') || ! $db->tableExists('groupe_optionnel_matiere')) {
            return self::OPTIONAL_SUBJECT_GROUPS_FALLBACK;
        }

        $rows = $db->table('groupe_optionnel g')
            ->select('g.id, g.id_semestre, g.id_option, gm.code_matiere')
            ->join('groupe_optionnel_matiere gm', 'gm.id_groupe_optionnel = g.id')
            ->orderBy('g.id', 'ASC')
            ->get()
            ->getResultArray();

        if ($rows === []) {
            return self::OPTIONAL_SUBJECT_GROUPS_FALLBACK;
        }

        $groups = [];
        foreach ($rows as $row) {
            $groupId = (int) $row['id'];
            if (! isset($groups[$groupId])) {
                $groups[$groupId] = [
                    'id_semestre' => $row['id_semestre'] !== null ? (int) $row['id_semestre'] : null,
                    'id_option' => $row['id_option'] !== null ? (int) $row['id_option'] : null,
                    'codes' => [],
                ];
            }

            $code = trim((string) ($row['code_matiere'] ?? ''));
            if ($code !== '') {
                $groups[$groupId]['codes'][] = $code;
            }
        }

        foreach ($groups as $groupId => $group) {
            $groups[$groupId]['codes'] = array_values(array_unique($group['codes']));
        }

        return array_values(array_filter($groups, static function (array $group): bool {
            return ! empty($group['codes']);
        }));
    }

    private function noteMatchesGroupScope(array $note, array $group): bool
    {
        $noteSemestre = isset($note['id_semestre']) ? (int) $note['id_semestre'] : null;
        $noteOption = $note['id_option'] !== null ? (int) $note['id_option'] : null;

        if ($group['id_semestre'] !== null && $noteSemestre !== (int) $group['id_semestre']) {
            return false;
        }

        if ($group['id_option'] !== null && $noteOption !== (int) $group['id_option']) {
            return false;
        }

        return true;
    }

    private function buildStudentNotesUrl(int $studentId, string $returnQuery): string
    {
        $base = site_url('etudiants/' . $studentId);
        $query = ltrim($returnQuery, '?');
        if ($query === '') {
            return $base;
        }

        return $base . '?' . $query;
    }

    private function resolveStudentReferenceId(string $nom, ?int $excludeId = null): ?int
    {
        $nom = trim($nom);
        if ($nom === '') {
            return null;
        }

        $builder = db_connect()->table('etudiant')->select('MIN(id) AS id')->where('nom', $nom);
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        $row = $builder->get()->getRowArray();
        if (! isset($row['id']) || $row['id'] === null) {
            return null;
        }

        return (int) $row['id'];
    }
}
