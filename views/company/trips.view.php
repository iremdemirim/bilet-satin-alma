<?php
/**
 * Firma Admini - Sefer Listeleme (Görünüm)
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Sefer Yönetimi</h1>
        <a href="/company/trips/add" class="btn btn-success">Yeni Sefer Ekle</a>
    </div>

    <?php if ($successMessage = get_flash_message('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($trips)): ?>
                <div class="alert alert-info">Firmanıza ait kayıtlı sefer bulunmamaktadır. İlk seferinizi ekleyerek başlayın!</div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Kalkış</th>
                                <th>Varış</th>
                                <th>Kalkış Zamanı</th>
                                <th>Fiyat</th>
                                <th>Kapasite</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trips as $trip): ?>
                                <tr>
                                    <td><?= htmlspecialchars($trip['departure_city']) ?></td>
                                    <td><?= htmlspecialchars($trip['destination_city']) ?></td>
                                    <td><?= date(DATETIME_FORMAT, strtotime($trip['departure_time'])) ?></td>
                                    <td><?= htmlspecialchars($trip['price']) ?> ₺</td>
                                    <td><?= htmlspecialchars($trip['capacity']) ?></td>
                                    <td>
                                        <?php if ($trip['status'] === TRIP_ACTIVE): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">İptal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/company/trips/edit?id=<?= $trip['id'] ?>" class="btn btn-primary btn-sm">Düzenle</a>
                                        <a href="/company/trips/delete?id=<?= $trip['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu seferi silmek istediğinizden emin misiniz?')">Sil</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .d-flex { display: flex; }
    .justify-content-between { justify-content: space-between; }
    .align-items-center { align-items: center; }
</style>