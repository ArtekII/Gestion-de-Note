<?= $this->extend('dashboard.php') ?>

<?= $this->section('content') ?>
<div class="table-card" style="padding:16px;">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <div>
      <h3 style="margin:0;">Ajout de notes (admin)</h3>
      <div style="color:var(--c-muted);">Tu peux saisir plusieurs lignes puis enregistrer en une fois.</div>
    </div>
    <a href="<?= site_url('list') ?>" class="btn btn-ghost btn-sm">Retour a la liste</a>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error" style="margin-top:12px;"><?= esc((string) session()->getFlashdata('error')) ?></div>
  <?php endif; ?>

  <form method="post" action="<?= site_url('notes/store') ?>" style="margin-top:14px;">
    <?= csrf_field() ?>

    <div id="rows-wrapper" style="display:flex;flex-direction:column;gap:10px;">
      <div class="note-row" data-row-index="0" style="display:grid;grid-template-columns:1.4fr 1fr 1fr 1.8fr .8fr .8fr .8fr auto;gap:8px;align-items:end;">
        <div>
          <label>Nom etudiant</label>
          <input name="rows[0][nom]" type="text" class="filter-select" placeholder="Ex: Etudiant 3" required />
        </div>
        <div>
          <label>Semestre</label>
          <select name="rows[0][id_semestre]" class="filter-select" required>
            <option value="">Choisir</option>
            <?php foreach ($semestres as $semestre): ?>
              <option value="<?= esc((string) $semestre['id']) ?>"><?= esc($semestre['nom'] . ' - ' . $semestre['annee']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label>Option</label>
          <select name="rows[0][id_option]" class="filter-select">
            <option value="">Tronc commun</option>
            <?php foreach ($options as $option): ?>
              <option value="<?= esc((string) $option['id']) ?>"><?= esc($option['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label>Matiere</label>
          <select name="rows[0][id_matiere]" class="filter-select" required>
            <option value="">Choisir</option>
            <?php foreach ($matieres as $matiere): ?>
              <option value="<?= esc((string) $matiere['id']) ?>">
                <?= esc($matiere['code_matiere'] . ' - ' . $matiere['matiere_nom']) ?>
                <?= esc(' (' . ($matiere['semestre_nom'] ?? '?') . ' / ' . ($matiere['option_nom'] ?? 'Tronc commun') . ')') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label>Note</label>
          <input name="rows[0][note]" type="number" step="0.01" min="0" max="20" class="filter-select" required />
        </div>
        <div>
          <label>Credit</label>
          <input name="rows[0][credit]" type="number" min="1" class="filter-select" placeholder="Auto" />
        </div>
        <div>
          <label>Resultat</label>
          <select name="rows[0][resultat]" class="filter-select" required>
            <option value="">Choisir</option>
            <option value="B">B</option>
            <option value="AB">AB</option>
            <option value="P">P</option>
            <option value="Aj">Aj</option>
            <option value="Comp.">Comp.</option>
          </select>
        </div>
        <button type="button" class="btn btn-ghost btn-sm remove-row" style="display:none;">Suppr.</button>
      </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:14px;">
      <button type="button" id="add-row" class="btn btn-secondary btn-sm">+ Ajouter une ligne</button>
      <button type="submit" class="btn btn-primary btn-sm">Enregistrer les notes</button>
    </div>
  </form>
</div>

<script>
(() => {
  const wrapper = document.getElementById('rows-wrapper');
  const addRowBtn = document.getElementById('add-row');
  let rowIndex = 1;

  const updateRemoveButtons = () => {
    const rows = wrapper.querySelectorAll('.note-row');
    rows.forEach((row) => {
      const btn = row.querySelector('.remove-row');
      if (!btn) return;
      btn.style.display = rows.length > 1 ? 'inline-flex' : 'none';
      btn.disabled = rows.length <= 1;
      btn.onclick = () => {
        row.remove();
        updateRemoveButtons();
      };
    });
  };

  addRowBtn.addEventListener('click', () => {
    const firstRow = wrapper.querySelector('.note-row');
    const clone = firstRow.cloneNode(true);

    clone.setAttribute('data-row-index', String(rowIndex));
    clone.querySelectorAll('input, select').forEach((el) => {
      const oldName = el.getAttribute('name') || '';
      el.setAttribute('name', oldName.replace('rows[0]', `rows[${rowIndex}]`));
      if (el.tagName === 'SELECT') {
        el.selectedIndex = 0;
      } else {
        el.value = '';
      }
    });

    wrapper.appendChild(clone);
    rowIndex += 1;
    updateRemoveButtons();
  });

  updateRemoveButtons();
})();
</script>
<?= $this->endSection() ?>
