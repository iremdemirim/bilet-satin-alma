<?php
/**
 * Navbar - Role göre dinamik menü
 */
$currentUser = $auth->getCurrentUser();
$role = $currentUser['role'] ?? '';
?>
<nav class="navbar">
    <div class="container">
        <div class="navbar-brand">
            <a href="/"><?= SITE_NAME ?></a>
        </div>
        
        <ul class="navbar-menu">
            <?php if ($role === ROLE_ADMIN): ?>
                <li><a href="/admin/dashboard">Panel</a></li>
                <li><a href="/admin/companies">Firmalar</a></li>
                <li><a href="/admin/company-admins">Firma Adminler</a></li>
                <li><a href="/admin/coupons">Kuponlar</a></li>
                
            <?php elseif ($role === ROLE_COMPANY): ?>
                <li><a href="/company/dashboard">Panel</a></li>
                <li><a href="/company/trips">Seferler</a></li>
                <li><a href="/company/coupons">Kuponlar</a></li>
                
            <?php elseif ($role === ROLE_USER): ?>
                <li><a href="/">Ana Sayfa</a></li>
                <li><a href="/dashboard">Hesabım</a></li>
                <li><a href="/my-tickets">Biletlerim</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="navbar-user">
            <?php if ($role === ROLE_USER): ?>
                <span class="user-balance">
                    💰 <?= number_format($currentUser['balance'], 0, ',', '.') ?> ₺
                </span>
            <?php endif; ?>
                <span class="user-name">
                    👤 <?= htmlspecialchars($currentUser['name']) ?>
                </span>
                <a href="/profile" class="btn btn-secondary btn-sm">Ayarlar</a>
                <a href="/logout" class="btn-logout">Çıkış</a>
        </div>
    </div>
</nav>