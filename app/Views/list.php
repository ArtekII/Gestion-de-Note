<?= $this->extend('dashboard.php') ?>

<?= $this->section('content') ?>
<div class="table-card">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Semestres</th>
        <th>Options</th>
        <th>Nb notes</th>
        <th>Moyenne</th>
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
            <td><?= esc($etudiant['semestres'] ?? '-') ?></td>
            <td><?= esc($etudiant['options'] ?? '-') ?></td>
            <td><?= esc((string) ($etudiant['nb_notes'] ?? 0)) ?></td>
            <td><?= esc((string) ($etudiant['moyenne_brute'] ?? 0)) ?>/20</td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center;color:var(--c-muted)">Aucun etudiant trouve.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>
