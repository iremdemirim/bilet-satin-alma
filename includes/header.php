<?php
/**
 * Header - Tüm sayfalarda kullanılacak HTML başlangıcı
 */
if (!defined('SITE_NAME')) {
    die('Bu dosya direkt olarak çağrılamaz.');
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Online otobüs bileti satın alma platformu">
    <title><?= htmlspecialchars($pageTitle ?? SITE_NAME) ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php
    // Navbar'ı dahil et (giriş yapmışsa)
    if (isset($auth) && $auth->isLoggedIn() && !isset($hideNavbar)) {
        require_once INCLUDES_PATH . '/navbar.php';
    }
    ?>
    
    <main class="main-content">