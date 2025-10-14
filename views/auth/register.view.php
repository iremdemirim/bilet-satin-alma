<?php
/**
 * Register View Template
 */
?>
<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Kayıt Ol</h1>
            <p>Bilet satın almak için hesap oluşturun</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/register" class="auth-form" autocomplete="off">
            <?= CSRF::getTokenField() ?>
            
            <div class="form-group">
                <label for="full_name">Ad Soyad *</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($fullName ?? '') ?>" required minlength="3" maxlength="100" autocomplete="name">
            </div>
            
            <div class="form-group">
                <label for="email">Email Adresi *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required autocomplete="email">
            </div>
            
            <div class="form-group">
                <label for="password">Şifre *</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    minlength="8"  <?php // minlength özelliğini 8 yap ?>
                    autocomplete="new-password"
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{8,}" <?php // Tarayıcı tarafında da kontrol ekler ?>
                    title="Şifre en az 8 karakter uzunluğunda olmalı ve en az bir büyük harf, bir küçük harf, bir rakam ve bir özel karakter içermelidir."
                >
                <?php // Bilgilendirme mesajını güncelle ?>
                <small>En az 8 karakter, bir büyük/küçük harf, bir rakam ve bir özel karakter içermelidir.</small>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Şifre Tekrar *</label>
                <input 
                    type="password" 
                    id="password_confirm" 
                    name="password_confirm" 
                    required
                    minlength="8" <?php // minlength özelliğini 8 yap ?>
                    autocomplete="new-password"
                >
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                Kayıt Ol
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Zaten hesabınız var mı? <a href="/login">Giriş Yap</a></p>
            <p><a href="/">← Ana Sayfaya Dön</a></p>
        </div>
    </div>
</div>