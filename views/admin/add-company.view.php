<?php
/**
 * Admin - Yeni Firma Ekleme (Görünüm)
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Yeni Firma Ekle</h1>
        <a href="/admin/companies" class="btn btn-secondary">← Geri Dön</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/admin/companies/add" enctype="multipart/form-data">
                <?= CSRF::getTokenField() ?>

                <div class="form-group">
                    <label for="company_name">Firma Adı *</label>
                    <input type="text" id="company_name" name="company_name" value="<?= htmlspecialchars($companyName) ?>" required>
                    <small>Firma'nın resmi adını girin (Örn: Metro Turizm).</small>
                </div>

                <div class="form-group">
                    <label for="logo">Firma Logosu</label>
                    <input type="file" id="logo" name="logo" accept="image/*">
                    <small>İsteğe bağlı. En iyi görünüm için kare veya yatay bir logo yükleyin (Maks. 2MB).</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Firmayı Kaydet</button>
            </form>
        </div>
    </div>
</div>

<style>
    .d-flex { display: flex; }
    .justify-content-between { justify-content: space-between; }
    .align-items-center { align-items: center; }
</style>