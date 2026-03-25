
<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<div class="table-responsive">
<table class="table tabla-formato m-5">

        <thead>
            <tr>
                <th>Departamentos de formacion</th>
                <th>Codigo</th>
                <th class="text-center">Ciclos / Modulos <i class="fas fa-book ms-2"></i></th>
                <th>Nuevas</th>
                <th>Verificadas</th>
            </tr>

        </thead>

        <tbody>
            <?php 

                 $total_nuevas = 0;
                 $total_verificadas = 0;
            
                    foreach ($datos['departamentos'] as $dep) :
                            if($dep->sin_ciclo == 0): ?>
                <tr>
                    <!-- departamento -->
                    <td><?php echo $dep->departamento; ?></td>

                    <!-- codigo departamento -->
                    <td><?php echo $dep->departamento_corto; ?></td>

                    <!-- ciclos / modulos -->
                    <td class="text-center">
                        <a href="<?php echo RUTA_URL?>/programaciones/departamento/<?php echo $dep->id_departamento?>" style="text-decoration:none">
                            <i class="fas fa-book" style="color:#2980b9"></i> <span style="color:#2980b9"> Ciclos</span>
                        </a>
                    </td>

                    <!-- nuevas -->
                    <td class="text-center" style="color:orangered">
                        <?php 
                            $suma_nuevas = 0;
                            if(!empty($datos['nuevas'][0]->suma)):
                            foreach ($datos['nuevas'] as $nuevas) {
                                if ($dep->id_departamento == $nuevas->id_departamento && $nuevas->suma > 0) {
                                    $suma_nuevas = $nuevas->suma;
                                    break;  
                                }
                            } endif;
                            echo $suma_nuevas > 0 ? $suma_nuevas : 0;  
                            $total_nuevas += $suma_nuevas;
                        ?>
                    </td>

                    <!-- verificadas profesor -->
                    <td class="text-center" style="color:orangered">
                        <?php 
                            $suma_verificadas = 0; 
                             if(!empty($datos['verificadas'][0]->suma)):
                            foreach ($datos['verificadas'] as $verificadas) {
                                if ($dep->id_departamento == $verificadas->id_departamento && $verificadas->suma > 0) {
                                    $suma_verificadas  = $verificadas->suma;
                                    break;  
                                }
                            } endif;
                            echo $suma_verificadas > 0 ? $suma_verificadas  : 0; 
                            $total_verificadas += $suma_verificadas;
                        ?>
                    </td>

                </tr>
            <?php endif; endforeach; ?>
        </tbody>

         <tfoot>
            <tr>
                <td colspan="3" style="font-weight: bold; color:#2980b9; background-color: #d6eaf8;">Totales</td>
                <td class="text-center" style="font-weight: bold; background-color: #d6eaf8; color:orangered;"><?php echo $total_nuevas; ?></td>
                <td class="text-center" style="font-weight: bold; background-color: #d6eaf8; color:orangered;"><?php echo $total_verificadas; ?></td>
            </tr>
        </tfoot>

</table>
</div>

    <?php require_once RUTA_APP . '/vistas/inc/footer.php'?>

