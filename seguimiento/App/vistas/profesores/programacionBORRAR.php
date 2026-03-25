

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_seguimiento.php'?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">




 
<div class="card m-5 mb-5 shadow-lg" style="width:85%">
<div class="card-body">
<div class="section mb-5">
                

        <?php if (!empty($datos['obtener_programacion']) && $datos['obtener_programacion'][0]->activa == 1) { ?>

                <!-- HAY PROGRAMACION ACTIVA -->
                <h5 style="color:#0583c3;">Actualmente, hay una programación activa. Revísala e indica si vas a cambiarla o no.</h5>
                <p style="color:#0583c3;">En el caso afirmativo, la nueva programación, quedara pendiente de revision por parte del jefe del departamento y de calidad. <br>
                Una vez revisada por ambos, tendras que verificarla introduciendo el nuevo codigo que aparecera al final del documento de la programacion.</p>

                <!-- BOTÓN DESCARGA -->
                <div class="row">
                    <form method="post" action="<?php echo RUTA_URL?>/PProgramacion/descargar_programacion/<?php echo $datos['datos_modulo'][0]->id_modulo ?>">
                        <input type="hidden" name="ruta_archivo" id="ruta" value="<?php echo $datos['obtener_programacion'][0]->ruta ?>">
                        <button type="submit" class="nav-link evaluacion-link mt-1 mb-4" style="padding: 5px 15px; color:orangered; margin-right: 10px;">
                            <i class="fas fa-file-pdf"></i> Descargar programación actual
                        </button>
                    </form>
                </div>

                <!-- CAMBIO PROGRAMACIÓN -->
                <?php
                  $cambia = $datos['datos_modulo'][0]->cambia_programacion;

                if ($cambia == -1) : ?>
                <form method="post" action="<?php echo RUTA_URL ?>/PProgramacion/cambiar_programacion/<?php echo $datos['datos_modulo'][0]->id_modulo ?>" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-12 col-md-4 mt-3">
                            <div class="input-group">
                                <label class="input-group-text" for="cambiar_programacion">¿Vas a cambiar la programación del módulo?</label>
                                <select class="form-select" id="cambiar_programacion" name="cambiar_programacion" required>
                                    <option value=""></option>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-3">
                            <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Enviar">
                        </div>
                    </div>
                    <div class="row mt-3" id="archivo_input" style="display:none;">
                        <div class="col-12 col-md-6">
                            <input type="file" name="archivo" id="archivo" class="form-control">
                        </div>
                    </div>
                </form>
                <?php endif; ?>


                <?php 
                    $cambia = $datos['datos_modulo'][0]->cambia_programacion;

                    if ($cambia == 0) { ?>
                        
                        <p style="color:#0583c3; margin-top:15px;">Respuesta enviada. En el caso de querer hacer alguna modificación, por favor, contacta con el departamento de calidad.</p>

                    <?php } else if ($cambia == 1) { ?>



                            <?php 
                            if($datos['obtener_programacion'][0]->codigo_verificacion != '' && $datos['obtener_programacion'][0]->verificada_profesor == 0 ) { ?>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <form action="<?php echo RUTA_URL?>/PProgramacion/enviar_codigo_verificacion/<?php echo $datos['datos_modulo'][0]->id_modulo?>" method="POST" id="form-verificacion" onsubmit="return validarCodigo('<?php echo $datos['obtener_programacion'][0]->codigo_verificacion; ?>')">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="cod_pro">Código verificación *</label>
                                                    <input type="text" class="form-control" name="codigo" id="cod_pro" required>
                                                    <button type="submit" class="btn btn-primary ms-2" id="boton-modal">Enviar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="input-group">
                                                <label class="input-group-text" for="cod_pro">Programacion verificada </label>
                                                <input type="text" class="form-control" name="codigo" id="cod_pro" value="<?php echo $datos['obtener_programacion'][0]->codigo_verificacion?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>  


                <?php } ?>

            <?php } else { ?>

                <!-- NO HAY PROGRAMACION ACTIVA -->
                <h5 class="m-2" style="color:orangered;">Actualmente, no hay ninguna programación activa para este módulo.</h5> 
                <p class="m-2" style="color:orangered;">Por favor, contacta con el departamento de calidad</p>

            <?php } ?>


            <!-- INFO DE LA PROGRAMACION -->
            <div class="row mt-4">
                <div class="col-12 col-md-5 mb-2 mb-md-0">
                    <div class="input-group">
                        <label class="input-group-text" for="cod_pro">Codigo prog. Modulo</label>
                        <input type="text" class="form-control" value="<?php echo $datos['datos_modulo'][0]->codigo_programacion?>" readonly>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <label class="input-group-text" for="cod_pro">Edicion actual</label>
                        <input type="text" class="form-control" 
                        value="<?php if(!empty($datos['obtener_programacion'])){   
                                    echo $datos['obtener_programacion'][0]->num_version;
                                }else{
                                    echo'';
                                }?>"
                        readonly>
                    </div>
                </div>
            </div>


</div>
</div>
</div>



               
<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>








<script>


    // REFERENTE A LAS PROGRAMACIONES (usando <select>)
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('cambiar_programacion');
        const archivoInput = document.getElementById('archivo_input');
        const archivoField = document.querySelector('input[name="archivo"]');

        select.addEventListener('change', function () {
            if (this.value === '1') {
                archivoInput.style.display = 'block';
                archivoField.setAttribute('required', true);
            } else {
                archivoInput.style.display = 'none';
                archivoField.removeAttribute('required');
            }
        });
    });



    // REFERENTE A LAS PROGRAMACIONES
    function validarCodigo(codigoVerificacion) {
        const codigoIngresado = document.getElementById('cod_pro').value;
        if (codigoIngresado === codigoVerificacion) {
            alert('Código verificado correctamente.');
            return true;
        } else {
            alert('El codigo no coincide. Revisa la programacion e introduce el codigo correcto.');
            return false; 
        }
    }



</script>


