

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">





<!-- Tabla responsive -->
<div class="table-responsive">
<table class="table mt-5 tabla-formato">


    <!-- CABECERA TABLA -->
    <thead>
        <tr>
            <th>ID</th>
            <th id="tabla-titulo-container">
                <span id="tabla-titulo">Cursos lectivos</span>
                <?php if(empty($datos['lectivo'])):?>
                <i data-bs-toggle="modal" data-bs-target="#nuevo_curso" class="fas fa-plus-circle ms-2" style="color: white; font-size: 1.3em; vertical-align: middle"></i>
                <?php endif; ?>
            </th>
            <th>Fecha inicio</th>
            <th>Fecha fin</th>
            <th>Estado</th>
            <th>Evaluaciones <i class="fas fa-clipboard-list"></i> </th>
            <th>Festivos <i class="fas fa-flag"></i></th>
            <th>Umbrales <i class="fas fa-chart-bar"></i></th>
            <th>Calendario <i class="fas fa-calendar-alt"></i></th>
            <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[50])):?>
                <th style="text-align:center">Opciones<i class="fas fa-cogs ms-2" style="color: white; font-size: 1.3em; vertical-align: middle"></i></th>
            <?php endif ?> 
            <th>Cerrar curso <i class="fas fa-lock"></i></th>
        </tr>
    </thead>
        

        <tbody>
            <?php foreach ($datos['cursos_lectivos'] as $lectivos) : ?>
                <tr>

                    <td><?php echo $lectivos->id_lectivo?></td>
                    <td><?php echo $lectivos->lectivo?></td>
                    <td><?php echo $lectivos->fecha_inicio?></td>
                    <td><?php echo $lectivos->fecha_fin?></td>
                    <td>
                        <?php if ($lectivos->cerrado == 0): ?>
                            <span class="text-success">
                                <i class="fas fa-check-circle"></i> Abierto
                            </span>
                        <?php else: ?>
                            <span class="text-danger">
                                <i class="fas fa-times-circle"></i> Cerrado
                            </span>
                        <?php endif; ?>
                    </td>


                    <!-- Evaluaciones -->
                    <td>
                        <?php if ($lectivos->cerrado == 0): ?>
                            <a style="text-decoration:none" href="<?php echo RUTA_URL?>/curso/evaluaciones">
                                <i class="fas fa-clipboard-list"></i> Evaluaciones
                            </a>
                        <?php else: ?>
                            <span style="opacity: 0.5; cursor: not-allowed; color:#0583c3;">
                                <i class="fas fa-clipboard-list"></i> Evaluaciones
                            </span>
                        <?php endif; ?>
                    </td>


                    <!-- Festivos -->
                    <td>
                        <?php if ($lectivos->cerrado == 0): ?>
                            <a style="text-decoration:none" href="<?php echo RUTA_URL?>/curso/festivos">
                                <i class="fas fa-flag"></i> Festivos
                            </a>
                        <?php else: ?>
                            <span style="opacity: 0.5; cursor: not-allowed; color:#0583c3;">
                                <i class="fas fa-flag"></i> Festivos
                            </span>
                        <?php endif; ?>
                    </td>

                    <!-- Umbrales -->
                    <td>
                        <?php if ($lectivos->cerrado == 0): ?>
                            <a style="text-decoration:none" href="<?php echo RUTA_URL?>/curso/curso_indicadores">
                                <i class="fas fa-chart-bar"></i> Umbrales
                            </a>
                        <?php else: ?>
                            <span style="opacity: 0.5; cursor: not-allowed; color:#0583c3;">
                                <i class="fas fa-chart-bar"></i> Umbrales
                            </span>
                        <?php endif; ?>
                    </td>

                    <!-- Calendario -->
                    <td>
                        <?php if ($lectivos->cerrado == 0): ?>
                            <a style="text-decoration:none" href="<?php echo RUTA_URL?>/curso/calendario">
                                <i class="fas fa-calendar-alt"></i> Calendario
                            </a>
                        <?php else: ?>
                            <span style="opacity: 0.5; cursor: not-allowed; color:#0583c3;">
                                <i class="fas fa-calendar-alt"></i> Calendario
                            </span>
                        <?php endif; ?>
                    </td>



                    <!-- BORRAR CURSO -->
                    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [50])) : ?>
                    <td class="text-center">
                        <?php if ($lectivos->cerrado == 0): ?>
                            <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $lectivos->id_lectivo?>">
                                <img class="icono" src="<?php echo RUTA_Icon ?>papelera.png" alt="Borrar">
                            </a>
                        <?php else: ?>
                            <span class="text-muted" title="Disponible al cerrar el curso">
                                <img class="icono" src="<?php echo RUTA_Icon ?>papelera.png" alt="Borrar (deshabilitado)" style="opacity: 0.5; cursor: not-allowed;">
                            </span>
                        <?php endif; ?>
                        <div class="modal fade" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true" id="borrar_<?php echo $lectivos->id_lectivo?>">
                        <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-3 shadow-lg">
                            <!-- modal header -->
                            <div class="modal-header">
                                <p class="modal-title ms-3">Borrado de curso</p>
                                <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                            </div>
                            <!-- modal body -->
                            <div class="modal-body mt-3">
                                <p style="text-align:center">
                                    Vas a borrar el curso <b>"<?php echo $lectivos->lectivo?>"</b>. Si confirmas, también borrarás sus evaluaciones, festivos y todos los seguimientos que puedan estar asociados a el. Estas seguro?
                                </p>
                                <?php $codigo = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"),0,6); ?>
                                <p class="text-center mt-3">
                                    Escribe el siguiente código para confirmar:
                                    <br><b id="codigo_<?php echo $lectivos->id_lectivo?>"><?php echo $codigo ?></b>
                                </p>
                                <div class="text-center">
                                    <input type="text" class="form-control text-center" placeholder="Introduce el código" id="input_codigo_<?php echo $lectivos->id_lectivo?>" onkeyup="verificarCodigo('<?php echo $lectivos->id_lectivo?>')">
                                </div>
                            </div>    
                            <!-- modal footer -->
                            <div class="modal-footer">                                 
                                <form action="<?php echo RUTA_URL?>/curso/borrar_curso/<?php echo $lectivos->id_lectivo?>" method="post">
                                    <input type="hidden" name="codigo_real" value="<?php echo $codigo ?>">
                                    <button type="submit" class="btn btn-danger" id="btn_borrar_<?php echo $lectivos->id_lectivo?>" disabled>
                                        Borrar
                                    </button>
                                </form>   
                            </div>                                 
                        </div>
                        </div>
                        </div>
                    </td>
                    <?php endif; ?>


                    <!-- CERRAR CURSO -->
                    <td>
                        <?php if ($lectivos->cerrado == 0): ?>
                            <a style="text-decoration:none" href="#" data-bs-toggle="modal" data-bs-target="#cerrar_<?php echo $lectivos->id_lectivo?>">
                                <i class="fas fa-lock"></i> Cerrar curso
                            </a>
                        <?php else: ?>
                            <span style="opacity: 0.5; cursor: not-allowed; color:#0583c3;" title="Curso ya cerrado">
                                <i class="fas fa-lock"></i> Cerrar curso
                            </span>
                        <?php endif; ?>

                        <div class="modal fade" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true" id="cerrar_<?php echo $lectivos->id_lectivo?>">
                        <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-3 shadow-lg">
                            <!-- modal header -->
                            <div class="modal-header">
                                <p class="modal-title ms-3">Cerrar curso</p>
                                <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                            </div>
                            <!-- modal body -->
                            <div class="modal-body mt-3">
                                <p style="text-align:center">
                                    Vas a cerrara el curso <b>"<?php echo $lectivos->lectivo?>"</b>. 
                                    Eso implica que a partir de ahora, no se van a poder hacer cambios en lo referente al seguimiento. Estas seguro?
                                </p>
                                <?php $codigoCerrar = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"),0,6); ?>
                                <p class="text-center mt-3">
                                    Escribe el siguiente código para confirmar:
                                    <br><b id="codigo_cerrar_<?php echo $lectivos->id_lectivo?>"><?php echo $codigoCerrar ?></b>
                                </p>
                                <div class="text-center">
                                    <input type="text" class="form-control text-center" placeholder="Introduce el código" id="input_codigo_cerrar_<?php echo $lectivos->id_lectivo?>"
                                        onkeyup="verificarCodigoCerrar('<?php echo $lectivos->id_lectivo?>')">
                                </div>
                            </div>    
                            <!-- modal footer -->
                            <div class="modal-footer">                                 
                            <form action="<?php echo RUTA_URL?>/curso/cerrar_curso/<?php echo $lectivos->id_lectivo?>" method="post">
                                <input type="hidden" name="codigo_real" value="<?php echo $codigoCerrar ?>">
                                <button type="submit" class="btn btn-danger"id="btn_cerrar_<?php echo $lectivos->id_lectivo?>"disabled>
                                    Cerrar curso
                                </button>
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
    







