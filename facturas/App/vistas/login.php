<?php require_once RUTA_APP.'/vistas/inc/header_no_login.php'; ?>


<div class="container">
    <br>
    <br>
    <br>    

    <h1 class="h3 mb-3 fw-normal">Log In</h1>
<?php print_r( $datos);?>
    <form method="post" class="card-body">
        <div class="form-floating mb-3">
            <input type="text" name="usuario" class="form-control" placeholder="" required>
            <label for="floatingInput">Usuario</label>
        </div>
        <div class="form-floating mb-3">
            <input type="password" name="pass" class="form-control" placeholder="" required>
            <label for="floatingInput">Contraseña</label>
        </div>
        <input type="submit" class="btn btn-success" value="Login">

    </form>

    <?php if (isset($datos['error']) && $datos['error'] == 'error_1' ): ?>
        <div class="alert alert-danger" role="alert">
            ERROR DE LOGIN !!!
        </div>
    <?php endif ?>
</div>


<?php require_once RUTA_APP.'/vistas/inc/footer.php' ?>
