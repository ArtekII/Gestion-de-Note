<?= $this->extend('dashboard.php') ?>

<?= $this->section('content') ?>
<div class="table-card" style="padding:16px;">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
    <div>
      <h3 style="margin:0;"><?= esc($nomEtudiant) ?></h3>
      <div style="color:var(--c-muted);">Moyenne: <strong><?= esc((string) $moyenne) ?>/20</strong></div>
    </div>
    <a href="<?= site_url('list') ?>" class="btn btn-ghost btn-sm">Retour a la liste</a>
  </div>

  <form method="get" style="margin-top:14px;display:flex;align-items:center;gap:10px;">
    <label for="option">Option:</label>
    <select id="option" name="option" class="filter-select" onchange="this.form.submit()">
      <option value="" <?= $selectedOption === '' ? 'selected' : '' ?>>Toutes les options</option>
      <?php foreach ($options as $option) { ?>
        <?php $optionId = (string) $option['nom']; ?>
        <option value="<?= esc($optionId) ?>" <?= $selectedOption === $optionId ? 'selected' : '' ?>>
          <?= esc($option['option_nom'] ?? ('Option #' . $optionId)) ?>
        </option>
      <?php } ?>
    </select>
    <noscript><button type="submit" class="btn btn-primary btn-sm">Filtrer</button></noscript>
  </form>

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
          </tr>
        <?php }; ?>
      <?php } else { ?>
        <tr>
          <td colspan="7" style="text-align:center;color:var(--c-muted);">Aucune note pour ce filtre.</td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>
