<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.0/font/bootstrap-icons.css" integrity="sha384-7ynz3n3tAGNUYFZD3cWe5PDcE36xj85vyFkawcF6tIwxvIecqKvfwLiaFdizhPpN" crossorigin="anonymous">

    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/estilos.css">
    <title><?php echo NOMBRE_SITIO ?></title>
</head>

<body>

    <header>
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <div class="container-fluid">
                <a href="<?php echo RUTA_URL?>" class="navbar-brand"><?php echo NOMBRE_SITIO ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav me-auto mb-2 mb-md-0">
                        <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [1, 2])) : ?>
                            <li class="nav-item">
                                <?php if (isset($datos['menuActivo']) && $datos['menuActivo'] == 1) : ?>
                                    <a class="nav-link active" aria-current="page" href="<?php echo RUTA_URL ?>/usuarios">Usuarios</a>
                                <?php else : ?>
                                    <a class="nav-link" aria-current="page" href="<?php echo RUTA_URL ?>/usuarios">Usuarios</a>
                                <?php endif ?>
                            </li>
                        <?php endif ?>
                    </ul>
                    <ul class="navbar-nav ms-auto mb-2 mb-md-0">
                        <li class="navbar-text">
                            <?php echo $datos['usuarioSesion']->nombre ?>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="<?php echo RUTA_URL ?>/login/logout">LogOut</a>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
    </header>


