

<?php require_once RUTA_APP . '/vistas/inc/header_general.php' ?>
<?php require_once RUTA_APP . '/vistas/inc/menu_informes.php' ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<?php

$indicadores_grados = $datos['indicadores_grados'];

$tabla = [];
$grados = [];

// Reorganizar la información
foreach ($indicadores_grados as $item) {

    $id_indicador = $item->id_indicador;
    $nombre_indicador = $item->indicador;
    $nombre_corto = $item->indicador_corto;
    $grado = $item->nombre_grado;
    $porcentaje = $item->porcentaje;

    // Guardar grados únicos
    if (!in_array($grado, $grados)) {
        $grados[] = $grado;
    }

    // Usamos id_indicador como clave única
    if (!isset($tabla[$id_indicador])) {
        $tabla[$id_indicador] = [
            'nombre' => $nombre_indicador,
            'corto' => $nombre_corto,
            'porcentajes' => []
        ];
    }

    $tabla[$id_indicador]['porcentajes'][$grado] = $porcentaje;
}
?>






<!-- Filtro de departamentos de formación -->
<div class="m-4" style="max-width: 600px;">
    <div class="input-group">
        <span class="input-group-text">Filtrar por departamento</span>
        <select class="form-select" id="departamentoSelect" onchange="window.location.href=this.value;">
            <option value="<?php echo RUTA_URL ?>/DInformes" <?php echo (basename($_SERVER['REQUEST_URI']) === 'verTodo') ? 'selected' : ''; ?>>
                Ver todos los cursos
            </option>
            <?php foreach ($datos['solo_formacion'] as $dep): ?>
                <option value="<?php echo RUTA_URL ?>/DInformes/por_departamento/<?php echo $dep->id_departamento.'-'.$dep->departamento_corto;?>"
                    <?php echo (strpos($_SERVER['REQUEST_URI'], 'por_departamento/'.$dep->id_departamento) !== false) ? 'selected' : ''; ?>>
                    <?php echo $dep->departamento; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>



<!-- BOTONES DESCARGAS -->
<div class="m-2 col-12 col-md-8 d-flex flex-wrap align-items-center justify-content-start gap-2">
    <form id="formDescarga" method="post" action="<?php echo RUTA_URL ?>/DInformes/descargar_pdf" style="display: inline-block;" target="_blank">
        <input type="hidden" name="id_evaluacion" value="<?php echo $id_evaluacion; ?>">
        <button type="submit" class="btn btn-custom" style="background-color: #d6eaf8; color:#0583c3;">
            <i class="fas fa-file-pdf"></i> Descargar informe (PDF)
        </button>
    </form>
    <form method="post" action="<?php echo RUTA_URL?>/DInformes/descargar_csv">
        <input type="hidden" name="id_evaluacion" value="<?php echo $id_evaluacion; ?>">
        <button type="submit" class="btn btn-custom">
            <i class="fas fa-file-csv"></i> Descargar informe (CSV)
        </button>
    </form>
</div>




<!-- Primera tabla (indicadores) -->
<?php if(!empty($datos['lectivo'])) : ?>
<div class="table-responsive" style="flex: 1 1 60%; min-width: 0;">
    <table class="table tabla-formato" style="font-size: 13px; white-space: nowrap;">
        <thead>
            <tr>
                <th>Indicador</th>
                <?php foreach ($grados as $grado): ?>
                    <th style="text-align:center"><?php echo $grado;?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tabla as $fila): ?>
                <tr>
                    <td><?php echo $fila['nombre']. ' (' .$fila['corto']. ')';?></td>
                    <?php foreach ($grados as $grado): ?>
                        <td style="text-align:center">
                            <?php echo isset($fila['porcentajes'][$grado]) ? $fila['porcentajes'][$grado] . '%' : '-'; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>




