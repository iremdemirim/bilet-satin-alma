<?php
/**
 * Admin - Firma Admini Düzenleme (Görünüm)
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Firma Admini Düzenle</h1>
        <a href="/admin/company-admins" class="btn btn-secondary">← Geri Dön</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/admin/company-admins/edit?id=<?= htmlspecialchars($user['id']) ?>">
                <?= CSRF::getTokenField() ?>

                <div class="form-group">
                    <label for="full_name">Ad Soyad *</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Adresi *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="company_id">Atanacak Firma *</label>
                    <select id="company_id" name="company_id" required>
                        <option value="">-- Firma Seçin --</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?= htmlspecialchars($company['id']) ?>" <?= ($user['company_id'] === $company['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($company['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <hr>

                <div class="form-group">
                    <label for="password">Yeni Şifre</label>
                    <input type="password" id="password" name="password">
                    <small>Şifreyi değiştirmek istemiyorsanız bu alanı boş bırakın.</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
            </form>
        </div>
    </div>
</div>

<style>
    .d-flex { display: flex; }
    .justify-content-between { justify-content: space-between; }
    .align-items-center { align-items: center; }
</style>