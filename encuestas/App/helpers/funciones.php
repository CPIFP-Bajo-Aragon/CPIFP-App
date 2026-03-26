<?php

// ─── Redirección ──────────────────────────────────────────────────────────────
function redireccionar($pagina){
    header('location: '.RUTA_URL.$pagina);
    exit();
}

// ─── Formato de fechas ────────────────────────────────────────────────────────
function formatoFecha($fechaIngles){
    return date("d/m/Y H:i:s", strtotime($fechaIngles));
}

function formatoFechaCorta($fechaIngles){
    return date("d/m/Y", strtotime($fechaIngles));
}

// ─── Privilegios ──────────────────────────────────────────────────────────────
function tienePrivilegios($rol_usuario, $rolesPermitidos){
    if (empty($rolesPermitidos) || in_array($rol_usuario, $rolesPermitidos)){
        return true;
    }
    return false;
}

/**
 * Calcula el id_rol del usuario para el módulo de encuestas
 * basándose en sus departamentos y roles de la BD general.
 *   100 → Profesor (acceso a sus encuestas)
 *   200 → Jefe de departamento
 *   300 → Equipo directivo / admin
 */
function obtenerRol($roles){
    $id_rol = 0;
    foreach($roles as $rol){
        if($rol->id_rol == 1){          // Equipo directivo
            $id_rol = 300;
        } elseif($rol->id_rol == 30){   // Jefe de departamento
            if($id_rol < 200) $id_rol = 200;
        } elseif($rol->id_rol == 10){   // Profesor
            if($id_rol < 100) $id_rol = 100;
        }
    }
    return $id_rol;
}

// ─── Token para acceso público ────────────────────────────────────────────────
function generarToken($longitud = 32){
    return bin2hex(random_bytes($longitud));
}

// ─── Año académico actual ─────────────────────────────────────────────────────
function cursoAcademicoActual(){
    $mes = (int)date('m');
    $anyo = (int)date('Y');
    if($mes >= 9){
        return $anyo . '-' . ($anyo + 1);
    } else {
        return ($anyo - 1) . '-' . $anyo;
    }
}

// ─── Etiqueta de trimestre ────────────────────────────────────────────────────
function etiquetaTrimestre($t){
    $map = [1 => '1er Trimestre', 2 => '2º Trimestre', 3 => '3er Trimestre'];
    return $map[$t] ?? '-';
}

// ─── Email: enlace de encuesta a empresa ─────────────────────────────────────
function email_encuesta_empresa($email_destino, $nombre_empresa, $enlace){
    $to      = $email_destino;
    $nombreTo = $nombre_empresa;
    $asunto  = 'Encuesta de satisfacción – CPIFP Bajo Aragón [No responder]';
    $cuerpo  = "Estimada empresa {$nombre_empresa},<br><br>
                Le invitamos a rellenar nuestra encuesta anual de satisfacción:<br>
                <a href=\"{$enlace}\">{$enlace}</a><br><br>
                Gracias por su colaboración.<br>
                Un saludo,<br>
                CPIFP Bajo Aragón.";
    return EnviarEmail::sendEmail($to, $nombreTo, $asunto, $cuerpo);
}
