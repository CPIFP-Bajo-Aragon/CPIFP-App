




<div class="table-responsive">
<table class="table tabla-formato mb-5">

        <thead>
            <tr>
                <th colspan="<?php echo count($datos['temas']) + 3; ?>" style="text-align: center; font-size: 17px;">
                    HORAS IMPARTIDAS POR EVALUACIÓN
                </th>
            </tr>
            <tr>
                <th class="text-center" style="white-space: normal; max-width: 150px; font-size: 13px;">Fecha</th>
                <?php foreach ($datos['temas'] as $temas): ?>
                    <th class="text-center" style="white-space: normal; max-width: 150px; font-size: 13px;">
                        <?php
                            switch ($temas->descripcion) {
                                case "Dual":
                                    echo "Dual";
                                    break;
                                case "Examenes":
                                    echo "Exams.";
                                    break;
                                case "Faltas":
                                    echo "Faltas Prof.";
                                    break;
                                case "Otros":
                                    echo "Otros";
                                    break;
                                case "Actividades":
                                    echo "Activ.";
                                    break;
                                default:
                                    echo "Tem." . $temas->tema;
                            }
                        ?>
                    </th>
                <?php endforeach; ?>
                <th class="text-center" style="white-space: normal; max-width: 150px; font-size: 13px;">Cont. Impartidos</th>
                <th class="text-center" style="white-space: normal; max-width: 150px; font-size: 13px;">Hrs. Previstas Eval.</th>
            </tr>
        </thead>


        <tbody>
        <?php for ($i=0; $i < sizeof($datos['evaluaciones']); $i++): ?>
            <tr>
                <!-- NOMBRE EVALUACIONES -->
                <td class="text-center" style="color:#0583c3;  font-weight: bold"><?php echo $datos['evaluaciones'][$i]->evaluacion?></td>
                <!-- VALORES TEMAS -->
                <?php foreach($datos['temas'] as $temas): ?>
                    <td class="text-center">
                    <?php foreach($datos['total_x_evaluaciones'] as $total_x_evaluaciones) :
                        for($j=0;$j<sizeof($total_x_evaluaciones);$j++):
                            if($total_x_evaluaciones[$j]->id_seguimiento==$datos['evaluaciones'][$i]->id_seguimiento && $temas->id_tema==$total_x_evaluaciones[$j]->id_tema)
                            echo $total_x_evaluaciones[$j]->total_horas;
                        endfor;
                    endforeach; ?>
                    </td>
                <?php endforeach; ?>
                <!-- VALORES IMP.CONTENIDOS -->
                <td class="text-center">
                    <?php foreach($datos['total_eva_contenidos'] as $total_eva_contenidos):
                        for ($j=0; $j <sizeof($total_eva_contenidos) ; $j++) {
                        if($total_eva_contenidos[$j]->id_seguimiento==$datos['evaluaciones'][$i]->id_seguimiento){
                            echo $total_eva_contenidos[$j]->total_contenidos;
                        }
                        }
                    endforeach; ?>
                </td>
                <td class="text-center">
                <?php foreach($datos['hrs_previstas_x_evaluacion'] as $hrs_previstas):
                        if($hrs_previstas->id_seguimiento==$datos['evaluaciones'][$i]->id_seguimiento){
                            echo $hrs_previstas->respuesta;
                        }
                        endforeach; ?>
                </td>
            </tr>
        <?php endfor; ?>
        </tbody>

</table>
</div>


