<?php
/**
 * Profil ve Şifre Değiştirme (Görünüm)
 */
?>
<div class="container container-narrow mt-4">
    <h1 class="mb-4">Profil Ayarları</h1>
    <div class="card">
        <div class="card-header">
            <h3>Şifre Değiştir</h3>
        </div>
        <div class="card-body">
            <?php if ($successMessage = get_flash_message('success')): ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/profile">
                <?= CSRF::getTokenField() ?>

                <div class="form-group">
                    <label for="current_password">Mevcut Şifre *</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Yeni Şifre *</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <small>En az 8 karakter, büyük/küçük harf, rakam ve özel karakter içermelidir.</small>
                </div>
                <div class="form-group">
                    <label for="new_password_confirm">Yeni Şifre Tekrar *</label>
                    <input type="password" id="new_password_confirm" name="new_password_confirm" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Şifreyi Güncelle</button>
            </form>
        </div>
    </div>
</div>