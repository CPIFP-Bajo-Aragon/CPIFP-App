

<?php require_once RUTA_APP . '/vistas/inc/header_general.php' ?>
<?php require_once RUTA_APP . '/vistas/inc/menu_informes.php' ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<?php

$minimos_por_grado_indicador = [];
$indicadores = [];
$tabla = [];
$grados = [];

foreach ($datos['indicadores_grados'] as $item) {
    $minimos_por_grado_indicador[$item->id_grado][$item->indicador_corto] = $item->porcentaje;
    $indicadores[$item->indicador_corto] = $item->indicador;

    if (!in_array($item->nombre_grado, $grados)) {
        $grados[] = $item->nombre_grado;
    }

    if (!isset($tabla[$item->id_indicador])) {
        $tabla[$item->id_indicador] = [
            'nombre' => $item->indicador,
            'corto' => $item->indicador_corto,
            'porcentajes' => []
        ];
    }

    $tabla[$item->id_indicador]['porcentajes'][$item->nombre_grado] = $item->porcentaje;
}


// Agrupación de módulos por grupo
$grupos = [];
foreach ($datos['resumen_modulos'] as $item) {
    $clave = $item->ciclo . ' - ' . $item->turno;
    $modulo_id = $item->id_modulo;

    if (!isset($grupos[$clave])) {
        $grupos[$clave] = [
            'grado' => $item->grado,
            'modulos' => []
        ];
    }

    if (!isset($grupos[$clave]['modulos'][$modulo_id])) {
        $grupos[$clave]['modulos'][$modulo_id] = [
            'ciclo' => $item->ciclo,
            'grado' => $item->grado,
            'id_grado' => $item->id_grado,
            'turno' => $item->turno,
            'curso' => $item->curso,
            'modulo' => $item->modulo,
            'nombre_corto' => $item->nombre_corto,
            'indicadores' => [],
        ];
    }

    $grupos[$clave]['modulos'][$modulo_id]['indicadores'][$item->indicador_corto] = [
        'valor' => $item->total,
        'conforme' => $item->modulo_conforme,
    ];
}
?>





<!-- Filtro de departamentos de formación -->
<div class="m-4" style="max-width: 600px;">
    <div class="input-group">
        <span class="input-group-text">Filtrar por departamento</span>
        <select class="form-select" id="departamentoSelect" onchange="window.location.href=this.value;">
            <option value="<?php echo RUTA_URL ?>/DInformes" <?php echo (basename($_SERVER['REQUEST_URI']) === 'verTodo') ? 'selected' : ''; ?>>
                Ver todos
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
<?php if (!empty($datos['departamento_actual'])): ?>
    <form id="formDescarga" method="post" action="<?php echo RUTA_URL?>/DInformes/descargar_pdf_departamento/<?php echo $datos['departamento_actual']; ?>" target="_blank" style="display: inline-block;">
        <button type="submit" class="btn btn-custom" style="background-color: #d6eaf8; color:#0583c3;">
            <i class="fas fa-file-pdf"></i> Descargar informe (PDF)
        </button>
    </form>
    <form method="post" action="<?php echo RUTA_URL?>/DInformes/descargar_csv_departamento/<?php echo $datos['departamento_actual']; ?>">
        <button type="submit" class="btn btn-custom">
            <i class="fas fa-file-csv"></i> Descargar informe (CSV)
        </button>
    </form>
<?php endif; ?>
</div>



<!-- tablas indicadores y modulos conformes / no conformes -->
<?php if (!empty($datos['lectivo'])) : ?>
<div class="m-4" style="display: flex; align-items: flex-start; gap: 10px; justify-content: flex-start;">

    <!-- Tabla de Indicadores -->
    <div class="table-responsive" style="width: auto; max-width: 700px; overflow-x: auto;">
        <table class='table tabla-formato' style='font-size: 13px; white-space: nowrap;'>
            <thead>
                <tr>
                    <th>Indicador</th>
                    <?php foreach ($grados as $grado): ?>
                        <th class="text-center"><?php echo $grado; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tabla as $fila): ?>
                    <tr>
                        <td><?php echo $fila['nombre'] . ' (' . $fila['corto'] . ')'; ?></td>
                        <?php foreach ($grados as $grado): ?>
                            <td class="text-center">
                                <?php echo isset($fila['porcentajes'][$grado]) ? $fila['porcentajes'][$grado] . '%' : '-'; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- módulos conformes / no conformes -->
    <div class="m-5 p-3" style="min-width: 200px; border: 1px solid #0583c3;">
        <?php
            $totalConformes = 0;
            $totalNoConformes = 0;

            foreach ($grupos as $grupo) {
                foreach ($grupo['modulos'] as $modulo) {
                    $esConforme = true;
                    foreach ($modulo['indicadores'] as $ind) {
                        if ($ind['conforme'] == 0) {
                            $esConforme = false;
                            break;
                        }
                    }
                    $esConforme ? $totalConformes++ : $totalNoConformes++;
                }
            }
        ?>
        <p><i class="fas fa-check-circle text-success"></i> <strong>Conformes:</strong> <?php echo $totalConformes; ?></p>
        <p><i class="fas fa-times-circle text-danger"></i> <strong>No Conformes:</strong> <?php echo $totalNoConformes; ?></p>
    </div>
