<?php require_once RUTA_APP.'/vistas/inc/header.php'?>

<div class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo RUTA_URL ?>">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Añadir Asesoria</li>
        </ol>
    </nav>

    <div class="row d-flex justify-content-center text-center mx-0 mt-2">
        <div class="col-12">
            <h1>Añadir Asesoria</h1>
        </div>
    </div>

    <?php if($datos["error"] == 1): ?>
        <div class="alert alert-danger" role="alert">
            Se ha de rellenar un campo obligatoriamente !!!
        </div>
    <?php endif ?>

    <form method="post" class="mb-5">
        <div class="row">
            <div class="mb-3 col-4" >
                <label for="nombre_as" class="form-label">Nombre*</label>
                <input type="text" class="form-control form-control-sm" id="nombre_as" name="nombre_as" autofocus required>
            </div>


            <div class="mb-3 col-4">
                <label for="dni_as" class="form-label">DNI</label>
                <input type="text" class="form-control form-control-sm" id="dni_as" name="dni_as">
            </div>

            <div class="mb-3 col-4">
                <label for="titulo_as" class="form-label">Título</label>
                <input type="text" class="form-control form-control-sm" id="titulo_as" name="titulo_as">
            </div>

            <div class="mb-3 col-4">
                <label for="telefono_as" class="form-label">Teléfono</label>
                <input type="text" class="form-control form-control-sm" id="telefono_as" name="telefono_as">
            </div>

            <div class="mb-3 col-4">
                <label for="email_as" class="form-label">Email*</label>
                <input type="email" class="form-control form-control-sm" id="email_as" name="email_as" required>
            </div>

            <div class="mb-3 col-4">
                <label for="domicilio_as" class="form-label">Domicilio</label>
                <input type="text" class="form-control form-control-sm" id="domicilio_as" name="domicilio_as">
            </div>

            <div class="mb-3 col-12">
                <label for="descripcion_as" class="form-label">Descripción</label>
                <textarea class="form-control form-control-sm" id="descripcion_as" name="descripcion_as"></textarea>
            </div>

            <div class="col-10">
                <button type="submit" class="w-100 btn btn-success btn-lg">Guardar</button>
            </div>

            <div class="col-2">
                <a class="w-100 btn btn-danger btn-lg" href="<?php echo RUTA_URL ?>/">Cancelar</a>
            </div>
        </div>
        
        
        
    </form>

    
</div>
<?php require_once RUTA_APP.'/vistas/inc/footer.php' ?>

