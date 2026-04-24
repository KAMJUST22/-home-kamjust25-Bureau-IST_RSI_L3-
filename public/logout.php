<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
logoutAgent();
header('Location: index.php');
exit;
