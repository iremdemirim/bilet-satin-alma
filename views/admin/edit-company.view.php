<?php
/**
 * Admin - Firma Düzenleme (Görünüm)
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Firmayı Düzenle</h1>
        <a href="/admin/companies" class="btn btn-secondary">← Geri Dön</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/admin/companies/edit?id=<?= htmlspecialchars($company['id']) ?>" enctype="multipart/form-data">
                <?= CSRF::getTokenField() ?>
                
                <input type="hidden" name="company_id" value="<?= htmlspecialchars($company['id']) ?>">

                <div class="form-group">
                    <label for="company_name">Firma Adı *</label>
                    <input type="text" id="company_name" name="company_name" value="<?= htmlspecialchars($company['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="logo">Firma Logosu</label>
                    <?php if (!empty($company['logo_path'])): ?>
                        <div class="mb-2">
                            <p>Mevcut Logo:</p>
                            <img src="<?= htmlspecialchars($company['logo_path']) ?>" alt="Mevcut Logo" style="max-width: 200px; height: auto; border: 1px solid #ccc; padding: 5px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="logo" name="logo" accept="image/*">
                    <small>Logoyu değiştirmek için yeni bir dosya seçin. Seçmezseniz mevcut logo korunur (Maks. 2MB).</small>
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