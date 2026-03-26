<?php require_once RUTA_APP . '/vistas/inc/header_general.php' ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<div class="p-5 shadow border mt-5 tarjeta">
<div class="container">
<div class="row">


    <!-- encabezado tarjeta -->
    <div class="row">
        <div class="col-12">
            <strong id="ciclo_encabezado">Edificios del centro</strong>
        </div>
    </div>


    <!-- tabla edificios -->
    <div class="table-responsive mt-4">
    <table class="table table-bordered tabla-formato">

        <thead>
            <tr>
                <!-- nuevo edificio -->
                <th>
                    <span>Nuevo edificio</span>
                    <i data-bs-toggle="modal" data-bs-target="#nuevo_edificio" class="fas fa-plus-circle ms-2 circulo_mas"></i>
                </th>
                <!-- nº espacios -->
                <th class="text-center">Espacios <i class="fas fa-door-open ms-2"></i></th>
                <!-- opciones -->
                <th class="text-center">Opciones <i class="fas fa-cogs ms-2 circulo_mas"></i></th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($datos['edificios'] as $edificio): ?>
        <tr>

            <!-- nombre edificio -->
            <td><?php echo $edificio->edificio ?></td>

            <!-- enlace a sus espacios -->
            <td class="text-center">
                <a href="<?php echo RUTA_URL ?>/edificio/edificio_espacios/<?php echo $edificio->id_edificio ?>">
                    <i class="fas fa-door-open"></i>
                    <span> <?php echo $edificio->num_espacios ?> espacio<?php echo ($edificio->num_espacios != 1) ? 's' : '' ?></span>
                </a>
            </td>

            <!-- opciones -->
            <td class="text-center">

                <!-- EDITAR EDIFICIO -->
                <a data-bs-toggle="modal" data-bs-target="#editar_<?php echo $edificio->id_edificio ?>">
                    <img class="icono" src="<?php echo RUTA_Icon ?>editar.png" alt="Editar">
                </a>
                <div class="modal fade" id="editar_<?php echo $edificio->id_edificio ?>">
                <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-3 shadow-lg">
                    <!-- modal header -->
                    <div class="modal-header">
                        <p class="modal-title ms-3">Editar edificio</p>
                        <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                    </div>
                    <!-- modal body -->
                    <div class="modal-body">
                    <div class="row ms-1 me-1">
                    <form action="<?php echo RUTA_URL ?>/edificio/editar_edificio/<?php echo $edificio->id_edificio ?>" method="post">
                        <div class="row mt-4">
                            <div class="input-group">
                                <label for="nombre_editar_<?php echo $edificio->id_edificio ?>" class="input-group-text">Nombre<sup>*</sup></label>
                                <input type="text" class="form-control" id="nombre_editar_<?php echo $edificio->id_edificio ?>"
                                       name="nombre" value="<?php echo $edificio->edificio ?>" required>
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


                <!-- BORRAR EDIFICIO -->
                <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $edificio->id_edificio ?>">
                    <img class="icono" src="<?php echo RUTA_Icon ?>papelera.png" alt="Borrar">
                </a>
                <div class="modal fade" id="borrar_<?php echo $edificio->id_edificio ?>">
                <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-3 shadow-lg">
                    <!-- modal header -->
                    <div class="modal-header">
                        <p class="modal-title ms-3">Borrar edificio</p>
                        <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                    </div>
                    <!-- modal body -->
                    <div class="modal-body mt-3">
                        <?php if ($edificio->num_espacios > 0): ?>
                            <p>El edificio <b>"<?php echo $edificio->edificio ?>"</b> tiene <b><?php echo $edificio->num_espacios ?></b> espacio(s) asociado(s). Si lo borras, se eliminarán también todos sus espacios. ¿Estás seguro?</p>
                        <?php else: ?>
                            <p>¿Estás seguro de que quieres borrar el edificio <b>"<?php echo $edificio->edificio ?>"</b>?</p>
                        <?php endif ?>
                    </div>
                    <!-- boton -->
                    <div class="modal-footer">
                        <form action="<?php echo RUTA_URL ?>/edificio/borrar_edificio/<?php echo $edificio->id_edificio ?>" method="post">
                            <input type="submit" class="btn" name="borrar" id="boton-modal" value="Borrar">
                        </form>
                    </div>
                </div>
                </div>
                </div>

            </td>

        </tr>
        <?php endforeach ?>
        </tbody>

    </table>
    </div>


</div>
</div>
</div>



<!-- NUEVO EDIFICIO -->
<div class="modal fade" id="nuevo_edificio" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content rounded-3 shadow-lg">

    <!-- modal header -->
    <div class="modal-header">
        <h5 class="modal-title ms-3">Nuevo edificio</h5>
        <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
    </div>

    <!-- modal body -->
    <div class="modal-body">
    <div class="row ms-1 me-1">
    <form action="<?php echo RUTA_URL ?>/edificio/nuevo_edificio" method="post">

        <div class="row mt-4">
            <div class="input-group">
                <label for="nombre" class="input-group-text">Nombre<sup>*</sup></label>
                <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Edificio principal">
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
