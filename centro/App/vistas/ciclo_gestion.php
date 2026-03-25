
<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<div class="p-5 shadow border mt-5 tarjeta">
<div class="container">
<div class="row">


        <div class="container">
            <div class="row">
                <div class="col-8">
                    <strong id="ciclo_encabezado">CICLO : <?php echo $datos['un_ciclo'][0]->ciclo.' ('.$datos['un_ciclo'][0]->ciclo_corto.')';?></strong>
                </div>
                <div class="col-4 text-end">
                    <a href="<?php echo RUTA_URL.'/ciclo'?>" class="btn btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>


        <?php $ciclo = $datos['un_ciclo'][0]; ?>
        <div class="mt-4">
        <div class="p-4 shadow-sm border rounded">
        <form action="<?php echo RUTA_URL?>/ciclo/editar_ciclo/<?php echo $ciclo->id_ciclo?>" method="post">

                <div class="row">
                    <!-- nombre ciclo -->
                    <div class="mb-4 col-md-8">
                        <div class="input-group">
                            <label for="ciclo" class="input-group-text">Nombre del ciclo<sup>*</sup></label>
                            <input type="text" class="form-control" id="ciclo" name="ciclo" value="<?php echo $ciclo->ciclo ?>" required>
                        </div>
                    </div>
                    <!-- codigo -->
                    <div class="mb-4 col-md-4">
                        <div class="input-group">
                            <label for="ciclo_corto" class="input-group-text">Código corto<sup>*</sup></label>
                            <input type="text" class="form-control" id="ciclo_corto" name="ciclo_corto" value="<?php echo $ciclo->ciclo_corto ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- departamento -->
                    <div class="mb-4 col-md-5">
                        <div class="input-group">
                            <label for="id_departamento" class="input-group-text">Departamento<sup>*</sup></label>
                            <select name="id_departamento" id="id_departamento" class="form-select" required>
                                <?php foreach($datos['departamentos'] as $depart): ?>
                                    <option value="<?php echo $depart->id_departamento?>" 
                                        <?php if($depart->id_departamento == $ciclo->id_departamento) echo "selected";?>> 
                                        <?php echo $depart->departamento?>
                                    </option>
                                <?php endforeach ?>   
                            </select>
                        </div>
                    </div>
                    <!-- tipo grado -->
                    <div class="mb-4 col-md-4">
                        <div class="input-group">
                            <label for="id_grado" class="input-group-text">Tipo de grado<sup>*</sup></label>
                            <select name="id_grado" id="id_grado" class="form-select" required>
                                <?php foreach($datos['grados'] as $grad): ?>
                                    <option value="<?php echo $grad->id_grado?>" 
                                        <?php if($grad->id_grado == $ciclo->id_grado) echo "selected";?>> 
                                        <?php echo $grad->nombre?>
                                    </option>
                                <?php endforeach ?>   
                            </select>
                        </div>
                    </div>
                    <!-- turno -->
                    <div class="mb-4 col-md-3">
                        <div class="input-group">
                            <label for="id_turno" class="input-group-text">Turno<sup>*</sup></label>
                            <select name="id_turno" id="id_turno" class="form-select" required>
                                <?php foreach($datos['turnos'] as $turno): ?>
                                    <option value="<?php echo $turno->id_turno?>" 
                                        <?php if($turno->id_turno == $ciclo->id_turno) echo "selected";?>> 
                                        <?php echo $turno->turno?>
                                    </option>
                                <?php endforeach ?>   
                            </select>
                        </div>
                    </div>
                </div>

                <!-- boton envio -->
                <div class="text-left">
                    <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Editar">
                </div>
            
        </form>
        </div>
        </div>



        <!-- nombre tabla cursos y modulos -->
        <div class="table-responsive mt-2">
        <table class="table table-bordered tabla-formato">

            <thead>
                <tr>
                    <!-- cursos -->
                    <th id="tabla-titulo-container">
                        <span id="tabla-titulo">Cursos</span>
                        <i data-bs-toggle="modal" data-bs-target="#nuevo_curso" class="fas fa-plus-circle ms-2 circulo_mas"></i>
                    </th> 
                    <!-- codigo -->
                    <th class="text-center">Codigo</th>
                    <!-- modulos -->
                    <th class="text-center">Modulos<i class="fas fa-book-open ms-2"></i></th>   
                    <!-- opciones -->
                    <th class="text-center">Opciones<i class="fas fa-cogs ms-2 circulo_mas"></i></th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($datos['cursos_ciclo'] as $cursos): ?>
                <tr>

                    <!-- nombre curso -->
                    <td><?php echo $cursos->nombre_curso;?></td>
                    <!-- codigo -->
                    <td class="text-center"><?php echo $cursos->curso;?></td>


                    <!-- VER MODULOS -->
                    <td class="text-center">
                        <a href="<?php echo RUTA_URL?>/modulo/<?php echo $ciclo->id_ciclo.'-'.$cursos->id_curso;?>"> 
                            <i class="fas fa-book-open" id="modulos_encabezado"></i> <span>Modulos</span>
                        </a>
                    </td>
    

                    <!-- EDITAR CURSO -->
                    <td class="text-center">
                        <a data-bs-toggle="modal" data-bs-target="#editar_<?php echo $cursos->id_curso?>">
                            <img class="icono" src="<?php echo RUTA_Icon?>editar.png"></img>
                        </a>
                        <div class="modal fade" id="editar_<?php echo $cursos->id_curso?>">
                        <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content rounded-3 shadow-lg">
                            <!-- modal header -->
                            <div class="modal-header">
                                <p class="modal-title ms-3">Edicion de cursos</p> 
                                <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                            </div>
                            <!-- modal body -->
                            <div class="modal-body">                         
                                <div class="row ms-1 me-1">                                                                                                                                                              
                                    <form action="<?php echo RUTA_URL?>/ciclo/editar_curso_ciclo/<?php echo $cursos->id_curso?>" method="post">
                                        <div class="row mt-2 mb-4">
                                            <div class="input-group">
                                                <label for="modulo" class="input-group-text">Codigo del curso<sup>*</sup></label>
                                                <input type="text" class="form-control" id="curso" name="curso" value="<?php echo $cursos->curso?>" required >
                                            </div>
                                        </div>
                                        <input type="hidden" name="id_ciclo" value="<?php echo $cursos->id_ciclo?>">
                                        <div class="modal-footer mt-4">
                                            <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Editar">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>


                        <!-- BORRAR CURSO-->
                        <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $cursos->id_curso?>">
                            <img class="icono" src="<?php echo RUTA_Icon?>papelera.png"></img>
                        </a>
                        <div class="modal fade" id="borrar_<?php echo $cursos->id_curso?>">
                        <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <!-- modal header -->
                            <div class="modal-header">
                                <p class="modal-title ms-3">Borrado de curso</p> 
                                <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                            </div>
                            <!-- modal body -->
                            <div class="modal-body mt-3"> 
                                <p>Vas a borrar el <b><?php echo $cursos->nombre_curso?></b> curso del ciclo <b><?php echo $cursos->ciclo?></b> y todos sus modulos, estas seguro ?</p>
                            </div>
                            <!-- boton modal -->
                            <div class="modal-footer">
                                <form action="<?php echo RUTA_URL?>/ciclo/borrar_curso_ciclo/<?php echo $cursos->id_curso?>" method="post">
                                    <input type="hidden" name="id_ciclo" value="<?php echo $cursos->id_ciclo?>">
                                    <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Borrar">
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

