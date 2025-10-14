<?php
/**
 * Login View Template
 */
?>
<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Giriş Yap</h1>
            <p>Hesabınıza giriş yapın</p>
        </div>
        
        <?php // Flash Mesajları Gösterim Alanı ?>
        <?php if ($successMessage = get_flash_message('success')): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($timeoutMessage = get_flash_message('warning')): ?>
            <div class="alert alert-warning">
                 <?= htmlspecialchars($timeoutMessage) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/login" class="auth-form" autocomplete="off">
            <?= CSRF::getTokenField() ?>
            
            <div class="form-group">
                <label for="email">Email Adresi</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required autofocus autocomplete="email">
            </div>
            
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                Giriş Yap
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Hesabınız yok mu? <a href="/register">Kayıt Ol</a></p>
            <p><a href="/">← Ana Sayfaya Dön</a></p>
        </div>
    </div>
</div>