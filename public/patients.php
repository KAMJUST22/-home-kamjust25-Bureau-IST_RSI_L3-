<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
requireAuth();

$conn = getOracleConnection();
$sql = "SELECT code_patient, nom, prenom, TO_CHAR(date_naissance, 'DD/MM/YYYY') AS date_naissance,
               sexe, adresse, telephone
        FROM PATIENT
        ORDER BY nom, prenom";
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

$patients = [];
while ($row = oci_fetch_assoc($stmt)) {
    $patients[] = $row;
}
oci_free_statement($stmt);

require __DIR__ . '/../includes/header.php';
?>

<section class="card">
    <h2>Liste des patients</h2>
    <p><a href="dashboard.php">&larr; Retour au tableau de bord</a></p>

    <table>
        <thead>
        <tr>
            <th>Code</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Naissance</th>
            <th>Sexe</th>
            <th>Téléphone</th>
            <th>Adresse</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($patients as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['CODE_PATIENT'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($p['NOM'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($p['PRENOM'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($p['DATE_NAISSANCE'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($p['SEXE'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($p['TELEPHONE'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($p['ADRESSE'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require __DIR__ . '/../includes/footer.php';