</div>
</div>
</div>




<!-- modal nuevo curso -->
<div class="modal fade" id="nuevo_curso">
<div class="modal-dialog modal-dialog-centered modal-md">
<div class="modal-content">

    <!-- modal header -->
    <div class="modal-header">
        <p class="modal-title ms-3">Nuevo curso</p> 
        <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
    </div>

    <!-- modal body -->
    <div class="modal-body info">                         
    <div class="row ms-1 me-1">                                                                                                           
    <form action="<?php echo RUTA_URL?>/ciclo/nuevo_curso/<?php echo $ciclo->id_ciclo?>" method="post">
        <!-- codigo curso -->
        <div class="row mt-4">
            <div class="input-group">
                <label for="codigo_curso" class="input-group-text">Código del Curso<sup>*</sup></label>
                <input type="text" class="form-control" id="codigo_curso" name="codigo_curso" required>
            </div>
        </div>
        <!-- curso -->
        <div class="row mt-4">
            <div class="input-group">
                <label for="id_curso" class="input-group-text">Curso<sup>*</sup></label>
                <select name="id_numero" id="id_numero" class="form-select" required>
                    <?php foreach($datos['seg_numero'] as $curso): ?>
                        <option value="<?php echo $curso->id_numero?>"> 
                            <?php echo $curso->nombre_curso;?>
                        </option>
                    <?php endforeach ?>   
                </select>
            </div>
        </div>

        <!-- boton envio -->
        <div class="modal-footer mt-4">
            <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
        </div>

    </form>
    </div>
    </div>

</div>
</div>
</div>




<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>



