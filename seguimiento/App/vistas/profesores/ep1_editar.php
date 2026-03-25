


<!-- ----------------------------------------
         TABLA EDICION
----------------------------------------------->



<div class="row">
    <div class=" col-8 m-3">
        <p>Si todos los contenidos de una unidad se han impartido en menos tiempo del que se había programado, presiona el boton editar tabla y cambia los valores que aparecen en la tabla
            para el mes en el que se hayan impartido esos contenidos, indicando  las horas correspondientes al total del tema.</p>
        <p>Si al acabar un mes se han impartido ya las horas previstas del tema, pero son necesarias más para acabarlo, 
            indicar en la columna ajuste el número de horas adicionales que serán necesarias</p>
        <p><strong>NOTA IMPORTANTE: si envias los resultados de la tabla editada, los valores finales del indicador EP1 seran los que se muestran en dicha tabla sin tener en cuenta el contenido del diario por lo
            que en el caso de tener que editar la tabla, se recomienda hacerlo al final del curso. Si por el contrario decides enviar los valores del diario y ya has editado la tabla con anterioridad, pulsa el 
        boton "restaurar tabla".</strong></p>  
    </div>
</div>


<div class="row">
    <div class="col-8 m-3">
        <button type="button" class="btn" id="boton-modal" >EDITAR TABLA</button>
    </div>
</div>




<div class="table-responsive" id="tabla-acumuladas-container" style="display: none;">



    <form method="post" action="<?php echo RUTA_URL?>/PHorasImpartidas/restaurar_tabla/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
        <input type="submit" class="btn btn-custom m-3" value="Restaurar tabla">
    </form>


<form method="post" id="enviar_diario" action="<?php echo RUTA_URL?>/PHorasImpartidas/nuevo_ep1/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
<table class="table tabla-formato" id="tabla_acumuladas">

    <thead>
        <tr>
            <th colspan="<?php echo count($datos['temas']) + 2; ?>" class="text-center" style="font-size: 17px;">
                TABLA PARA EDICION DE CONTENIDOS Y REAJUSTES DE HORAS
            </th>
        </tr>
        <tr>
            <th class="text-center">Fecha</th>
            <?php foreach($datos['temas'] as $temas):
                if (!in_array($temas->descripcion, ["Dual", "Examenes", "Otros", "Faltas", "Actividades"])): ?>
                    <th class="text-center"><?php echo "Tem." . $temas->tema ?></th>
            <?php endif; endforeach; ?>
            <th class="text-center">Dual</th>
            <th class="text-center">Exams.</th>
            <th class="text-center">Ajustes</th>
            <th class="text-center">Cont. Impartidos</th>
            <th class="text-center">Hrs. Previstas Acum.</th>
            <th class="text-center">EP1</th>
        </tr>
    </thead>





    <tbody>


            <!-- FILA PROGRAMADAS -->
            <tr>
                <td class="text-center font-weight-bold text-primary">PROGRAMADAS</td>
                <?php 
                    $total_programadas = 0;
                    foreach($datos['temas'] as $temas):
                    if (!in_array($temas->descripcion, ["Otros", "Faltas", "Actividades"])): ?>
                        <td class="text-center font-weight-bold text-primary">
                            <?php 
                                echo $temas->total_horas;
                                $total_programadas += $temas->total_horas;
                            ?>
                        </td>
                <?php endif; endforeach; ?>
                <td class="text-center"></td>
            </tr>


            <!-- FILAS POR MES -->
            <?php foreach ($datos['meses'] as $mes): 
                $numero_mes = $mes->numero;
                $pregunta = $mes->pregunta;
            ?>



            <tr>

                <!-- Nombre del Mes -->
                <td class="text-center"><?php echo $mes->mes; ?></td>

                <!-- ACUMULADAS -->
                <?php foreach ($datos['temas'] as $temas):
                    if (!in_array($temas->descripcion, ["Otros", "Faltas", "Actividades"])): 
                        $valor = '';
                        foreach ($datos['edicion_tema'] as $edicion_tema):
                            if ($edicion_tema->id_pregunta == $pregunta && $temas->id_tema == $edicion_tema->id_tema):
                                $valor = $edicion_tema->horas_acumuladas;
                            endif;
                        endforeach; ?>

                    <td class="text-center">
                        <input 
                            type="number" 
                            name="acumuladas[<?php echo $numero_mes ?>][<?php echo $temas->id_tema ?>]" 
                            value="<?php echo $valor ?>" 
                            class="form-control text-center acumulada-input" 
                            style="width: 60px;" 
                            data-tema-id="<?php echo $temas->id_tema ?>" 
                            data-mes="<?php echo $numero_mes ?>">
                    </td>
                <?php endif; endforeach;?>


                <!-- AJUSTES -->
                <td class="text-center">
                    <?php 
                    $ajustes_valor = '';
                    foreach($datos['edicion_mes'] as $edicion_tema):
                        if ($edicion_tema->id_pregunta == $pregunta):
                            $ajustes_valor = $edicion_tema->ajustes;
                        endif;
                    endforeach;
                    ?>
                    <input 
                        type="number" 
                        name="ajustes[<?php echo $numero_mes ?>]" 
                        value="<?php echo $ajustes_valor ?>" 
                        class="form-control text-center input-ajuste" 
                        style="width: 60px;"
                        data-mes="<?php echo $numero_mes ?>">

                </td>


                <!-- TOTAL IMPARTIDO -->
                <td class="text-center" id="contenidos_<?php echo $numero_mes ?>">
                    <?php 
                    $valor_contenidos = 0;
                    foreach($datos['edicion_mes'] as $edicion_tema):
                        if ($edicion_tema->id_pregunta == $pregunta):
                            $valor_contenidos = $edicion_tema->contenidos_impartidos;
                        endif;
                    endforeach;
                    ?>
                    <span class="contenidos-impartidos" data-original="<?php echo $valor_contenidos ?>">
                        <?php echo $valor_contenidos ?>
                    </span>
                    <input type="hidden" 
                        name="contenidos_impartidos[<?php echo $numero_mes ?>]" 
                        id="input_contenidos_<?php echo $numero_mes ?>" 
                        value="<?php echo $valor_contenidos ?>">
                </td>



                <!-- TOTAL MES PREVISTAS ACUMULADAS -->
                <td class="text-center" id="previstas_<?php echo $numero_mes ?>">
                    <?php 
                    foreach($datos['acumuladas_mes'] as $acumuladas_mes): 
                        if($pregunta == $acumuladas_mes->id_pregunta): 
                            $valor_previstas = $acumuladas_mes->previstas_acumuladas;?>
                            <span><?php echo $valor_previstas?></span>
                       <?php endif; 
                    endforeach;
                    ?>
                    <input type="hidden" name="previstas[<?php echo $numero_mes ?>]" value="<?php echo $valor_previstas ?>">
                </td>


                
                <!-- EP1 -->
                <td class="text-center font-weight-bold" id="ep1_<?php echo $numero_mes?>">
                    <?php foreach($datos['edicion_mes'] as $ep1):
                        if ($ep1->id_pregunta == $pregunta):
                             echo $ep1->ep1 . ' %';
                        endif; 
                    endforeach;
                    ?> 
                </td>



            </tr>

        <?php endforeach; ?>

        <!-- Botón enviar -->
        <tr>
            <td colspan="<?php echo count($datos['temas']) + 5; ?>" class="text-center">
                <input type="submit" class="btn mt-3" id="boton-modal" value="Enviar resultados">
            </td>
        </tr>

    </tbody>

