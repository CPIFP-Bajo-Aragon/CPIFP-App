<?php

function redireccionar($pagina) {
    header('location: ' . RUTA_URL . $pagina);
    exit();
}

function tienePrivilegios($rol_usuario, $rolesPermitidos) {
    if (empty($rolesPermitidos) || in_array($rol_usuario, $rolesPermitidos)) {
        return true;
    }
    return false;
}

function obtenerRol($roles) {
    $id_rol = 0;
    foreach ($roles as $rol) {
        if ($rol->id_rol == 50 && $id_rol < 50)       { $id_rol = 50; }
        elseif ($rol->id_rol == 30 && $id_rol < 30)   { $id_rol = 30; }
        elseif (in_array($rol->id_rol, [10,20,40]) && $id_rol < 10) { $id_rol = 10; }
    }
    return $id_rol;
}

function dibujarBotonesPaginacion($totalPaginas, $paginaActual) {
    if ($totalPaginas <= 1) return;
    echo '<nav><ul class="pagination">';
    if ($paginaActual > 1)
        echo '<li><button onclick="location.href=\'?pagina='.($paginaActual-1).'\'">&laquo; Anterior</button></li>';
    if ($paginaActual > 3) {
        echo '<li><button onclick="location.href=\'?pagina=1\'">1</button></li>';
        if ($paginaActual > 4) echo '<li><span>...</span></li>';
    }
    for ($i = max(1,$paginaActual-2); $i <= min($totalPaginas,$paginaActual+2); $i++) {
        if ($i == $paginaActual)
            echo '<li class="active"><button disabled>'.$i.'</button></li>';
        else
            echo '<li><button onclick="location.href=\'?pagina='.$i.'\'">'.$i.'</button></li>';
    }
    if ($paginaActual < $totalPaginas-2) {
        if ($paginaActual < $totalPaginas-3) echo '<li><span>...</span></li>';
        echo '<li><button onclick="location.href=\'?pagina='.$totalPaginas.'\'">'.$totalPaginas.'</button></li>';
    }
    if ($paginaActual < $totalPaginas)
        echo '<li><button onclick="location.href=\'?pagina='.($paginaActual+1).'\'">Siguiente &raquo;</button></li>';
    echo '</ul></nav>';
}

function formatearTamanio($bytes) {
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 0)    . ' KB';
    return $bytes . ' B';
}
