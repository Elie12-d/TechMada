<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>

<div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start" class="form-layout">

  <!-- Formulaire principal -->
  <div>
    <div class="form-section">
      <h3>Détails de la demande</h3>

      <form action="<?= base_url('employe/demandes/store') ?>" method="post">
        <?= csrf_field() ?>

        <div class="f-group" style="margin-bottom:1rem">
          <label class="f-label">Type de congé <span style="color:var(--danger)">*</span></label>
          <select class="f-select" name="type_conge_id" required>
            <option value="">-- Choisir un type --</option>
            <?php foreach($typesConge ?? [] as $type): ?>
              <option value="<?= $type['id'] ?>" 
                      <?= old('type_conge_id') == $type['id'] ? 'selected' : '' ?>>
                <?= $type['libelle'] ?> (<?= $type['solde'] ?? 'N/A' ?> j restants)
              </option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($validation) && $validation->hasError('type_conge_id')): ?>
            <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= $validation->getError('type_conge_id') ?></div>
          <?php endif; ?>
        </div>

        <div class="form-grid-2" style="margin-bottom:1rem">
          <div class="f-group">
            <label class="f-label">Date de début <span style="color:var(--danger)">*</span></label>
            <input type="date" class="f-input" name="date_debut" value="<?= old('date_debut') ?>" required/>
            <?php if(isset($validation) && $validation->hasError('date_debut')): ?>
              <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= $validation->getError('date_debut') ?></div>
            <?php endif; ?>
          </div>
          <div class="f-group">
            <label class="f-label">Date de fin <span style="color:var(--danger)">*</span></label>
            <input type="date" class="f-input" name="date_fin" value="<?= old('date_fin') ?>" required/>
            <?php if(isset($validation) && $validation->hasError('date_fin')): ?>
              <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= $validation->getError('date_fin') ?></div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Calcul automatique -->
        <div class="f-computed" id="calculDuree" style="display:none">
          <div class="f-computed-num" id="nbJours">0</div>
          <div class="f-computed-label">jours calendaires calculés<br><span style="font-size:.7rem;opacity:.7" id="periodeAffichee"></span></div>
        </div>

        <div class="f-group" style="margin-bottom:1rem">
          <label class="f-label">Motif (optionnel)</label>
          <textarea class="f-textarea" name="motif" placeholder="Précisez le motif de votre demande si nécessaire..."><?= old('motif') ?></textarea>
          <div class="f-hint">Le motif est visible par le responsable RH.</div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-forest"><i class="bi bi-send"></i> Soumettre la demande</button>
          <a href="<?= base_url('employe/dashboard') ?>" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Panneau latéral : solde & règles -->
  <div style="display:flex;flex-direction:column;gap:1rem">
    <div class="data-card" style="margin:0">
      <div class="data-card-head"><h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels</h3></div>
      <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
        <?php foreach($soldes ?? [] as $solde): ?>
          <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
              <span style="font-size:.8rem;color:var(--ink)"><?= $solde['type'] ?></span>
              <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500">
                <?= $solde['restants'] ?> j
              </span>
            </div>
            <div class="solde-bar">
              <div class="solde-fill" style="width:<?= ($solde['restants'] / $solde['total']) * 100 ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="flash flash-info" style="margin:0">
      <i class="bi bi-info-circle-fill"></i>
      <span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre responsable.</span>
    </div>
    <div style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem">
      <div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem">
        <i class="bi bi-clipboard-check" style="color:var(--forest);margin-right:5px"></i>Rappel des règles
      </div>
      <ul style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7">
        <li>Préavis minimum : 48h avant la date de début</li>
        <li>Pas de chevauchement avec une demande en cours</li>
        <li>Solde insuffisant = demande refusée automatiquement</li>
      </ul>
    </div>
  </div>

</div>

<script>
// Calcul automatique de la durée
document.querySelectorAll('input[name="date_debut"], input[name="date_fin"]').forEach(input => {
  input.addEventListener('change', calculerDuree);
});

function calculerDuree() {
  const debut = document.querySelector('input[name="date_debut"]').value;
  const fin = document.querySelector('input[name="date_fin"]').value;
  
  if(debut && fin) {
    const d1 = new Date(debut);
    const d2 = new Date(fin);
    const jours = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
    
    document.getElementById('nbJours').textContent = jours;
    document.getElementById('periodeAffichee').textContent = 
      `du ${d1.toLocaleDateString('fr-FR')} au ${d2.toLocaleDateString('fr-FR')}`;
    document.getElementById('calculDuree').style.display = 'flex';
  }
}

// Calcul au chargement
calculerDuree();
</script>

<?php $this->endSection(); ?>
