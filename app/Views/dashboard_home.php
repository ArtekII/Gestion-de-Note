<?= $this->extend('dashboard.html') ?>

<?= $this->section('content') ?>
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-header">
      <div class="kpi-label">Etudiants enregistres</div>
    </div>
    <div class="kpi-value"><?= esc((string) ($totalEtudiants ?? 0)) ?></div>
    <div class="kpi-delta up">Donnees chargees depuis la table `etudiant`</div>
  </div>
</div>
<?= $this->endSection() ?>
