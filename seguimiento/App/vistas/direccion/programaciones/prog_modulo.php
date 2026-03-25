

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">






<div class="card m-4">

    <div class="card-header" style="background-color:#0583c3;">
        <div style="color:white; display: flex; align-items: center;">
            <H5 style="margin-right: 10px;"><b><?php echo $datos['modulo'][0]->modulo.' ('.$datos['modulo'][0]->curso.') - '. $datos['modulo'][0]->turno?></b></H5>
            <a class="nav-link" style="font-size: 1.3em;" href="<?php echo RUTA_URL?>/programaciones/ciclo/<?php echo $datos['modulo'][0]->id_ciclo?>">
                <i class="fas fa-arrow-circle-left" style="vertical-align: middle;"></i>
            </a>
        </div>
    </div>

    <div class="mt-4 mb-4">
        <form method="post" action="<?php echo RUTA_URL ?>/programaciones/subir_programacion/<?php echo $datos['modulo'][0]->id_modulo ?>" enctype="multipart/form-data">
            <input type="hidden" name="ruta_archivo" value="">
            <label for="archivo_<?php echo $datos['modulo'][0]->id_modulo ?>" class="btn btn-custom mt-2 ms-4">
                <i class="fas fa-upload"></i> Subir programación
            </label>
            <input type="file" name="archivo" id="archivo_<?php echo $datos['modulo'][0]->id_modulo ?>" style="display: none;" onchange="mostrarBotonEnviar(<?php echo $datos['modulo'][0]->id_modulo ?>); mostrarNombreArchivo()">
            

            <div class="row mt-3 mb-3 ms-4 g-2" style="max-width: 100%;">
                <div class="col-auto">
                    <div class="input-group">
                        <span class="input-group-text" for="edicion_<?php echo $datos['modulo'][0]->id_modulo ?>">Edición</span>
                        <input type="number" name="num_version" id="edicion_<?php echo $datos['modulo'][0]->id_modulo ?>" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="col-auto d-flex align-items-end">
                    <button type="submit" class="btn btn-custom" style="display: none;" id="submitButton_<?php echo $datos['modulo'][0]->id_modulo ?>">Enviar</button>
                </div>
            </div>


            <div id="nombreArchivo_<?php echo $datos['modulo'][0]->id_modulo ?>" class="mt-2 ms-4" style="color: #0583c3;"></div>
        </form>
    </div>

</div>




<div class="table-responsive">
<table class="table tabla-formato">

        <thead>
            <tr>
                <th colspan="7">PROGRAMACIONES DEL MODULO</th>
            </tr>
            <tr>
                <th style="text-align:center">Fecha</th>
                <th style="text-align:center">Edicion</th>
                <th style="text-align:center">Nueva</th>
                <th style="text-align:center">Activa</th>
                <th style="text-align:center">Codigo programacion</th>
                <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[50])):?>
                <th style="text-align:center" colspan="2">Opciones<i class="fas fa-cogs ms-2" style="color: white; font-size: 1.3em; vertical-align: middle"></i></th>
                <?php endif ?> 
            </tr>
        </thead>
        

        <!-- BODY TABLA -->
        <tbody id="tablaCuerpo">
            <?php foreach ($datos['programaciones_modulo'] as $programaciones):?>

                <tr>
                    
                    <td><?php echo date('d-m-Y', strtotime($programaciones->fecha)); ?></td>
                    <td style="text-align:center"><?php echo $programaciones->num_version;?></td>
                    <td style="text-align:center"><?php echo ($programaciones->nueva == 1) ? 'Si' : 'No'; ?></td>
                    <td style="text-align:center"><?php echo ($programaciones->activa == 1) ? 'Si' : 'No'; ?></td>
                    <td><?php echo $programaciones->codigo_programacion;?></td>
  
                    <td style="text-align:center; display: flex; justify-content: center; align-items: center;">

                        <!-- Botón de descarga -->
                        <form method="post" action="<?php echo RUTA_URL ?>/programaciones/descargar_programacion">
                            <input type="hidden" name="ruta_archivo" id="ruta" value="<?php echo $programaciones->ruta ?>">
                            <button type="submit" class="nav-link evaluacion-link">
                                <i class="fas fa-file-pdf fa-lg" style="color:orangered;"></i>
                            </button>
                        </form>

                        <!-- Borrar programación -->
                        <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $programaciones->id_programacion?>">
                            <img class="icono" src="<?php echo RUTA_Icon ?>papelera.png" style="width: 24px; height: 24px;">
                        </a>

                        <div class="modal fade" tabindex="-1" aria-hidden="true" id="borrar_<?php echo $programaciones->id_programacion?>">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-3 shadow-lg">
                                    <div class="modal-header">
                                        <p class="modal-title ms-3">Borrado de programaciones</p>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body mt-3"> 
                                        <p>Vas a borrar la edicion <b><?php echo $programaciones->num_version?></b> del módulo <b><?php echo $programaciones->modulo?></b> ,estas seguro ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="<?php echo RUTA_URL?>/programaciones/borrar_programacion/<?php echo $programaciones->id_programacion?>" method="post">
                                            <input type="hidden" name="ruta_archivo" id="ruta" value="<?php echo $programaciones->ruta ?>">
                                            <input type="hidden" name="id_modulo" id="id_modulo" value="<?php echo $programaciones->id_modulo?>">
                                            <input type="submit" class="btn" name="borrar" id="boton-modal" value="Borrar">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>

</table>
</div>





<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>    



<script>


     // Mostrar el nombre del archivo seleccionado
 function mostrarNombreArchivo() {
    var archivoInput = document.getElementById('archivo_<?php echo $datos['modulo'][0]->id_modulo?>');
    var nombreArchivo = archivoInput.files[0] ? archivoInput.files[0].name : ''; // Obtiene el nombre del archivo
    var nombreArchivoDiv = document.getElementById('nombreArchivo_<?php echo $datos['modulo'][0]->id_modulo?>');
    
    // Muestra el nombre del archivo en el div
    if (nombreArchivo) {
        nombreArchivoDiv.textContent =  nombreArchivo;
    } else {
        nombreArchivoDiv.textContent = ''; // Si no hay archivo, no muestra nada
    }
}
function mostrarBotonEnviar(idModulo) {
    // Muestra el botón de enviar solo para el módulo correspondiente
    var submitButton = document.getElementById("submitButton_" + idModulo);
    submitButton.style.display = "inline-block";  // Muestra el botón
}

</script>














          




