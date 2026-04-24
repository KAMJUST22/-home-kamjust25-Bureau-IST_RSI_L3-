<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (isAuthenticated()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($login === '' || $password === '') {
        $error = 'Veuillez saisir votre login et votre mot de passe.';
    } elseif (!loginAgent($login, $password)) {
        $error = 'Identifiants invalides.';
    } else {
        header('Location: dashboard.php');
        exit;
    }
}

require __DIR__ . '/../includes/header.php';
?>

<section class="card login-card">
    <h2>Connexion agent</h2>
    <p>Connectez-vous pour consulter les médecins, patients et consultations récentes.</p>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="post" class="form-grid">
        <label for="login">Login</label>
        <input type="text" id="login" name="login" required>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Se connecter</button>
    </form>
</section>

<?php require __DIR__ . '/../includes/footer.php';
