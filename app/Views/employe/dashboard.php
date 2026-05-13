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
    <div class="metric-val"><?= $congesEnAttente['total_jours'] ?? 0 ?></div>
    <div class="metric-label">En attente</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
    <div class="metric-val"><?= $congesApprouvees['total_jours'] ?? 0 ?></div>
    <div class="metric-label">Approuvée</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
    <div class="metric-val">18</div>
    <div class="metric-label">Jours restants</div>
    <div class="metric-sub">sur 30 cette année</div>
  </div>
  <div class="metric">
    <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
    <div class="metric-val"><?= $congesRefusees['total_jours'] ?? 0 ?></div>
    <div class="metric-label">Refusée</div>
  </div>
</div>

<div class="data-card">
  <div class="data-card-head"><h3>Mes soldes de congés — <?= date('Y') ?></h3></div>
  <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
    <?php
    // Configuration des types de congé
    $congeTypes = [
        [
            'name' => 'Congé annuel',
            'data' => $congeAnnuelle ?? [],
            'total' => 30
        ],
        [
            'name' => 'Congé maladie', 
            'data' => $congeMaladie ?? [],
            'total' => 10
        ],
        [
            'name' => 'Congé spécial',
            'data' => $congeSpeciale ?? [],
            'total' => 5
        ]
    ];

    foreach($congeTypes as $type):
        // Calculer les jours pris (uniquement les congés approuvés)
        $joursPris = 0;
        foreach($type['data'] as $conge) {
            if($conge->statut === 'approuvee') {
                $joursPris += $conge->nb_jours;
            }
        }
        $joursRestants = $type['total'] - $joursPris;
        $pourcentage = $type['total'] > 0 ? ($joursPris / $type['total']) * 100 : 0;
        $barClass = $pourcentage > 80 ? 'warn' : '';
    ?>
    <div class="solde-card" style="margin:0">
      <div class="solde-header">
        <span class="solde-type"><?= $type['name'] ?></span>
        <span class="solde-nums"><strong><?= $joursRestants ?></strong> / <?= $type['total'] ?> j</span>
      </div>
      <div class="solde-bar"><div class="solde-fill <?= $barClass ?>" style="width:<?= $pourcentage ?>%"></div></div>
      <div class="solde-label"><?= $joursRestants ?> jours restants · <?= $joursPris ?> pris</div>
    </div>
    <?php endforeach; ?>
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
