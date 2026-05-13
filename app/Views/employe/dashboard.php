<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>

<!-- Flash messages -->
<?php if (session()->has('success')): ?>
  <div class="flash flash-success">
    <i class="bi bi-check-circle-fill"></i>
    <?= session('success') ?>
  </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
  <div class="flash flash-error">
    <i class="bi bi-exclamation-circle-fill"></i>
    <?= session('error') ?>
  </div>
<?php endif; ?>

<!-- Métriques -->
<div class="metrics">
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
    <div class="metric-val"><?= $metriques['en_attente'] ?? 0 ?></div>
    <div class="metric-label">En attente</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
    <div class="metric-val"><?= $metriques['approuvees'] ?? 0 ?></div>
    <div class="metric-label">Approuvées</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
    <div class="metric-val"><?= $metriques['jours_restants'] ?? 0 ?></div>
    <div class="metric-label">Jours restants</div>
    <div class="metric-sub">sur <?= $metriques['jours_totaux'] ?? 30 ?> cette année</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
    <div class="metric-val"><?= $metriques['refusees'] ?? 0 ?></div>
    <div class="metric-label">Refusée</div>
  </div>
</div>

<!-- Soldes de congés -->
<div class="data-card">
  <div class="data-card-head"><h3>Mes soldes de congés — <?= date('Y') ?></h3></div>
  <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
    <?php foreach($soldes ?? [] as $solde): ?>
      <div class="solde-card" style="margin:0">
        <div class="solde-header">
          <span class="solde-type"><?= $solde['type'] ?></span>
          <span class="solde-nums"><strong><?= $solde['restants'] ?></strong> / <?= $solde['total'] ?> j</span>
        </div>
        <div class="solde-bar">
          <div class="solde-fill <?= $solde['urgence'] ?? '' ?>" style="width:<?= ($solde['restants'] / $solde['total']) * 100 ?>%"></div>
        </div>
        <div class="solde-label"><?= $solde['restants'] ?> jours restants · <?= $solde['pris'] ?> pris</div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Dernières demandes -->
<div class="data-card">
  <div class="data-card-head">
    <h3>Mes dernières demandes</h3>
    <a href="<?= base_url('employe/demandes') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
  </div>
  <table class="tbl">
    <thead>
      <tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Action</th></tr>
    </thead>
    <tbody>
      <?php if(!empty($demandes)): ?>
        <?php foreach($demandes as $demande): ?>
          <tr>
            <td><span class="type-badge t-<?= strtolower(str_replace(' ', '-', $demande['type'])) ?>"><?= ucfirst($demande['type']) ?></span></td>
            <td class="td-muted"><?= date('d M Y', strtotime($demande['date_debut'])) ?></td>
            <td class="td-muted"><?= date('d M Y', strtotime($demande['date_fin'])) ?></td>
            <td class="td-mono"><?= $demande['duree'] ?> j</td>
            <td><span class="statut s-<?= strtolower($demande['statut']) ?>"><?= ucfirst($demande['statut']) ?></span></td>
            <td>
              <?php if($demande['statut'] === 'en attente'): ?>
                <form action="<?= base_url('employe/demandes/' . $demande['id'] . '/annuler') ?>" method="post" style="display:inline">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn-sm btn-cancel"><i class="bi bi-x"></i> Annuler</button>
                </form>
              <?php else: ?>
                <span class="td-muted" style="font-size:.75rem">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center;padding:2rem">
            <div class="empty">
              <i class="bi bi-inbox"></i>
              <p>Aucune demande pour le moment</p>
            </div>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $this->endSection(); ?>
