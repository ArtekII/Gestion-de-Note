<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SysInfo - <?= esc($pageTitle ?? 'Tableau de bord') ?></title>
  <link rel="stylesheet" href="<?= base_url('style.css') ?>" />
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24" width="18" height="18"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
      </div>
      <div>
        <div class="brand-name">SysInfo</div>
        <div class="brand-sub">v2.4.0</div>
      </div>
    </div>

    <div class="sidebar-section">Navigation</div>

    <a href="<?= site_url('dashboard') ?>" class="nav-item <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
      Tableau de bord
    </a>

    <a href="<?= site_url('list') ?>" class="nav-item <?= ($activeMenu ?? '') === 'list' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
      Liste etudiants
      <span class="nav-badge"><?= esc((string) ($totalEtudiants ?? 0)) ?></span>
    </a>

    <?php if ((string) session('role') === 'admin'): ?>
      <a href="<?= site_url('notes/create') ?>" class="nav-item <?= ($activeMenu ?? '') === 'note_form' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Ajouter des notes
      </a>
    <?php endif; ?>

    <div class="sidebar-bottom">
      <a href="<?= site_url('auth/logout') ?>" class="user-row">
        <div class="avatar">AD</div>
        <div class="user-info">
          <div class="name"><?= esc((string) session('email')) ?></div>
          <div class="role"><?= esc((string) session('role')) ?></div>
        </div>
      </a>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="topbar-title"><?= esc($topbarTitle ?? ($pageTitle ?? 'Tableau de bord')) ?></div>
      <div class="topbar-search">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Rechercher..." />
      </div>
    </div>

    <div class="content">
      <div class="page-header">
        <div>
          <h2><?= esc($pageTitle ?? 'Tableau de bord') ?></h2>
          <div class="breadcrumb">Accueil / <span><?= esc($pageTitle ?? 'Tableau de bord') ?></span></div>
        </div>
      </div>

      <?= $this->renderSection('content') ?>
    </div>
  </div>
</div>
</body>
</html>
