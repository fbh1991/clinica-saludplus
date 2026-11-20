<?php

require_once __DIR__ . "/../includes/security.php";
require_once("../includes/config.php");

// Sólo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once("../includes/DB.php");
require_once("../includes/SQL.php");

$mensaje = "";

// Crear usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
}

if (isset($_POST['crear'])) {

    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $sexo = $_POST['sexo'] ?? 'Otro';
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'user';

    $errores = [];

    // Validar nombre y apellidos
    if (!preg_match("/^[\p{L}\s'-’]+$/u", $nombre)) {
        $errores[] = 'El nombre solo puede contener letras, espacios, guiones y apóstrofes';
    }

    if (!preg_match("/^[\p{L}\s'-’]+$/u", $apellidos)) {
        $errores[] = 'Los apellidos solo pueden contener letras, espacios, guiones y apóstrofes';
    }

    // Validar email
    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 254
    ) {
        $errores[] = 'El email no es válido';
    }

    // Validar teléfono
    if (!preg_match('/^\d{9}$/', $telefono)) {
        $errores[] = 'El teléfono debe contener exactamente 9 dígitos';
    }

    // Validar fecha
    if (
        !valid_date($fecha) ||
        $fecha > date('Y-m-d')
    ) {
        $errores[] = 'La fecha de nacimiento no es válida';
    }

    // Validar usuario
    if (
        strlen($usuario) < 4 ||
        strlen($usuario) > 40 ||
        !preg_match('/^[a-zA-Z0-9_.-]+$/', $usuario)
    ) {
        $errores[] = 'El usuario debe tener entre 4 y 40 caracteres y solo puede contener letras, números, puntos, guiones y guiones bajos';
    }

    // Validar contraseña
    if (strlen($password) < 10) {
        $errores[] = 'La contraseña debe tener al menos 10 caracteres';
    }

    // Validar sexo y rol
    if (!in_array($sexo, ['Hombre', 'Mujer', 'Otro'], true)) {
        $sexo = 'Otro';
    }

    if (!in_array($rol, ['user', 'admin'], true)) {
        $rol = 'user';
    }

    if (!empty($errores)) {

        $mensaje = "<div class='mensaje-error'>" .
            htmlspecialchars(implode('. ', $errores)) .
            "</div>";

    } else {

        try {

            if (SQL::existeUsuario($usuario)) {

                $mensaje = "<div class='mensaje-error'>El usuario ya existe</div>";

            } else {

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $conn = DB::conn();
                $conn->beginTransaction();

                $idUser = SQL::insertarUsuarioData(
                    $nombre,
                    $apellidos,
                    $email,
                    $telefono,
                    $fecha,
                    $direccion,
                    $sexo
                );

                SQL::insertarLoginAdmin(
                    $idUser,
                    $usuario,
                    $passwordHash,
                    $rol
                );

                $conn->commit();

                $mensaje = "<div class='mensaje-ok'>Usuario creado correctamente</div>";
            }

        } catch (PDOException | RuntimeException $e) {

            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }

            $mensaje = "<div class='mensaje-error'>Error al crear usuario</div>";
        }
    }
}

// Editar usuario
if (isset($_POST['editar'])) {

    $idUser = (int) ($_POST['idUser'] ?? 0);

    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $sexo = $_POST['sexo'] ?? 'Otro';
    $rol = $_POST['rol'] ?? 'user';

    $errores = [];

    // Validar identificador
    if ($idUser <= 0) {
        $errores[] = 'El usuario seleccionado no es válido';
    }

    // Permitir letras, espacios, guiones y apóstrofes
    if (!preg_match("/^[\p{L}\s'-’]+$/u", $nombre)) {
        $errores[] = 'El nombre contiene caracteres no válidos';
    }

    if (!preg_match("/^[\p{L}\s'-’]+$/u", $apellidos)) {
        $errores[] = 'Los apellidos contienen caracteres no válidos';
    }

    // Validar email
    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 254
    ) {
        $errores[] = 'El email no es válido';
    }

    // Validar teléfono
    if (!preg_match('/^\d{9}$/', $telefono)) {
        $errores[] = 'El teléfono debe contener exactamente 9 dígitos';
    }

    // Validar fecha
    if (
        !valid_date($fecha) ||
        $fecha > date('Y-m-d')
    ) {
        $errores[] = 'La fecha de nacimiento no es válida';
    }

    // Validar sexo
    if (!in_array($sexo, ['Hombre', 'Mujer', 'Otro'], true)) {
        $sexo = 'Otro';
    }

    // Validar rol
    if (!in_array($rol, ['user', 'admin'], true)) {
        $rol = 'user';
    }

    // Evitar que el administrador se quite sus propios permisos
    if ($idUser === (int) $_SESSION['idUser']) {
        $rol = 'admin';
    }

    if (!empty($errores)) {

        $mensaje = "<div class='mensaje-error'>" .
            htmlspecialchars(implode('. ', $errores)) .
            "</div>";

    } else {

        $conn = null;

        try {

            $conn = DB::conn();
            $conn->beginTransaction();

            SQL::actualizarUsuarioAdmin(
                $idUser,
                $nombre,
                $apellidos,
                $email,
                $telefono,
                $fecha,
                $direccion,
                $sexo
            );

            SQL::actualizarRolUsuario($idUser, $rol);

            $conn->commit();

            $mensaje = "<div class='mensaje-ok'>Usuario actualizado correctamente</div>";

        } catch (PDOException | RuntimeException $e) {

            if ($conn !== null && $conn->inTransaction()) {
                $conn->rollBack();
            }

            $mensaje = "<div class='mensaje-error'>Error al actualizar usuario</div>";
        }
    }
}