</table>
</form>
</div>





<!-- FOOTER -->
<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>




<script>

    // Función que cambia entre "EDITAR TABLA" y "NO EDITAR"
    document.getElementById('boton-modal').addEventListener('click', function() {
        const tabla = document.getElementById('tabla-acumuladas-container');
        const boton = this;
        if (tabla.style.display === 'none') {
            tabla.style.display = 'block';  
            boton.textContent = 'NO EDITAR'; 
        } else {
            tabla.style.display = 'none';  
            boton.textContent = 'EDITAR TABLA'; 
        }
    });




document.addEventListener('DOMContentLoaded', function () {
function actualizarContenidosYEP1(numeroMes) {

            // 1. Sumar todas las acumuladas del mes
            let suma = 0;
            document.querySelectorAll(`input.acumulada-input[data-mes='${numeroMes}']`).forEach(input => {
                suma += parseFloat(input.value) || 0;
            });

            // 2. Obtener ajuste
            const ajusteInput = document.querySelector(`.input-ajuste[data-mes='${numeroMes}']`);
            const ajuste = parseFloat(ajusteInput?.value) || 0;

            // 3. Calcular contenidos impartidos
            const totalImpartido = Math.max(suma - ajuste, 0);

            // 4. Actualizar celda visible
            const contenidosCell = document.getElementById(`contenidos_${numeroMes}`);
            if (contenidosCell) {
                const span = contenidosCell.querySelector('.contenidos-impartidos');
                span.textContent = totalImpartido;
                span.dataset.original = totalImpartido;
            }

            // 5. Calcular EP1
            let divisor = 0;
            if (parseInt(numeroMes) === 6) {
                divisor = totalProgramadas;
            } else {
                const previstasSpan = document.querySelector(`#previstas_${numeroMes} span`);
                divisor = parseFloat(previstasSpan?.textContent) || 0;
            }

            let ep1 = divisor > 0 ? (totalImpartido / divisor) * 100 : 0;
            ep1 = ep1.toFixed(1);

            // 6. Actualizar input hidden
            const inputContenidos = document.getElementById(`input_contenidos_${numeroMes}`);
            const inputEP1 = document.getElementById(`input_ep1_${numeroMes}`);
            if (inputContenidos) inputContenidos.value = totalImpartido;
            if (inputEP1) inputEP1.value = ep1;

            // 7. Actualizar celda EP1 visual
            const ep1Cell = document.getElementById(`ep1_${numeroMes}`);
            if (ep1Cell) ep1Cell.textContent = ep1 + ' %';
        }

        // Detectar cambios en ajustes y acumuladas
        document.querySelectorAll('.input-ajuste, .acumulada-input').forEach(input => {
            input.addEventListener('input', function () {
                const numeroMes = this.dataset.mes;
                actualizarContenidosYEP1(numeroMes);
            });
        });

        // Inicialización por si ya hay datos al cargar
        const mesesUnicos = new Set();
        document.querySelectorAll('.acumulada-input').forEach(input => {
            mesesUnicos.add(input.dataset.mes);
        });

});


const totalProgramadas = <?php echo $total_programadas; ?>;


</script>

