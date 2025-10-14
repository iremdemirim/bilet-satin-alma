<?php
/**
 * Ödeme (Checkout) Sayfası - Görünüm
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
                
                <?php if ($checkoutData['discount'] > 0): ?>
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

                <form action="/apply-coupon" method="POST" class="d-flex gap-2">
                    <?= CSRF::getTokenField() ?>
                    <input type="text" name="coupon_code" class="form-control" placeholder="Kupon Kodunu Girin" <?= !empty($checkoutData['coupon_code']) ? 'disabled' : '' ?>>
                    <button type="submit" class="btn btn-secondary" <?= !empty($checkoutData['coupon_code']) ? 'disabled' : '' ?>>Uygula</button>
                </form>
            </div>
            
        </div>
        <div class="card-footer text-right">
            <form action="/process-payment" method="POST">
                <?= CSRF::getTokenField() ?>
                <a href="/trip/<?= $trip['id'] ?>" class="btn btn-secondary">Geri Dön</a>
                <button type="submit" class="btn btn-success btn-lg">Ödemeyi Onayla ve Bitir</button>
            </form>
        </div>
    </div>
</div>

<style>
    .checkout-summary p { margin-bottom: 0.5rem; }
    .font-weight-bold { font-weight: 600; }
    .text-success { color: var(--success-color); }
    .total-price { margin-top: 1rem; }
    .gap-2 { gap: 0.5rem; }
    .form-control { flex-grow: 1; } /* Input'un genişlemesi için */
</style>