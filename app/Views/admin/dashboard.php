<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>

<!-- Métriques admin -->
<div class="metrics">
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-people"></i></div></div>
    <div class="metric-val"><?= $metriques['employes_actifs'] ?? 0 ?></div>
    <div class="metric-label">Employés actifs</div>
    <div class="metric-sub up"><i class="bi bi-arrow-up-short"></i> <?= $metriques['augmentation_employes'] ?? '+0' ?> ce mois</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
    <div class="metric-val"><?= $metriques['demandes_attente'] ?? 0 ?></div>
    <div class="metric-label">Demandes en attente</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div>
    <div class="metric-val"><?= $metriques['approuvees_mois'] ?? 0 ?></div>
    <div class="metric-label">Approuvées ce mois</div>
    <div class="metric-sub up"><i class="bi bi-arrow-up-short"></i> <?= $metriques['augmentation_approuvees'] ?? '+0' ?> vs mois dernier</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-building"></i></div></div>
    <div class="metric-val"><?= $metriques['departements'] ?? 0 ?></div>
    <div class="metric-label">Départements</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-person-slash"></i></div></div>
    <div class="metric-val"><?= $metriques['absents_aujourd_hui'] ?? 0 ?></div>
    <div class="metric-label">Absents aujourd'hui</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

  <!-- Demandes récentes -->
  <div class="data-card" style="margin:0">
    <div class="data-card-head">
      <h3>Demandes récentes</h3>
      <a href="<?= base_url('rh/demandes') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir →</a>
    </div>
    <table class="tbl">
      <thead>
        <tr><th>Employé</th><th>Type</th><th>Durée</th><th>Statut</th></tr>
      </thead>
      <tbody>
        <?php if(!empty($demandesRecentes)): ?>
          <?php foreach($demandesRecentes as $demande): ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:7px">
                  <div class="avatar <?= $demande['avatar_class'] ?? 'av-green' ?>" style="width:28px;height:28px;font-size:.62rem">
                    <?= $demande['user_initials'] ?? 'XX' ?>
                  </div>
                  <span class="td-name" style="font-size:.84rem"><?= $demande['user_name'] ?? 'Employé' ?></span>
                </div>
              </td>
              <td><span class="type-badge t-<?= strtolower(str_replace(' ', '-', $demande['type'])) ?>"><?= ucfirst($demande['type']) ?></span></td>
              <td class="td-mono"><?= $demande['duree'] ?? 0 ?> j</td>
              <td><span class="statut s-<?= strtolower($demande['statut']) ?>"><?= ucfirst(str_replace('_', ' ', $demande['statut'])) ?></span></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" style="text-align:center;padding:1rem">
              <span style="color:var(--muted);font-size:.8rem">Aucune demande</span>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Absents du jour + soldes critiques -->
  <div style="display:flex;flex-direction:column;gap:1rem">
    <div class="data-card" style="margin:0">
      <div class="data-card-head">
        <h3><i class="bi bi-person-slash" style="color:var(--muted);margin-right:5px"></i>Absents aujourd'hui</h3>
      </div>
      <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
        <?php if(!empty($absents)): ?>
          <?php foreach($absents as $absent): ?>
            <div style="display:flex;align-items:center;gap:8px">
              <div class="avatar <?= $absent['avatar_class'] ?? 'av-green' ?>" style="width:30px;height:30px;font-size:.65rem">
                <?= $absent['initials'] ?? 'XX' ?>
              </div>
              <div>
                <div style="font-size:.83rem;font-weight:500;color:var(--ink)"><?= $absent['name'] ?? 'Employé' ?></div>
                <div style="font-size:.72rem;color:var(--muted)">
                  <?= $absent['type'] ?? 'Congé' ?> · retour <?= date('d/m', strtotime($absent['date_fin'])) ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="text-align:center;padding:1rem">
            <span style="color:var(--muted);font-size:.8rem">Personne absent aujourd'hui</span>
          </div>
        <?php endif; ?>
      </div>
    </div>
    
    <?php if(!empty($soldesCritiques)): ?>
      <div class="flash flash-warn" style="margin:0">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span style="font-size:.8rem">
          <?= count($soldesCritiques) ?> employé(s) ont un solde critique (≤ 2 jours). 
          <a href="#" style="color:var(--warn);font-weight:500">Voir les soldes →</a>
        </span>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php $this->endSection(); ?>
