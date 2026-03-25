
<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<div class="p-5 shadow border mt-5 tarjeta">
<div class="container">
<div class="row">


        <!-- encabezado tarjeta -->
        <div class="row">
            <div class="col-12">
                <strong id="ciclo_encabezado">Departamentos del centro</strong>
            </div>
        </div>
        <div class="row mt-3 mb-3">
            <div class="col-12 col-md-5 d-flex flex-column flex-md-row">
                <button id="todos" class="btn btn-md btn-custom mb-2 mb-md-0 active me-2" onclick="filtrarTablaDep(4)">Ver todos</button>
                <button id="estrategicos" class="btn btn-md btn-custom mb-2 mb-md-0 me-2" onclick="filtrarTablaDep(0)">Estrategicos</button>
                <button id="formacion" class="btn btn-md btn-custom mb-2 mb-md-0 me-2" onclick="filtrarTablaDep(1)">De formación</button>
            </div>
        </div>



        <!-- tabla departamentos -->
        <div class="table-responsive">
        <table class="table table-bordered tabla-formato">

            <thead>
                <tr>
                    <!-- nuevo departamento -->
                    <th>
                        <span>Nuevo departamento</span>
                        <i data-bs-toggle="modal" data-bs-target="#nueva_accion" class="fas fa-plus-circle ms-2 circulo_mas"></i>
                    </th>
                    <!-- codigo departamento -->
                    <th class="text-center">Codigo</th>
                    <!-- miembros -->
                    <th class="text-center">Miembros <i class="fas fa-users ms-2"></i></th>
                    <!-- ciclos formativos -->
                    <th class="text-center">Ciclos Formativos <i class="fas fa-book ms-2"></i></th>
                    <!-- opciones -->
                    <th class="text-center">Opciones <i class="fas fa-cogs ms-2 circulo_mas"></i></th>
                </tr>
            </thead>


            <tbody>
            <?php foreach ($datos['ges_departamentos'] as $dep) : ?>
            <tr class="filtrar accion_<?php echo $dep->isFormacion;?>">

                    <!-- nombre departamento -->
                    <td><?php echo $dep->departamento?></td>
                    <!-- codigo departamento -->
                    <td class="text-center"><?php echo $dep->departamento_corto;?></td>
                    <!-- enlace miembros departamento -->
                    <td class="text-center">
                        <a href="<?php echo RUTA_URL?>/departamento/departamento_miembros/<?php echo $dep->id_departamento?>"> 
                            <i class="fas fa-users"></i><span> Miembros</span>
                        </a>
                    </td>
                    <!-- enlace ciclos departamento -->
                    <td class="text-center">
                        <?php if ($dep->isFormacion == 1 && $dep->sin_ciclo == 0): ?>
                            <a href="<?php echo RUTA_URL?>/departamento/departamento_ciclos/<?php echo $dep->id_departamento?>">
                                <i class="fas fa-book"></i> <span> Ciclos</span>
                            </a>
                        <?php else: ?>
                            ---
                        <?php endif; ?>
                    </td>
                    <!-- enlaces opciones -->
                    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [50])) : ?>
                    <td class="text-center">


                        <!-- EDITAR DEPARTAMENTO-->
                        <a data-bs-toggle="modal" data-bs-target="#editar_<?php echo $dep->id_departamento?>">
                            <img class="icono" src="<?php echo RUTA_Icon?>editar.png" alt="Editar">
                        </a>
                        <div class="modal fade" id="editar_<?php echo $dep->id_departamento?>">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-3 shadow-lg">

                            <!-- modal header -->
                            <div class="modal-header">
                                <p class="modal-title ms-3">Edición departamento</p> 
                                <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- modal body -->
                            <div class="modal-body">
                            <div class="row ms-1 me-1">
                            <form action="<?php echo RUTA_URL ?>/departamento/editar_departamento/<?php echo $dep->id_departamento?>" method="post">
                                <!-- nombre -->
                                <div class="row mt-4">
                                    <div class="input-group">
                                        <label for="nombre" class="input-group-text">Nombre<sup>*</sup></label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $dep->departamento?>" required>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <!-- codigo -->
                                    <div class="col-6">
                                        <div class="input-group">
                                            <label for="nombre_corto" class="input-group-text">Código<sup>*</sup></label>
                                            <input type="text" class="form-control" id="nombre_corto" name="nombre_corto" value="<?php echo $dep->departamento_corto?>" required>
                                        </div>
                                    </div>
                                    <!-- tipo -->
                                    <div class="col-6">
                                        <div class="input-group">
                                            <label for="isFormacion" class="input-group-text">Tipo<sup>*</sup></label>
                                            <select name="isFormacion" id="isFormacion" required> 
                                                <option value="0" <?php if($dep->isFormacion==0){ echo "selected";};?>>Estrategico</option>
                                                <option value="1" <?php if($dep->isFormacion==1){ echo "selected";};?>>Formación</option>
                                            </select>
                                        </div>
                                    </div>  
                                </div>   
                                <!-- sin ciclo -->
                                <div class="row mt-4">
                                    <div class="col-7">
                                        <div class="input-group">
                                            <label for="sin_ciclo" class="input-group-text">¿ Va a tener ciclos asociados ?<sup>*</sup></label>
                                            <select name="sin_ciclo" id="sin_ciclo" class="form-control" required>
                                                <option value="1" <?php if($dep->sin_ciclo==1){ echo "selected";};?>>No</option>
                                                <option value="0" <?php if($dep->sin_ciclo==0){ echo "selected";};?>>Si</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- boton -->
                                <div class="modal-footer mt-4">
                                    <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
                                </div>
                            </form>
                            </div>
                            </div>

                        </div>
                        </div>
                        </div>


                        <!-- BORRAR DEPARTAMENTO -->
                        <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $dep->id_departamento?>">
                            <img class="icono" src="<?php echo RUTA_Icon?>papelera.png" alt="Borrar">
                        </a>
                        <div class="modal fade" id="borrar_<?php echo $dep->id_departamento?>">
                        <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-3 shadow-lg">
                                <!-- modal header -->
                                <div class="modal-header">
                                    <p class="modal-title ms-3">Borrado departamento</p> 
                                    <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                                </div>
                                <!-- modal body -->
                                <div class="modal-body mt-3">
                                    <?php if(boolval($dep->isFormacion)){ ?>
                                        <p>Si borras el departamento de <b>"<?php echo $dep->departamento?>"</b>, borrarás todos los ciclos y profesores asociados a él. ¿Estás seguro?</p>
                                    <?php } else { ?>
                                        <p>Si borras el departamento de <b>"<?php echo $dep->departamento?>"</b>, borrarás los miembros asociados a él. ¿Estás seguro?</p>
                                    <?php } ?>
                                </div>
                                <!-- boton -->
                                <div class="modal-footer">
                                    <form action="<?php echo RUTA_URL?>/departamento/borrar_departamento/<?php echo $dep->id_departamento?>" method="post">
                                        <input type="submit" class="btn" name="borrar" id="boton-modal" value="Borrar">
                                    </form>
                                </div>
                        </div>
                        </div>
                        </div>

                    </td>
                    <?php endif; ?>

            </tr>
            <?php endforeach; ?>
            </tbody>
        
        </table>
        </div>

   
