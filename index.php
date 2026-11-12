<?php
session_start();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica SaludPlus</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body id="bodyHiper">
    <!--Aquí incluyo la barra de navegación que cambia según sea admin, user o visitante-->
    <?php include("includes/navbar.php"); ?>
    <main>
        <!-- Sección banner principal -->
        <section class="banner-content">
            <h1 class="fade-in">Clínica SaludPlus</h1>
            <p class="fade-in delay-1">Cuidamos de tu salud con tecnología y confianza</p>
            <!--Botón pedir cita (si se inicia sesión como admin desaparece)-->
            <?php if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin'): ?>
                <a href="<?php echo isset($_SESSION['rol']) ? 'views/citaciones.php' : 'views/login.php'; ?>" class="btn-banner fade-in delay-2">Pedir cita</a><?php endif; ?>
        </section>
        <!-- Menú con hipervinculos de las secciones de la página-->
        <section class="hipervinculos" id="hipervinculos">
            <div class="flex flexHiper">
                <div class="flex gap5">
                    <img src="images/images_index/clinica.ico" alt="Icono clínica" class="icon"><a href="#clinicas" class="linkHiper">Centros médicos</a>
                </div>
                <div class="flex gap5">
                    <img src="images/images_index/clinica.ico" alt="Icono clínica"class="icon"><a href="#tecnologia" class="linkHiper">Tecnología</a>
                </div>
                <div class="flex gap5">
                    <img src="images/images_index/clinica.ico" alt="Icono clínica" class="icon"><a href="#cirugia" class="linkHiper">Cirugía</a>
                </div>
                <div class="flex gap5">
                    <img src="images/images_index/clinica.ico" alt="Icono clínica" class="icon"><a href="#especialidades" class="linkHiper">Especialidades médicas</a>
                </div>
                <div class="flex gap5">
                    <img src="images/images_index/clinica.ico" alt="Icono clínica"class="icon"><a href="#personal" class="linkHiper">Nuestro equipo</a>
                </div>
                <div class="flex subir">
                    <a href="#bodyHiper"><img src="images/images_index/subir.png" alt="Subir arriba" class="iconSubir"></a>
                </div>
            </div>
        </section>
        <!-- Contenedor principal de las secciones-->
        <div class="container">
            <!-- Sección centros médicos -->
            <section class="hero" id="clinicas">
                <img src="images/images_index/map_spain.png" alt="Mapa de centros médicos">
                <h1>Nuestros centros médicos</h1>
                <p>
                    Contamos con una amplia red de clínicas y hospitales en todo el país, ofreciendo atención médica cercana y de calidad.
                </p>
            </section>
            <!-- Sección tecnología -->
            <section class="card" id="tecnologia">
                <img src="images/images_index/tecnologia.png" alt="Tecnología médica">
                <h1>Tecnología Médica Avanzada</h1>
                <p>
                    Disponemos de equipos de última generación para diagnóstico y tratamiento.
                </p>
            </section>
            <!-- Sección cirugía -->
            <section class="card" id="cirugia">

                <img src="images/images_index/cirugia.jpg" alt="Cirugía hospitalaria">
                <h1>Cirugía Especializada</h1>
                <p>
                    Nuestro equipo médico realiza intervenciones con los más altos estándares.
                </p>
            </section>
            <!-- Sección articulos -->
            <section class="card" id="especialidades">

                <?php include("views/articulos.php"); ?>

            </section>
            <!-- Sección personal -->
            <section class="sectionPersonal">
                <article class="articulo flex gap20 mrgAuto lastArticle">
                    <div class="card" id="personal">
                        <h1>Nuestro personal</h1>

                        <p>
                            Contamos con un gran equipo de profesionales.
                        </p>
                        <div class="contenedor_thumbnail flex">
                            <div>
                                <img src="images/images_index/doctor.jpg" alt="Doctor" class="medicos">
                            </div>
                            <div>
                                <img src="images/images_index/doctora.jpg" alt="Doctora" class="medicos">
                            </div>
                            <div>
                                <img src="images/images_index/enfermera.jpg" alt="Enfermera" class="medicos">
                            </div>
                            <div>
                                <img src="images/images_index/enfermero.jpg" alt="Enfermero" class="medicos">
                            </div>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </main>
    <!-- Footer -->
    <footer>
        © 2026 Clínica SaludPlus
        <h3>Síguenos en redes sociales</h3>
        <p>
            <a href="https://www.facebook.com/" target="_blank"><img src="images/images_index/logofacebook.png" alt="Facebook"></a>
            <a href="https://www.instagram.com/" target="_blank"><img src="images/images_index/logoinstagram.png" alt="Instagram"></a>
            <a href="https://x.com/?lang=es" target="_blank"><img src="images/images_index/logotwitter.png" alt="X"></a>
        </p>
    </footer>
    <!-- Scripts -->
    <script src="scripts/scrollSpy.js"></script>
</body>
</html>