// Llamo al método para obtener usuario
$usuarios = SQL::obtenerUsuariosAdmin();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de usuarios - Clínica SaludPlus</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/usuariosadmin.css">
</head>

<body>

    <?php include("../includes/navbar.php"); ?>

    <div class="usuarios-container">
        <div class="usuarios-hero">
            <h1>Administración de usuarios</h1>
            <p>Gestiona las cuentas de usuarios y administradores de la plataforma.</p>
        </div>

        <?= $mensaje ?>

        <h2>Crear nuevo usuario</h2>

        <div class="usuarios-card">
            <form class="usuarios-form" method="POST">
                    <?= csrf_field() ?>
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellidos" placeholder="Apellidos" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="telefono" placeholder="Teléfono" required>
                <input type="date" name="fecha" required>
                <input type="text" name="direccion" placeholder="Dirección">
                <select name="sexo">
                    <option value="Hombre">Hombre</option>
                    <option value="Mujer">Mujer</option>
                    <option value="Otro">Otro</option>
                </select>
                <input type="text" name="usuario" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <select name="rol">
                    <option value="user">Usuario</option>
                    <option value="admin">Administrador</option>
                </select>
                <button class="usuarios-btn" type="submit" name="crear">Crear usuario</button>
            </form>
        </div>
        <hr>
        <h2>Lista de usuarios</h2>

        <?php foreach ($usuarios as $fila): ?>

            <div class="usuario-card">
                <div class="usuario-info">
                    <div class="usuario-badge">
                        <?= strtoupper(htmlspecialchars($fila['rol'])) ?>
                    </div>
                    <h3><?= htmlspecialchars($fila['nombre'] . " " . $fila['apellidos']) ?></h3>
                    <p><strong>Email:</strong><?= htmlspecialchars($fila['email']) ?></p>
                    <p><strong>Usuario:</strong><?= htmlspecialchars($fila['usuario']) ?></p>
                    <p><strong>Teléfono:</strong><?= htmlspecialchars($fila['telefono']) ?></p>
                </div>

                <!-- Formulario para editar/eliminar usuario -->
                <form class="usuarios-form editar-form" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="idUser" value="<?= $fila['idUser'] ?>">
                    <input type="text" name="nombre" value="<?= htmlspecialchars($fila['nombre']) ?>" required>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($fila['apellidos']) ?>" required>
                    <input type="email" name="email" value="<?= htmlspecialchars($fila['email']) ?>" required>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($fila['telefono']) ?>" required>
                    <input type="date" name="fecha" value="<?= htmlspecialchars($fila['fecha_nacimiento']) ?>" required>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($fila['direccion']) ?>">
                    <select name="sexo">
                        <option value="Hombre" <?= $fila['sexo'] === "Hombre" ? "selected" : "" ?>>Hombre</option>
                        <option value="Mujer" <?= $fila['sexo'] === "Mujer" ? "selected" : "" ?>>Mujer</option>
                        <option value="Otro" <?= $fila['sexo'] === "Otro" ? "selected" : "" ?>>Otro</option>
                    </select>
                    <select name="rol">
                        <option value="user" <?= $fila['rol'] === "user" ? "selected" : "" ?>>Usuario</option>
                        <option value="admin" <?= $fila['rol'] === "admin" ? "selected" : "" ?>>Administrador</option>
                    </select>
                    <button class="usuarios-btn btn-actualizar" type="submit" name="editar">Actualizar usuario</button>
                </form>
                <form method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="eliminar" value="<?= (int) $fila['idUser'] ?>">
                    <button class="btn-eliminar" type="submit">Eliminar usuario</button>
                </form>
            </div>

        <?php endforeach; ?>

    </div>
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