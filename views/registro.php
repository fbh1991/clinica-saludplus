<?php

require_once("../includes/config.php");
require_once("../includes/DB.php");
require_once("../includes/SQL.php");

// Conexión a la base de datos
$conn = DB::conn();

// Variable para mostrar mensajes
$mensaje = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recoger y limpiar datos
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $fecha = $_POST['fecha'];
    $direccion = trim($_POST['direccion']);
    $sexo = $_POST['sexo'];

    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    // Array de errores
    $errores = [];

    // Nombre: solo letras y espacios
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre)) {
        $errores[] = "El nombre solo puede contener letras.";
    }

    // Apellidos: solo letras y espacios
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $apellidos)) {
        $errores[] = "Los apellidos solo pueden contener letras.";
    }

    // Email válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }

    // Teléfono: exactamente 9 números
    if (!preg_match("/^[0-9]{9}$/", $telefono)) {
        $errores[] = "El teléfono debe tener 9 dígitos.";
    }

    // Fecha de nacimiento
    if (empty($fecha)) {
        $errores[] = "Debes indicar una fecha de nacimiento.";
    } else {

        $fechaNacimiento = new DateTime($fecha);
        $hoy = new DateTime();

        if ($fechaNacimiento > $hoy) {
            $errores[] = "La fecha de nacimiento no puede ser futura.";
        }
    }

    // Usuario
    if (strlen($usuario) < 4) {
        $errores[] = "El usuario debe tener al menos 4 caracteres.";
    }

    // Contraseña
    if (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    // Si hay errores los mostramos
    if (!empty($errores)) {

        $mensaje = "<div class='error'>";

        foreach ($errores as $error) {
            $mensaje .= $error . "<br>";
        }

        $mensaje .= "</div>";
    } else {
        try {
            // Verificar si el usuario ya existe
            if (SQL::existeUsuario($usuario)) {
                $mensaje = "<div class='error'>El usuario ya existe</div>";
            } else {
                // Encriptar contraseña
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Iniciar transacción
                $conn->beginTransaction();

                // Insertar datos personales
                $idUser = SQL::insertarUsuarioData($nombre, $apellidos, $email, $telefono, $fecha, $direccion, $sexo);

                // Insertar datos de login
                SQL::insertarLogin($idUser, $usuario, $passwordHash);
                // Confirmar cambios
                $conn->commit();
                // Redireccionar al login
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            // Revertir cambios si ocurre un error
            $conn->rollBack();
            $mensaje = "<div class='error'>Error al registrar usuario</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Clínica SaludPlus</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/registro.css">
</head>
<body>
    <!--Aquí incluyo la barra de navegación que cambia según sea admin, user o visitante-->
    <?php include("../includes/navbar.php"); ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <section class="container">
            <h1 class="tituloRegistro">Crear cuenta</h1>
            <p class="subtituloRegistro">Regístrate para acceder a nuestros servicios médicos</p>

            <!-- Mensaje de error -->
            <?= $mensaje ?>

            <form method="POST">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellidos" placeholder="Apellidos" required>
                <input type="email" name="email" placeholder="Correo electrónico" required>
                <input type="text" name="telefono" placeholder="Teléfono" required>
                <input type="date" name="fecha" required>
                <input type="text" name="direccion" placeholder="Dirección">
                <select name="sexo">
                    <option value="Hombre">Hombre</option>
                    <option value="Mujer">Mujer</option>
                    <option value="No especificado">No especificado</option>
                </select>
                <input type="text" name="usuario" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Registrarse</button>
            </form>
        </section>
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