



<div class="table-responsive">
<table class="table tabla-formato">

    <thead>
        <tr>
            <th colspan="<?php echo count($datos['temas']) +3 ; ?>" style="text-align: center; font-size: 17px;">
                HORAS IMPARTIDAS ACUMULADAS - INDICADOR EP1
            </th>
        </tr>
        <tr>
            <th class="text-center" style="white-space: normal; word-wrap: break-word; max-width: 150px; text-align: center; font-size: 13px;">Fecha</th>
            <?php foreach($datos['temas'] as $temas):
            if($temas->descripcion != "Dual" && $temas->descripcion != "Examenes" && $temas->descripcion != "Otros" && $temas->descripcion != "Faltas" && $temas->descripcion != "Actividades"):?>
            <th class="text-center" style="white-space: normal; word-wrap: break-word; max-width: 150px; text-align: center; font-size: 13px;">
            <?php echo "Tem." . $temas->tema?>
            </th>
            <?php endif; endforeach;?>
            <th class="text-center" style="white-space: normal; word-wrap: break-word; max-width: 150px; text-align: center; font-size: 13px;">Dual</th>
            <th class="text-center" style="white-space: normal; word-wrap: break-word; max-width: 150px; text-align: center; font-size: 13px;">Exams.</th>
            <th class="text-center" style="white-space: normal; word-wrap: break-word; max-width: 150px; text-align: center; font-size: 13px;">Cont. Impartidos</th>
            <th class="text-center" style="white-space: normal; word-wrap: break-word; max-width: 150px; text-align: center; font-size: 13px;">Hrs. Previstas Acum.</th>
            <th class="text-center" style="white-space: normal; word-wrap: break-word; max-width: 150px; text-align: center; font-size: 13px;">EP1</th>
        </tr>
    </thead>


    <tbody>

        <!-- FILA DE PROGRAMADAS -->
        <tr>
            <td class="text-center" style="color: #0583c3; font-weight:bold">PROGRAMADAS</td>
            <?php 
                $total_programadas = 0;
                foreach($datos['temas'] as $temas):
                if($temas->descripcion != "Otros" && $temas->descripcion != "Faltas" && $temas->descripcion != "Actividades"): ?>
                    <td class="text-center" style="color: #0583c3; font-weight:bold">
                    <?php 
                    echo $temas->total_horas;
                    $total_programadas += $temas->total_horas;
                    ?>
                    </td>
            <?php endif; endforeach;?>
            <td class="text-center"><?php echo $total_programadas;?></td>
        </tr>


        <?php for ($i = 0; $i < sizeof($datos['meses']); $i++): ?>
        
        <tr>

            <!-- NOMBRE MESES -->
            <td id="<?php echo $datos['meses'][$i]->numero?>" class="text-center"><?php echo $datos['meses'][$i]->mes?></td>


            <!-- ACUMULADAS -->
            <?php foreach($datos['temas'] as $temas):
                if ($temas->descripcion != "Otros" && $temas->descripcion != "Faltas" && $temas->descripcion != "Actividades"):?>
                <td class="text-center" >
                <?php 
                    foreach ($datos['acumuladas'] as $acumuladas):
                    if ($temas->id_tema == $acumuladas['id_tema'] && $datos['meses'][$i]->numero == $acumuladas['numero_mes']):
                       echo $acumuladas['horas_acumuladas'];
                    endif;
                    endforeach;
                ?>
                </td>
            <?php endif; endforeach; ?>


            <!-- TOTAL MES CONTENIDO IMPARTIDO IMPARTIDO -->
            <td class="text-center" id="total_<?php echo $datos['meses'][$i]->numero?>" style="font-weight: bold;">
                <?php foreach($datos['total_mes'] as $mes):
                    if ($datos['meses'][$i]->numero == $mes['numero_mes']):
                        echo $mes['total_mes'];
                    endif;
                endforeach;
                ?>
            </td> 


            <!-- TOTAL MES PREVISTAS ACUMULADAS -->
            <td class="text-center" id="previstas_<?php echo $datos['meses'][$i]->numero?>">
                <?php foreach($datos['acumuladas_mes'] as $acumuladas_mes): 
                    if($datos['meses'][$i]->numero == $acumuladas_mes->mes): ?>
                    <span><?php echo $acumuladas_mes->previstas_acumuladas?></span>
                <?php endif; endforeach; ?>
            </td> 
          

            <!-- VALORES EP1 -->
            <td class="text-center" id="total_<?php echo $datos['meses'][$i]->numero?>" style="font-weight: bold;">
                <?php foreach($datos['valores_ep1'] as $ep1):
                    if ($datos['meses'][$i]->pregunta == $ep1->id_pregunta):
                            echo $ep1->respuesta.' %';
                    endif;
                endforeach; ?>
            </td> 


        </tr>

        <?php endfor;?>

    </tbody>

</table>
</div>










