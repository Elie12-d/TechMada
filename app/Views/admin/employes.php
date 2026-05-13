<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>

<!-- Formulaire ajout -->
<div class="form-section">
  <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i>Ajouter un employé</h3>
  
  <form action="<?= base_url('admin/employes/store') ?>" method="post">
    <?= csrf_field() ?>
    
    <div class="form-grid-2" style="margin-bottom:1rem">
      <div class="f-group">
        <label class="f-label">Prénom</label>
        <input type="text" class="f-input" name="prenom" placeholder="Jean" value="<?= old('prenom') ?>"/>
        <?php if(isset($validation) && $validation->hasError('prenom')): ?>
          <div class="f-error"><?= $validation->getError('prenom') ?></div>
        <?php endif; ?>
      </div>
      <div class="f-group">
        <label class="f-label">Nom</label>
        <input type="text" class="f-input" name="nom" placeholder="Rakoto" value="<?= old('nom') ?>"/>
        <?php if(isset($validation) && $validation->hasError('nom')): ?>
          <div class="f-error"><?= $validation->getError('nom') ?></div>
        <?php endif; ?>
      </div>
      <div class="f-group">
        <label class="f-label">Email</label>
        <input type="email" class="f-input" name="email" placeholder="jean.rakoto@techmada.mg" value="<?= old('email') ?>"/>
        <?php if(isset($validation) && $validation->hasError('email')): ?>
          <div class="f-error"><?= $validation->getError('email') ?></div>
        <?php endif; ?>
      </div>
      <div class="f-group">
        <label class="f-label">Mot de passe initial</label>
        <input type="password" class="f-input" name="password" placeholder="À communiquer à l'employé"/>
        <?php if(isset($validation) && $validation->hasError('password')): ?>
          <div class="f-error"><?= $validation->getError('password') ?></div>
        <?php endif; ?>
      </div>
      <div class="f-group">
        <label class="f-label">Département</label>
        <select class="f-select" name="departement_id">
          <option value="">-- Sélectionner --</option>
          <?php foreach($departements ?? [] as $dept): ?>
            <option value="<?= $dept['id'] ?>" <?= old('departement_id') == $dept['id'] ? 'selected' : '' ?>>
              <?= $dept['nom'] ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if(isset($validation) && $validation->hasError('departement_id')): ?>
          <div class="f-error"><?= $validation->getError('departement_id') ?></div>
        <?php endif; ?>
      </div>
      <div class="f-group">
        <label class="f-label">Rôle</label>
        <select class="f-select" name="role">
          <option value="employe" <?= old('role') === 'employe' ? 'selected' : '' ?>>Employé</option>
          <option value="rh" <?= old('role') === 'rh' ? 'selected' : '' ?>>Responsable RH</option>
          <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
        </select>
      </div>
      <div class="f-group">
        <label class="f-label">Date d'embauche</label>
        <input type="date" class="f-input" name="date_embauche" value="<?= old('date_embauche', date('Y-m-d')) ?>"/>
        <?php if(isset($validation) && $validation->hasError('date_embauche')): ?>
          <div class="f-error"><?= $validation->getError('date_embauche') ?></div>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="flash flash-info" style="margin-bottom:1rem">
      <i class="bi bi-info-circle-fill"></i>
      <span style="font-size:.82rem">Les soldes de congés seront initialisés automatiquement selon les types de congé configurés.</span>
    </div>
    
    <div class="form-actions">
      <button type="submit" class="btn-forest"><i class="bi bi-plus"></i> Créer l'employé</button>
      <button type="reset" class="btn-secondary">Réinitialiser</button>
    </div>
  </form>
</div>

<!-- Liste employés -->
<div class="data-card">
  <div class="data-card-head">
    <h3>Tous les employés</h3>
    <div style="display:flex;gap:6px">
      <input type="text" class="f-input" placeholder="Rechercher..." style="width:200px;padding:6px 10px;font-size:.8rem" id="searchInput"/>
      <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto" id="filterDept">
        <option value="">Tous les depts</option>
        <?php foreach($departements ?? [] as $dept): ?>
          <option value="<?= $dept['id'] ?>"><?= $dept['nom'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <table class="tbl">
    <thead>
      <tr>
        <th>Employé</th>
        <th>Département</th>
        <th>Rôle</th>
        <th>Embauche</th>
        <th>Statut</th>
        <th>Solde annuel</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($employes)): ?>
        <?php foreach($employes as $emp): ?>
          <tr data-search="<?= strtolower($emp['nom'] . ' ' . $emp['prenom']) ?>" data-dept="<?= $emp['departement_id'] ?? '' ?>">
            <td>
              <div class="profile-row">
                <div class="avatar <?= $emp['avatar_class'] ?? 'av-green' ?>" style="width:32px;height:32px;font-size:.68rem">
                  <?= strtoupper(substr($emp['prenom'], 0, 1)) . strtoupper(substr($emp['nom'], 0, 1)) ?>
                </div>
                <div class="profile-info">
                  <div class="pname"><?= $emp['prenom'] ?> <?= $emp['nom'] ?></div>
                  <div class="pdept"><?= $emp['email'] ?></div>
                </div>
              </div>
            </td>
            <td class="td-muted"><?= $emp['dept_libelle'] ?? 'N/A' ?></td>
            <td>
              <span class="type-badge" style="background:#f1efe8;color:#444441">
                <?= ucfirst($emp['role']) ?>
              </span>
            </td>
            <td class="td-muted td-mono" style="font-size:.78rem"><?= $emp['date_embauche'] ?></td>
            <td>
              <span class="statut <?= $emp['statut'] === 'actif' ? 's-approuvee' : 's-annulee' ?>" style="font-size:.68rem">
                <?= ucfirst($emp['statut']) ?>
              </span>
            </td>
            <td>
              <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--forest)">
                <?= $emp['solde_annuel'] ?? 30 ?> / 30 j
              </span>
            </td>
            <td>
              <div class="action-btns">
                <a href="<?= base_url('admin/employes/' . $emp['id'] . '/edit') ?>" class="btn-sm btn-edit">
                  <i class="bi bi-pencil"></i> Éditer
                </a>
                <form action="<?= base_url('admin/employes/' . $emp['id'] . '/delete') ?>" method="post" style="display:inline" 
                      onsubmit="return confirm('Êtes-vous sûr ?')">
                  <?= csrf_field() ?>
                  <input type="hidden" name="_method" value="DELETE">
                  <button type="submit" class="btn-sm btn-del"><i class="bi bi-slash-circle"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" style="text-align:center;padding:2rem">
            <div class="empty">
              <i class="bi bi-people"></i>
              <p>Aucun employé</p>
            </div>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
// Filtre de recherche
document.getElementById('searchInput')?.addEventListener('keyup', function() {
  const query = this.value.toLowerCase();
  document.querySelectorAll('tbody tr').forEach(row => {
    const search = row.getAttribute('data-search');
    row.style.display = search && search.includes(query) ? '' : 'none';
  });
});

// Filtre département
document.getElementById('filterDept')?.addEventListener('change', function() {
  const dept = this.value;
  document.querySelectorAll('tbody tr').forEach(row => {
    const rowDept = row.getAttribute('data-dept');
    row.style.display = !dept || rowDept === dept ? '' : 'none';
  });
});
</script>

<?php $this->endSection(); ?>
