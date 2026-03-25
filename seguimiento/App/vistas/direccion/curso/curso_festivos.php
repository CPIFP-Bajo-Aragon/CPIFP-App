

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">




<table class="table table-bordered m-5 tabla-formato">
  
        <thead>
            <tr>
                <th colspan="4" >
                    Configuracion de festivos
                    <a class="nav-link ms-2" style="font-size: 1.3em; display: inline-flex; align-items: center;" href="<?php echo RUTA_URL?>/curso">
                        <i class="fas fa-arrow-circle-left" style="vertical-align: middle"></i>
                    </a>
                </th>
            </tr>
            <tr>
                <th>
                    Festivos 
                    <i data-bs-toggle="modal" data-bs-target="#festivo_nuevo" class="fas fa-plus-circle ms-2" 
                    style="color: white; font-size: 1.3em; vertical-align: middle">
                    </i>
                </th>

                <th class="text-center">Fecha incio<i class="fas fa-calendar ms-2" style="font-size: 1.3em; vertical-align: middle"></i></th>
                <th class="text-center">Fecha fin<i class="fas fa-calendar ms-2" style="font-size: 1.3em; vertical-align: middle"></i></th>
                <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[50])):?>
                    <th style="text-align: center;">Opciones<i class="fas fa-cogs ms-2" style="font-size: 1.3em; vertical-align: middle"></i></th>
                <?php endif ?> 
            </tr>
        </thead>


        <tbody>
            <?php foreach($datos['calendario_festivos'] as $calendario_festivos):?>
                <tr>
                    <td><?php echo $calendario_festivos->descripcion?></td>
                    <td class="text-center"><?php echo $calendario_festivos->fecha_inicio?></td>
                    <td class="text-center"><?php echo $calendario_festivos->fecha_fin?></td>

                    <td class="text-center">
                        <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $calendario_festivos->id_festivo_inicio.'-'.$calendario_festivos->id_festivo_fin ?>">
                            <img class="icono" src="<?php echo RUTA_Icon ?>papelera.png">
                        </a>
                        <div class="modal fade" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true" id="borrar_<?php echo $calendario_festivos->id_festivo_inicio.'-'.$calendario_festivos->id_festivo_fin?>">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-3 shadow-lg">
                                    <div class="modal-header">
                                        <p class="modal-title ms-3">Borrado de festivos</p>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body mt-3"> 
                                        <p>Vas a borrar el festivo <b><?php echo $calendario_festivos->descripcion?></b> y todas las fechas que comprende. Estas seguro?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="<?php echo RUTA_URL?>/curso/borrar_festivo/<?php echo $calendario_festivos->id_festivo_inicio.'-'.$calendario_festivos->id_festivo_fin?>" method="post">
                                            <input type="submit" class="btn" name="borrar" id="boton-modal" value="Borrar">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
            <?php endforeach;?>
        </tbody>

    </table>






<!-- MODAL FECHAS FESTIVAS -->
<div class="modal fade" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true" id="festivo_nuevo">
<div class="modal-dialog modal-dialog-centered modal-md">
<div class="modal-content rounded-3 shadow-lg">
          

        <div class="modal-header">
            <p class="modal-title ms-3">Nuevo festivo</p> 
            <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
        </div>
    
        <div class="modal-body">                         
        <div class="row ms-1 me-1"> 
        <form method="post" action="<?php echo RUTA_URL?>/curso/nuevo_festivo">

            <div class="row mt-4 mb-4">
                <div class="input-group">
                    <label for="festivo" class="input-group-text" style="width: 120px;">Nombre <sup>*</sup></label>
                    <input type="text" class="form-control form-control-md" id="festivo" name="festivo" required >
                </div>
            </div>  

            <div class="row mt-4 mb-4">
                <div class="input-group" style="width: 350px;">
                    <label for="fecha_ini" class="input-group-text" style="width: 120px;">Fecha Inicio <sup>*</sup></label>
                    <input type="date" class="form-control form-control-md" id="fecha_ini" name="fecha_ini" 
                    <?php
                        $fecha_inicio = $datos['lectivo'][0]->fecha_inicio;
                        $fecha_fin = $datos['lectivo'][0]->fecha_fin;
                        $fecha_inicio = DateTime::createFromFormat('d-m-Y', $fecha_inicio)->format('Y-m-d');
                        $fecha_fin = DateTime::createFromFormat('d-m-Y', $fecha_fin)->format('Y-m-d');
                    ?>
                    min="<?php echo $fecha_inicio?>" max="<?php echo $fecha_fin?>" required >
                </div>
            </div>

            <div class="row mt-4 mb-4">
                <div class="input-group" style="width: 350px;">
                    <label for="fecha_fin" class="input-group-text" style="width: 120px;">Fecha Fin<sup>*</sup></label>
                    <input type="date" class="form-control form-control-md" id="fecha_fin" name="fecha_fin"
                    <?php
                        $fecha_inicio = $datos['lectivo'][0]->fecha_inicio;
                        $fecha_fin = $datos['lectivo'][0]->fecha_fin;
                        $fecha_inicio = DateTime::createFromFormat('d-m-Y', $fecha_inicio)->format('Y-m-d');
                        $fecha_fin = DateTime::createFromFormat('d-m-Y', $fecha_fin)->format('Y-m-d');
                    ?>
                    min="<?php echo $fecha_inicio?>" max="<?php echo $fecha_fin?>" required >
                </div>
            </div>

            <div class="modal-footer">
                <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">      
            </div>
            
        </form>
        </div>
        </div>

</div>
</div>
</div>




<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>   

