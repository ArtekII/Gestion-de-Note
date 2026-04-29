<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SysInfo — Connexion</title>
  <link rel="stylesheet" href="<?= base_url('style.css') ?>" />
</head>
<body>

<div class="login-page">
  <div class="login-card">

    <div class="login-logo">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24" width="22" height="22"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
      </div>
      <div>
        <h1>Gestion de note</h1>
        <span>Système d'Information</span>
      </div>
    </div>

    <h2>Connexion</h2>
    <p class="subtitle">Connectez-vous a votre espace de travail</p>


    <form method="POST" action="<?= base_url('auth/login-process') ?>">
      <?= csrf_field() ?>

      <div class="field-group">
        <label>Adresse e-mail</label>
        <div class="input-wrap">
          <div class="icon">
            <svg viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
          </div>
          <input type="email" name="email" placeholder="vous@exemple.com" value="admin@gmail.com" required />
        </div>
      </div>

      <div class="field-group">
        <label>Mot de passe</label>
        <div class="input-wrap">
          <div class="icon">
            <svg viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <input type="password" name="password" placeholder="••••••••" value="admin123" required />
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-full">
        <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Se connecter
      </button>
    </form>

  </div>
</div>

<script src="<?= base_url('script.js') ?>"></script>
</body>
</html>
