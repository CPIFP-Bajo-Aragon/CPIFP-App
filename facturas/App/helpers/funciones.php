<?php

    //Para redireccionar la pagina
    function redireccionar($pagina){
        header('location: '.RUTA_URL.$pagina);
    }


    function formatoFecha($fechaIngles){
        return date("d/m/Y H:i:s", strtotime($fechaIngles));     // Obetnemos el formato de fecha en español
    }


    function hoyMenos6Meses(){
        $fecha_actual = date("Y-m-d");
        return date("Y-m-d",strtotime($fecha_actual."- 6 month"));
    }


    function tienePrivilegios($rol_usuario,$rolesPermitidos){
        // si $rolesPermitidos es vacio, se tendran privilegios
        if (empty($rolesPermitidos) || in_array($rol_usuario, $rolesPermitidos)) {
            return true;
        }
    }



    function obtenerRol($roles){
        $id_rol = 0;
       
        foreach($roles as $rol){
            if($rol->id_rol==30 || $rol->id_rol==50){           // 30 Es jefe de departamento y 50 equipo directivo
                $id_rol=$rol->id_rol*10;
            }
        }
       
        return $id_rol;
    }




    function dibujarBotonesPaginacion($totalPaginas, $paginaActual) {
        if ($totalPaginas <= 1) return;
        
        echo '<nav><ul class="pagination">';
        
        // Botón «Anterior»
        if ($paginaActual > 1) {
            echo '<li><button onclick="location.href=\'?pagina='.($paginaActual - 1).'\'">&laquo; Anterior</button></li>';
        }
        
        // Primera página
        if ($paginaActual > 3) {
            echo '<li><button onclick="location.href=\'?pagina=1\'">1</button></li>';
            if ($paginaActual > 4) {
                echo '<li><span>...</span></li>';
            }
        }
        
        // Páginas alrededor de la actual
        for ($i = max(1, $paginaActual - 2); $i <= min($totalPaginas, $paginaActual + 2); $i++) {
            if ($i == $paginaActual) {
                echo '<li class="active"><button disabled>'.$i.'</button></li>';
            } else {
                echo '<li><button onclick="location.href=\'?pagina='.$i.'\'">'.$i.'</button></li>';
            }
        }
        
        // Última página
        if ($paginaActual < $totalPaginas - 2) {
            if ($paginaActual < $totalPaginas - 3) {
                echo '<li><span>...</span></li>';
            }
            echo '<li><button onclick="location.href=\'?pagina='.$totalPaginas.'\'">'.$totalPaginas.'</button></li>';
        }
        
        // Botón «Siguiente»
        if ($paginaActual < $totalPaginas) {
            echo '<li><button onclick="location.href=\'?pagina='.($paginaActual + 1).'\'">Siguiente &raquo;</button></li>';
        }
        
        echo '</ul></nav>';
    }

    function transformarFecha($fecha)
    {
     $f = new DateTime($fecha);
     return $f->format('d/m/Y');
        
    }

?>
