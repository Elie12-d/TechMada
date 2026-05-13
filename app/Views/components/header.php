<!-- HEADER/TOPBAR COMPONENT -->
<div class="topbar">
  <div>
    <div class="topbar-title"><?= $pageTitle ?? 'Tableau de bord' ?></div>
    <div class="topbar-breadcrumb">
      <?php if(!empty($breadcrumbs)): ?>
        <?php foreach($breadcrumbs as $i => $crumb): ?>
          <?php if($i > 0): ?>
            <i class="bi bi-chevron-right" style="font-size:.6rem"></i>
          <?php endif; ?>
          <?php if(!empty($crumb['url'])): ?>
            <a href="<?= $crumb['url'] ?>"><?= $crumb['label'] ?></a>
          <?php else: ?>
            <?= $crumb['label'] ?>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php else: ?>
        Accueil
      <?php endif; ?>
    </div>
  </div>

  <?php if(!empty($actions)): ?>
    <div class="topbar-actions">
      <?php foreach($actions as $action): ?>
        <?php if($action['type'] === 'button'): ?>
          <a href="<?= $action['url'] ?? '#' ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
            <i class="<?= $action['icon'] ?? 'bi bi-plus-lg' ?>"></i> 
            <?= $action['label'] ?? 'Action' ?>
          </a>
        <?php elseif($action['type'] === 'badge'): ?>
          <span style="font-size:.8rem;color:var(--muted);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)">
            <i class="<?= $action['icon'] ?? 'bi bi-hourglass-split' ?>"></i> 
            <?= $action['label'] ?? 'Badge' ?>
          </span>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
