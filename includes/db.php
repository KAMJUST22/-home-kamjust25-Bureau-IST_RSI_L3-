<?php

declare(strict_types=1);

$config = require __DIR__ . '/../config/config.php';

/**
 * Retourne une connexion OCI Oracle.
 *
 * @return resource
 */
function getOracleConnection()
{
    static $connection = null;

    if ($connection !== null) {
        return $connection;
    }

    $config = require __DIR__ . '/../config/config.php';
    $oracle = $config['oracle'];

    $connection = @oci_connect(
        $oracle['username'],
        $oracle['password'],
        $oracle['connection_string'],
        $oracle['charset']
    );

    if (!$connection) {
        $error = oci_error();
        throw new RuntimeException('Connexion Oracle impossible: ' . ($error['message'] ?? 'Erreur inconnue'));
    }

    return $connection;
}
