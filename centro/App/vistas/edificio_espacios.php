<?php require_once RUTA_APP . '/vistas/inc/header_general.php' ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<div class="p-5 shadow border mt-5 tarjeta">
<div class="container">
<div class="row">


    <!-- encabezado tarjeta -->
    <div class="container">
        <div class="row">
            <div class="col-8">
                <strong id="ciclo_encabezado"><?php echo $datos['edificio']->edificio ?></strong>
            </div>
            <div class="col-4 text-end">
                <a href="<?php echo RUTA_URL . '/edificio' ?>" class="btn btn-volver">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>


    <!-- tabla espacios -->
    <div class="table-responsive mt-4">
    <table class="table table-bordered tabla-formato">

        <thead>
            <tr>
                <!-- nuevo espacio -->
                <th>
                    <span>Nuevo espacio / aula</span>
                    <i data-bs-toggle="modal" data-bs-target="#nuevo_espacio" class="fas fa-plus-circle ms-2 circulo_mas"></i>
                </th>
                <!-- opciones -->
                <th class="text-center">Opciones <i class="fas fa-cogs ms-2 circulo_mas"></i></th>
            </tr>
        </thead>

        <tbody>
        <?php if (empty($datos['espacios'])): ?>
        <tr>
            <td colspan="2" class="text-center text-muted fst-italic">No hay espacios registrados en este edificio.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($datos['espacios'] as $espacio): ?>
        <tr>

            <!-- nombre espacio -->
            <td><?php echo $espacio->ubicacion ?></td>

            <!-- opciones -->
            <td class="text-center">

                <!-- EDITAR ESPACIO -->
                <a data-bs-toggle="modal" data-bs-target="#editar_<?php echo $espacio->id_ubicacion ?>">
                    <img class="icono" src="<?php echo RUTA_Icon ?>editar.png" alt="Editar">
                </a>
                <div class="modal fade" id="editar_<?php echo $espacio->id_ubicacion ?>">
                <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-3 shadow-lg">
                    <!-- modal header -->
                    <div class="modal-header">
                        <p class="modal-title ms-3">Editar espacio</p>
                        <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                    </div>
                    <!-- modal body -->
                    <div class="modal-body">
                    <div class="row ms-1 me-1">
                    <form action="<?php echo RUTA_URL ?>/edificio/editar_espacio/<?php echo $espacio->id_ubicacion ?>" method="post">
                        <!-- campo oculto para volver al edificio correcto -->
                        <input type="hidden" name="id_edificio" value="<?php echo $datos['edificio']->id_edificio ?>">
                        <div class="row mt-4">
                            <div class="input-group">
                                <label for="nombre_editar_<?php echo $espacio->id_ubicacion ?>" class="input-group-text">Nombre<sup>*</sup></label>
                                <input type="text" class="form-control" id="nombre_editar_<?php echo $espacio->id_ubicacion ?>"
                                       name="nombre" value="<?php echo $espacio->ubicacion ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer mt-4">
                            <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
                        </div>
                    </form>
                    </div>
                    </div>
                </div>
                </div>
                </div>


                <!-- BORRAR ESPACIO -->
                <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $espacio->id_ubicacion ?>">
                    <img class="icono" src="<?php echo RUTA_Icon ?>papelera.png" alt="Borrar">
                </a>
                <div class="modal fade" id="borrar_<?php echo $espacio->id_ubicacion ?>">
                <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-3 shadow-lg">
                    <!-- modal header -->
                    <div class="modal-header">
                        <p class="modal-title ms-3">Borrar espacio</p>
                        <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                    </div>
                    <!-- modal body -->
                    <div class="modal-body mt-3">
                        <p>¿Estás seguro de que quieres borrar el espacio <b>"<?php echo $espacio->ubicacion ?>"</b>?</p>
                    </div>
                    <!-- boton -->
                    <div class="modal-footer">
                        <form action="<?php echo RUTA_URL ?>/edificio/borrar_espacio/<?php echo $espacio->id_ubicacion ?>" method="post">
                            <!-- campo oculto para volver al edificio correcto -->
                            <input type="hidden" name="id_edificio" value="<?php echo $datos['edificio']->id_edificio ?>">
                            <input type="submit" class="btn" name="borrar" id="boton-modal" value="Borrar">
                        </form>
                    </div>
                </div>
                </div>
                </div>

            </td>

        </tr>
        <?php endforeach ?>
        <?php endif ?>
        </tbody>

    </table>
    </div>


</div>
</div>
</div>



<!-- NUEVO ESPACIO -->
<div class="modal fade" id="nuevo_espacio" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content rounded-3 shadow-lg">

    <!-- modal header -->
    <div class="modal-header">
        <h5 class="modal-title ms-3">Nuevo espacio en <?php echo $datos['edificio']->edificio ?></h5>
        <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
    </div>

    <!-- modal body -->
    <div class="modal-body">
    <div class="row ms-1 me-1">
    <form action="<?php echo RUTA_URL ?>/edificio/nuevo_espacio/<?php echo $datos['edificio']->id_edificio ?>" method="post">

        <div class="row mt-4">
            <div class="input-group">
                <label for="nombre" class="input-group-text">Nombre<sup>*</sup></label>
                <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Aula 1.01">
            </div>
        </div>
        <div class="modal-footer mt-4">
            <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
        </div>

    </form>
    </div>
    </div>

</div>
</div>
</div>



<script src="<?php echo RUTA_URL ?>/public/js/centro.js"></script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
