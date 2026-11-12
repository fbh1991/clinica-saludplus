<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . "/config.php");

?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?php echo BASE_URL?>css/navbar.css">

<header class="header">

    <nav class="navbar">

        <!-- LOGO -->
        <div class="logo">
            <a href="<?php echo BASE_URL?>index.php">
                Salud<span>Plus</span>
            </a>
        </div>

        <!-- MENU -->
        <!-- BOTON HAMBURGUESA -->

<div class="hamburger" id="hamburger">

    <span></span>
    <span></span>
    <span></span>

</div>

<!-- MENU -->

<ul class="nav-links" id="navLinks">

            <li>
                <a href="<?php echo BASE_URL?>index.php">
                    Inicio
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL?>views/noticias.php">
                    Noticias
                </a>
            </li>

            <?php if (!isset($_SESSION['rol'])): ?>

                <!-- VISITANTE -->

                <li>
                    <a href="<?php echo BASE_URL?>views/login.php">
                        Login
                    </a>
                </li>

                <li>
                    <a class="btn-nav"
                       href="<?php echo BASE_URL?>views/registro.php">
                        Registro
                    </a>
                </li>

            <?php elseif ($_SESSION['rol'] === 'user'): ?>

                <!-- USER -->

                <li>
                    <a href="<?php echo BASE_URL?>views/citaciones.php">
                        Mis citas
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL?>views/perfil.php">
                        Perfil
                    </a>
                </li>

                <li>
                    <a class="btn-logout"
                       href="<?php echo BASE_URL?>logout.php">
                        Cerrar sesión
                    </a>
                </li>

            <?php elseif ($_SESSION['rol'] === 'admin'): ?>

                <!-- ADMIN -->

                <li>
                    <a href="<?php echo BASE_URL?>admin/usuarios.php">
                        Usuarios
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL?>admin/citas.php">
                        Citas
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL?>admin/noticias.php">
                        Noticias
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL?>views/perfil.php">
                        Perfil
                    </a>
                </li>

                <li>
                    <a class="btn-logout"
                       href="<?php echo BASE_URL?>logout.php">
                        Salir
                    </a>
                </li>

            <?php endif; ?>

        </ul>

    </nav>
<script src="<?= BASE_URL ?>scripts/navbar.js"></script>
</header>