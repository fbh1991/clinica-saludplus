<?php

require_once __DIR__ . "/includes/security.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

csrf_validate();

// Vaciar variables de sesión
$_SESSION = [];

// Eliminar cookie de sesión
if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir sesión
session_destroy();

// Redirigir al inicio
header("Location: index.php");

exit();

?>