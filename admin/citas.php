<?php

require_once __DIR__ . "/../includes/security.php";
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
$idUsuarioSeleccionado = isset($_GET['idUser']) ? (int) $_GET['idUser'] : (int) ($_POST['idUser'] ?? 0);
/* CREAR CITA */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
}

if (isset($_POST['crear'])) {

    $idUser = (int) ($_POST['idUser'] ?? 0);
    $fecha = (string) ($_POST['fecha'] ?? '');
    $motivo = trim((string) ($_POST['motivo'] ?? ''));

    try {

        // Comprobar que el paciente existe
        $paciente = SQL::obtenerUsuario($idUser);

        if (!$paciente) {

            $mensaje = "<p class='mensaje-error'>El paciente seleccionado no existe</p>";

        } elseif (
            !valid_date($fecha) ||
            $fecha < date('Y-m-d')
        ) {

            $mensaje = "<p class='mensaje-error'>La fecha de la cita no es válida</p>";

        } elseif (
            $motivo === '' ||
            mb_strlen($motivo, 'UTF-8') > 500
        ) {

            $mensaje = "<p class='mensaje-error'>El motivo debe tener entre 1 y 500 caracteres</p>";

        } else {

            SQL::crearCita($idUser, $fecha, $motivo);

            $mensaje = "<p class='mensaje-ok'>Cita creada correctamente</p>";
            $idUsuarioSeleccionado = $idUser;
        }

    } catch (PDOException | RuntimeException $e) {

        $mensaje = "<p class='mensaje-error'>Error al crear cita</p>";
    }
}

/* ELIMINAR CITA */
if (isset($_POST['eliminar'])) {

    $idCita = (int) $_POST['eliminar'];
    $idUser = (int) ($_POST['idUser'] ?? 0);

    try {

        // Comprobar que la cita existe y pertenece al paciente seleccionado
        $citasPaciente = SQL::obtenerCitasPorUsuario($idUser);
        $citaExiste = false;

        foreach ($citasPaciente as $cita) {
            if ((int) $cita['idCita'] === $idCita) {
                $citaExiste = true;
                break;
            }
        }

        if (!$citaExiste) {

            $mensaje = "<p class='mensaje-error'>La cita seleccionada no existe</p>";

        } else {

            SQL::eliminarCitaAdmin($idCita, $idUser);

            $mensaje = "<p class='mensaje-ok'>Cita eliminada correctamente</p>";
            $idUsuarioSeleccionado = $idUser;
        }

    } catch (PDOException | RuntimeException $e) {

        $mensaje = "<p class='mensaje-error'>Error al eliminar cita</p>";
    }
}

/* EDITAR CITA */
if (isset($_POST['editar'])) {

    $idCita = (int) ($_POST['idCita'] ?? 0);
    $idUser = (int) ($_POST['idUser'] ?? 0);
    $fecha = (string) ($_POST['fecha'] ?? '');
    $motivo = trim((string) ($_POST['motivo'] ?? ''));

    try {

        // Comprobar que la cita existe y pertenece al paciente seleccionado
        $citasPaciente = SQL::obtenerCitasPorUsuario($idUser);
        $citaExiste = false;

        foreach ($citasPaciente as $cita) {
            if ((int) $cita['idCita'] === $idCita) {
                $citaExiste = true;
                break;
            }
        }

        if (!$citaExiste) {

            $mensaje = "<p class='mensaje-error'>La cita seleccionada no existe</p>";

        } elseif (
            !valid_date($fecha) ||
            $fecha < date('Y-m-d')
        ) {

            $mensaje = "<p class='mensaje-error'>La fecha de la cita no es válida</p>";

        } elseif (
            $motivo === '' ||
            mb_strlen($motivo, 'UTF-8') > 500
        ) {

            $mensaje = "<p class='mensaje-error'>El motivo debe tener entre 1 y 500 caracteres</p>";

        } else {

            SQL::actualizarCitaAdmin($idCita, $idUser, $fecha, $motivo);

            $mensaje = "<p class='mensaje-ok'>Cita actualizada correctamente</p>";
            $idUsuarioSeleccionado = $idUser;
        }

    } catch (PDOException | RuntimeException $e) {

        $mensaje = "<p class='mensaje-error'>Error al actualizar cita</p>";
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
                    <?= csrf_field() ?>
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
                    <?= csrf_field() ?>
                        <input type="hidden" name="idCita" value="<?= (int) $fila['idCita'] ?>">
                        <input type="hidden" name="idUser" value="<?= (int) $idUsuarioSeleccionado ?>">
                        <input type="date" name="fecha" value="<?= htmlspecialchars($fila['fecha_cita']) ?>">
                        <input type="text" name="motivo" value="<?= htmlspecialchars($fila['motivo_cita']) ?>">
                        <button class="btn-actualizar" type="submit" name="editar">Actualizar</button>
                    </form>
                    <form method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="idUser" value="<?= (int) $idUsuarioSeleccionado ?>">
                        <input type="hidden" name="eliminar" value="<?= (int) $fila['idCita'] ?>">
                        <button class="eliminar-btn" type="submit">Eliminar cita</button>
                    </form>
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