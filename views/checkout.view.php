<?php
/**
 * Ödeme (Checkout) Sayfası - Görünüm (Uygulanabilir Kuponlar Eklendi)
 */
?>
<div class="container container-narrow mt-4">
    <div class="card">
        <div class="card-header">
            <h2>Ödeme Onayı</h2>
        </div>
        <div class="card-body">
            <div class="checkout-summary">
                <h4>Sefer Bilgileri</h4>
                <p><strong>Firma:</strong> <?= htmlspecialchars($trip['company_name']) ?></p>
                <p><strong>Güzergah:</strong> <?= htmlspecialchars($trip['departure_city']) ?> → <?= htmlspecialchars($trip['destination_city']) ?></p>
                <p><strong>Kalkış Zamanı:</strong> <?= date(DATETIME_FORMAT, strtotime($trip['departure_time'])) ?></p>
                <hr>
                <h4>Yolcu Bilgileri</h4>
                <p><strong>Seçilen Koltuklar:</strong> <span class="font-weight-bold"><?= implode(', ', $checkoutData['seats']) ?></span></p>
                <p><strong>Toplam Bilet:</strong> <?= count($checkoutData['seats']) ?> adet</p>
                <hr>
                <h4>Fiyat Detayları</h4>
                <p>Bilet Tutarı: <?= number_format($basePrice, 2, ',', '.') ?> ₺</p>

                <?php if (!empty($checkoutData['discount']) && $checkoutData['discount'] > 0): ?>
                    <p class="text-success">
                        Kupon İndirimi (<?= htmlspecialchars($checkoutData['coupon_code']) ?>):
                        -<?= number_format($checkoutData['discount'], 2, ',', '.') ?> ₺
                    </p>
                <?php endif; ?>

                <h3 class="total-price">Toplam Ödenecek Tutar: <?= number_format($totalPrice, 2, ',', '.') ?> ₺</h3>
            </div>
            <hr>

            <div class="coupon-section">
                <h4>İndirim Kuponu</h4>
                <?php if ($flashError = get_flash_message('error')): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($flashError) ?></div>
                <?php endif; ?>
                <?php if ($flashSuccess = get_flash_message('success')): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
                <?php endif; ?>

                <form action="/apply-coupon" method="POST" class="d-flex gap-2 mb-3" id="coupon-form">
                    <?= CSRF::getTokenField() ?>
                    <input type="text" id="coupon-input" name="coupon_code" class="form-control" placeholder="Kupon Kodunu Girin" value="<?= htmlspecialchars($checkoutData['coupon_code'] ?? '') ?>" <?= !empty($checkoutData['coupon_code']) ? 'readonly' : '' ?>>
                    <button type="submit" class="btn btn-secondary" <?= !empty($checkoutData['coupon_code']) ? 'disabled' : '' ?>>Uygula</button>
                    <?php if (!empty($checkoutData['coupon_code'])): ?>
                        <a href="/remove-coupon" class="btn btn-danger btn-sm" title="Uygulanan Kuponu Kaldır" onclick="return confirm('Kuponu kaldırmak istediğinize emin misiniz?')">Kaldır</a>
                    <?php endif; ?>
                </form>

                <?php // --- YENİ EKLENEN KUPON GÖSTERME ALANI --- ?>
                <?php if (empty($checkoutData['coupon_code']) && !empty($applicableCoupons)): ?>
                    <div class="available-coupons mt-2">
                        <strong>Bu Sefere Özel Kuponlar:</strong>
                        <div class="coupon-tags mt-1">
                            <?php foreach ($applicableCoupons as $coupon): ?>
                                <button type="button" class="btn btn-outline-success btn-sm coupon-tag mb-1" data-code="<?= htmlspecialchars($coupon['code']) ?>">
                                    <?= htmlspecialchars($coupon['code']) ?> (%<?= number_format($coupon['discount'], 0) ?> <?= $coupon['company_id'] ? '' : ' - Genel' ?>)
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <small>Kuponlardan birine tıklayarak kodu otomatik doldurabilirsiniz.</small>
                    </div>
                <?php endif; ?>
                <?php // --- YENİ ALAN BİTTİ --- ?>

            </div>
             </div> <div class="card-footer text-right">
            <form action="/process-payment" method="POST">
                <?= CSRF::getTokenField() ?>
                <a href="/trip/<?= htmlspecialchars($trip['id']) ?>" class="btn btn-secondary">Geri Dön</a>
                <button type="submit" class="btn btn-success btn-lg">Ödemeyi Onayla ve Bitir</button>
            </form>
            </div> </div> </div> <style>
    .checkout-summary p { margin-bottom: 0.5rem; }
    .font-weight-bold { font-weight: 600; }
    .text-success { color: var(--success-color); }
    .total-price { margin-top: 1rem; color: var(--primary-color); font-weight: bold;}
    .gap-2 { gap: 0.5rem; }
    .form-control { flex-grow: 1; }
    .available-coupons strong { display: block; margin-bottom: 0.5rem; }
    .coupon-tags button { cursor: pointer; }
    #coupon-form a.btn { align-self: center; } /* Kaldır butonunu hizala */

    /* --- YENİ EKLENEN KUPON ETİKET STİLLERİ --- */
    .btn-outline-success {
        color: var(--success-color);
        border-color: var(--success-color);
    }
    .btn-outline-success:hover {
        color: #fff;
        background-color: var(--success-color);
        border-color: var(--success-color);
    }
    .coupon-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    /* --- YENİ STİLLER BİTTİ --- */
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const couponTags = document.querySelectorAll('.coupon-tag');
    const couponInput = document.getElementById('coupon-input');

    couponTags.forEach(tag => {
        tag.addEventListener('click', function() {
            couponInput.value = this.dataset.code;
            // İsteğe bağlı: Input'a odaklanma
            // couponInput.focus();
        });
    });
});
</script>