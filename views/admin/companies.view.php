<?php
/**
 * Admin - Firma Listeleme Sayfası (Görünüm)
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Firma Yönetimi</h1>
        <a href="/admin/companies/add" class="btn btn-success">Yeni Firma Ekle</a>
    </div>

    <?php // Flash Mesajları Gösterim Alanı ?>
    <?php if ($successMessage = get_flash_message('success')): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($companies)): ?>
                <div class="alert alert-info">Sistemde kayıtlı firma bulunmamaktadır. İlk firmanızı ekleyerek başlayın!</div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Firma Adı</th>
                                <th>Eklenme Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($companies as $company): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($company['logo_path'] ?? '/assets/images/default-logo.png') ?>" alt="<?= htmlspecialchars($company['name']) ?>" style="width: 100px; height: auto;">
                                    </td>
                                    <td><?= htmlspecialchars($company['name']) ?></td>
                                    <td><?= date(DATE_FORMAT, strtotime($company['created_at'])) ?></td>
                                    <td>
                                        <a href="/admin/companies/edit?id=<?= $company['id'] ?>" class="btn btn-primary btn-sm">Düzenle</a>
                                        <a href="/admin/companies/delete?id=<?= $company['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu firmayı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')">Sil</a>
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