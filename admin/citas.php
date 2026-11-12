<?php

session_start();
require_once("../includes/config.php");

// Solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once("../includes/DB.php");
require_once("../includes/SQL.php");

$mensaje = "";

/* USUARIO SELECCIONADO */
$idUsuarioSeleccionado = isset($_GET['idUser']) ? (int)$_GET['idUser']: 0;
/* CREAR CITA */
if (isset($_POST['crear'])) {

    $idUser = (int) $_POST['idUser'];
    $fecha = $_POST['fecha'];
    $motivo = trim($_POST['motivo']);

    if ($fecha >= date("Y-m-d")) {
        try {
            SQL::crearCita($idUser, $fecha, $motivo);
            $mensaje = "<p class='mensaje-ok'>Cita creada correctamente</p>";
            $idUsuarioSeleccionado = $idUser;
        } catch (PDOException $e) {
            $mensaje = "<p class='mensaje-error'>Error al crear cita</p>";
        }
    } else {
        $mensaje = "<p class='mensaje-error'>Fecha inválida</p>";
    }
}

/* ELIMINAR CITA */
if (isset($_GET['eliminar'])) {

    $idCita = (int) $_GET['eliminar'];

    try {
        SQL::eliminarCitaAdmin($idCita);
        $mensaje = "<p class='mensaje-ok'>Cita eliminada correctamente</p>";
    } catch (PDOException $e) {
        $mensaje = "<p class='mensaje-error'>Error al eliminar cita</p>";
    }
}

/* EDITAR CITA */
if (isset($_POST['editar'])) {

    $idCita = (int) $_POST['idCita'];
    $fecha = $_POST['fecha'];
    $motivo = trim($_POST['motivo']);

    if ($fecha >= date("Y-m-d")) {
        try {
            SQL::actualizarCitaAdmin($idCita, $fecha, $motivo);
            $mensaje = "<p class='mensaje-ok'>Cita actualizada correctamente</p>";
        } catch (PDOException $e) {
            $mensaje = "<p class='mensaje-error'>Error al actualizar cita</p>";
        }
    }
}

/* OBTENER USUARIOS */

$usuarios = SQL::obtenerUsuarios();

/* OBTENER CITAS FILTRADAS */

$citas = [];
if ($idUsuarioSeleccionado > 0) {
    $citas = SQL::obtenerCitasPorUsuario($idUsuarioSeleccionado);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de citas</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/citasadmin.css">
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<div class="admin-container">

    <div class="admin-header">
        <h1>Administración de citas</h1>
        <p>Gestiona las citas médicas de los pacientes.</p>
    </div>

    <?= $mensaje ?>

    <h2>Seleccionar paciente</h2>
    <div class="admin-card">
        <form method="GET" class="admin-form">
            <select name="idUser" required>
                <option value="">Seleccionar usuario</option>

                <?php foreach ($usuarios as $u): ?>

                    <option value="<?= $u['idUser'] ?>"<?= ($idUsuarioSeleccionado == $u['idUser']) ? 'selected' : '' ?>><?= htmlspecialchars($u['nombre'] . " " . $u['apellidos']) ?></option>

                <?php endforeach; ?>

            </select>
            <button class="btn-principal" type="submit">Ver citas</button>
        </form>
    </div>

    <?php if ($idUsuarioSeleccionado > 0): ?>

        <hr>
        <h2>Nueva cita</h2>
        <div class="admin-card">
            <form class="admin-form" method="POST">
                <input type="hidden" name="idUser" value="<?= $idUsuarioSeleccionado ?>">
                <input type="date" name="fecha" required>
                <input type="text" name="motivo" placeholder="Motivo de la cita" required>
                <button class="btn-principal" type="submit" name="crear">Crear cita</button>
            </form>
        </div>
        <hr>
        <h2>Lista de citas</h2>

        <?php if (count($citas) > 0): ?>

            <?php foreach ($citas as $fila): ?>

                <div class="cita-card">
                    <div class="paciente-info">
                        <strong>Paciente:</strong>
                        <?= htmlspecialchars($fila['nombre'] . " " . $fila['apellidos']) ?>
                    </div>
                    <div class="fecha-cita">
                        <?= htmlspecialchars($fila['fecha_cita']) ?>
                    </div>
                    <div class="motivo-cita">
                        <strong>Motivo:</strong><br>
                        <?= htmlspecialchars($fila['motivo_cita']) ?>
                    </div>
                    <form class="admin-form" method="POST">
                        <input type="hidden" name="idCita" value="<?= $fila['idCita'] ?>">
                        <input type="date" name="fecha" value="<?= htmlspecialchars($fila['fecha_cita']) ?>">
                        <input type="text" name="motivo" value="<?= htmlspecialchars($fila['motivo_cita']) ?>">
                        <button class="btn-actualizar" type="submit" name="editar">Actualizar</button>
                    </form>
                    <a class="eliminar-btn" href="citas.php?idUser=<?= $idUsuarioSeleccionado ?>&eliminar=<?= $fila['idCita'] ?>">Eliminar cita</a>
                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>No hay citas para este usuario.</p>

        <?php endif; ?>

    <?php endif; ?>

</div>
<footer>
    © 2026 Clínica SaludPlus
    <h3>Síguenos en redes sociales</h3>
    <p>
        <a href="https://www.facebook.com/" target="_blank"><img src="../images/images_index/logofacebook.png" alt="Facebook"></a>
        <a href="https://www.instagram.com/" target="_blank"><img src="../images/images_index/logoinstagram.png" alt="Instagram"></a>
        <a href="https://x.com/?lang=es" target="_blank"><img src="../images/images_index/logotwitter.png" alt="X"></a>
    </p>
</footer>
</body>
</html>