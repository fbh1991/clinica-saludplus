<?php

session_start();
require_once("../includes/config.php");

// Sólo usuarios

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'user') {

    header("Location: login.php");
    exit();
}

require_once("../includes/DB.php");
require_once("../includes/SQL.php");

$idUser = $_SESSION['idUser'];

$mensaje = "";

// Crear cita

if (isset($_POST['crear'])) {

    $fecha = $_POST['fecha'];
    $motivo = trim($_POST['motivo']);

    // No permitir fechas pasadas
    if ($fecha >= date("Y-m-d")) {

        try {
            SQL::crearCita($idUser, $fecha, $motivo);

            $mensaje = "<div class='mensaje-ok'>Cita creada correctamente</div>";
        } catch (PDOException $e) {
            $mensaje = "<div class='mensaje-error'>Error al crear la cita</div>";
        }
    } else {
        $mensaje = "<div class='mensaje-error'>No puedes crear citas en fechas pasadas</div>";
    }
}

// Eliminar cita

if (isset($_GET['eliminar'])) {

    $idCita = (int) $_GET['eliminar'];

    SQL::eliminarCita($idCita, $idUser);

    header("Location: citaciones.php");
    exit();
}

// Editar citas

if (isset($_POST['editar'])) {

    $idCita = (int) $_POST['idCita'];

    $fecha = $_POST['fecha'];

    $motivo = trim($_POST['motivo']);

    if ($fecha >= date("Y-m-d")) {

        SQL::actualizarCita($idCita, $idUser, $fecha, $motivo);
        header("Location: citaciones.php");
        exit();
    }
}

// Llamada al método para obtener citas
$citas = SQL::obtenerCitas($idUser);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis citas - Clínica SaludPlus</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/citaciones.css">
</head>
<body>
    <!--Aquí incluyo la barra de navegación que cambia según sea admin, user o visitante-->
    <?php include("../includes/navbar.php"); ?>

    <main class="citas-container">
        <section class="citas-hero">
            <h1>Mis citas médicas</h1>
            <p>Gestiona tus consultas médicas y revisa tu agenda personal.</p>
        </section>

        <?= $mensaje; ?>

        <!-- Sección crear cita -->
        <section>
            <h2>Nueva cita</h2>
            <div class="citas-card">
                <form class="citas-form" method="POST">
                    <input type="date" name="fecha" required>
                    <input type="text" name="motivo" placeholder="Motivo de la cita" required>
                    <button class="citas-btn" type="submit" name="crear">Crear cita</button>
                </form>
            </div>
        </section>
        <hr>
        <!-- Section que contiene las citas -->
        <section>
            <h2>Mis citas</h2>

            <?php if (empty($citas)): ?>

                <div class="sin-citas">

                    No tienes citas registradas actualmente.

                </div>

            <?php endif; ?>

            <?php foreach ($citas as $fila): ?>

                <article class="cita-card">
                    <div class="fecha-cita">

                        <?= htmlspecialchars($fila['fecha_cita']) ?>

                    </div>
                    <div class="motivo-cita">
                        <strong>Motivo:</strong>
                        <p><?= htmlspecialchars($fila['motivo_cita']) ?></p>
                    </div>

                    <?php if ($fila['fecha_cita'] >= date("Y-m-d")): ?>

                        <form class="citas-form" method="POST">
                            <input type="hidden" name="idCita" value="<?= $fila['idCita'] ?>">
                            <input type="date" name="fecha" value="<?= htmlspecialchars($fila['fecha_cita']) ?>" required>
                            <input type="text" name="motivo" value="<?= htmlspecialchars($fila['motivo_cita']) ?>" required>
                            <button class="citas-btn btn-editar" type="submit" name="editar">Actualizar</button>
                        </form>
                        <a class="btn-eliminar" href="citaciones.php?eliminar=<?= $fila['idCita'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta cita?')">Eliminar</a>

                    <?php else: ?>

                        <div class="cita-realizada">
                            Cita ya realizada
                        </div>

                    <?php endif; ?>

                </article>

            <?php endforeach; ?>

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