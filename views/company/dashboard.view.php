<?php
/**
 * Firma Admini Dashboard - Görünüm Dosyası
 */
?>
<div class="container mt-4">
    <h1 class="mb-4">
        Firma Paneli: 
        <span style="color: var(--primary-color);"><?= htmlspecialchars($company['name'] ?? 'Firma Atanmamış') ?></span>
    </h1>

    <div class="card mb-4">
        <div class="card-body">
            <p>Hoş geldin, <strong><?= htmlspecialchars($companyAdmin['name']) ?></strong>!</p>
            <p>Bu panel üzerinden firmanıza ait seferleri ve kuponları yönetebilirsiniz.</p>
        </div>
    </div>

    <div class="grid grid-3 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <h2><?= $tripCount ?></h2>
                <p>Aktif Sefer Sayısı</p>
            </div>
        </div>
        <div class="card text-center">
            <div class="card-body">
                <h2>0</h2>
                <p>Bu Ayki Bilet Satışı</p>
            </div>
        </div>
        <div class="card text-center">
            <div class="card-body">
                <h2>0</h2>
                <p>Firma Kuponları</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Yönetim Menüsü</h3>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item">
                    <a href="/company/trips">Sefer Yönetimi</a>
                    <p class="text-secondary">Yeni seferler ekleyin, mevcut seferleri düzenleyin veya iptal edin.</p>
                </li>
                <li class="list-group-item">
                    <a href="/company/coupons">İndirim Kuponu Yönetimi</a>
                    <p class="text-secondary">Firmanıza özel indirim kuponları oluşturun.</p>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
    .list-group { list-style: none; padding: 0; }
    .list-group-item { padding: 1rem; border-bottom: 1px solid var(--gray-200); }
    .list-group-item:last-child { border-bottom: none; }
    .list-group-item a { font-size: 1.2rem; font-weight: 600; text-decoration: none; color: var(--primary-color); }
    .list-group-item p { margin-top: 0.25rem; }
</style>