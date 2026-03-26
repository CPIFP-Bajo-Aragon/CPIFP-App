
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>



<div class="p-4 shadow-sm border rounded mt-5 tarjeta">

        <!-- titulo -->
        <div class="mb-4 d-flex flex-wrap">
            <h3 class="mi_perfil_estilo">Mi perfil</h3>
        </div>
        <!-- nombre -->
        <div class="row mt-2">
            <div class="input-group">
                <label for="nombre" class="input-group-text col-12 col-md-4 input_label_mi_perfil">Nombre y apellidos<sup>*</sup></label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $datos['usuarioSesion']->nombre_completo;?>" readonly>
            </div>
        </div>
        <!-- correo electronico -->
        <div class="row mt-4">
            <div class="input-group">
                <label for="email" class="input-group-text col-12 col-md-4 input_label_mi_perfil">Correo electronico<sup>*</sup></label>
                <input type="text" class="form-control" id="email" name="email" value="<?php echo $datos['usuarioSesion']->email?>" readonly>
            </div>
        </div>
        <!-- formulario cambio contraseña -->
        <form action="<?php echo RUTA_URL?>/usuario/cambiar_password/<?php echo $datos['usuarioSesion']->id_profesor?>" method="post">
            <div class="row mt-4">
                <div class="col-12 col-md-5 mb-4">
                    <div class="input-group">
                        <label for="pass_new" class="input-group-text input_label_mi_perfil">Nuevo Password<sup>*</sup></label>
                        <input type="password" class="form-control" id="pass_new" name="pass_new" required>
                        <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">👁️</span>
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Cambiar">
                </div>
            </div>
        </form>


        <table class="table table-bordered mt-5 tabla-formato">
            <thead>
                <tr>
                    <th>Departamentos<i class="fas fa-building ms-2"></i></th>
                    <th>Roles<i class="fas fa-user ms-2"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['usuarioSesion']->roles as $departamento): ?>
                    <tr>
                        <td><?php echo $departamento->departamento;?></td>
                        <td><?php echo $departamento->rol;?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


</div>



<script>
    function togglePassword() {
        const passwordInput = document.getElementById('pass_new');
        const eyeIcon = event.currentTarget;
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.textContent = '🙈';
        } else {
            passwordInput.type = 'password';
            eyeIcon.textContent = '👁️';
        }
    }
</script>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>