




<div class="table-responsive">
<table class="table tabla-formato mb-5">

            <thead>
                <tr>
                    <th colspan="<?php echo count($datos['temas']) + 3; ?>" style="text-align: center; font-size: 17px;">
                        HORAS IMPARTIDAS POR MESES
                    </th>
                </tr>
                <tr>
                    <th class="text-center" style="white-space: normal; max-width: 150px; font-size: 13px;">Fecha</th>

                    <?php foreach ($datos['temas'] as $temas): ?>
                        <th class="text-center" style="white-space: normal; max-width: 150px; font-size: 13px;">
                            <?php
                                switch ($temas->descripcion) {
                                    case "Dual": echo "Dual"; break;
                                    case "Examenes": echo "Exams."; break;
                                    case "Faltas": echo "Faltas Prof."; break;
                                    case "Otros": echo "Otros"; break;
                                    case "Actividades": echo "Activ."; break;
                                    default: echo "Tem." . $temas->tema;
                                }
                            ?>
                        </th>
                    <?php endforeach; ?>
                    <th class="text-center" style="white-space: normal; max-width: 150px; font-size: 13px;">Cont. Impartidos</th>
                    <th class="text-center" style="white-space: normal; max-width: 150px; font-size: 13px;">Hrs. Previstas mes</th>
                </tr>
            </thead>



      <tbody>
        <?php for ($i=0; $i < sizeof($datos['meses']); $i++): ?>
            <tr>

              <!-- NOMBRE MESES -->
              <td class="text-center"><?php echo $datos['meses'][$i]->mes?></td>

              <!-- VALORES TEMAS -->
              <?php foreach($datos['temas'] as $temas): ?>
                <td class="text-center">
                  <?php foreach($datos['valores'] as $valores) :
                      if($datos['meses'][$i]->numero == $valores->mes  && $temas->id_tema==$valores->id_tema):
                          echo $valores->total_horas;
                        endif;
                  endforeach; ?>
                </td>
              <?php endforeach; ?>

              <!-- VALORES IMP.CONTENIDOS -->
              <td class="text-center">
                <?php foreach($datos['horas_mes_temas'] as $horas_mes_temas):
                      if($datos['meses'][$i]->numero==$horas_mes_temas->mes){
                        echo $horas_mes_temas->total_horas;
                      }
                endforeach; ?>
              </td>

              <!-- HORAS PREVISTAS MES -->
              <td class="text-center">
              <?php foreach($datos['horas_previstas_mes'] as $horas_previstas_mes):
                      if($datos['meses'][$i]->numero==$horas_previstas_mes->mes){
                        echo $horas_previstas_mes->total_horas_clase;
                      }
                endforeach; ?>
              </td>

            </tr>
          <?php endfor; ?>
        </tbody>

        <tfoot>

            <!-- FILA DEL TOTAL -->
            <tr>
                <td class="text-center" style="color: #0583c3;  font-weight: bold">TOTAL HORAS</td>
                <?php foreach ($datos['temas'] as $temas):?>
                <td class="text-center" style="color: #0583c3 ;  font-weight: bold">
                  <?php foreach ($datos['horas_temas'] as $horas):
                    if($horas->id_tema==$temas->id_tema):
                      echo $horas->total_horas;
                    endif;
                  endforeach; ?>
                </td>
                <?php endforeach ?>
                <td></td>
                <td></td>
            </tr>

            <!-- FILA DE PROGRAMADAS -->
            <tr>
                <td class="text-center" style="color: #0583c3; font-weight: bold;">PROGRAMADAS</td>
                <?php foreach($datos['temas'] as $temas): ?>
                    <td class="text-center" style="color: #0583c3;">
                        <?php echo $temas->total_horas; ?>
                    </td>
                <?php endforeach; ?>
                <td></td>
                <td></td>
            </tr>


            <!-- FILA DE DESVIACION -->
            <tr>
                <td class="text-center" style="color: #0583c3">DESVIACION</td>
                <?php foreach ($datos['temas'] as $temas):?>
                <td class="text-center" style="color: #0583c3">
                    <?php foreach ($datos['horas_temas'] as $horas):
                      if(($horas->id_tema==$temas->id_tema) && ($temas->descripcion!="Otros" && $temas->descripcion!="Examenes" && $temas->descripcion!="Faltas" && $temas->descripcion!="Actividades" )):
                        echo ($temas->total_horas - $horas->total_horas);
                      endif;
                    endforeach; ?>
                </td>
                <?php endforeach ?>
                <td></td>
                <td></td>
            </tr>

            <!-- FILA % DESVIACION -->
            <tr>
                <td class="text-center" style="color: #0583c3">% DESV.</td>
                <?php foreach ($datos['temas'] as $temas):?>
                <td class="text-center" style="color: #0583c3">
                    <?php foreach ($datos['horas_temas'] as $horas):
                      if(($horas->id_tema==$temas->id_tema) && ($temas->descripcion!="Otros" && $temas->descripcion!="Examenes" && $temas->descripcion!="Faltas" && $temas->descripcion!="Actividades" )):
                          $total = (($temas->total_horas - $horas->total_horas) * 100) / $temas->total_horas;
                          echo round($total ,2, PHP_ROUND_HALF_UP).' %';
                      endif;
                    endforeach; ?>
                </td>
                <?php endforeach ?>
                <td></td>
                <td></td>
            </tr>

      </tfoot>

</table>
</div>