</div>
<?php endif; ?>





<?php foreach ($grupos as $grupo => $datosGrupo): ?>
<?php $modulos = $datosGrupo['modulos']; $grado = $datosGrupo['grado']; ?>
<div class="m-4">
<div class="table-responsive">
<table class="table tabla-formato">

        <thead>
            <tr>
                <th colspan="<?php echo (4 + count($indicadores)); ?>" class="text-center fs-5">
                    <?php echo $grupo . ' (' . $grado . ')'; ?>
                </th>
            </tr>
            <tr>
                <th>Curso</th>
                <th>Módulo</th>
                <th class="text-center">Código módulo</th>
                <?php foreach ($indicadores as $codigo => $descripcion): ?>
                    <th class="text-center"><?php echo $codigo; ?></th>
                <?php endforeach; ?>
                <th class="text-center">ESTADO</th>
            </tr>
        </thead>


        <tbody>
        <?php
        $modulosPorCurso = [];
        foreach ($modulos as $modulo) {
            $modulosPorCurso[$modulo['curso']][] = $modulo;
        }

        foreach ($modulosPorCurso as $curso => $modulosCurso):

            $acumulados = [];
            $cuentas = [];


                foreach ($modulosCurso as $modulo): ?>
                <tr>
                    <td><?php echo $modulo['curso']; ?></td>
                    <td><?php echo $modulo['modulo']; ?></td>
                    <td class="text-center"><?php echo $modulo['nombre_corto']; ?></td>

                    <?php foreach ($indicadores as $codigo => $descripcion): ?>
                        <?php if (isset($modulo['indicadores'][$codigo])): 

                            $valorNum = $modulo['indicadores'][$codigo]['valor'];
                            $valor = $valorNum . ' %';

                            $acumulados[$codigo] = ($acumulados[$codigo] ?? 0) + $valorNum;
                            $cuentas[$codigo] = ($cuentas[$codigo] ?? 0) + 1;

                            $style = $modulo['indicadores'][$codigo]['conforme'] == 0
                                ? "color: orangered; font-weight: bold; background-color: #f8d7da;"
                                : "color: green; font-weight: bold; background-color: #d4edda;";
                        ?>
                            <td class="text-center" style="<?php echo $style; ?>"><?php echo $valor; ?></td>
                        <?php else: ?>
                            <td class="text-center" style="color: green; font-weight: bold; background-color: #d4edda;">---</td>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php
                        $esConforme = !in_array(0, array_column($modulo['indicadores'], 'conforme'));
                        $estadoColor = $esConforme ? 'green' : 'orangered';
                        $estadoTexto = $esConforme ? 'CONFORME' : 'NO CONFORME';
                    ?>
                    <td style="font-weight: bold; color: <?php echo $estadoColor; ?>;"><?php echo $estadoTexto; ?></td>
                </tr>
                <?php endforeach; ?>


                <!-- Promedios -->
                <tr class="fw-bold">
                    <td colspan="3" style="background-color:#0583c3; color:white;">Promedio <?php echo $curso; ?></td>
                    <?php foreach ($indicadores as $codigo => $descripcion): ?>
                        <?php if (!empty($cuentas[$codigo])): 
                            $media = number_format($acumulados[$codigo] / $cuentas[$codigo], 2);
                        ?>
                            <td style="background-color:#0583c3; color:white;"><?php echo $media ?> %</td>
                        <?php else: ?>
                            <td style="background-color:#0583c3; color:white;" class="text-center">---</td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <td style="background-color:#0583c3; color:white;"></td>
                </tr>

            
        <?php endforeach; ?>
        </tbody>


</table>
</div>
</div>
<?php endforeach; ?>



<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>

