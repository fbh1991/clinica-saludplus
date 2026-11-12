<?php

session_start();
require_once("../includes/config.php");

// Proteger acceso
if (!isset($_SESSION['rol'])) {
    header("Location: login.php");
    exit();
}

require_once("../includes/DB.php");
require_once("../includes/SQL.php");

$idUser = $_SESSION['idUser'];

$mensaje = "";

// Actualizar datos
if (isset($_POST['actualizar'])) {

    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $fecha = $_POST['fecha'];
    $direccion = trim($_POST['direccion']);
    $sexo = $_POST['sexo'];

    try {

        SQL::actualizarDatosUsuario($idUser, $nombre, $apellidos, $email, $telefono, $fecha, $direccion, $sexo);
        $mensaje = "<p class='mensaje-ok'>Datos actualizados correctamente</p>";
    } catch (PDOException $e) {
        $mensaje = "<p class='mensaje-error'>Error al actualizar datos</p>";
    }
}

// Cambiar contraseña
if (isset($_POST['cambiar_pass'])) {

    $passwordNueva = trim($_POST['nueva_password']);

    if (strlen($passwordNueva) >= 6) {

        try {

            $nuevaHash = password_hash($passwordNueva, PASSWORD_DEFAULT);

            SQL::actualizarPassword($idUser, $nuevaHash);

            $mensaje = "<p class='mensaje-ok'>Contraseña actualizada correctamente</p>";
        } catch (PDOException $e) {
            $mensaje = "<p class='mensaje-error'>Error al actualizar contraseña</p>";
        }
    } else {
        $mensaje = "<p class='mensaje-error'>La contraseña debe tener al menos 6 caracteres</p>";
    }
}

// Llamo al método para obtener datos del usuario
$usuario = SQL::obtenerUsuario($idUser);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Clínica SaludPlus</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/perfil.css">
</head>

<body>

    <!--Aquí incluyo la barra de navegación que cambia según sea admin, user o visitante-->
    <?php include("../includes/navbar.php"); ?>

    <main class="perfil-container">
        <section class="perfil-hero">
            <h1>Mi perfil</h1>
            <p>Gestiona tu información personal y la seguridad de tu cuenta.</p>
        </section>

        <!-- Mensajes -->
        <?= $mensaje ?>

        <section>
            <h2>Datos personales</h2>
            <div class="perfil-card">
                <form class="perfil-form" method="POST">
                    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" placeholder="Nombre" required>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>" placeholder="Apellidos" required>
                    <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" placeholder="Correo electrónico" required>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>" placeholder="Teléfono" required>
                    <input type="date" name="fecha" value="<?= htmlspecialchars($usuario['fecha_nacimiento']) ?>" required>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($usuario['direccion']) ?>" placeholder="Dirección">
                    <select name="sexo">
                        <option value="Hombre" <?= $usuario['sexo'] === "Hombre" ? "selected" : "" ?>>Hombre</option>
                        <option value="Mujer" <?= $usuario['sexo'] === "Mujer" ? "selected" : "" ?>>Mujer</option>
                        <option value="Otro" <?= $usuario['sexo'] === "Otro" ? "selected" : "" ?>>Otro</option>
                    </select>
                    <button class="perfil-btn" type="submit" name="actualizar">Actualizar datos</button>
                </form>
            </div>
        </section>
        <!-- Cambiar contraseña -->
        <section>
            <h2>Cambiar contraseña</h2>
            <div class="perfil-card password-card">
                <form class="perfil-form password-form" method="POST">
                    <input type="password" name="nueva_password" placeholder="Nueva contraseña" required>
                    <button class="perfil-btn password-btn" type="submit" name="cambiar_pass">Cambiar contraseña</button>
                </form>
            </div>
        </section>
    </main>
    <footer>
        © 2026 Clínica SaludPlus
        <h3>Síguenos en redes sociales</h3>
        <p>
            <a href="https://www.facebook.com/" target="_blank"><img src="../images/images_index/logofacebook.png"
                    alt="Facebook"></a>
            <a href="https://www.instagram.com/" target="_blank"><img src="../images/images_index/logoinstagram.png"
                    alt="Instagram"></a>
            <a href="https://x.com/?lang=es" target="_blank"><img src="../images/images_index/logotwitter.png" alt="X"></a>
        </p>
    </footer>
</body>

</html>