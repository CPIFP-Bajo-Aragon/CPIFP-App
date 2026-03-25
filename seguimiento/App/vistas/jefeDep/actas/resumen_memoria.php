
<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_mi_departamento.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/jefeDep/estilos_jefeDep.css">


<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">



<?php

$indicadores = [];
foreach ($datos['indicadores_grados'] as $indicador) {
    $indicadores[$indicador->indicador_corto] = $indicador->indicador;
}

// Agrupación de grupos y módulos con indicadores
$grupos = [];

if(!empty($datos['his_total_modulo'] )){
foreach ($datos['his_total_modulo'] as $item) {
    $clave = $item->ciclo . ' - ' . $item->turno;
    $modulo_id = $item->id_modulo;

    if (!isset($grupos[$clave])) {
        $grupos[$clave] = ['grado' => $item->grado, 'modulos' => []];
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
        'conforme' => $item->modulo_conforme
    ];
}
}


// Totales por curso
$totales_curso = [];
if(!empty($datos['his_total_curso'] )){
foreach ($datos['his_total_curso'] as $item) {
    $curso = $item->curso;
    $codigo = $item->indicador_corto;
    $totales_curso[$curso][$codigo] = ['valor' => $item->total, 'conforme' => $item->conforme];
}
}

// Tabla de indicadores por grado
$tabla = [];
$grados = [];
foreach ($datos['indicadores_grados'] as $item) {
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

?>





<?php if(!empty($datos['lectivo'])): ?>
<div class="m-4">

    <div class="col-12 col-md-8 d-flex flex-wrap align-items-center justify-content-start gap-2">
        <form method="post" action="<?php echo RUTA_URL?>/JDActas/descargar_resumen_csv">
            <input type="hidden" name="id_evaluacion" value="<?php echo $id_evaluacion; ?>">
            <button type="submit" class="btn btn-custom">
                <i class="fas fa-file-csv"></i> Descargar resumen (CSV)
            </button>
        </form>
        <form id="formDescarga" method="post" action="<?php echo RUTA_URL ?>/JDActas/descargar_resumen_pdf" style="display: inline-block;" target="_blank">
            <input type="hidden" name="id_evaluacion" value="<?php echo $id_evaluacion; ?>">
            <button type="submit" class="btn btn-custom">
                <i class="fas fa-file-pdf"></i> Descargar resumen (PDF)
            </button>
        </form>
    </div>

    <table class="table tabla-formato fuente_13px">
        <thead>
            <tr>
                <th>Indicador</th>
                <?php foreach ($grados as $grado): ?>
                    <th class="text-center"><?php echo htmlspecialchars($grado); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tabla as $fila): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila['nombre']) . ' (' . htmlspecialchars($fila['corto']) . ')'; ?></td>
                    <?php foreach ($grados as $grado): ?>
                        <td class="text-center"><?php echo isset($fila['porcentajes'][$grado]) ? $fila['porcentajes'][$grado] . '%' : '-'; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
<?php endif; ?>




<?php 
foreach ($grupos as $grupo => $datosGrupo):
    $modulos = $datosGrupo['modulos'];
    $grado = $datosGrupo['grado'];
?>

<div class="table-responsive">
<table class="table tabla-formato">

    <thead>
        <tr>
            <th colspan="<?php echo 4 + count($indicadores); ?>" class="text-center fs-5">
                <?php echo $grupo . ' (' . $grado . ')'; ?>
            </th>
        </tr>
        <tr>
            <th>Curso</th>
            <th>Módulo</th>
            <th class="text-center">Código</th>
            <?php foreach ($indicadores as $codigo => $descripcion): ?>
            <th class="text-center">
                <?php echo $codigo;?> <i class="bi bi-info-circle-fill circulo_info" title="<?php echo $descripcion;?>"></i>
            </th>
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

            foreach ($modulosCurso as $modulo): ?>
                <tr>
                    <td><?php echo $modulo['curso']; ?></td>
                    <td><?php echo $modulo['modulo']; ?></td>
                    <td class="text-center"><?php echo $modulo['nombre_corto']; ?></td>
                    <?php
                        foreach ($indicadores as $codigo => $descripcion):
                            $valor = '---';
                            $estilo_umbral = 'umbral-alto';
                            if (isset($modulo['indicadores'][$codigo])) {
                                $valorNum = $modulo['indicadores'][$codigo]['valor'];
                                $valor = $valorNum . ' %';
                                if ($modulo['indicadores'][$codigo]['conforme'] == 0) {
                                    $estilo_umbral = 'umbral-bajo';
                                }
                            }
                            echo "<td class='text-center {$estilo_umbral}'>{$valor}</td>";
                        endforeach;
                    ?>
                    <?php
                        $esConforme = !in_array(0, array_column($modulo['indicadores'], 'conforme'));
                        $estadoClase = $esConforme ? 'texto_verde_fuerte' : 'texto_naranja_fuerte';
                        $estadoTexto = $esConforme ? 'CONFORME' : 'NO CONFORME';
                    ?>
                    <td class="text-center <?php echo $estadoClase; ?>"><?php echo $estadoTexto; ?></td>
                </tr>
            <?php endforeach; ?>


            <tr class="fw-bold">
                <td colspan="3" class="fila_promedios">
                    Promedio <?php echo $curso;?>
                </td>
                <?php foreach ($indicadores as $codigo => $descripcion): 
                    $estilo_promedios = "fila_promedios";
                    if (isset($totales_curso[$curso][$codigo])) {
                        $valor = number_format($totales_curso[$curso][$codigo]['valor'], 2);
                        echo "<td class='text-center {$estilo_promedios}'>{$valor} %</td>";
                    } else {
                        echo "<td class='text-center {$estilo_promedios}'>---</td>";
                    }
                endforeach; ?>
                <td class="fila_promedios"></td>
            </tr>

        <?php endforeach; ?>
    </tbody>


</table>
</div>
<?php endforeach; ?>




<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>