<!-- NUEVO CURSO -->
<div class="modal fade" id="nuevo_curso" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-md">
<div class="modal-content rounded-3 shadow-lg">


    <!-- Modal Header -->
    <div class="modal-header">
        <p class="modal-title ms-3">Nuevo Curso</p>
        <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
    </div>

    <!-- Modal Body -->
    <div class="modal-body">
    <div class="row ms-1 me-1">
    <form action="<?php echo RUTA_URL?>/curso/nuevo_curso" method="post">

            <!-- fecha inicio -->
            <div class="row mb-4">
            <div class="col-12">
                <div class="input-group">
                    <label for="fecha_ini" class="input-group-text">Fecha Inicio <sup>*</sup></label>
                    <input type="date" class="form-control form-control-md" id="fecha_ini" name="fecha_ini" required >
                </div>
            </div>
            </div>

            <!-- fecha fin -->
            <div class="row mb-4">
            <div class="col-12">
                <div class="input-group">
                    <label for="fecha_fin" class="input-group-text">Fecha Fin <sup>*</sup></label>
                    <input type="date" class="form-control form-control-md" id="fecha_fin" name="fecha_fin" required >
                </div>
            </div>
            </div>

            <!-- numero evaluaciones -->
            <div class="row mb-4">
            <div class="col-12 ">
                <div class="input-group">
                    <label for="numero_evaluaciones" class="input-group-text">Nº Evaluaciones <sup>*</sup></label>
                    <input type="number" class="form-control form-control-md" id="numero_evaluaciones" name="numero_evaluaciones" required min="1">
                </div>
            </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
            </div>

    </form>
    </div>
    </div>

</div>
</div>
</div>





<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>




<script>

    // VERIFICACION BORRADO CURSO
    function verificarCodigo(id){
        let codigo = document.getElementById("codigo_" + id).innerText;
        let input = document.getElementById("input_codigo_" + id).value;
        let boton = document.getElementById("btn_borrar_" + id);
        if(input === codigo){
            boton.disabled = false;
        }else{
            boton.disabled = true;
        }
    }


    // VERIFICACION CERRAR CURSO
    function verificarCodigoCerrar(id){
        let codigo = document.getElementById("codigo_cerrar_" + id).innerText;
        let input = document.getElementById("input_codigo_cerrar_" + id).value;
        let boton = document.getElementById("btn_cerrar_" + id);
        if(input === codigo){
            boton.disabled = false;
        }else{
            boton.disabled = true;
        }
    }

</script>

