<?php
/**
 * Firma Admini - Yeni Sefer Ekleme (Görünüm)
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Yeni Sefer Ekle</h1>
        <a href="/company/trips" class="btn btn-secondary">← Geri Dön</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/company/trips/add">
                <?= CSRF::getTokenField() ?>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="departure_city">Kalkış Şehri *</label>
                        <input type="text" id="departure_city" name="departure_city" value="<?= htmlspecialchars($inputs['departure_city']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="destination_city">Varış Şehri *</label>
                        <input type="text" id="destination_city" name="destination_city" value="<?= htmlspecialchars($inputs['destination_city']) ?>" required>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="departure_date">Kalkış Tarihi *</label>
                        <input type="date" id="departure_date" name="departure_date" value="<?= htmlspecialchars($inputs['departure_date']) ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="departure_time_input">Kalkış Saati *</label>
                        <input type="time" id="departure_time_input" name="departure_time_input" value="<?= htmlspecialchars($inputs['departure_time_input']) ?>" required>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="arrival_date">Varış Tarihi *</label>
                        <input type="date" id="arrival_date" name="arrival_date" value="<?= htmlspecialchars($inputs['arrival_date']) ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="arrival_time_input">Varış Saati *</label>
                        <input type="time" id="arrival_time_input" name="arrival_time_input" value="<?= htmlspecialchars($inputs['arrival_time_input']) ?>" required>
                    </div>
                </div>
                
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="price">Bilet Fiyatı (₺) *</label>
                        <input type="number" id="price" name="price" value="<?= htmlspecialchars($inputs['price']) ?>" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="capacity">Toplam Koltuk Sayısı *</label>
                        <input type="number" id="capacity" name="capacity" value="<?= htmlspecialchars($inputs['capacity']) ?>" min="1" max="60" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Seferi Kaydet</button>
            </form>
        </div>
    </div>
</div>

<style>
    .d-flex { display: flex; }
    .justify-content-between { justify-content: space-between; }
    .align-items-center { align-items: center; }
</style>