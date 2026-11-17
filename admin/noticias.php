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

$idUser = $_SESSION['idUser'];

$mensaje = "";

// Crear noticia
if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_validate(); }

if (isset($_POST['crear'])) {

    $titulo = trim($_POST['titulo']);
    $texto = trim($_POST['texto']);
    $fecha = date("Y-m-d");

    // Validar contenido y archivo en el servidor (no confiar en el nombre/extensión del cliente).
    $errores = [];
    if ($titulo === '' || mb_strlen($titulo, 'UTF-8') > 180) {
        $errores[] = 'El título es obligatorio y no puede superar 180 caracteres.';
    }
    if ($texto === '' || mb_strlen($texto, 'UTF-8') > 12000) {
        $errores[] = 'El contenido es obligatorio y no puede superar 12.000 caracteres.';
    }

    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        $errores[] = 'Debes seleccionar una imagen válida.';
    } elseif ($_FILES['imagen']['size'] > 3 * 1024 * 1024) {
        $errores[] = 'La imagen no puede superar los 3 MB.';
    }

    if (empty($errores)) {
        $tmp = $_FILES['imagen']['tmp_name'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
        $tipos = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];
        $imagenInfo = @getimagesize($tmp);

        if (!isset($tipos[$mime]) || $imagenInfo === false || $imagenInfo[0] > 6000 || $imagenInfo[1] > 6000) {
            $errores[] = 'El archivo debe ser una imagen JPG, PNG o WEBP válida (máximo 6000 × 6000 píxeles).';
        } else {
            $extension = $tipos[$mime];
            $nuevoNombre = bin2hex(random_bytes(16)) . '.' . $extension;
            $directorio = __DIR__ . '/../images/images_noticias/';
            if (!is_dir($directorio) && !mkdir($directorio, 0755, true)) {
                $errores[] = 'No se pudo preparar el directorio de imágenes.';
            } elseif (move_uploaded_file($tmp, $directorio . $nuevoNombre)) {
                try {
                    SQL::crearNoticia($titulo, $nuevoNombre, $texto, $fecha, $idUser);
                    $mensaje = "<p class='mensaje-ok'>Noticia creada correctamente</p>";
                } catch (Throwable $e) {
                    @unlink($directorio . $nuevoNombre);
                    error_log('No se pudo guardar una noticia: ' . $e->getMessage());
                    $mensaje = "<p class='mensaje-error'>Error al guardar noticia</p>";
                }
            } else {
                $errores[] = 'No se pudo guardar la imagen.';
            }
        }
    }

    if (!empty($errores)) {
        $mensaje = "<p class='mensaje-error'>" . htmlspecialchars(implode(' ', $errores), ENT_QUOTES, 'UTF-8') . "</p>";
    }
}

// Eliminar noticia
if (isset($_POST['eliminar'])) {
    $idNoticia = (int) $_POST['eliminar'];
    try {
        $noticia = SQL::obtenerImagenNoticia($idNoticia);
        if ($noticia) {
            $nombreSeguro = basename((string) $noticia['imagen']);
            $rutaImagen = __DIR__ . '/../images/images_noticias/' . $nombreSeguro;
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
    if ($titulo === '' || mb_strlen($titulo, 'UTF-8') > 180 || $texto === '' || mb_strlen($texto, 'UTF-8') > 12000) {
        $mensaje = "<p class='mensaje-error'>Revisa el título y el contenido de la noticia.</p>";
    } else try {
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
                    <?= csrf_field() ?>
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
                    <?= csrf_field() ?>
                        <input type="hidden" name="idNoticia" value="<?= $fila['idNoticia'] ?>">
                        <input type="text" name="titulo" value="<?= htmlspecialchars($fila['titulo']) ?>">
                        <textarea name="texto"><?= htmlspecialchars($fila['texto']) ?></textarea>
                        <button class="btn-actualizar" type="submit" name="editar">Actualizar noticia</button>
                    </form>
                    <form method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta noticia?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="eliminar" value="<?= (int) $fila['idNoticia'] ?>">
                        <button class="eliminar-btn" type="submit">Eliminar noticia</button>
                    </form>
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