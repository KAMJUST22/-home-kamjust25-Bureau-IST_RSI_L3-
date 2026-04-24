<?php

declare(strict_types=1);

$config = require __DIR__ . '/../config/config.php';
$appName = $config['app']['name'];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
    <h1><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?></h1>
    <?php if (isset($_SESSION['agent'])): ?>
        <div class="user-info">
            Connecté: <?= htmlspecialchars($_SESSION['agent']['nom'] ?? $_SESSION['agent']['login'], ENT_QUOTES, 'UTF-8'); ?>
            | <a href="logout.php">Déconnexion</a>
        </div>
    <?php endif; ?>
</header>
<main class="container">
