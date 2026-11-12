<?php

if (!defined('BASE_URL')) {

    // Normalizamos separadores para evitar problemas en Windows

    $configPath = str_replace('\\', '/', __DIR__);

    $docRoot    = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\'));

    $relativePath = str_replace($docRoot, '', $configPath);

    // Subimos un nivel (salimos de /includes)

    $basePath = dirname($relativePath);

    define('BASE_URL', rtrim($basePath, '/') . '/');

}