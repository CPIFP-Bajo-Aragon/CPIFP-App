

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<div class="p-5 shadow border mt-5 tarjeta">
<div class="container">
<div class="row">

        <!-- encabezado tarjeta -->
        <div class="container">
            <div class="row">
                <strong id="ciclo_encabezado"><?php echo $datos['curso_ciclo'][0]->ciclo;?></strong>
            </div>
            <div class="row">
                <div class="col-8">
                    <strong id="ciclo_encabezado"><?php echo $datos['curso_ciclo'][0]->curso.' ('.$datos['curso_ciclo'][0]->nombre_curso.')'?></strong>
                </div>
                <div class="col-4 text-end">
                    <a href="<?php echo RUTA_URL.'/ciclo/ciclo_gestion/'.$datos['curso_ciclo'][0]->id_ciclo;?>" class="btn btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>


    <!-- tabla modulos -->
    <div class="table-responsive">
    <table class="table table-bordered tabla-formato">

        <thead>
            <tr>
                <!-- nuevo modulo -->
                <th><span id="tabla-titulo">Nuevo modulo</span>
                    <i data-bs-toggle="modal" data-bs-target="#nuevo_modulo" class="fas fa-plus-circle ms-2 circulo_mas"></i>
                </th>
                <!-- curso -->
                <th class="text-center">Curso<i class="fas fa-graduation-cap ms-2"></i></th>   
                <!-- horas totales  -->
                <th class="text-center">Hrs. Totales<i class="fas fa-clock ms-2"></i></th>  
                <!-- horas semanales   -->
                <th class="text-center">Hrs. Semanales<i class="fas fa-clock ms-2"></i></th> 
                <!-- opciones     -->
                <th class="text-center">Opciones<i class="fas fa-cogs ms-2 circulo_mas"></i></th>
            </tr>
        </thead>


        <tbody>
            <?php foreach($datos['modulos_un_curso'] as $modulo): ?> 
            <tr>
                <!-- nombre modulo -->
                <td><?php echo $modulo->modulo.' ('. $modulo->nombre_corto.')'?></td>
                <!-- curso -->
                <td class="text-center"><?php echo $modulo->nombre_curso;?></td>
                <!-- horas totales -->
                <td class="text-center"><?php echo $modulo->horas_totales?> hrs.</td>
                <!-- horas semanales -->
                <td class="text-center"><?php echo $modulo->horas_semanales?> hrs.</td>

                 <!-- OPCIONES -->
                <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[50])):?>            
                <td class="text-center">

                    <!-- EDITAR MODULO-->
                    <a data-bs-toggle="modal" data-bs-target="#editar_<?php echo $modulo->id_modulo?>">
                        <img class="icono" src="<?php echo RUTA_Icon?>editar.png"></img>
                    </a>
                    <div class="modal fade" id="editar_<?php echo $modulo->id_modulo?>">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content rounded-3 shadow-lg">

                        <!-- modal header -->
                        <div class="modal-header">
                            <p class="modal-title ms-3">Edicion de modulos</p> 
                            <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- modal body -->
                        <div class="modal-body info">                         
                        <div class="row ms-1 me-1">                                                                                                                                                              
                        <form action="<?php echo RUTA_URL?>/modulo/editar_modulo/<?php echo $modulo->id_modulo?>" method="post">
                            <!-- nombre -->
                            <div class="row mt-2 mb-4">
                                <div class="input-group">
                                    <label for="modulo" class="input-group-text input_label_estilo">Nombre<sup>*</sup></label>
                                    <input type="text" class="form-control form-control-md" id="modulo" name="modulo" value="<?php echo $modulo->modulo?>" required >
                                </div>
                            </div>
                            <!-- codigo -->
                            <div class="row mb-4">
                                <div class="col-9">
                                    <div class="input-group">
                                        <label for="modulo_codigo" class="input-group-text input_label_estilo">Codigo<sup>*</sup></label>
                                        <input type="text" class="form-control form-control-md" id="modulo_codigo" name="modulo_codigo" value="<?php echo $modulo->nombre_corto?>"required >
                                    </div>
                                </div>
                            </div> 
                            <!-- hrs. totales -->
                            <div class="row mb-4">
                                <div class="col-9">
                                    <div class="input-group">
                                        <label for="horas_totales" class="input-group-text input_label_estilo">Hrs. Totales<sup>*</sup></label>
                                        <input type="number" class="form-control form-control-md" id="horas_totales" name="horas_totales" value="<?php echo $modulo->horas_totales?>" required >
                                    </div>
                                </div>
                            </div> 
                            <!-- hrs. semanales -->
                            <div class="row mb-4">
                                <div class="col-9">
                                    <div class="input-group">
                                        <label for="horas_semanales" class="input-group-text input_label_estilo">Hrs. Semanales<sup>*</sup></label>
                                        <input type="number" class="form-control form-control-md" id="horas_semanales" name="horas_semanales" value="<?php echo $modulo->horas_semanales?>" required >
                                    </div>
                                </div>
                            </div> 
                            <!-- cod. programacion -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="input-group">
                                        <label for="codigo_programacion" class="input-group-text">Cod. Programacion<sup>*</sup></label>
                                        <input type="text" class="form-control form-control-md" id="codigo_programacion" name="codigo_programacion" value="<?php echo $modulo->codigo_programacion?>" required >
                                    </div>
                                </div>
                            </div> 
                            <!-- departamentos -->
                            <div class="row mb-4">
                                <div class="input-group">
                                    <label for="departamento" class="input-group-text input_label_estilo">Departamento<sup>*</sup></label>
                                    <select class="form-control form-control-md" id="departamento" name="id_departamento" required>
                                        <option value="">Selecciona un departamento</option>
                                        <?php foreach($datos['departamentos'] as $departamento): ?>
                                            <option value="<?php echo $departamento->id_departamento; ?>"
                                                <?php if ($modulo->departamento_modulo == $departamento->id_departamento) echo 'selected'; ?>>
                                                <?php echo $departamento->departamento; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" class="btn mt-3 mb-4" name="id_ciclo" id="id_ciclo" value="<?php echo $modulo->id_ciclo?>"> 
                            <input type="hidden" class="btn mt-3 mb-4" name="id_curso" id="id_curso" value="<?php echo $modulo->id_curso?>"> 

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



                    <!-- BORRAR MODULO -->
                    <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $modulo->id_modulo?>">
                        <img class="icono" src="<?php echo RUTA_Icon?>papelera.png"></img>
                    </a>
                    <div class="modal fade" id="borrar_<?php echo $modulo->id_modulo?>">
                    <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-3 shadow-lg">   
                        <!-- modal header -->
                        <div class="modal-header">
                            <p class="modal-title ms-3">Borrado de modulos</p> 
                            <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                        </div>
                        <!-- modal body -->
                        <div class="modal-body mt-2"> 
                            <p>Vas a borrar el modulo <b>"<?php echo $modulo->modulo?>"</b>, estas seguro ? </p>
                        </div>
                        <!-- formulario envio -->
                        <div class="modal-footer mt-1">
                            <form action="<?php echo RUTA_URL?>/modulo/borrar_modulo/<?php echo $modulo->id_modulo?>" method="post">
                                <input type="hidden" name="id_ciclo" value="<?php echo $modulo->id_ciclo?>">
                                <input type="hidden" name="id_curso" value="<?php echo $modulo->id_curso?>">
                                <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Borrar">
                            </form>
                        </div>
                    </div>
                    </div>
                    </div> 

            </td>
            <?php endif ?>

        </tr>
        <?php endforeach ?>
        </tbody>

    </table>
    </div>