<?php if(!empty($datos['lectivo'])) : ?>
<div class="table-responsive">
<table class="table tabla-formato">

        <thead class="table-light">
            <tr>
                <th colspan="9" class="text-center">
                    <?php echo $datos['lectivo'][0]->lectivo;?>
                </th>
            </tr>
            <tr>
                <th>Ciclo Formativo</th>
                <th>Curso</th>
                <?php 
                    $indicadores = [];
                    foreach ($datos['total_curso'] as $item) {
                        if (!in_array($item->indicador_corto, $indicadores)) {
                            $indicadores[] = $item->indicador_corto;
                        }
                    }
                    foreach ($indicadores as $indicador) { ?>
                        <th style='text-align:center'><?php echo $indicador?></th>
                    <?php }
                ?>
                <th class="text-center">ESTADO</th>
            </tr>
        </thead>

        
        <tbody>
            <?php
                $umbrales = [];
                foreach ($datos['indicadores_grados'] as $umbral) {
                    $umbrales[$umbral->id_grado][$umbral->indicador_corto] = $umbral->porcentaje;
                }

                // Agrupar los totales por curso
                $agrupado = [];
                foreach ($datos['total_curso'] as $item) {
                    $key = $item->ciclo . '|' . $item->curso;
                    if (!isset($agrupado[$key])) {
                        $agrupado[$key] = [
                            'ciclo' => $item->ciclo,
                            'curso' => $item->curso,
                            'id_grado' => $item->id_grado,
                            'valores' => []
                        ];
                    }
                    $agrupado[$key]['valores'][$item->indicador_corto] = [
                        'valor' => $item->total,
                        'id_indicador' => $item->id_indicador
                    ];
                }


                foreach ($agrupado as $fila): ?>
                <tr>
                    <!-- nombre ciclo -->
                    <td><?php echo $fila['ciclo'];?></td>
                    <!-- nombre curso -->
                    <td><?php echo $fila['curso'];?></td>
                    <!-- porcentajes indicador -->
                    <?php  $esConforme = true;
                        foreach ($indicadores as $indicador_corto):
                            if (isset($fila['valores'][$indicador_corto])):

                                $valor = $fila['valores'][$indicador_corto]['valor'];
                                $id_grado = $fila['id_grado'];
                                $umbral = $umbrales[$id_grado][$indicador_corto] ?? null;
                                $formato = number_format($valor, 2) . ' %';

                                if ($umbral !== null && $valor < $umbral):
                                    $esConforme = false;?>
                                        <td style="color: orangered; font-weight: bold; background-color: #f8d7da;"><?php echo $formato; ?></td>
                                <?php else: ?>
                                    <td style="color: green; font-weight: bold; background-color: #d4edda;"><?php echo $formato; ?></td>
                                <?php endif;
                            else: ?>
                            <td style="color: green; font-weight: bold; background-color: #d4edda;">---</td>
                        <?php endif; endforeach; ?>
                    <!-- conforme / no conforme -->
                    <td style="font-weight: bold; color: <?php echo $esConforme ? 'green' : 'orangered'; ?>;">
                        <?php echo $esConforme ? 'CONFORME' : 'NO CONFORME'; ?>
                    </td>
                </tr>
                <?php endforeach; ?>


                <!-- fila promedios -->
                <tr class="fw-bold">
                    <td colspan="2" style="background-color:#0583c3; color:white;">Promedio general</td>
                    <?php $promedios = [];
                        foreach ($datos['promedio_anual'] as $item) {
                            $promedios[$item->id_indicador] = $item->promedio;
                        }
                        $mapaIndicadores = [];
                        foreach ($datos['total_curso'] as $item) {
                            $mapaIndicadores[$item->indicador_corto] = $item->id_indicador;
                        }
                        foreach ($indicadores as $indicador_corto): 
                            $id_indicador = $mapaIndicadores[$indicador_corto] ?? null;
                            if ($id_indicador && isset($promedios[$id_indicador])): ?>
                                <td style="background-color:#0583c3;  color:white;"><?php echo number_format($promedios[$id_indicador], 2); ?> %</td>
                            <?php else: ?>
                                <td style="background-color:#0583c3;  color:white;">---</td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <td style="background-color:#0583c3;"></td>
                </tr>
        </tbody>

</table>
</div>
<?php endif; ?>




<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
