<?php
/**
 * Ana Sayfa View
 */
?>

<div class="container container-narrow">

    <?php // --- YENİ EKLENEN BÖLÜM BAŞLANGICI --- ?>
    <?php if (!$auth->isLoggedIn()): ?>
        <div class="visitor-header">
            <a href="/login" class="btn btn-secondary">Giriş Yap</a>
            <a href="/register" class="btn btn-primary">Kayıt Ol</a>
        </div>
    <?php endif; ?>
    <?php // --- YENİ EKLENEN BÖLÜM BİTİŞİ --- ?>
    
    <div class="card">
        <div class="card-header">
            <h2>🚌 Nereye Gitmek İstersiniz?</h2>
        </div>
        <div class="card-body">
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

    <?php if ($isSearchPerformed): ?>
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
    .trip-card { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; padding: var(--spacing-md); }
    .trip-card-header { flex: 1; display: flex; align-items: center; gap: var(--spacing-md); min-width: 200px; }
    .company-logo { width: 40px; height: 40px; object-fit: contain; }
    .company-name { font-weight: 600; }
    .trip-card-body { display: flex; gap: var(--spacing-xl); flex: 2; justify-content: center; flex-wrap: wrap; }
    .time-info { text-align: center; }
    .price-info { font-size: 1.5rem; font-weight: 700; color: var(--primary-color); }
    .trip-card-footer { flex: 1; text-align: right; min-width: 150px; }

    /* --- YENİ EKLENEN CSS KURALI --- */
    .visitor-header {
        display: flex;
        justify-content: flex-end;
        gap: var(--spacing-md);
        padding-top: var(--spacing-lg);
        padding-bottom: var(--spacing-md);
    }
</style>