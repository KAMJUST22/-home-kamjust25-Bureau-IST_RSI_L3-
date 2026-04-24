<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require __DIR__ . '/../includes/header.php';
?>

<section class="card">
    <h2>Tableau de bord</h2>
    <p>Bienvenue dans le système de gestion de la clinique.</p>
    <div class="menu-grid">
        <a class="menu-item" href="medecins.php">Voir les médecins</a>
        <a class="menu-item" href="patients.php">Voir les patients</a>
        <a class="menu-item" href="consultations_24h.php">Consultations + traitements (24h)</a>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php';
