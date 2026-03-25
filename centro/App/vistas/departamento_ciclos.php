

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<div class="p-5 shadow border mt-5 tarjeta">
<div class="container">
<div class="row">


        <!-- encabezado tarjeta -->
        <div class="container">
            <div class="row">
                <div class="col-8">
                    <strong id="ciclo_encabezado">Departamento: <?php echo $datos['departamento'][0]->departamento;?></strong>
                </div>
                <div class="col-4 text-end">
                    <a href="<?php echo RUTA_URL.'/departamento'?>" class="btn btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>

        <!-- tabla ciclos departamento -->
        <div class="table-responsive">
        <table class="table table-bordered tabla-formato">
            <thead>
                <tr>
                    <!-- ciclos -->
                    <th>Ciclos <i class="fas fa-book ms-2"></i></th>
                    <!-- grado -->
                    <th class="text-center">Grado <i class="fas fa-graduation-cap ms-2"></i></th>
                    <!-- turno -->
                    <th class="text-center">Turno <i class="fas fa-sun ms-2"></i></th>  
                    <!-- opciones -->
                    <th class="text-center">Opciones<i class="fas fa-cogs ms-2 circulo_mas"></i></th> 
                </tr>
            </thead>
            <tbody>
            <?php foreach($datos['ciclos_dep'] as $ciclo): ?> 
                <tr>
                    <!-- ciclos -->
                    <td><?php echo $ciclo->ciclo.' ('.$ciclo->ciclo_corto.')'?></td>
                    <!-- grado -->
                    <td class="text-center"><?php echo $ciclo->nombre?></td>
                    <!-- turno -->
                    <td class="text-center"><?php echo $ciclo->turno?></td>
                    <!-- VER CICLO -->
                    <td class="text-center">
                        <a href="<?php echo RUTA_URL;?>/ciclo/ciclo_gestion/<?php echo $ciclo->id_ciclo?>" class="text-decoration-none">
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



