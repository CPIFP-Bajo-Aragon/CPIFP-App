<?php require_once RUTA_APP.'/vistas/inc/header_no_login.php'; ?>


<div class="container">
    <br>
    <br>
    <br>    

    <h1 class="h3 mb-3 fw-normal">Preguntas</h1>

    <p> se van a mostrar <?php echo $datos["numPreguntas"]?> preguntas</p>

    <?php //print_r($datos["preguntas"]);?>

    <div class="col-md-5">
            <?php foreach($datos["preguntas"] as $pregunta): ?>
                <p><?php echo $pregunta->enunciado;?></p>
            <?php endforeach ?>
            
        </div>











    <?php if (isset($datos['error']) && $datos['error'] == 'error_1' ): ?>
        <div class="alert alert-danger" role="alert">
            ERROR DE LOGIN !!!
        </div>
    <?php endif ?>
</div>


<?php require_once RUTA_APP.'/vistas/inc/footer.php' ?>
