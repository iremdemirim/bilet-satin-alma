<?php
/**
 * Admin - Yeni Firma Admini Ekleme (Görünüm)
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Yeni Firma Admini Ekle</h1>
        <a href="/admin/company-admins" class="btn btn-secondary">← Geri Dön</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/admin/company-admins/add">
                <?= CSRF::getTokenField() ?>

                <div class="form-group">
                    <label for="full_name">Ad Soyad *</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($fullName) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Adresi *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                    <small>Bu email, kullanıcının sisteme giriş yapacağı email olacaktır.</small>
                </div>

                <div class="form-group">
                    <label for="company_id">Atanacak Firma *</label>
                    <select id="company_id" name="company_id" required>
                        <option value="">-- Firma Seçin --</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?= htmlspecialchars($company['id']) ?>" <?= ($companyId === $company['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($company['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Şifre *</label>
                    <input type="password" id="password" name="password" required>
                    <small>En az 8 karakter, bir büyük harf ve bir rakam içermelidir.</small>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Şifre Tekrar *</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Firma Adminini Kaydet</button>
            </form>
        </div>
    </div>
</div>

<style>
    .d-flex { display: flex; }
    .justify-content-between { justify-content: space-between; }
    .align-items-center { align-items: center; }
</style>