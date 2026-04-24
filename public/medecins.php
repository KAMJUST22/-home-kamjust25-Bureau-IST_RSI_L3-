<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
requireAuth();

$conn = getOracleConnection();
$sql = 'SELECT m.code_medecin, m.nom, m.prenom, m.specialite, s.libelle_service
        FROM MEDECIN m
        LEFT JOIN SERVICE s ON m.code_service = s.code_service
        ORDER BY s.libelle_service, m.nom, m.prenom';
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

$medecins = [];
while ($row = oci_fetch_assoc($stmt)) {
    $medecins[] = $row;
}
oci_free_statement($stmt);

require __DIR__ . '/../includes/header.php';
?>

<section class="card">
    <h2>Liste des médecins</h2>
    <p><a href="dashboard.php">&larr; Retour au tableau de bord</a></p>

    <table>
        <thead>
        <tr>
            <th>Code</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Spécialité</th>
            <th>Service</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($medecins as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['CODE_MEDECIN'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($m['NOM'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($m['PRENOM'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($m['SPECIALITE'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($m['LIBELLE_SERVICE'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require __DIR__ . '/../includes/footer.php';
