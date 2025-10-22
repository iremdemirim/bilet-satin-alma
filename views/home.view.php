<?php
/**
 * Ana Sayfa View
 */
?>

<div class="container container-narrow">

    <?php // Ziyaretçi ise giriş/kayıt butonlarını göster ?>
    <?php if (!$auth->isLoggedIn()): ?>
        <div class="visitor-header">
            <a href="/login" class="btn btn-secondary">Giriş Yap</a>
            <a href="/register" class="btn btn-primary">Kayıt Ol</a>
        </div>
    <?php endif; ?>

    <div class="card mt-2"> <?php  ?>
        <div class="card-header">
            <h2>🚌 Nereye Gitmek İstersiniz?</h2>
        </div>
        <div class="card-body">

            <?php // --- HATA MESAJI GÖSTERİM ALANI --- ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-error mb-3">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <?php // --- HATA MESAJI GÖSTERİM ALANI BİTTİ --- ?>

            <form method="GET" action="/" class="search-form">
                <div class="form-group">
                    <label for="departure_city">Kalkış Yeri</label>
                    <select id="departure_city" name="departure_city" required>
                        <option value="">Nereden</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= htmlspecialchars($city) ?>" <?= ($departureCity === $city) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($city) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="destination_city">Varış Yeri</label>
                    <select id="destination_city" name="destination_city" required>
                        <option value="">Nereye</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= htmlspecialchars($city) ?>" <?= ($destinationCity === $city) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($city) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="departure_date">Yolculuk Tarihi (İsteğe Bağlı)</label>
                    <input type="date" id="departure_date" name="departure_date" value="<?= htmlspecialchars($departureDate) ?>" min="<?= date('Y-m-d') ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">Sefer Bul</button>
            </form>
        </div>
    </div>

    <?php if ($isSearchPerformed && empty($error)): ?>
        <div class="search-results mt-4">
            <h2>Arama Sonuçları</h2>
            <p class="text-secondary"><?= htmlspecialchars($departureCity) ?> → <?= htmlspecialchars($destinationCity) ?></p>
            <hr>

            <?php if (empty($trips)): ?>
                <div class="alert alert-warning">
                    Aradığınız kriterlere uygun sefer bulunamadı.
                </div>
            <?php else: ?>
                <?php foreach ($trips as $trip): ?>
                    <div class="card trip-card">
                        <div class="trip-card-header">
                            <img src="<?= htmlspecialchars($trip['logo_path'] ?? '/assets/images/default-logo.png') ?>" alt="<?= htmlspecialchars($trip['company_name']) ?> Logo" class="company-logo">
                            <span class="company-name"><?= htmlspecialchars($trip['company_name']) ?></span>
                        </div>
                        <div class="trip-card-body">
                            <div class="time-info">
                                <strong>Kalkış:</strong> <?= date(DATETIME_FORMAT, strtotime($trip['departure_time'])) ?>
                            </div>
                            <div class="time-info">
                                <strong>Varış:</strong> <?= date(DATETIME_FORMAT, strtotime($trip['arrival_time'])) ?>
                            </div>
                            <div class="price-info">
                                <?= number_format($trip['price'], 0) ?> ₺
                            </div>
                        </div>
                        <div class="trip-card-footer">
                            <a href="/trip/<?= htmlspecialchars($trip['id']) ?>" class="btn btn-secondary btn-sm">Koltuk Seç</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<style>
    .trip-card { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; padding: var(--spacing-md); margin-bottom: var(--spacing-md); } /* margin-bottom eklendi */
    .trip-card-header { flex: 1; display: flex; align-items: center; gap: var(--spacing-md); min-width: 200px; margin-bottom: var(--spacing-sm); } /* margin-bottom eklendi */
    .company-logo { width: 40px; height: 40px; object-fit: contain; }
    .company-name { font-weight: 600; }
    .trip-card-body { display: flex; gap: var(--spacing-lg); flex-wrap: wrap; flex: 2; justify-content: space-around; margin-bottom: var(--spacing-sm); } /* justify-content değiştirildi, gap küçültüldü, margin-bottom eklendi */
    .time-info { text-align: center; }
    .price-info { font-size: 1.5rem; font-weight: 700; color: var(--primary-color); margin-left: auto; padding-left: var(--spacing-md); } /* Otomatik sola yaslama için */
    .trip-card-footer { flex-basis: 100%; text-align: right; margin-top: var(--spacing-sm); } /* Responsive tasarım için footer alta alındı */

    .visitor-header {
        display: flex;
        justify-content: flex-end;
        gap: var(--spacing-md);
        padding-top: var(--spacing-lg);
        padding-bottom: var(--spacing-md);
    }
    /* Mobil görünüm için ek stil */
     @media (max-width: 576px) {
        .trip-card-body { justify-content: center; gap: var(--spacing-md); }
        .price-info { margin-left: 0; padding-left: 0; width: 100%; text-align: center; margin-top: var(--spacing-sm); }
        .trip-card-footer { text-align: center; }
    }
</style>