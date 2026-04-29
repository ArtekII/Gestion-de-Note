<?= $this->extend('dashboard.php') ?>

<?= $this->section('content') ?>
<div class="table-card">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Semestre</th>
        <th>Option</th>
        <th>Matiere</th>
        <th>Note</th>
        <th>Credit</th>
        <th>Resultat</th>
      </tr>
    </thead>
    <tbody>
      <?php if (! empty($etudiants)): ?>
        <?php foreach ($etudiants as $etudiant): ?>
          <tr>
            <td><?= esc((string) $etudiant['id']) ?></td>
            <td>
              <a href="<?= site_url('etudiants/' . $etudiant['id']) ?>">
                <?= esc($etudiant['nom']) ?>
              </a>
            </td>
            <td><?= esc((string) $etudiant['id_semestre']) ?></td>
            <td><?= esc((string) $etudiant['id_option']) ?></td>
            <td><?= esc((string) $etudiant['id_matiere']) ?></td>
            <td><?= esc((string) $etudiant['note']) ?></td>
            <td><?= esc((string) $etudiant['credit']) ?></td>
            <td><?= esc($etudiant['resultat']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="8" style="text-align:center;color:var(--c-muted)">Aucun etudiant trouve.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>
