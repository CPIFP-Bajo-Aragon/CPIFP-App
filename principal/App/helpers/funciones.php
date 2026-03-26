<?php


//Para redireccionar la pagina
function redireccionar($pagina){
    header('location: ' . RUTA_URL . $pagina);
}



function obtenerRol($roles){
    $id_rol = 0;
    foreach($roles as $rol){
        if($rol->id_rol==30 && $id_rol < $rol->id_rol){           // Jefe Departamento
            $id_rol = 30;
        }elseif($rol->id_rol==50 && $id_rol < $rol->id_rol){           // Equipo directivo
            $id_rol = 50;
        }elseif(($rol->id_rol==10 || $rol->id_rol==20 || $rol->id_rol==40) && $id_rol < $rol->id_rol){        // Profesor o Tutor
            $id_rol = 10;
        }
    }
    return $id_rol;
}


function tienePrivilegios($rol_usuario, $rolesPermitidos){
    // si $rolesPermitidos es vacio, se tendran privilegios
    if (empty($rolesPermitidos) || in_array($rol_usuario, $rolesPermitidos)) {
        return true;
    }
}

function generarCadenaAleatoria($longitud){
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';      //Generamos cadena aleatoria
    $cadenaAleatoria = substr(str_shuffle($permitted_chars), 0, $longitud);
    return $cadenaAleatoria;
}


function emailNuevoPassword($email_destino,$password){
    $to = $email_destino;
    $nombreTo = '';
    $asunto = 'Nueva Contraseña [No responder a este correo]';
    $cuerpo ="Su contraseña ha sido regenerada:
                <br><br><br> <strong>{$password}</strong> 
                <br><br> Un Saludo
                <br> CPIFP Bajo Aragón.";
    return EnviarEmail::sendEmail($to,$nombreTo,$asunto,$cuerpo);
}


// ++++++++++++++++++++ Aqui duplicamos las funciones de rol de sus respectivas aplicaciones ++++++++++++++++++ //
// ++++++++++++++++++++ Para mostrar o no los botones en caso de no tener privilegios +++++++++++++++++++++++++ //

function obtenerRol_orientacion($roles){

    $id_rol = 0;
    foreach($roles as $rol){
        if($rol->id_departamento==9){           // 9 departamento Conserje
            if($rol->id_rol==5){                // 5 rol de conserje
                $id_rol = 100;                  // Crea Asesoria
            }
        }elseif($rol->id_departamento==10 || $rol->id_departamento==38){     // 10 departamento PEAC y 38 dep IOPE
            if($rol->id_rol==10){               // 10 rol de Profesor
                $id_rol = 200;
            }
            if($rol->id_rol==30){               // Jefe Departamento
                $id_rol = 300;                  // el root
            }
        }
    }

    return $id_rol;
}


//********************************************************************/
//********************************************************************/
//*********************** ROLES SEGUIMIENTO *************************/
//********************************************************************/
//********************************************************************/

function obtenerRol_seguimiento($roles){
    $id_rol = 0;
    foreach($roles as $rol){
        if($rol->id_rol==30 && $id_rol < $rol->id_rol){  // Jefe Departamento
            $id_rol = 30;
        }elseif($rol->id_rol==50 && $id_rol < $rol->id_rol){  // Equipo directivo
            $id_rol = 50;
        }elseif(($rol->id_rol==10 || $rol->id_rol==20 || $rol->id_rol==40 ) && $id_rol < $rol->id_rol){ // Profesor,Tutor o Tecnico
            $id_rol = 10;
        }elseif($rol->id_rol == 5 && $id_rol < 5){ // Conserje
            $id_rol = 5;
        }
    }
    return $id_rol;
}




// Defino un rol de Mantenimiento, aunque no lo este usando la aplicación actual
function obtenerRol_mantenimiento($roles){

    $id_rol = 0;
    foreach($roles as $rol){
        if($rol->id_departamento==2){           // 9 departamento Informatica
            if($rol->id_rol>=10){                // 5 rol de Profesor
                $id_rol = 200;                  // Tecnico
            }
            if($rol->id_rol==30){                // 5 rol de conserje
                $id_rol = 300;                  // Jefe Departamento
            }
        }elseif($rol->id_departamento!=9){      // No es Conserjeria
            if($rol->id_rol==30){               // Jefe Departamento
                $id_rol = 100;                  // Puede poner incidencias
            }
        }
    }

    return $id_rol;
}

