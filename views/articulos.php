<!DOCTYPE html>
<html>

<head>
    <title>Clínica SaludPlus</title>
    <link rel="stylesheet" href="../css/articulos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <article>
        <div>
            <h1>Servicios médicos</h1>
            <p>Disponemos de los mejores servicios para tu salud.</p>
        </div>
        <div class="container">
            <div class="row">
                <?php
                /***
                 * 1-Le asignamos a la variable $filename el valor, con el mismo nombre que el archivo .json ('articulos.json').
                 * 2-Cargamos el contenido de la variable $filename a la variable '$json' a través de la función 'file_get_contents()'
                 * 3-Convertimos el archivo '.json' en array php y se lo asignamos a la variable '$articulosclinica' (esta variable contiene el array convertido).
                 * 4-Por último, con el bucle 'foreach', ya podemos recorrer el array que contiene todos los datos '.json' y mostrarlos.
                 */
                $filename = "json/articulos.json";
                $json = file_get_contents($filename);
                $articulosclinica = json_decode($json, true);
                ?>
                <?php
                foreach ($articulosclinica as $value) {
                ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo $value["urlImg"] ?>" class="card-img-top" alt="imagen de articulos clinica">
                            <div class="card-body">
                                <h5 class="card-text"><?php echo $value["titulo"] ?></p>
                            </div>
                        </div>
                    </div>
                    <br><br>
                <?php
                }
                ?>
            </div>
        </div>
    </article>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>