</div>
</div>
</div>



<!--------------------------- NUEVO MODULO ------------------------>


<div class="modal fade" id="nuevo_modulo">
<div class="modal-dialog modal-dialog-centered modal-md">
<div class="modal-content rounded-3 shadow-lg">

        <!-- modal header -->
        <div class="modal-header">
            <p class="modal-title ms-3">Nuevo modulo</p> 
            <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
        </div>

         <!-- modal body -->
        <div class="modal-body info">                         
        <div class="row ms-1 me-1">                                                                                                                                      
        <form action="<?php echo RUTA_URL?>/modulo/nuevo_modulo/<?php echo $datos['curso_ciclo'][0]->id_ciclo?>" method="post">
            <!-- nombre -->
            <div class="row mt-2 mb-4">
                <div class="input-group">
                    <label for="modulo" class="input-group-text input_label_estilo">Nombre<sup>*</sup></label>
                    <input type="text" class="form-control" id="modulo" name="modulo" required >
                </div>
            </div>
            <!-- codigo -->
            <div class="row mb-4">
                <div class="col-9">
                    <div class="input-group">
                        <label for="modulo_codigo" class="input-group-text input_label_estilo">Codigo<sup>*</sup></label>
                        <input type="text" class="form-control" id="modulo_codigo" name="modulo_codigo" required >
                    </div>
                </div>
            </div> 
            <!-- horas totales -->
            <div class="row mb-4">
                <div class="col-9">
                    <div class="input-group">
                        <label for="horas_totales" class="input-group-text input_label_estilo">Hrs. Totales<sup>*</sup></label>
                        <input type="number" class="form-control form-control-md" id="horas_totales" name="horas_totales" required >
                    </div>
                </div>
            </div> 
            <!-- horas semanales -->
            <div class="row mb-4">
                <div class="col-9">
                    <div class="input-group">
                        <label for="horas_semanales" class="input-group-text input_label_estilo">Hrs. Semanales<sup>*</sup></label>
                        <input type="number" class="form-control form-control-md" id="horas_semanales" name="horas_semanales" required >
                    </div>
                </div>
            </div> 
            <!-- cod.programacion -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="input-group">
                        <label for="codigo_programacion" class="input-group-text">Cod. Programacion<sup>*</sup></label>
                        <input type="text" class="form-control form-control-md" id="codigo_programacion" name="codigo_programacion" required >
                    </div>
                </div>
            </div> 
            <!-- departamento -->
            <div class="row mb-4">
                <div class="input-group">
                    <label for="departamento" class="input-group-text input_label_estilo">Departamento<sup>*</sup></label>
                    <select class="form-control form-control-md" id="departamento" name="id_departamento" required>
                        <option value="">Selecciona un departamento</option>
                        <?php foreach($datos['departamentos'] as $departamentos): ?>
                            <option name="id_departamento" value=<?php echo $departamentos->id_departamento;?>><?php echo $departamentos->departamento;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <input type="hidden" class="btn mt-3 mb-4" name="id_curso" id="id_curso" value="<?php echo $datos['curso_ciclo'][0]->id_curso?>"> 

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


