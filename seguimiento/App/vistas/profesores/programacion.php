

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_seguimiento.php'?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


<div class="card m-5 mb-5 shadow-lg" style="width:85%">
<div class="card-body">
<div class="section mb-5">


<?php $prog = !empty($datos['obtener_programacion']) ? $datos['obtener_programacion'][0] : null; ?>


<!-- MENSAJE INICIAL -->
<h5 style="color: #0583c3;">
    <?= (!$prog)
        ? 'MODULO SIN PROGRAMACIÓN ACTIVA'
        : 'MODULO CON PROGRAMACIÓN ACTIVA' ?>
</h5>
<p style="color: #0583c3; font-size:17px;">
    <?= (!$prog)
        ? 'Sube una programación usando el formulario a continuación. Puedes hacer tantos cambios como quieras hasta que tu jefe de departamento la verifique.'
        : 'Revisa la programación del módulo e indica si quieres cambiarla o no.' ?>
</p>



<!-- DESCARGA SI HAY PROGRAMACIÓN -->
<?php if ($prog): ?>
<div class="row">
    <form method="post" action="<?= RUTA_URL ?>/PProgramacion/descargar_programacion/<?= $datos['datos_modulo'][0]->id_modulo ?>">
        <input type="hidden" name="ruta_archivo" value="<?= $prog->ruta ?>">
        <button type="submit" class="btn btn-custom mt-1 mb-4">
            <i class="fas fa-download"></i> Descargar programación actual
        </button>
    </form>
</div>
<?php endif; ?>



<!-- FORMULARIO PARA SUBIR / CAMBIAR PROGRAMACIÓN -->
<?php if (
    !$prog || 
    ($prog && $prog->verificada_jefe_dep != 1)
): ?>
<div class="row mt-4 mb-3">
    <div class="col-12">
        <div class="alert <?= !$prog ? 'alert-primary' : 'alert-warning' ?>" role="alert">
            <?= !$prog 
                ? 'Subir nueva programación' 
                : 'Programación SIN VERIFICAR por tu Jefe de Departamento. Puedes hacer cambios hasta que sea verificada.' ?>
        </div>
    </div>
</div>

<form method="post" action="<?= RUTA_URL ?>/PProgramacion/cambiar_programacion/<?= $datos['datos_modulo'][0]->id_modulo ?>" enctype="multipart/form-data">
    <div class="row mt-4 mb-3">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <label class="input-group-text">¿Subes nueva programación?</label>
                <select class="form-select" id="cambiar_programacion" name="cambiar_programacion" required>
                    <option value="">Selecciona...</option>
                    <option value="1">Sí</option>
                    <option value="0" <?= !$prog ? 'disabled' : '' ?>>No</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4 mt-3">
            <input type="file" name="archivo" class="form-control" id="archivo_input">
        </div>
        <div class="col-12 col-md-3 mt-3">
            <input type="submit" class="btn btn-primary" value="Enviar">
        </div>
    </div>
</form>
<?php endif; ?>




<!-- ESTADOS -->
<?php if ($prog): ?>

    <!-- hay programacion activa pero profesor NO la cambia -->
    <?php if ($prog->nueva == 0):?>

            <?php if ($prog->verificada_jefe_dep != 1 && !(!$prog || ($prog && !($prog->nueva == 0 && $prog->verificada_jefe_dep == 1)))): ?>
                <!-- no mostramos mensaje  -->
            <?php elseif ($prog->verificada_jefe_dep == 1): ?>
                <div class="alert alert-success mt-4">
                    CICLO COMPLETADO. Programación validada por el Jefe de Departamento. Para hacer cambios en tu programacion, contacta con Calidad.
                </div>
            <?php endif; ?>

    <!-- hay programacion activa pero profesor SI la cambia -->
    <?php else: ?>

            <?php if ($prog->verificada_jefe_dep == 1): ?>

                    <?php if (empty($prog->codigo_verificacion)): ?>
                        <div class="alert alert-info mt-4">
                            Programación verificada por el Jefe de Departamento y pendiente de revisión por Calidad.
                        </div>

                    <?php elseif ($prog->verificada_profesor == 0): ?>
                        <div class="alert alert-info mt-4">
                            Introduce el código de verificación que Calidad ha escrito en la ultima hoja de tu programacin para completar el ciclo.
                        </div>

                        <form action="<?= RUTA_URL ?>/PProgramacion/enviar_codigo_verificacion/<?= $datos['datos_modulo'][0]->id_modulo ?>" 
                            method="POST" onsubmit="return validarCodigo('<?= $prog->codigo_verificacion; ?>')">
                            <div class="input-group mt-3">
                                <label class="input-group-text" for="cod_pro">Código de verificación *</label>
                                <input type="text" class="form-control" name="codigo" id="cod_pro" required>
                                <button type="submit" class="btn btn-success ms-2">Verificar</button>
                            </div>
                        </form>

                    <?php else: ?>
                        <div class="alert alert-success mt-4">
                            CICLO COMPLETADO. Para hacer cambios en tu programación, contacta con Calidad.
                        </div>
                    <?php endif; ?>

            <?php endif; ?>

    <?php endif; ?>

<?php endif; ?>


</div>
</div>
</div>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>



<script>


    document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('cambiar_programacion');
    const archivo = document.getElementById('archivo_input');

    if(select && archivo) {
        select.addEventListener('change', function() {
            if (this.value === '1') {
                archivo.removeAttribute('disabled');  // habilita el input
                archivo.setAttribute('required', true); // obligatorio
            } else {
                archivo.setAttribute('disabled', true); // deshabilita
                archivo.removeAttribute('required');     // no obligatorio
            }
        });
    }
});

    function validarCodigo(codigoVerificacion) {
        const codigoIngresado = document.getElementById('cod_pro').value;
        if (codigoIngresado === codigoVerificacion) {
            alert('Código verificado correctamente.');
            return true;
        } else {
            alert('Código incorrecto.');
            return false;
        }
    }

</script>