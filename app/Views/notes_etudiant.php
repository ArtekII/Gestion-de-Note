<?= $this->extend('dashboard.php') ?>

<?= $this->section('content') ?>
<div class="table-card" style="padding:16px;">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
    <div>
      <h3 style="margin:0;"><?= esc($nomEtudiant) ?></h3>
      <div style="color:var(--c-muted);">Moyenne: <strong><?= esc((string) $moyenne) ?>/20</strong></div>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
      <?php if ((string) session('role') === 'admin'): ?>
        <form method="post" action="<?= site_url('etudiants/' . (int) $etudiantId . '/notes/reset') ?>" onsubmit="return confirm('Supprimer toutes les notes de cet etudiant ?');">
          <?= csrf_field() ?>
          <button type="submit" class="btn btn-danger btn-sm">Reinitialiser toutes les notes</button>
        </form>
      <?php endif; ?>
      <a href="<?= site_url('list') ?>" class="btn btn-ghost btn-sm">Retour a la liste</a>
    </div>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" style="margin-top:12px;"><?= esc((string) session()->getFlashdata('success')) ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error" style="margin-top:12px;"><?= esc((string) session()->getFlashdata('error')) ?></div>
  <?php endif; ?>

  <form method="get" style="margin-top:14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
    <label for="annee">Annee:</label>
    <select id="annee" name="annee" class="filter-select" onchange="this.form.submit()">
      <option value="" <?= ($selectedAnnee ?? '') === '' ? 'selected' : '' ?>>Toutes les annees</option>
      <?php foreach (($annees ?? []) as $annee) { ?>
        <option value="<?= esc($annee['value']) ?>" <?= ($selectedAnnee ?? '') === $annee['value'] ? 'selected' : '' ?>>
          <?= esc($annee['label']) ?>
        </option>
      <?php } ?>
    </select>

    <label for="semestre">Semestre:</label>
    <select id="semestre" name="semestre" class="filter-select" onchange="this.form.submit()">
      <option value="" <?= ($selectedSemestre ?? '') === '' ? 'selected' : '' ?>>Tous les semestres</option>
      <?php foreach (($semestres ?? []) as $semestre) { ?>
        <option value="<?= esc($semestre['value']) ?>" <?= ($selectedSemestre ?? '') === $semestre['value'] ? 'selected' : '' ?>>
          <?= esc($semestre['label']) ?>
        </option>
      <?php } ?>
    </select>

    <label for="option">Option:</label>
    <select id="option" name="option" class="filter-select" onchange="this.form.submit()">
      <option value="" <?= $selectedOption === '' ? 'selected' : '' ?>>Toutes les options</option>
      <?php foreach (($options ?? []) as $option) { ?>
        <?php $optionValue = (string) $option['value']; ?>
        <option value="<?= esc($optionValue) ?>" <?= $selectedOption === $optionValue ? 'selected' : '' ?>>
          <?= esc($option['label']) ?>
        </option>
      <?php } ?>
    </select>
    <noscript><button type="submit" class="btn btn-primary btn-sm">Filtrer</button></noscript>
  </form>

  <?php
    $returnQuery = http_build_query([
      'annee' => (string) ($selectedAnnee ?? ''),
      'semestre' => (string) ($selectedSemestre ?? ''),
      'option' => (string) ($selectedOption ?? ''),
    ]);
  ?>

  <table style="margin-top:16px;">
    <thead>
      <tr>
        <th>ID</th>
        <th>Semestre</th>
        <th>Option</th>
        <th>Matiere</th>
        <th>Note</th>
        <th>Credit</th>
        <th>Resultat</th>
        <?php if ((string) session('role') === 'admin'): ?>
          <th>Actions</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php if (! empty($notes)) { ?>
        <?php foreach ($notes as $ligne){ ?>
          <tr>
            <td><?= esc((string) $ligne['id']) ?></td>
            <td><?= esc($ligne['semestre_nom'] ?? ('Semestre #' . $ligne['id_semestre'])) ?></td>
            <td><?= esc($ligne['option_nom'] ?? ('Option #' . $ligne['id_option'])) ?></td>
            <td>
              <?php if (! empty($ligne['matiere_nom'])) { ?>
                <?= esc(($ligne['code_matiere'] ?? '') . ' - ' . $ligne['matiere_nom']) ?>
              <?php } else { ?>
                <?= esc('Matiere #' . $ligne['id_matiere']) ?>
              <?php } ?>
            </td>
            <td><?= esc((string) $ligne['note']) ?></td>
            <td><?= esc((string) $ligne['credit']) ?></td>
            <td><?= esc($ligne['resultat']) ?></td>
            <?php if ((string) session('role') === 'admin'): ?>
              <td>
                <div style="display:flex;gap:6px;align-items:center;">
                  <a href="<?= site_url('notes/' . (int) $ligne['id'] . '/edit?return_query=' . urlencode((string) $returnQuery)) ?>" class="btn btn-secondary btn-sm">Modifier</a>
                  <form method="post" action="<?= site_url('notes/' . (int) $ligne['id'] . '/delete') ?>" onsubmit="return confirm('Supprimer cette note ?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_query" value="<?= esc((string) $returnQuery) ?>" />
                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                  </form>
                </div>
              </td>
            <?php endif; ?>
          </tr>
        <?php }; ?>
      <?php } else { ?>
        <tr>
          <td colspan="<?= (string) ((string) session('role') === 'admin' ? 8 : 7) ?>" style="text-align:center;color:var(--c-muted);">Aucune note pour ce filtre.</td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>
