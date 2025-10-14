<?php
/**
 * Admin - Kupon Listeleme (Görünüm)
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Kupon Yönetimi</h1>
        <a href="/admin/coupons/add" class="btn btn-success">Yeni Kupon Ekle</a>
    </div>

    <?php if ($successMessage = get_flash_message('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($coupons)): ?>
                <div class="alert alert-info">Sistemde kayıtlı kupon bulunmamaktadır.</div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Kod</th>
                                <th>İndirim Oranı</th>
                                <th>Kullanım (Kullanılan/Limit)</th>
                                <th>Geçerli Firma</th>
                                <th>Son Kullanma Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coupons as $coupon): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($coupon['code']) ?></strong></td>
                                    <td>%<?= htmlspecialchars($coupon['discount']) ?></td>
                                    <td><?= htmlspecialchars($coupon['used_count']) ?> / <?= htmlspecialchars($coupon['usage_limit']) ?></td>
                                    <td>
                                        <?php if ($coupon['company_name']): ?>
                                            <?= htmlspecialchars($coupon['company_name']) ?>
                                        <?php else: ?>
                                            <span class="badge badge-info">Genel Kupon</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date(DATETIME_FORMAT, strtotime($coupon['expire_date'])) ?></td>
                                    <td>
                                        <a href="/admin/coupons/edit?id=<?= $coupon['id'] ?>" class="btn btn-primary btn-sm">Düzenle</a>
                                        <a href="/admin/coupons/delete?id=<?= $coupon['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu kuponu silmek istediğinizden emin misiniz?')">Sil</a>
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