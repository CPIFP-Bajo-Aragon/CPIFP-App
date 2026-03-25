


<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">




<div class="table-responsive">
<table class="table m-5 tabla-formato">

    <thead>
        <tr>
            <th>Id</th>
            <th>
                <?php echo $datos['indicador'][0]->indicador.' ('.$datos['indicador'][0]->indicador_corto.')'?>
                <a class="nav-link ms-2" style="font-size: 1.3em; display: inline-flex; align-items: center;" 
                href="<?php echo RUTA_URL?>/indicadores">
                    <i class="fas fa-arrow-circle-left" style="vertical-align: middle"></i>
                </a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($datos['preguntas'] as $pre): ?>
            <tr>
                <td><?php echo $pre->id_pregunta?></td>
                <td><?php echo $pre->pregunta?></td>
            </tr>
        <?php endforeach ?>
    </tbody>

</table>
</div>




<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>   