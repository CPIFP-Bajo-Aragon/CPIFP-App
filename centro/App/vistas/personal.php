
<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<div class="p-5 shadow border mt-5 tarjeta">
<div class="container">
<div class="row">

        <!-- encabezado tarjeta -->
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <strong id="ciclo_encabezado">Personal del centro</strong>
                </div>
            </div>
            <div class="row mt-3 mb-3">
                <div class="input-group col-12 col-md-5 ">
                    <label for="filtroProfesor" class="input-group-text">Filtro profesor</label>
                    <input type="text" id="filtroProfesor" class="form-control" placeholder="Escribe el nombre del profesor" onkeyup="filtrarTablaPersonal()">
                </div>
            </div>
        </div>  


        <!-- tabla ciclos -->
        <div class="table-responsive">
        <table class="table table-bordered tabla-formato">

            <thead>
                <tr>
                    <!-- nuevo miembro -->
                    <th>
                        <span id="tabla-titulo">Nuevo miembro</span>
                        <i data-bs-toggle="modal" data-bs-target="#nuevo_miembro" class="fas fa-plus-circle ms-2 circulo_mas"></i>
                    </th>
                    <!-- departamento -->
                    <th>Departamentos<i class="fas fa-building ms-2"></i></th>    
                    <!-- email -->
                    <th>Email<i class="fas fa-envelope ms-2"></i></th> 
                    <!-- activo -->
                    <th class="text-center">Activo<i class="fas fa-check-circle ms-2"></i></th>
                    <!-- opciones -->
                    <th class="text-center">Opciones<i class="fas fa-cogs ms-2 circulo_mas"></i></th>
                </tr>
            </thead>


            <tbody>
            <?php foreach($datos['profesores'] as $prof): ?>   
            <tr>
                <!-- nombre profesor -->
                <td><?php echo $prof->nombre_completo?></td>
                <!-- departamento -->
                <td>
                    <?php foreach ($datos['prof_dep'] as $pd):
                        if($pd->id_profesor==$prof->id_profesor):
                            echo $pd->departamento." ; ";
                        endif;
                        endforeach;?>
                </td>
                <!-- email -->
                <td><?php echo $prof->email?></td>
                <!-- activo -->
                <td class="text-center"><?php if($prof->activo==1){
                        echo 'Si';
                    } else{
                        echo 'No';
                    }?>
                </td>

                <!-- opciones -->
                <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[50])):?>            
                <td class="text-center">
                
                    <!-- VER PERSONAL CENTRO -->
                    <a href="<?php echo RUTA_URL;?>/personal/personal_gestion/<?php echo $prof->id_profesor?>" class="text-decoration-none">
                        <img class="icono" id="icono_ver" src="<?php echo RUTA_Icon?>ver.png" alt="Ver">
                    </a>

                    <!-- BORRAR PROFESORES -->
                    <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $prof->id_profesor?>">
                        <img class="icono" src="<?php echo RUTA_Icon?>papelera.png"></img>
                    </a>
                    <div class="modal fade" id="borrar_<?php echo $prof->id_profesor?>">
                    <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <!-- modal header -->
                        <div class="modal-header">
                            <p class="modal-title ms-3">Borrado de miembros</p> 
                            <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                        </div>
                        <!-- modal body -->
                        <div class="modal-body mt-3"> 
                            <p>Vas a borrar a<b> "<?php echo $prof->nombre_completo?> "</b>, estas seguro?</p>
                        </div>
                        <!-- boton envio -->
                        <div class="modal-footer">
                            <form action="<?php echo RUTA_URL?>/personal/borrar_profesor/<?php echo $prof->id_profesor?>" method="post">
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





<!-- NUEVO PROFESOR -->
<div class="modal fade" id="nuevo_miembro" tabindex="-1" aria-labelledby="nuevo_miembroLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg">
<div class="modal-content">

        <form action="<?php echo RUTA_URL?>/personal/nuevo_profesor" method="post">

            <!-- modal header -->
            <div class="modal-header">
                <h5 class="modal-title ms-3" id="nuevo_miembroLabel">Nuevo miembro</h5>
                <button type="button" class="btn-close me-4" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- modal body -->
            <div class="modal-body">                         
            <div class="row ms-1 me-1"> 

                    <!-- nombre y apellidos -->
                    <div class="row mt-2">
                        <div class="input-group">
                            <label for="nombre" class="input-group-text">Nombre y apellidos<sup>*</sup></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                    </div>
                    <!-- correo electronico -->
                    <div class="row mt-4">
                        <div class="input-group">
                            <label for="email" class="input-group-text">Correo electronico<sup>*</sup></label>
                            <input type="text" class="form-control" id="email" name="email" required>
                        </div>
                    </div> 
                    <!-- activo -->
                    <div class="row mt-4">
                        <div class="col-12 col-sm-4 mb-4">
                            <div class="input-group">
                                <label for="activo" class="input-group-text">Activo</label>
                                <select id="activo" name="activo" class="form-control" required>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <!-- es admin? -->
                        <div class="col-12 col-sm-5">
                            <div class="input-group">
                                <label for="admin" class="input-group-text">¿Es administrador?</label>
                                <select id="admin" name="admin" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                            </div>
                        </div>
                    </div>
            </div>
            </div>

            <!-- boton envio -->
            <div class="modal-footer mt-4">
                <input type="submit" class="btn btn-primary" name="aceptar" id="boton-modal" value="Confirmar">
            </div>

      </form>
      
</div>
</div>
</div>



<script src="<?php echo RUTA_URL;?>/public/js/centro.js"></script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>







