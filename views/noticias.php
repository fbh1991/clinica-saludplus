<?php

require_once("../includes/config.php");
require_once("../includes/DB.php");
require_once("../includes/SQL.php");

// LLamo al método para obtener noticias de la base de datos 

$noticias = SQL::obtenerNoticias();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias - Clínica SaludPlus</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/noticias.css">
</head>
<body>
    <!--Aquí incluyo la barra de navegación que cambia según sea admin, user o visitante-->
    <?php include("../includes/navbar.php"); ?>
    <main class="main-content">
        <section class="container">
            <h1 class="tituloNoticias">Noticias</h1>

            <!-- Aquí incluyo el listado de noticias de la base de datos -->
            <?php foreach ($noticias as $fila): ?>

                <article class="card">
                    <div class="card-image">
                        <!-- Imagen de las noticia -->
                        <img src="../images/images_noticias/<?= htmlspecialchars($fila['imagen']) ?>" alt="Imagen noticia">
                    </div>
                    <div class="card-body">
                        <!-- Titulo de las noticia -->
                        <h2 class="card-title"><?= htmlspecialchars($fila['titulo']) ?></h2>
                        <!-- Texto de las noticia -->
                        <p class="card-text"><?= nl2br(htmlspecialchars($fila['texto'])) ?></p>
                        <!-- Autor de la noticia -->
                        <p class="autor"><?= htmlspecialchars($fila['nombre'] . ' ' . $fila['apellidos']) ?></p>
                        <!-- Fecha de publicación de la noticia -->
                        <small class="fecha"><?= htmlspecialchars($fila['fecha']) ?></small>
                    </div>
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