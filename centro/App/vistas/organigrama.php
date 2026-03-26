<?php require_once RUTA_APP . '/vistas/inc/header_general.php' ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php
// ── Paleta de colores por siglas de departamento ──────────────────────────
$colores = [
    'ADM'  => ['cab'=>'#dce8f7', 'nom'=>'#0b3b7a', 'sig'=>'#0b3b7a'],
    'TMV'  => ['cab'=>'#fde8d0', 'nom'=>'#7a3200', 'sig'=>'#c45e00'],
    'ELE'  => ['cab'=>'#fff3cc', 'nom'=>'#7a5000', 'sig'=>'#c47d00'],
    'SAN'  => ['cab'=>'#d8ecf5', 'nom'=>'#073d5a', 'sig'=>'#0b6b9a'],
    'IFC'  => ['cab'=>'#e8d8f5', 'nom'=>'#4a1570', 'sig'=>'#6b21a8'],
    'FOL'  => ['cab'=>'#d8f0e0', 'nom'=>'#0d5c2e', 'sig'=>'#1a6b3c'],
    'LEOA' => ['cab'=>'#fde0e0', 'nom'=>'#7a1010', 'sig'=>'#8b1a1a'],
];
$color_default = ['cab'=>'#e8e8e8', 'nom'=>'#333', 'sig'=>'#555'];

function color(array $colores, string $siglas, string $campo): string {
    $siglas = strtoupper(trim($siglas));
    return $colores[$siglas][$campo] ?? ($campo === 'cab' ? '#e8e8e8' : ($campo === 'nom' ? '#333' : '#555'));
}
?>

<style>
:root {
    --az: #0b2a85;
    --gc: #2C3E50;
    --gb: #f4f6f9;
    --gbr: #cfd8e3;
    --font: 'Segoe UI', system-ui, sans-serif;
}
.org-wrap { font-family: var(--font); font-size: 11.5px; color: #1a1a1a;
            padding: 12px 16px 32px; background: var(--gb); }

/* Cabecera */
.org-hdr { display:flex; align-items:center; justify-content:space-between;
           margin-bottom:12px; padding-bottom:8px; border-bottom:2px solid var(--az); }
.org-hdr h1 { font-size:14px; font-weight:700; color:var(--az);
              text-transform:uppercase; letter-spacing:.4px; margin:0 0 2px; }
.org-hdr p  { margin:0; font-size:9.5px; color:#777; }
.btn-print  { background:var(--az); color:#fff; border:none; border-radius:4px;
              padding:5px 12px; font-size:11px; font-weight:600; cursor:pointer; }
.btn-print:hover { background:#1a4db5; }

/* Leyenda */
.leyenda { display:flex; gap:14px; margin-bottom:8px; font-size:9.5px; color:#555; align-items:center; }
.ley-item { display:flex; align-items:center; gap:4px; }
.ld { width:7px; height:7px; border-radius:50%; background:var(--az); display:inline-block; }
.lp { width:5px; height:5px; border-radius:50%; background:#888; display:inline-block; }

/* Equipo directivo */
.blq-ed { background:var(--az); color:#fff; border-radius:5px;
          padding:7px 12px 10px; margin-bottom:10px; }
.blq-ed h2 { font-size:10px; font-weight:700; text-align:center; letter-spacing:1px;
             text-transform:uppercase; margin:0 0 7px; padding-bottom:4px;
             border-bottom:1px solid rgba(255,255,255,.25); }
.ed-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px,1fr)); gap:5px; }
.ed-c { background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.22);
        border-radius:4px; padding:5px 7px; text-align:center; }
