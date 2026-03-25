

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">





<!-- Tabla responsive -->
<div class="table-responsive">
<table class="table m-5 tabla-formato">

    <thead>
        <tr>
            <th>Id</th>
            <th>Indicadores</th>
            <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[50])):?>
                <th style="text-align:center">Opciones<i class="fas fa-cogs ms-2" style="color: white; font-size: 1.3em; vertical-align: middle"></i></th>
            <?php endif ?> 
        </tr>
    </thead>


    <tbody>
        <?php foreach($datos['indicadores'] as $indicadores): ?>
        <tr>
            <!-- id INDICADOR -->
            <td><?php echo $indicadores->id_indicador?></td>
            <!-- NOMBRE INDICADOR -->
            <td><?php echo '('.$indicadores->indicador_corto.') '.$indicadores->indicador?></td>
            <!-- VISUALIZAR PREGUNTAS -->
            <td style="text-align: center;">
                <a href="<?php echo RUTA_URL?>/indicadores/preguntas/<?php echo $indicadores->id_indicador?>"> 
                    <img class="icono" src="<?php echo RUTA_Icon?>ver.png"></img> 
                </a> 
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>

</table>
</div>
   
          


<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>



