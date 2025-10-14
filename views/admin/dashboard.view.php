<?php
/**
 * Admin Dashboard - Görünüm Dosyası
 */
?>
<div class="container mt-4">
    <h1 class="mb-4">Admin Paneli</h1>

    <div class="grid grid-3 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <h2><?= $companyCount ?></h2>
                <p>Toplam Firma</p>
            </div>
        </div>
        <div class="card text-center">
            <div class="card-body">
                <h2><?= $userCount ?></h2>
                <p>Toplam Yolcu</p>
            </div>
        </div>
        <div class="card text-center">
            <div class="card-body">
                <h2><?= $tripCount ?></h2>
                <p>Toplam Sefer</p>
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
                    <a href="/admin/companies">Firma Yönetimi</a>
                    <p class="text-secondary">Yeni otobüs firmaları ekleyin, mevcutları düzenleyin veya silin.</p>
                </li>
                <li class="list-group-item">
                    <a href="/admin/company-admins">Firma Admini Yönetimi</a>
                    <p class="text-secondary">Firmalara yeni yetkililer atayın veya mevcutları yönetin.</p>
                </li>
                <li class="list-group-item">
                    <a href="/admin/coupons">Genel Kupon Yönetimi</a>
                    <p class="text-secondary">Tüm firmalarda geçerli indirim kuponları oluşturun.</p>
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