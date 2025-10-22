<?php
/**
 * Firma Admini - Yeni Kupon Ekleme (Görünüm)
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Yeni Firma Kuponu Ekle</h1>
        <a href="/company/coupons" class="btn btn-secondary">← Geri Dön</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/company/coupons/add">
                <?= CSRF::getTokenField() ?>

                <div class="form-group">
                    <label for="code">Kupon Kodu *</label>
                    <input type="text" id="code" name="code" value="<?= htmlspecialchars($inputs['code']) ?>" required style="text-transform: uppercase;">
                    <small>Benzersiz bir kod girin (Örn: SONBAHAR10).</small>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="discount">İndirim Oranı (%) *</label>
                        <input type="number" id="discount" name="discount" value="<?= htmlspecialchars($inputs['discount']) ?>" min="1" max="100" step="0.5" required>
                    </div>
                    <div class="form-group">
                        <label for="usage_limit">Kullanım Limiti *</label>
                        <input type="number" id="usage_limit" name="usage_limit" value="<?= htmlspecialchars($inputs['usage_limit']) ?>" min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="expire_date">Son Kullanma Tarihi *</label>
                    <input type="date" id="expire_date" name="expire_date" value="<?= htmlspecialchars($inputs['expire_date']) ?>" min="<?= date('Y-m-d', strtotime('+1 year')) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Kuponu Kaydet</button>
            </form>
        </div>
    </div>
</div>

<style>
    .d-flex { display: flex; }
    .justify-content-between { justify-content: space-between; }
    .align-items-center { align-items: center; }
</style>