</div>
</div>
</div>





<!-- NUEVO DEPARTAMENTO -->
<div class="modal fade" id="nueva_accion" tabindex="-1" aria-labelledby="nuevaAccionLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg">
<div class="modal-content rounded-3 shadow-lg">

        <!-- modal header -->
        <div class="modal-header">
            <h5 class="modal-title ms-3" id="nuevaAccionLabel">Nuevo Departamento</h5>
            <button type="button" class="btn-close me-4" data-bs-dismiss="modal" aria-label="Cerrar Modal"></button>
        </div>

        <!-- modal body -->
        <div class="modal-body">
        <div class="row ms-1 me-1">
        <form action="<?php echo RUTA_URL?>/departamento/nuevo_departamento" method="post">

            <!-- nombre -->
            <div class="row mt-4">
                <div class="input-group">
                    <label for="nombre" class="input-group-text">Nombre<sup>*</sup></label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
            </div>

            <div class="row mt-4">
                <!-- codigo -->
                <div class="col-6">
                    <div class="input-group">
                        <label for="nombre_corto" class="input-group-text">Código<sup>*</sup></label>
                        <input type="text" class="form-control" id="nombre_corto" name="nombre_corto" required>
                    </div>
                </div>
                <!-- tipo -->
                <div class="col-6">
                    <div class="input-group">
                        <label for="isFormacion" class="input-group-text">Tipo<sup>*</sup></label>
                        <select name="isFormacion" id="isFormacion" class="form-control" required>
                            <option value="0">Estrategico</option>
                            <option value="1">Formación</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- sin ciclo -->
            <div class="row mt-4">
                <div class="col-7">
                    <div class="input-group">
                        <label for="sin_ciclo" class="input-group-text">¿ Va a tener ciclos asociados ?<sup>*</sup></label>
                        <select name="sin_ciclo" id="sin_ciclo" class="form-control" required>
                            <option value="1">No</option>
                            <option value="0">Si</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- boton -->
            <div class="modal-footer mt-4">
                <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
            </div>


        </form>
        </div>
        </div>
            
</div>
</div>
</div>



<script src="<?php echo RUTA_URL;?>/public/js/centro.js"></script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>





