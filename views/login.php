<?php

session_start();
require_once("../includes/config.php");
require_once("../includes/DB.php");
require_once("../includes/SQL.php");

// Variable de error
$error = "";

// Procesar login
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    try {

        // Buscar usuario
        $fila = SQL::obtenerLoginUsuario($usuario);

        // Verificar si existe
        if ($fila) {

            // Verificar contraseña
            if (password_verify($password, $fila['password'])) {

                // Regenerar el identificador para reducir el riesgo de fijación de sesión.
                session_regenerate_id(true);

                // Crear sesión
                $_SESSION['idUser'] = $fila['idUser'];
                $_SESSION['usuario'] = $fila['usuario'];
                $_SESSION['rol'] = $fila['rol'];

                // Redirigir al inicio
                header("Location: ../index.php");
                exit();
            } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "Usuario no encontrado";
        }
    } catch (PDOException $e) {
        $error = "Error en el sistema";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Clínica SaludPlus</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/login.css">
</head>
<body>
    <!--Aquí incluyo la barra de navegación que cambia según sea admin, user o visitante-->
    <?php include("../includes/navbar.php"); ?>

    <!-- Contenedor principal -->
    <main class="main-content">
        <p class="login-help">Accede con tu cuenta registrada para consultar tus servicios.</p>
        <h1>Iniciar sesión</h1>

        <!-- Mensaje de error -->
        <?php if (!empty($error)): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <section class="container">
            <form method="POST">
                <input type="text" name="usuario" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Entrar</button>
            </form>
        </section>
        <p class="registro-texto">¿No tienes cuenta?<a href="registro.php">Regístrate aquí</a></p>
    </main>
    <!-- Footer -->
    <footer>
        <p>© 2026 Clínica SaludPlus</p>
        <h3>Síguenos en redes sociales</h3>
        <div class="redes-sociales">
            <a href="https://www.facebook.com/" target="_blank"><img src="../images/images_index/logofacebook.png" alt="logo facebook"></a>
            <a href="https://www.instagram.com/" target="_blank"><img src="../images/images_index/logoinstagram.png" alt="logo instagram"></a>
            <a href="https://x.com/?lang=es" target="_blank"><img src="../images/images_index/logotwitter.png" alt="logo x"></a>
        </div>
    </footer>
</body>
</html>