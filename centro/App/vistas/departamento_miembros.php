

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<div class="p-5 shadow border mt-5 tarjeta">
<div class="container">
<div class="row">


        <!-- encabezado tarjeta -->
        <div class="container">
            <div class="row">
                <div class="col-8">
                    <strong id="ciclo_encabezado">Departamento: <?php echo $datos['info_departamento'][0]->departamento;?></strong>
                </div>
                <div class="col-4 text-end">
                    <a href="<?php echo RUTA_URL . '/departamento'?>" class="btn btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>

 
        <!-- tabla miembros departamento -->
        <div class="table-responsive">
        <table class="table table-bordered tabla-formato">

            <thead>
                <tr>
                    <!-- miembros -->
                    <th class="text-center">Miembros <i class="fas fa-users ms-2"></i></th>
                    <!-- Rol -->
                    <th class="text-center">Rol <i class="fas fa-user ms-2"></i></th>
                    <!-- email -->
                    <th class="text-center">Email <i class="fas fa-envelope ms-2"></i></th>
                    <!-- activo -->
                    <th class="text-center">Activo <i class="fas fa-check ms-2"></i></th>
                    <!-- opciones -->
                    <th class="text-center">Opciones<i class="fas fa-cogs ms-2 circulo_mas"></i></th>
                </tr>
            </thead>


            <tbody>
            <?php foreach($datos['profesores_agrupados'] as $profes):?>    
                <tr>
                    <!-- nombre completo -->
                    <td><?php echo $profes['nombre_completo']?></td>
                    <!-- Rol -->
                    <td><?php echo implode(', ', $profes['roles']) ?></td>
                    <!-- email -->
                    <td><?php echo $profes['email']?></td>
                    <!-- activo -->
                    <td class="text-center">
                        <?php if ($profes['activo'] == 1) {
                            echo 'Si';
                        }else{
                            echo 'No';
                        }?>
                    </td>
                    <!-- opciones -->
                    <td class="text-center">                        
                        <a href="<?php echo RUTA_URL;?>/personal/personal_gestion/<?php echo $profes['id_profesor']?>" class="text-decoration-none">
                            <img class="icono" id="icono_ver" src="<?php echo RUTA_Icon?>ver.png" alt="Ver">
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>

        </table>
        </div>

</div>
</div>
</div>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>



