

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<div class="p-5 shadow border mt-5 tarjeta">
<div class="container">
<div class="row">


        <!-- encabezado tarjeta -->
        <div class="container">
            <div class="row">
                <div class="col-8">
                    <strong id="ciclo_encabezado"><?php echo $datos['info_profe'][0]->nombre_completo;?></strong>
                </div>
                <div class="col-4 text-end">
                    <a href="<?php echo RUTA_URL . '/personal'?>" class="btn btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>


        <!-- datos del usuario -->
        <div class="mt-4">
        <div class="p-4 shadow-sm border rounded">
        <form action="<?php echo RUTA_URL?>/personal/editar_profesor/<?php echo $datos['info_profe'][0]->id_profesor?>" method="post">

                <!-- nombre y apellidos -->
                <div class="row mt-2">
                    <div class="input-group">
                        <label for="nombre" class="input-group-text">Nombre y apellidos<sup>*</sup></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $datos['info_profe'][0]->nombre_completo?>" required>
                    </div>
                </div>
                <!-- email -->
                <div class="row mt-4">
                    <div class="input-group">
                        <label for="email" class="input-group-text">Correo electronico<sup>*</sup></label>
                        <input type="text" class="form-control" id="email" name="email" value="<?php echo $datos['info_profe'][0]->email?>" required>
                    </div>
                </div>
                <!-- activo -->
                <div class="row mt-4">
                    <div class="col-12 col-sm-6 col-md-3 mb-4">
                        <div class="input-group">
                            <label for="activo" class="input-group-text">Activo</label>
                            <select id="activo" name="activo" class="form-control" required>
                                <option value="1" <?php echo ($datos['info_profe'][0]->activo == 1) ? 'selected' : ''; ?>>Sí</option>
                                <option value="0" <?php echo ($datos['info_profe'][0]->activo == 0) ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <!-- es admin -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="input-group">
                            <label for="admin" class="input-group-text">¿Es administrador?</label>
                            <select id="admin" name="admin" class="form-control">
                                <option value="1" <?php echo ($datos['info_profe'][0]->isAdmin == 1) ? 'selected' : ''; ?>>Sí</option>
                                <option value="0" <?php echo ($datos['info_profe'][0]->isAdmin == 0) ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- boton envio -->
                <div class="text-left mt-4 d-flex gap-2">
                    <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Actualizar">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cambiar_password">
                        <i class="fas fa-key me-1"></i> Cambiar contraseña
                    </button>
                </div>

        </form>
        </div>
        </div>


             
        <!-- tabla departamentos  -->
        <div class="table-responsive mt-2">
        <table class="table table-bordered tabla-formato">

            <thead>
                <tr>
                    <!-- asignar departamento -->
                    <th id="tabla-titulo-container">
                        <span id="tabla-titulo">Asignar a departamento</span>
                        <i data-bs-toggle="modal" data-bs-target="#nuevo_departamento" class="fas fa-plus-circle ms-2 circulo_mas"></i>
                    </th> 
                    <!-- rol -->
                    <th>Rol <i class="fa fa-user ms-2"></i></th>
                    <!-- opcines -->
                    <th class="text-center">Opciones<i class="fas fa-cogs ms-2 circulo_mas"></i></th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($datos['todos_roles'] as $departamento): ?>
            <tr>

                <!-- departamento -->
                <td><?php echo $departamento->departamento;?></td>
                <!-- rol -->
                <td><?php echo $departamento->rol;?></td>

                <!-- BORRAR DEPARTAMENTO-->
                <td class="text-center">
                    <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $departamento->id_departamento?>">
                        <img class="icono" src="<?php echo RUTA_Icon?>papelera.png"></img>
                    </a>
                    <div class="modal fade" id="borrar_<?php echo $departamento->id_departamento?>">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <!-- modal header -->
                                <div class="modal-header">
                                    <p class="modal-title ms-3">Borrado de departamento</p> 
                                    <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                                </div>
                                <!-- modal body -->
                                <div class="modal-body mt-3"> 
                                    <p>Vas a eliminar a <b><?php echo $departamento->nombre_completo?></b> del departamento de <b><?php echo $departamento->departamento?></b>, estas seguro ? </p>
                                </div>
                                <!-- boton envio -->
                                <div class="modal-footer">
                                    <form action="<?php echo RUTA_URL?>/personal/borrar_departamento/<?php echo $departamento->id_profesor?>" method="post">
                                        <input type="hidden" name="id_departamento" value="<?php echo $departamento->id_departamento?>">
                                        <input type="hidden" name="id_rol" value="<?php echo $departamento->id_rol?>">
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



