<?php
/**
 * Admin - Firma Admini Listeleme (Görünüm)
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Firma Admini Yönetimi</h1>
        <a href="/admin/company-admins/add" class="btn btn-success">Yeni Admin Ekle</a>
    </div>

    <?php if ($successMessage = get_flash_message('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($companyAdmins)): ?>
                <div class="alert alert-info">Sistemde kayıtlı firma admini bulunmamaktadır.</div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Ad Soyad</th>
                                <th>Email</th>
                                <th>Atandığı Firma</th>
                                <th>Kayıt Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($companyAdmins as $admin): ?>
                                <tr>
                                    <td><?= htmlspecialchars($admin['full_name']) ?></td>
                                    <td><?= htmlspecialchars($admin['email']) ?></td>
                                    <td>
                                        <?php if ($admin['company_name']): ?>
                                            <?= htmlspecialchars($admin['company_name']) ?>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Atanmamış</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date(DATE_FORMAT, strtotime($admin['created_at'])) ?></td>
                                    <td>
                                        <a href="/admin/company-admins/edit?id=<?= $admin['id'] ?>" class="btn btn-primary btn-sm">Düzenle</a>
                                        <a href="/admin/company-admins/delete?id=<?= $admin['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">Sil</a>
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