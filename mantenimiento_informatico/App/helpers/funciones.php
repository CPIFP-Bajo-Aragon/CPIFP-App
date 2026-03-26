<?php

    //Para redireccionar la pagina
    function redireccionar($pagina){
        header('location: '.RUTA_URL.$pagina);
    }


    function formatoFecha($fechaIngles){
        return date("d/m/Y H:i:s", strtotime($fechaIngles));     // Obetnemos el formato de fecha en español
    }


    function formatoMinutosAHoras($totalMinutos){
        $horas = floor($totalMinutos/60);
        $minutos = $totalMinutos % 60;
        $formato = "$horas h";
        if ($minutos){
            $minutos = str_pad($minutos, 2, "0", STR_PAD_LEFT);
            $formato .= " $minutos'";
        }
        return $formato;
    }


    function formatoHorasMinutosAMinutos($horasMinutos){
        list($horas, $minutos) = explode(':', $horasMinutos);        // convertimos xx:xx a minutos
        $totalMinutos = ($horas * 60) + $minutos;
        return $totalMinutos;
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
            if(($rol->id_rol == 40)){                               // 40 rol Tecnico
                $id_rol = 300;                                      // Tecnico
            } else if ($rol->id_rol == 50 && $id_rol < 200){        // 50 rol de Equipo Directivo y tiene rol menor en la iteracion
                $id_rol = 200;                                      // Equipo Directivo
            } else if ($rol->id_rol == 30 && $id_rol < 100){        // 30 rol de Jefe Departamento y tiene rol menor en la iteracion
                $id_rol = 100;                                      // Jefe Departamento
            }
        }
    
        return $id_rol;
    }


    function email_aviso_tecnicos($emails,$nombres){
        $to = $emails;
        $nombreTo = $nombres;
        $asunto = 'Nueva Incidencia [No responder a este correo]';
        $cuerpo ="Se ha creado una nueva incidencia. Accede a la plataforma para más información: <a href='".$_SERVER['HTTP_HOST']."'>".$_SERVER['HTTP_HOST']."</a>. Gracias";
        return EnviarEmail::sendEmailMultiple($to,$nombreTo,$asunto,$cuerpo);
    }
