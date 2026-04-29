<?= $this->extend('dashboard.php') ?>

<?= $this->section('content') ?>
<div class="table-card" style="padding:16px;">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <div>
      <h3 style="margin:0;">Modifier la note #<?= esc((string) ($note['id'] ?? '')) ?></h3>
      <div style="color:var(--c-muted);">Mets a jour les informations de la note.</div>
    </div>
    <a href="<?= site_url('etudiants/' . (int) ($studentRefId ?? 0) . ($returnQuery !== '' ? '?' . $returnQuery : '')) ?>" class="btn btn-ghost btn-sm">Retour aux notes</a>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error" style="margin-top:12px;"><?= esc((string) session()->getFlashdata('error')) ?></div>
  <?php endif; ?>

  <form method="post" action="<?= site_url('notes/' . (int) ($note['id'] ?? 0) . '/update') ?>" style="margin-top:14px;">
    <?= csrf_field() ?>
    <input type="hidden" name="return_query" value="<?= esc((string) ($returnQuery ?? '')) ?>" />

    <div style="display:grid;grid-template-columns:1.4fr 1fr 1fr 1.8fr .8fr .8fr .8fr;gap:8px;align-items:end;">
      <div>
        <label>Nom etudiant</label>
        <input name="nom" type="text" class="filter-select" required value="<?= esc((string) old('nom', $note['nom'] ?? '')) ?>" />
      </div>
      <div>
        <label>Semestre</label>
        <select name="id_semestre" class="filter-select" required>
          <option value="">Choisir</option>
          <?php foreach ($semestres as $semestre): ?>
            <?php $semId = (int) $semestre['id']; ?>
            <option value="<?= esc((string) $semId) ?>" <?= (int) old('id_semestre', (string) ($note['id_semestre'] ?? 0)) === $semId ? 'selected' : '' ?>>
              <?= esc($semestre['nom'] . ' - ' . $semestre['annee']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Option</label>
        <select name="id_option" class="filter-select">
          <option value="">Tronc commun</option>
          <?php foreach ($options as $option): ?>
            <?php $optId = (int) $option['id']; ?>
            <option value="<?= esc((string) $optId) ?>" <?= (int) old('id_option', (string) ($note['id_option'] ?? 0)) === $optId ? 'selected' : '' ?>>
              <?= esc($option['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Matiere</label>
        <select name="id_matiere" class="filter-select" required>
          <option value="">Choisir</option>
          <?php foreach ($matieres as $matiere): ?>
            <?php $matId = (int) $matiere['id']; ?>
            <option value="<?= esc((string) $matId) ?>" <?= (int) old('id_matiere', (string) ($note['id_matiere'] ?? 0)) === $matId ? 'selected' : '' ?>>
              <?= esc($matiere['code_matiere'] . ' - ' . $matiere['matiere_nom']) ?>
              <?= esc(' (' . ($matiere['semestre_nom'] ?? '?') . ' / ' . ($matiere['option_nom'] ?? 'Tronc commun') . ')') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Note</label>
        <input name="note" type="number" step="0.01" min="0" max="20" class="filter-select" required value="<?= esc((string) old('note', $note['note'] ?? '')) ?>" />
      </div>
      <div>
        <label>Credit</label>
        <input name="credit" type="number" min="1" class="filter-select" required value="<?= esc((string) old('credit', $note['credit'] ?? '')) ?>" />
      </div>
      <div>
        <label>Resultat</label>
        <?php $selectedResultat = (string) old('resultat', $note['resultat'] ?? ''); ?>
        <select name="resultat" class="filter-select" required>
          <option value="">Choisir</option>
          <?php foreach (['B', 'AB', 'P', 'Aj', 'Comp.'] as $res): ?>
            <option value="<?= esc($res) ?>" <?= $selectedResultat === $res ? 'selected' : '' ?>><?= esc($res) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:14px;">
      <button type="submit" class="btn btn-primary btn-sm">Enregistrer la modification</button>
      <a href="<?= site_url('etudiants/' . (int) ($studentRefId ?? 0) . ($returnQuery !== '' ? '?' . $returnQuery : '')) ?>" class="btn btn-ghost btn-sm">Annuler</a>
    </div>
  </form>
</div>
<?= $this->endSection() ?>
