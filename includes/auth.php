<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

$config = require __DIR__ . '/../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_name($config['app']['session_name']);
    session_start();
}

function isAuthenticated(): bool
{
    return isset($_SESSION['agent']);
}

function requireAuth(): void
{
    if (!isAuthenticated()) {
        header('Location: index.php');
        exit;
    }
}

function loginAgent(string $login, string $password): bool
{
    $conn = getOracleConnection();

    $sql = 'SELECT id_agent, login, mot_de_passe_hash, nom_complet
            FROM AGENT
            WHERE login = :login AND actif = \'O\'';

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':login', $login);
    oci_execute($stmt);

    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$row) {
        return false;
    }

    if (!password_verify($password, $row['MOT_DE_PASSE_HASH'])) {
        return false;
    }

    $_SESSION['agent'] = [
        'id' => $row['ID_AGENT'],
        'login' => $row['LOGIN'],
        'nom' => $row['NOM_COMPLET'],
    ];

    return true;
}

function logoutAgent(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}
