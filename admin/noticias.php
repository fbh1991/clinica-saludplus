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

$idUser = $_SESSION['idUser'];

$mensaje = "";

// Crear noticia
if (isset($_POST['crear'])) {

    $titulo = trim($_POST['titulo']);
    $texto = trim($_POST['texto']);
    $fecha = date("Y-m-d");

    // Validar imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
        $nombreImagen = $_FILES['imagen']['name'];
        $extension = strtolower(pathinfo($nombreImagen, PATHINFO_EXTENSION));
        if (in_array($extension, $permitidos)) {

            // Genero nombre unico para la imagen de la noticia
            $nuevoNombre = uniqid("noticia_", true) . "." . $extension;
            $rutaFisica = "../images/images_noticias/" . $nuevoNombre;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFisica)) {
                try {
                    SQL::crearNoticia($titulo, $nuevoNombre, $texto, $fecha, $idUser);
                    $mensaje = "<p class='mensaje-ok'>Noticia creada correctamente</p>";
                } catch (PDOException $e) {
                    $mensaje = "<p class='mensaje-error'>Error al guardar noticia</p>";
                }
            } else {
                $mensaje = "<p class='mensaje-error'>Error al subir imagen</p>";
            }
        } else {
            $mensaje = "<p class='mensaje-error'>Formato de imagen no permitido</p>";
        }
    } else {
        $mensaje = "<p class='mensaje-error'>Debes seleccionar una imagen</p>";
    }
}

// Eliminar noticia
if (isset($_GET['eliminar'])) {
    $idNoticia = (int) $_GET['eliminar'];
    try {
        $noticia = SQL::obtenerImagenNoticia($idNoticia);
        if ($noticia) {
            $rutaImagen = "../images/images_noticias/" . $noticia['imagen'];
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
            SQL::eliminarNoticia($idNoticia);
            $mensaje = "<p class='mensaje-ok'>Noticia eliminada correctamente</p>";
        }
    } catch (PDOException $e) {
        $mensaje = "<p class='mensaje-error'>Error al eliminar noticia</p>";
    }
}

// Editar noticia
if (isset($_POST['editar'])) {
    $idNoticia = (int) $_POST['idNoticia'];
    $titulo = trim($_POST['titulo']);
    $texto = trim($_POST['texto']);
    try {
        SQL::actualizarNoticia($idNoticia, $titulo, $texto);
        $mensaje = "<p class='mensaje-ok'>Noticia actualizada correctamente</p>";
    } catch (PDOException $e) {
        $mensaje = "<p class='mensaje-error'>Error al actualizar noticia</p>";
    }
}

// Llamo al método para obtener noticias
$noticias = SQL::obtenerNoticias();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de noticias</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/noticiasadmin.css">
</head>

<body>

    <?php include("../includes/navbar.php"); ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Administración de noticias</h1>
            <p>Publica, edita y elimina noticias de la clínica.</p>
        </div>

        <?= $mensaje ?>

        <h2>Nueva noticia</h2>
        <div class="admin-card">
            <form class="cms-form" method="POST" enctype="multipart/form-data">
                <input type="text" name="titulo" placeholder="Título de la noticia" required>
                <textarea name="texto" placeholder="Contenido de la noticia" required></textarea>
                <input type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp" required>
                <button type="submit" name="crear">Publicar noticia</button>
            </form>
        </div>
        <hr>
        <h2>Noticias publicadas</h2>

        <?php foreach ($noticias as $fila): ?>

            <div class="noticia-card">
                <img class="noticia-img" src="../images/images_noticias/<?= htmlspecialchars($fila['imagen']) ?>" alt="Imagen noticia">
                <div class="noticia-content">
                    <h3><?= htmlspecialchars($fila['titulo']) ?></h3>
                    <div class="fecha-publicacion">
                        <?= htmlspecialchars($fila['fecha']) ?>
                    </div>
                    <div class="noticia-texto">
                        <?= nl2br(htmlspecialchars($fila['texto'])) ?>
                    </div>
                    <form class="cms-form" method="POST">
                        <input type="hidden" name="idNoticia" value="<?= $fila['idNoticia'] ?>">
                        <input type="text" name="titulo" value="<?= htmlspecialchars($fila['titulo']) ?>">
                        <textarea name="texto"><?= htmlspecialchars($fila['texto']) ?></textarea>
                        <button class="btn-actualizar" type="submit" name="editar">Actualizar noticia</button>
                    </form>
                    <a class="eliminar-btn" href="noticias.php?eliminar=<?= $fila['idNoticia'] ?>">Eliminar noticia</a>
                </div>
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