<!-- modal asignar departamento -->
<div class="modal fade" id="nuevo_departamento">
<div class="modal-dialog modal-dialog-centered modal-lg">
<div class="modal-content">

        <!-- modal header -->
        <div class="modal-header">
            <p class="modal-title ms-3">Asignacion de departamento</p> 
            <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
        </div>

        <!-- modal body -->
        <div class="modal-body info">                         
        <div class="row ms-1 me-1">                                                                                                           
        <form action="<?php echo RUTA_URL?>/personal/asignar_departamento/<?php echo $datos['info_profe'][0]->id_profesor?>" method="post">
            <!-- departamentos -->
            <div class="row mt-4">
                <div class="input-group">
                    <label for="departamento" class="input-group-text">Departamento</label>
                    <select id="departamento" name="departamento" class="form-control form-control-md" required onchange="toggleRoles()">
                        <option value="">Selecciona un departamento</option>
                        <?php foreach ($datos['departamentos'] as $departamento): ?>
                            <option value="<?php echo $departamento->id_departamento; ?>">
                                <?php echo $departamento->departamento?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- roles -->
            <div class="row mt-4" id="rolesContainer" >
                <div class="input-group">
                    <label for="roles" class="input-group-text">Selecciona un rol</label>
                    <select id="roles" name="rol" class="form-control form-control-md" required>
                    <option value="">Selecciona un rol</option>
                        <?php foreach ($datos['roles'] as $rol): ?>
                            <option value="<?php echo $rol->id_rol; ?>"><?php echo $rol->rol; ?></option>
                        <?php endforeach; ?>
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





<!-- modal cambiar contraseña -->
<div class="modal fade" id="cambiar_password">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

        <!-- modal header -->
        <div class="modal-header">
            <p class="modal-title ms-3">
                <i class="fas fa-key me-2"></i>
                Cambiar contraseña de <b><?php echo $datos['info_profe'][0]->nombre_completo; ?></b>
            </p>
            <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
        </div>

        <!-- modal body -->
        <div class="modal-body info">
        <div class="row ms-1 me-1">
        <form action="<?php echo RUTA_URL ?>/personal/cambiar_password/<?php echo $datos['info_profe'][0]->id_profesor ?>" method="post">

            <!-- nueva contraseña -->
            <div class="row mt-3">
                <div class="input-group">
                    <label for="nueva_password" class="input-group-text">Nueva contraseña</label>
                    <input type="password" class="form-control" id="nueva_password" name="nueva_password"
                           minlength="6" required placeholder="Mínimo 6 caracteres">
                </div>
            </div>

            <!-- confirmar contraseña -->
            <div class="row mt-3">
                <div class="input-group">
                    <label for="confirmar_password" class="input-group-text">Confirmar contraseña</label>
                    <input type="password" class="form-control" id="confirmar_password" name="confirmar_password"
                           minlength="6" required placeholder="Repite la contraseña">
                </div>
            </div>

            <!-- aviso de error de coincidencia (JS) -->
            <div class="row mt-2">
                <div class="col">
                    <small id="error_pass" class="text-danger d-none">Las contraseñas no coinciden.</small>
                </div>
            </div>

            <!-- boton envio -->
            <div class="modal-footer mt-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <input type="submit" class="btn" id="btn_confirmar_pass" value="Confirmar cambio">
            </div>

        </form>
        </div>
        </div>

</div>
</div>
</div>

<script>
// Validación en cliente: comprobar que las dos contraseñas coinciden antes de enviar
(function () {
    const nueva     = document.getElementById('nueva_password');
    const confirmar = document.getElementById('confirmar_password');
    const error     = document.getElementById('error_pass');
    const btnConf   = document.getElementById('btn_confirmar_pass');

    function validar() {
        if (confirmar.value && nueva.value !== confirmar.value) {
            error.classList.remove('d-none');
            btnConf.disabled = true;
        } else {
            error.classList.add('d-none');
            btnConf.disabled = false;
        }
    }

    nueva.addEventListener('input', validar);
    confirmar.addEventListener('input', validar);

    // Limpiar campos al cerrar la modal para evitar que queden datos visibles
    document.getElementById('cambiar_password').addEventListener('hidden.bs.modal', function () {
        nueva.value     = '';
        confirmar.value = '';
        error.classList.add('d-none');
        btnConf.disabled = false;
    });
})();
</script>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>





