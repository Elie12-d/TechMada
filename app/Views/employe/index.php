<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>

<div class="data-card">
  <div class="data-card-head">
    <h3>Toutes mes demandes</h3>
    <div style="display:flex;gap:6px">
      <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto">
        <option value="">Tous les statuts</option>
        <option value="en_attente">En attente</option>
        <option value="approuvee">Approuvée</option>
        <option value="refusee">Refusée</option>
        <option value="annulee">Annulée</option>
      </select>
    </div>
  </div>
  <table class="tbl">
    <thead>
      <tr>
        <th>Type</th>
        <th>Début</th>
        <th>Fin</th>
        <th>Durée</th>
        <th>Statut</th>
        <th>Commentaire RH</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($demandes)): ?>
        <?php foreach($demandes as $demande): ?>
          <tr>
            <td>
              <span class="type-badge t-<?= strtolower(str_replace(' ', '-', $demande['type'])) ?>">
                <?= ucfirst($demande['type']) ?>
              </span>
            </td>
            <td class="td-muted"><?= date('d M Y', strtotime($demande['date_debut'])) ?></td>
            <td class="td-muted"><?= date('d M Y', strtotime($demande['date_fin'])) ?></td>
            <td class="td-mono"><?= $demande['duree'] ?? 0 ?> j</td>
            <td>
              <span class="statut s-<?= strtolower($demande['statut']) ?>">
                <?= ucfirst(str_replace('_', ' ', $demande['statut'])) ?>
              </span>
            </td>
            <td style="font-size:.78rem;color:var(--muted)">
              <?php if($demande['commentaire_rh']): ?>
                <span style="color:var(--success)"><i class="bi bi-check-circle"></i> <?= $demande['commentaire_rh'] ?></span>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
            <td>
              <?php if($demande['statut'] === 'en_attente'): ?>
                <form action="<?= base_url('employe/demandes/' . $demande['id'] . '/annuler') ?>" method="post" style="display:inline">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn-sm btn-cancel" onclick="return confirm('Êtes-vous sûr ?')">
                    <i class="bi bi-x"></i> Annuler
                  </button>
                </form>
              <?php else: ?>
                <span class="td-muted" style="font-size:.75rem">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" style="text-align:center;padding:2rem">
            <div class="empty">
              <i class="bi bi-inbox"></i>
              <p>Aucune demande de congé</p>
            </div>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $this->endSection(); ?>
