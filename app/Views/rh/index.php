<?php $this->extend('layout/main'); ?>

<?php
$demandes = $demandes ?? [];
$departements = $departements ?? [];
$typesConge = $typesConge ?? [];

$userRole = strtolower($userRole ?? session()->get('role') ?? 'rh');
if ($userRole === 'responsable') {
  $userRole = 'rh';
}

$roleLabel = $roleLabel ?? ($userRole === 'rh' ? 'RH' : ucfirst($userRole));

$departementOptions = [];
foreach ($departements as $departement) {
  $nom = trim((string)($departement['nom'] ?? ''));
  if ($nom !== '') {
    $departementOptions[] = $nom;
  }
}

$departementOptions = array_values(array_unique($departementOptions));

$statutCounts = [
  'en_attente' => count(array_filter($demandes, fn ($d) => ($d['statut'] ?? '') === 'en_attente')),
  'approuvee' => count(array_filter($demandes, fn ($d) => ($d['statut'] ?? '') === 'approuvee')),
  'refusee' => count(array_filter($demandes, fn ($d) => ($d['statut'] ?? '') === 'refusee')),
];
?>

<?php $this->section('content'); ?>

<!-- Flash messages -->
<?php if (session()->has('success')): ?>
  <div class="flash flash-success">
    <i class="bi bi-check-circle-fill"></i>
    <?= session('success') ?>
  </div>
<?php endif; ?>

<!-- Filtre -->
<div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
  <button style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--forest);background:var(--forest);color:var(--white);cursor:pointer">
    Tous (<?= count($demandes) ?>)
  </button>
  <button style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer">
    En attente (<?= $statutCounts['en_attente'] ?>)
  </button>
  <button style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer">
    Approuvées (<?= $statutCounts['approuvee'] ?>)
  </button>
  <button style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer">
    Refusées (<?= $statutCounts['refusee'] ?>)
  </button>
  <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto;margin-left:auto">
    <option>Tous les départements</option>
    <?php if(!empty($departementOptions)): ?>
      <?php foreach($departementOptions as $departementNom): ?>
        <option value="<?= esc($departementNom) ?>"><?= esc($departementNom) ?></option>
      <?php endforeach; ?>
    <?php endif; ?>
  </select>
</div>

<div class="data-card">
  <div class="data-card-head"><h3>Toutes les demandes</h3></div>
  <table class="tbl">
    <thead>
      <tr>
        <th>Employé</th>
        <th>Type</th>
        <th>Période</th>
        <th>Durée</th>
        <th>Solde dispo</th>
        <th>Statut</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($demandes)): ?>
        <?php foreach($demandes as $demande): ?>
          <?php
            $type = $demande['type'] ?? $demande['type_libelle'] ?? 'congé';
            $userName = trim((string)($demande['user_name'] ?? (($demande['prenom'] ?? '') . ' ' . ($demande['nom'] ?? ''))));
            $userName = $userName !== '' ? $userName : 'Employé';
            $userDept = $demande['user_dept'] ?? $demande['departement_nom'] ?? 'Département';
            $initials = $demande['user_initials'] ?? strtoupper(mb_substr((string)($demande['prenom'] ?? $userName), 0, 1)) . strtoupper(mb_substr((string)($demande['nom'] ?? ''), 0, 1));
            $avatarClass = $demande['avatar_class'] ?? 'av-green';
          ?>
          <tr data-demande-id="<?= (int)($demande['id'] ?? 0) ?>">
            <td>
              <div class="profile-row">
                <div class="avatar <?= esc($avatarClass) ?>" style="width:32px;height:32px;font-size:.7rem">
                  <?= esc($initials) ?>
                </div>
                <div class="profile-info">
                  <div class="pname"><?= esc($userName) ?></div>
                  <div class="pdept">
                    <?= esc($userDept) ?> · 
                    <?= date('d M', strtotime($demande['date_debut'])) ?> → 
                    <?= date('d M', strtotime($demande['date_fin'])) ?>
                  </div>
                </div>
              </div>
            </td>
            <td><span class="type-badge t-<?= strtolower(str_replace(' ', '-', (string)$type)) ?>"><?= esc(ucfirst((string)$type)) ?></span></td>
            <td class="td-muted" style="font-size:.8rem">
              <?= date('d/m', strtotime($demande['date_debut'])) ?> – 
              <?= date('d/m/Y', strtotime($demande['date_fin'])) ?>
            </td>
            <td class="td-mono"><?= $demande['duree'] ?? 0 ?> j</td>
            <td>
              <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--success);font-weight:500">
                <?= $demande['solde_dispo'] ?? 0 ?> j
              </span>
              <span style="font-size:.72rem;color:var(--muted)"> dispo</span>
            </td>
            <td>
              <span class="statut s-<?= strtolower($demande['statut']) ?>">
                <?= ucfirst(str_replace('_', ' ', $demande['statut'])) ?>
              </span>
            </td>
            <td>
              <?php if($demande['statut'] === 'en_attente'): ?>
                <div class="action-btns">
                  <form action="<?= base_url('rh/demandes/' . $demande['id'] . '/approuver') ?>" method="post" style="display:inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-sm btn-approve">
                      <i class="bi bi-check-lg"></i> Approuver
                    </button>
                  </form>
                  <button class="btn-sm btn-refuse" onclick="afficherRefus(<?= (int)($demande['id'] ?? 0) ?>)">
                    <i class="bi bi-x-lg"></i> Refuser
                  </button>
                </div>
              <?php else: ?>
                <span class="td-muted" style="font-size:.75rem">Traité</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" style="text-align:center;padding:2rem">
            <div class="empty">
              <i class="bi bi-inbox"></i>
              <p>Aucune demande à traiter</p>
            </div>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Modal refus -->
<div id="modalRefus" style="display:none;margin-top:1.5rem">
  <div class="form-section" style="border-color:var(--danger-br);background:var(--danger-bg)">
    <h3 style="color:var(--danger)"><i class="bi bi-x-circle"></i> Confirmer le refus</h3>
    <form id="formRefus" method="post">
      <?= csrf_field() ?>
      <input type="hidden" id="refusDemandeId" name="id" value=""/>
      
      <div style="font-size:.875rem;color:var(--ink);margin-bottom:1rem" id="refusDetails">
        <!-- Rempli par JS -->
      </div>
      
      <div class="f-group">
        <label class="f-label">Commentaire pour l'employé (optionnel)</label>
        <textarea class="f-textarea" name="commentaire" 
                  placeholder="Ex : Solde insuffisant, veuillez contacter les RH..."></textarea>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn-sm btn-refuse" style="padding:9px 16px;font-size:.875rem">
          <i class="bi bi-x-lg"></i> Confirmer le refus
        </button>
        <button type="button" class="btn-secondary" onclick="cacherRefus()">
          <i class="bi bi-arrow-left"></i> Annuler
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function afficherRefus(id) {
  const row = document.querySelector(`tr[data-demande-id="${id}"]`);
  document.getElementById('refusDemandeId').value = id;
  const nameNode = row ? row.querySelector('.pname') : null;
  const demandeur = nameNode ? nameNode.textContent.trim() : 'cet employé';
  document.getElementById('refusDetails').innerHTML = 
    `Demande de <strong>${demandeur}</strong>`;
  document.getElementById('modalRefus').style.display = 'block';
  document.getElementById('formRefus').action = `<?= base_url('rh/demandes') ?>/${id}/refuser`;
}

function cacherRefus() {
  document.getElementById('modalRefus').style.display = 'none';
}
</script>

<?php $this->endSection(); ?>
