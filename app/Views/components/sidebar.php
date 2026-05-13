<!-- SIDEBAR COMPONENT -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="sidebar-logo-icon">
      <i class="<?= $sidebarIcon ?? 'bi bi-briefcase' ?>"></i>
    </div>
    <div class="sidebar-brand-name">
      TechMada RH
      <span><?= $sidebarSubtitle ?? 'Espace employé' ?></span>
    </div>
  </div>

  <?php if(!empty($menuItems)): ?>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <?php foreach($menuItems as $item): ?>
        <li>
          <a href="<?= $item['url'] ?? '#' ?>" class="<?= ($item['active'] ?? false) ? 'active' : '' ?>">
            <i class="<?= $item['icon'] ?? 'bi bi-dot' ?>"></i> 
            <?= $item['label'] ?? 'Menu' ?>
            <?php if(!empty($item['badge'])): ?>
              <span class="nav-badge <?= $item['badge']['class'] ?? '' ?>">
                <?= $item['badge']['text'] ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if(!empty($user)): ?>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar <?= $user['avatarClass'] ?? 'av-green' ?>">
          <?= $user['initials'] ?? 'SR' ?>
        </div>
        <div>
          <div class="user-name"><?= $user['name'] ?? 'Utilisateur' ?></div>
          <div class="user-role"><?= $user['role'] ?? 'Employé' ?></div>
        </div>
        <a href="<?= base_url('login') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion">
          <i class="bi bi-box-arrow-right"></i>
        </a>
      </div>
    </div>
  <?php endif; ?>
</aside>
