<?php

session_start();
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
if (isset($_POST['crear'])) {

    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $fecha = $_POST['fecha'];
    $direccion = trim($_POST['direccion']);
    $sexo = $_POST['sexo'];

    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    if (strlen($password) < 6) {
        $mensaje = "<div class='mensaje-error'>La contraseña debe tener al menos 6 caracteres</div>";
    } else {
        try {
            if (SQL::existeUsuario($usuario)) {
                $mensaje = "<div class='mensaje-error'>El usuario ya existe</div>";
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $conn = DB::conn();
                $conn->beginTransaction();

                // Insertar datos personales
                $idUser = SQL::insertarUsuarioData($nombre, $apellidos, $email, $telefono, $fecha, $direccion, $sexo);

                // Insertar login
                SQL::insertarLoginAdmin($idUser, $usuario, $passwordHash, $rol);
                $conn->commit();
                $mensaje = "<div class='mensaje-ok'>Usuario creado correctamente</div>";
            }
        } catch (PDOException $e) {
            if (isset($conn)) {
                $conn->rollBack();
            }
            $mensaje = "<div class='mensaje-error'>Error al crear usuario</div>";
        }
    }
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {

    $idUser = (int) $_GET['eliminar'];

    // Evitar borrarse a sí mismo (administrador)
    if ($idUser !== $_SESSION['idUser']) {

        try {
            $conn = DB::conn();
            $conn->beginTransaction();
            SQL::eliminarLogin($idUser);
            SQL::eliminarUsuarioData($idUser);
            $conn->commit();
            $mensaje = "<div class='mensaje-ok'>Usuario eliminado correctamente</div>";
        } catch (PDOException $e) {

            if (isset($conn)) {
                $conn->rollBack();
            }

            $mensaje = "<div class='mensaje-error'>Error al eliminar usuario</div>";
        }
    } else {
        $mensaje = "<div class='mensaje-error'>No puedes eliminar tu propia cuenta</div>";
    }
}

// Editar usuario
if (isset($_POST['editar'])) {

    $idUser = (int) $_POST['idUser'];

    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $fecha = $_POST['fecha'];
    $direccion = trim($_POST['direccion']);
    $sexo = $_POST['sexo'];
    $rol = $_POST['rol'];

    try {

        SQL::actualizarUsuarioAdmin($idUser, $nombre, $apellidos, $email, $telefono, $fecha, $direccion, $sexo);

        SQL::actualizarRolUsuario($idUser, $rol);

        $mensaje = "<div class='mensaje-ok'>Usuario actualizado correctamente</div>";
    } catch (PDOException $e) {
        $mensaje = "<div class='mensaje-error'>Error al actualizar usuario</div>";
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
                <a class="btn-eliminar" href="usuarios.php?eliminar=<?= $fila['idUser'] ?>">Eliminar usuario</a>
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