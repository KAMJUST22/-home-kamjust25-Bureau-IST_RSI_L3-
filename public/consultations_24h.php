<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
requireAuth();

$conn = getOracleConnection();
$sql = "SELECT c.num_consultation,
               TO_CHAR(c.date_consult, 'DD/MM/YYYY HH24:MI') AS date_consult,
               p.nom || ' ' || p.prenom AS patient,
               m.nom || ' ' || m.prenom AS medecin,
               c.diagnostic,
               c.montant_total,
               LISTAGG(t.libelle || ' x' || ct.quantite, ', ') WITHIN GROUP (ORDER BY t.libelle) AS traitements
        FROM CONSULTATION c
        JOIN PATIENT p ON c.code_patient = p.code_patient
        JOIN MEDECIN m ON c.code_medecin = m.code_medecin
        LEFT JOIN CONSULTATION_TRAITEMENT ct ON c.num_consultation = ct.num_consultation
        LEFT JOIN TRAITEMENT t ON ct.code_traitement = t.code_traitement
        WHERE c.date_consult >= SYSDATE - 1
        GROUP BY c.num_consultation, c.date_consult, p.nom, p.prenom, m.nom, m.prenom, c.diagnostic, c.montant_total
        ORDER BY c.date_consult DESC";

$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

$consultations = [];
while ($row = oci_fetch_assoc($stmt)) {
    $consultations[] = $row;
}
oci_free_statement($stmt);

require __DIR__ . '/../includes/header.php';
?>

<section class="card">
    <h2>Consultations et traitements des 24 dernières heures</h2>
    <p><a href="dashboard.php">&larr; Retour au tableau de bord</a></p>

    <?php if (empty($consultations)): ?>
        <p>Aucune consultation enregistrée dans les 24 dernières heures.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>N°</th>
                <th>Date</th>
                <th>Patient</th>
                <th>Médecin</th>
                <th>Diagnostic</th>
                <th>Traitements</th>
                <th>Montant total</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($consultations as $c): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$c['NUM_CONSULTATION'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($c['DATE_CONSULT'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($c['PATIENT'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($c['MEDECIN'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($c['DIAGNOSTIC'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($c['TRAITEMENTS'] ?? 'Aucun', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars(number_format((float)$c['MONTANT_TOTAL'], 2, ',', ' '), ENT_QUOTES, 'UTF-8'); ?> FCFA</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/footer.php';
