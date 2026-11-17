<?php

// Configuración local opcional. Este archivo no se versiona.
$localConfig = __DIR__ . '/.env.php';
if (is_file($localConfig)) {
    require_once $localConfig;
}

// También se admiten variables de entorno para despliegues.
$dbHost = $host ?? (getenv('DB_HOST') ?: '127.0.0.1');
$dbName = $db ?? (getenv('DB_NAME') ?: 'clinical_fbh');
$dbUser = $user ?? (getenv('DB_USER') ?: 'root');
$dbPass = $pass ?? (getenv('DB_PASS') ?: '');

class DB
{
    public static function conn()
    {
        static $conn = null;

        if ($conn instanceof PDO) {
            return $conn;
        }

        global $dbHost, $dbName, $dbUser, $dbPass;

        try {
            $conn = new PDO(
                "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
                $dbUser,
                $dbPass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            return $conn;
        } catch (PDOException $e) {
            // No mostrar credenciales, host ni detalles internos al visitante.
            error_log('Error de conexión a la base de datos: ' . $e->getMessage());
            throw new RuntimeException('No se pudo conectar con la base de datos.');
        }
    }
}
