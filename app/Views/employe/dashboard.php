<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>

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

<div class="metrics">
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
    <div class="metric-val"><?= $nbConges['en_attente'] ?? 0 ?></div>
    <div class="metric-label">En attente</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
    <div class="metric-val"><?= $nbConges['approuvee'] ?? 0 ?></div>
    <div class="metric-label">Approuvées</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
    <div class="metric-val">18</div>
    <div class="metric-label">Jours restants</div>
    <div class="metric-sub">sur 30 cette année</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
    <div class="metric-val"><?= $nbConges['refusee'] ?? 0 ?></div>
    <div class="metric-label">Refusée</div>
  </div>
</div>

<div class="data-card">
  <div class="data-card-head"><h3>Mes soldes de congés — <?= date('Y') ?></h3></div>
  <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
    <div class="solde-card" style="margin:0">
      <div class="solde-header">
        <span class="solde-type">Congé annuel</span>
        <span class="solde-nums"><strong>18</strong> / 30 j</span>
      </div>
      <div class="solde-bar"><div class="solde-fill" style="width:60%"></div></div>
      <div class="solde-label">18 jours restants · 12 pris</div>
    </div>
    <div class="solde-card" style="margin:0">
      <div class="solde-header">
        <span class="solde-type">Congé maladie</span>
        <span class="solde-nums"><strong>8</strong> / 10 j</span>
      </div>
      <div class="solde-bar"><div class="solde-fill" style="width:80%"></div></div>
      <div class="solde-label">8 jours restants · 2 pris</div>
    </div>
    <div class="solde-card" style="margin:0">
      <div class="solde-header">
        <span class="solde-type">Congé spécial</span>
        <span class="solde-nums"><strong>1</strong> / 5 j</span>
      </div>
      <div class="solde-bar"><div class="solde-fill warn" style="width:20%"></div></div>
      <div class="solde-label">1 jour restant · 4 pris</div>
    </div>
  </div>
</div>

<div class="data-card">
  <div class="data-card-head">
    <h3>Mes dernières demandes</h3>
    <a href="<?= base_url('employer/demandes') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
  </div>
  <table class="tbl">
    <thead>
      <tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Action</th></tr>
    </thead>
    <tbody>
      <?php if(!empty($conges)): ?>
        <?php foreach(array_slice($conges, 0, 3) as $conge): ?>
          <tr>
            <td>
              <?php 
                $typeMap = [1 => 'annuel', 2 => 'maladie', 3 => 'special', 4 => 'sans-solde'];
                $typeClass = $typeMap[$conge['type_conge_id']] ?? 'annuel';
              ?>
              <span class="type-badge t-<?= $typeClass ?>"><?= ucfirst(str_replace('-', ' ', $typeClass)) ?></span>
            </td>
            <td class="td-muted"><?= date('d M Y', strtotime($conge['date_debut'])) ?></td>
            <td class="td-muted"><?= date('d M Y', strtotime($conge['date_fin'])) ?></td>
            <td class="td-mono">
              <?php 
                $debut = new DateTime($conge['date_debut']);
                $fin = new DateTime($conge['date_fin']);
                $duree = $fin->diff($debut)->days + 1;
                echo $duree;
              ?> j
            </td>
            <td>
              <?php
                $statutMap = ['en_attente' => 's-attente', 'approuvee' => 's-approuvee', 'refusee' => 's-refusee', 'annulee' => 's-annulee'];
                $statusClass = $statutMap[$conge['statut']] ?? 's-attente';
              ?>
              <span class="statut <?= $statusClass ?>"><?= ucfirst(str_replace('_', ' ', $conge['statut'])) ?></span>
            </td>
            <td>
              <?php if($conge['statut'] === 'en_attente'): ?>
                <form action="<?= base_url('employer/cancel/' . $conge['id']) ?>" method="post" style="display:inline">
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
