

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">





<div class="table-responsive">
<table class="table tabla-formato m-5">

        <thead>
            <tr>
                <th colspan="5" style="vertical-align: middle;">
                    <div style="color:white; display: flex; justify-content: center;  height: 100%;">
                        <h5 style="margin-right: 20px;"><b>Departamento de <?php echo $datos['ciclos'][0]->departamento;?></b></h5>
                        <a class="nav-link" href="<?php echo RUTA_URL.'/programaciones'?>">
                            <i class="fas fa-arrow-circle-left"></i> Volver
                        </a>
                    </div>
                </th>
            </tr>
            <tr>
                <th>Ciclos formativos</th>
                <th>Codigo</th>
                <th class="text-center">Modulos <i class="fas fa-book-open ms-2"></i></th>
                <th>Nuevas</th>
                <th>Verificadas</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($datos['ciclos'] as $ciclos) : ?>
                <tr>
                    <!-- ciclo -->
                    <td><?php echo $ciclos->ciclo; ?></td>
                    <!-- ciclo corto -->
                    <td><?php echo $ciclos->ciclo_corto; ?></td>
                    <!-- modulos -->
                    <td class="text-center">
                        <a href="<?php echo RUTA_URL ?>/programaciones/ciclo/<?php echo $ciclos->id_ciclo ?>" style="text-decoration: none">
                            <i class="fas fa-book-open" style="color:#2980b9"></i> <span style="color:#2980b9"> Modulos</span>
                        </a>
                    </td>
                    <!-- nuevas -->
                    <td class="text-center" style="color:orangered">
                        <?php 
                            $suma_nuevas = 0; // Inicializamos la variable para la suma
                            // Buscar la suma de programaciones nuevas para este ciclo
                            foreach ($datos['nuevas'] as $nuevas) {
                                if ($ciclos->id_ciclo == $nuevas->id_ciclo && $nuevas->suma > 0) {
                                    $suma_nuevas = $nuevas->suma;
                                    break;  // Salir del bucle una vez que encontramos la suma
                                }
                            }
                            echo $suma_nuevas > 0 ? $suma_nuevas : 0; // Mostrar la suma si es mayor que 0, o 0 si no se encontró
                        ?>
                    </td>

                    <!-- verificadas profesor -->
                    <td class="text-center" style="color:orangered">
                        <?php 
                            $suma_verificadas  = 0; // Inicializamos la variable para la suma
                            // Buscar la suma de programaciones nuevas para este ciclo
                            foreach ($datos['verificadas'] as $verificadas) {
                                if ($ciclos->id_ciclo == $verificadas->id_ciclo && $verificadas->suma > 0) {
                                    $suma_verificadas = $verificadas->suma;
                                    break;  // Salir del bucle una vez que encontramos la suma
                                }
                            }
                            echo $suma_verificadas > 0 ? $suma_verificadas  : 0; // Mostrar la suma si es mayor que 0, o 0 si no se encontró
                        ?>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>


</table>
</div>





<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>