.ed-c .cargo { font-size:8.5px; color:rgba(255,255,255,.7); display:block; margin-bottom:2px; }
.ed-c .nomb  { font-size:10.5px; font-weight:700; color:#fff; line-height:1.25; }

/* Secciones genéricas */
.sec-label { font-size:9.5px; font-weight:700; text-transform:uppercase;
             letter-spacing:.7px; color:var(--gc); margin-bottom:6px; padding-left:2px; }
.blq { background:#fff; border:1px solid var(--gbr); border-radius:5px;
       overflow:hidden; margin-bottom:10px; }
.blq-h { background:var(--gc); color:#fff; font-size:9.5px; font-weight:700;
         letter-spacing:.7px; text-transform:uppercase; padding:4px 9px; }
.sec-grid { display:grid; grid-template-columns:1fr 1fr; }
.sec-it { padding:5px 8px; border-right:1px solid var(--gbr); border-bottom:1px solid var(--gbr); }
.sec-it:nth-child(even) { border-right:none; }
.sec-it .sc { font-size:8.5px; color:#666; line-height:1.3; margin-bottom:1px; }
.sec-it .sn { font-size:10.5px; font-weight:700; color:#1a1a1a; }

/* Departamentos */
.org-body { display:grid; grid-template-columns:1fr 1fr; gap:8px; align-items:start; }
.dep { background:#fff; border:1px solid var(--gbr); border-radius:5px; overflow:hidden; }
.dep-h { padding:5px 8px; display:flex; align-items:center;
         justify-content:space-between; border-bottom:1px solid var(--gbr); }
.dep-n { font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.3px; }
.dep-s { font-size:8px; font-weight:700; padding:1px 5px; border-radius:3px; color:#fff; }
.dep-b { padding:5px 8px 7px; }

.jd { font-weight:700; font-size:10.5px; color:#1a1a1a; display:flex;
      align-items:center; padding:2px 0; line-height:1.3; }
.jd::before { content:''; display:inline-block; width:5px; height:5px;
              border-radius:50%; background:var(--az); margin-right:5px; flex-shrink:0; }
.pf { font-size:10px; color:#3a3a3a; display:flex; align-items:center;
      padding:1.5px 0; line-height:1.3; }
.pf::before { content:''; display:inline-block; width:3px; height:3px;
              border-radius:50%; background:#999; margin-right:6px; flex-shrink:0; }
.tec { font-size:9.5px; color:#666; font-style:italic; display:flex;
       align-items:center; padding:1.5px 0; line-height:1.3; }
.tec::before { content:'T'; display:inline-block; font-size:8px; font-style:normal;
               font-weight:700; color:#fff; background:#888; border-radius:3px;
               padding:0 3px; margin-right:5px; flex-shrink:0; }
.sep { border:none; border-top:1px dashed var(--gbr); margin:3px 0; }

.full { grid-column: 1 / -1; }
.two-col { display:grid; grid-template-columns:1fr 1fr; gap:0 16px; }

/* Tabla tutores */
.tut-table { width:100%; font-size:10.5px; border-collapse:collapse; }
.tut-table thead tr { background:#f0f0f0; }
.tut-table th, .tut-table td { padding:3px 8px; border-bottom:1px solid #eee; text-align:left; }
.tut-table th { font-weight:700; font-size:9.5px; }
.tut-table td.grupo { font-weight:700; }

.pie { text-align:right; font-size:9px; color:#aaa; margin-top:12px;
       border-top:1px solid #ddd; padding-top:5px; }

@media (max-width:680px) {
    .org-body, .sec-grid { grid-template-columns:1fr; }
    .full { grid-column:1; }
    .ed-grid { grid-template-columns:1fr 1fr; }
}
@media print {
    .btn-print { display:none; }
    .org-wrap { background:#fff; padding:6px; }
    .dep, .blq { break-inside:avoid; }
}
</style>

<div class="org-wrap">

    <!-- ── Cabecera ──────────────────────────────────────────────── -->
    <div class="org-hdr">
        <div>
            <h1><i class="fas fa-sitemap me-2"></i>Personal Docente — Organigrama</h1>
            <p>CPIFP Bajo Aragón &nbsp;·&nbsp; Curso 2025/26 &nbsp;·&nbsp; <?php echo date('d/m/Y') ?></p>
        </div>
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print me-1"></i>Imprimir
        </button>
    </div>

    <div class="leyenda">
        <span class="ley-item"><span class="ld"></span> Jefe/a de departamento</span>
        <span class="ley-item"><span class="lp"></span> Profesor/a</span>
        <span class="ley-item"><span style="display:inline-block;font-size:8px;font-weight:700;color:#fff;background:#888;border-radius:3px;padding:0 3px;">T</span> Técnico/a</span>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         EQUIPO DIRECTIVO
    ══════════════════════════════════════════════════════════════ -->
    <div class="blq-ed">
        <h2><i class="fas fa-star me-1" style="font-size:8px;opacity:.8"></i>Equipo Directivo</h2>
        <div class="ed-grid">
            <?php foreach ($datos['equipo_directivo'] as $m): ?>
            <div class="ed-c">
                <span class="cargo"><?php echo htmlspecialchars($m->departamento) ?></span>
                <span class="nomb"><?php echo htmlspecialchars($m->nombre_completo) ?></span>
            </div>
            <?php endforeach ?>
            <?php if (empty($datos['equipo_directivo'])): ?>
            <div class="ed-c"><span class="nomb" style="font-style:italic;opacity:.6">Sin datos</span></div>
            <?php endif ?>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         DEPARTAMENTOS ESTRATÉGICOS (isFormacion=0, excl. DIR)
    ══════════════════════════════════════════════════════════════ -->
    <?php if (!empty($datos['estrategicos'])): ?>
    <div class="sec-label"><i class="fas fa-chess-king me-1"></i>Departamentos estratégicos &amp; servicios</div>
    <div class="blq" style="margin-bottom:10px;">
        <div class="blq-h">Áreas de gestión y calidad</div>
        <div class="sec-grid">
            <?php foreach ($datos['estrategicos'] as $dep): ?>
                <?php foreach ($dep['miembros'] as $nombre): ?>
                <div class="sec-it">
                    <div class="sc"><?php echo htmlspecialchars($dep['departamento']) ?></div>
                    <div class="sn"><?php echo htmlspecialchars($nombre) ?></div>
                </div>
                <?php endforeach ?>
            <?php endforeach ?>
        </div>
    </div>
    <?php endif ?>

    <!-- ══════════════════════════════════════════════════════════════
         DEPARTAMENTOS DE FORMACIÓN
    ══════════════════════════════════════════════════════════════ -->
    <div class="sec-label"><i class="fas fa-chalkboard me-1"></i>Departamentos de formación</div>

    <div class="org-body">

    <?php
    $total_deps = count($datos['departamentos']);
    $i = 0;
    foreach ($datos['departamentos'] as $id_dep => $dep):
        $i++;
        $siglas = strtoupper(trim($dep['departamento_corto']));
        $cab    = color($colores, $siglas, 'cab');
        $nom    = color($colores, $siglas, 'nom');
        $sig    = color($colores, $siglas, 'sig');

        // Si es el último y el número total es impar → ocupa toda la fila
        $full = ($i === $total_deps && $total_deps % 2 !== 0) ? 'full' : '';

        // Para dept. ancho completo → lista en 2 columnas si hay ≥4 profesores
        $inner_cols = ($full && count($dep['profesores']) >= 4) ? 'two-col' : '';
    ?>
    <div class="dep <?php echo $full ?>">
        <div class="dep-h" style="background:<?php echo $cab ?>">
            <span class="dep-n" style="color:<?php echo $nom ?>"><?php echo htmlspecialchars($dep['departamento']) ?></span>
            <span class="dep-s" style="background:<?php echo $sig ?>"><?php echo htmlspecialchars($siglas) ?></span>
        </div>
        <div class="dep-b">

            <?php if (!empty($dep['jefes'])): ?>
                <?php foreach ($dep['jefes'] as $jd): ?>
                <div class="jd"><?php echo htmlspecialchars($jd) ?></div>
                <?php endforeach ?>
            <?php endif ?>

            <?php if (!empty($dep['profesores']) || !empty($dep['tecnicos'])): ?>
            <hr class="sep">
            <?php endif ?>

            <?php if ($inner_cols): ?>
            <div class="<?php echo $inner_cols ?>">
            <?php endif ?>

                <?php foreach ($dep['profesores'] as $pf): ?>
                <div class="pf"><?php echo htmlspecialchars($pf) ?></div>
                <?php endforeach ?>

            <?php if ($inner_cols): ?>
            </div>
            <?php endif ?>

            <?php foreach ($dep['tecnicos'] as $tec): ?>
            <div class="tec"><?php echo htmlspecialchars($tec) ?></div>
            <?php endforeach ?>

            <?php if (empty($dep['jefes']) && empty($dep['profesores']) && empty($dep['tecnicos'])): ?>
            <span style="font-style:italic;font-size:9.5px;color:#aaa;">Sin personal asignado</span>
            <?php endif ?>

        </div>
    </div>

    <?php endforeach ?>

    </div><!-- /org-body -->

    <!-- ══════════════════════════════════════════════════════════════
         GRUPOS Y TUTORES
    ══════════════════════════════════════════════════════════════ -->
    <div style="margin-top:12px;">
        <div class="sec-label"><i class="fas fa-users me-1"></i>Grupos y tutores</div>
        <div class="blq">
            <div class="blq-h">Tutores asignados por grupo</div>

            <?php if (empty($datos['tutores'])): ?>
            <div style="padding:10px 9px; font-size:10.5px; color:#888; font-style:italic;">
                No hay tutores asignados. Utiliza el módulo <strong>Tutores</strong> para realizar las asignaciones.
            </div>

            <?php else: ?>
            <?php
            // Agrupar tutores por departamento para presentarlos secccionados
            $tutores_dep = [];
            foreach ($datos['tutores'] as $t) {
                $tutores_dep[$t->departamento][] = $t;
            }
            ?>
            <table class="tut-table">
                <thead>
                    <tr>
                        <th style="width:90px">Grupo</th>
                        <th style="width:80px">Ciclo</th>
                        <th>Tutor/a</th>
                        <th class="d-none d-md-table-cell">Departamento</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tutores_dep as $dep_nombre => $tutores): ?>
                    <tr>
                        <td colspan="4" style="background:#f5f5f5; font-size:9px; font-weight:700;
                                               text-transform:uppercase; letter-spacing:.5px;
                                               color:#555; padding:3px 8px;">
                            <?php echo htmlspecialchars($dep_nombre) ?>
                        </td>
                    </tr>
                    <?php foreach ($tutores as $t): ?>
                    <tr>
                        <td class="grupo"><?php echo htmlspecialchars($t->curso) ?></td>
                        <td style="color:#666; font-size:9.5px;"><?php echo htmlspecialchars($t->ciclo_corto) ?></td>
                        <td><?php echo htmlspecialchars($t->nombre_completo) ?></td>
                        <td class="d-none d-md-table-cell" style="color:#888; font-size:9.5px;">
                            <?php echo htmlspecialchars($t->departamento_corto) ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                <?php endforeach ?>
                </tbody>
            </table>
            <?php endif ?>

        </div>
    </div>

    <div class="pie">CPIFP Bajo Aragón &nbsp;·&nbsp; Datos obtenidos de la base de datos del centro &nbsp;·&nbsp; <?php echo date('d/m/Y H:i') ?></div>

</div><!-- /org-wrap -->

<?php require_once RUTA_APP . '/vistas/inc/footer_general.php' ?>
