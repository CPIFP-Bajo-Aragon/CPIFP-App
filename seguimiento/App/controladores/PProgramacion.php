<?php


class PProgramacion extends Controlador{

    private $pProgramacionModelo;



        public function __construct(){
            Sesion::iniciarSesion($this->datos);
            $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
            if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
                redireccionar('/');
            }
            $this->pProgramacionModelo = $this->modelo('pProgramacionM');
        }




        public function index($id_modulo){

            $this->datos['lectivo'] = $this->pProgramacionModelo->obtener_lectivo(); 
            $lectivo = $this->datos['lectivo'];
            $id_lectivo = $lectivo[0]->id_lectivo;
            $id_profe = $this->datos['usuarioSesion']->id_profesor;
            $this->datos['datos_modulo'] = $this->pProgramacionModelo->info_modulo($id_profe, $id_modulo, $lectivo[0]->id_lectivo); // trae toda la info de un modulo para el curso actual
        

            // PARA LAS PROGRAMACIONES
            $this->datos['obtener_programacion']=$this->pProgramacionModelo->obtener_programacion_modulo($id_modulo); // trae la programacion que esta activa=1

            $this->datos['modulo'] = $this->pProgramacionModelo->obtener_modulos($id_profe, $id_lectivo);

            // BLOQUEAR ENLACES SI NO ESTA TODO OK
            $resultado = [];
            $modulo_profesor = $this->datos['datos_modulo'];
            $hay_temas = $this->pProgramacionModelo->hay_temas($modulo_profesor[0]->id_modulo);
            $hay_horas = $this->pProgramacionModelo->hay_horas($modulo_profesor[0]->id_modulo);
            $hay_seguimiento = $this->pProgramacionModelo->hay_seguimiento($modulo_profesor[0]->id_modulo);
            $hay_suma = $this->pProgramacionModelo->suma_temas($modulo_profesor[0]->id_modulo);
            $resultado[] = [
                'id_modulo' => $modulo_profesor[0]->id_modulo,
                'hay_temas' => $hay_temas[0]->hay_temas,
                'hay_horas' => $hay_horas[0]->hay_horas,
                'hay_seguimiento' => $hay_seguimiento[0]->hay_seguimiento,
                'hay_suma' => $hay_suma[0]->suma,
                'horas_totales_modulo' => $modulo_profesor[0]->horas_totales
            ];
            $this->datos['resultado'] = $resultado;



            $this->vista('profesores/programacion',$this->datos);
        }







public function subir_programacion($id_modulo) {


    $this->datos['lectivo'] = $this->pProgramacionModelo->obtener_lectivo(); // trae toda la info del lectivo
    $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
    $nombre_lectivo = trim($this->datos['lectivo'][0]->lectivo);


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $id_profe=$this->datos['usuarioSesion']->id_profesor;
        $info_modulo = $this->pProgramacionModelo->info_modulo($id_profe,$id_modulo, $id_lectivo);
        $programacion = $info_modulo[0]->codigo_programacion;
        $ultima_programacion = $this->pProgramacionModelo->ultima_edicion($id_modulo);

        if(empty($ultima_programacion)){
            $edicion = 1;
        }else{
            $edicion = $ultima_programacion[0]->edicion+1;
        }

        // Comprobar si se ha subido un archivo
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
            $archivo = $_FILES['archivo'];
            $nombre_original = $archivo['name'];
            $tipo = $archivo['type'];
            $tmp = $archivo['tmp_name'];
            $tam = $archivo['size'];


            // CARPETAS PARA PROGRAMACIONES
            $directorio_programaciones = $_SERVER['DOCUMENT_ROOT'] . '/archivos_programaciones/';
            $carpeta_anio = $id_lectivo.'-'.$nombre_lectivo;
            $directorio_destino = $directorio_programaciones. $carpeta_anio.'/';


              // Crear carpetas si no existen
            if (!is_dir($directorio_programaciones)) {
                if (!mkdir($directorio_programaciones, 0777, true)) {
                    die("No se pudo crear el directorio base.");
                }
            }


            if (!is_dir($directorio_destino)) {
                if (!mkdir($directorio_destino, 0777, true)) {
                    die("No se pudo crear el directorio del año lectivo.");
                }

                // Esperar hasta que el sistema reconozca la carpeta (máx 2 segundos)
                $intentos = 0;
                while (!is_dir($directorio_destino) && $intentos < 20) {
                    usleep(100000); // 0.1 seg
                    $intentos++;
                }

                if (!is_dir($directorio_destino)) {
                    die("El directorio de destino no está disponible tras varios intentos.");
                }
            }

            $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
            $nombre_archivo = $id_modulo . '-' . $edicion . '-' . pathinfo($nombre_original, PATHINFO_FILENAME) . '.' . $extension;
            $ruta_destino = $directorio_destino . $nombre_archivo;


            $tipos_permitidos = [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];

            if (in_array($tipo, $tipos_permitidos)) {
                // Comprobar si el archivo ya existe
                if (!file_exists($ruta_destino)) {
                    // Mover el archivo desde el directorio temporal al destino
                    if (move_uploaded_file($tmp, $ruta_destino)) {
                        $programacion = [
                            'id_modulo' => $id_modulo,
                            'programacion' => $programacion,
                            'id_lectivo' => $id_lectivo,
                            'fecha' => date("Y-m-d H:i:s"),
                            'ruta' => $ruta_destino, 
                            'edicion' => $edicion,
                            'id_profesor' => $id_profe
                        ];

                        $this->pProgramacionModelo->subir_programacion($programacion, $id_modulo);
                        redireccionar('/PProgramacion'.'/'.$id_modulo);

                    } else {
                        echo "Error al mover el archivo.";
                    }
                } else {
                    echo "El archivo ya existe en el servidor.";
                }
            } else {
                echo "Solo se permiten archivos de tipo: DOC, DOCX ";
            }
        } else {
            echo "Error en la subida del archivo o no se ha seleccionado un archivo.";
        }
    } else {
        echo "Método de solicitud no válido.";
    }
}





public function cambiar_programacion($id_modulo){

    

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $cambiar_programacion = $_POST['cambiar_programacion'];

        $this->datos['lectivo'] = $this->pProgramacionModelo->obtener_lectivo(); // trae toda la info del lectivo
        $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
        $nombre_lectivo = $this->datos['lectivo'][0]->lectivo;

        $id_profe = $this->datos['usuarioSesion']->id_profesor;
        $info_modulo = $this->pProgramacionModelo->info_modulo($id_profe,$id_modulo, $id_lectivo);
        $programacion = $info_modulo[0]->codigo_programacion;

        $ultima_programacion = $this->pProgramacionModelo->ultima_edicion($id_modulo);

            
        if($cambiar_programacion==1){

            $num_version = $ultima_programacion[0]->num_version+1;


            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {

                $nombre_original = $_FILES['archivo']['name'];
                $tipo = $_FILES['archivo']['type'];
                $tmp = $_FILES['archivo']['tmp_name'];


                // CARPETAS PARA PROGRAMACIONES
                $directorio_programaciones = $_SERVER['DOCUMENT_ROOT'] . '/archivos_programaciones/';
                $carpeta_anio = $id_lectivo.'-'.$nombre_lectivo;
                $directorio_destino = $directorio_programaciones. $carpeta_anio.'/';

                    
                // Nombre del archivo: id_modulo-nombreOriginal.ext
                $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
                $nombre_archivo = $id_modulo . '-' . $num_version . '-' . pathinfo($nombre_original, PATHINFO_FILENAME) . '.' . $extension;
                $ruta = $directorio_destino . $nombre_archivo;


                // Crear carpetas si no existen
                if (!is_dir($directorio_programaciones)) {
                    if (!mkdir($directorio_programaciones, 0777, true)) {
                        die("No se pudo crear el directorio base.");
                    }
                }


                if (!is_dir($directorio_destino)) {
                    if (!mkdir($directorio_destino, 0777, true)) {
                        die("No se pudo crear el directorio del año lectivo.");
                    }

                    // Esperar hasta que el sistema reconozca la carpeta (máx 2 segundos)
                    $intentos = 0;
                    while (!is_dir($directorio_destino) && $intentos < 20) {
                        usleep(100000); // 0.1 seg
                        $intentos++;
                    }

                    if (!is_dir($directorio_destino)) {
                        die("El directorio de destino no está disponible tras varios intentos.");
                    }
                }


                $permitidos = [
                    'application/msword',  // para archivos .doc
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'  // para archivos .docx
                ];


                if (in_array($tipo, $permitidos)) {
                    // Comprobar si el archivo ya existe
                    if (!file_exists($ruta)) {
                        // Mover el archivo del directorio temporal al directorio de destino
                        if (move_uploaded_file($tmp, $ruta)) {
                            $programacion = [
                                'id_modulo'=>$id_modulo,
                                'programacion' => $programacion,
                                'id_lectivo' => $id_lectivo,
                                'fecha' => date("Y-m-d H:i:s"),
                                'ruta'=> $ruta, 
                                'edicion' => $num_version,
                                'id_profesor' => $id_profe
                            ];

                            $this->pProgramacionModelo->cambia_programacion($programacion, $id_modulo);
                            redireccionar('/PProgramacion'.'/'.$id_modulo);
                        } else {
                            echo "Error al mover el archivo.";
                        }

                    } else {
                        echo "El archivo ya existe en el servidor.";
                    }
                } else {
                    echo "Solo se permiten archivos de tipo: DOC, DOCX ";
                }
            } else {
                echo "Error en la subida del archivo.";
            }

        }else{

                $ultima = $ultima_programacion[0];


                // CARPETAS PARA PROGRAMACIONES
                $directorio_programaciones = $_SERVER['DOCUMENT_ROOT'] . '/archivos_programaciones/';
                $carpeta_anio = $id_lectivo.'-'.$nombre_lectivo;
                $directorio_destino = $directorio_programaciones. $carpeta_anio.'/';


                // Copiar archivo original a la carpeta del año actual
                $ruta_origen = $ultima->ruta;
                $nombre_archivo_original = basename($ruta_origen);
                $nuevo_nombre = $nombre_archivo_original;
                $nueva_ruta = $directorio_destino . $nuevo_nombre;

                if($id_lectivo != $ultima->id_lectivo){

                    if (copy($ruta_origen, $nueva_ruta)) {

                        $programacion = [
                            'id_modulo' => $id_modulo,
                            'programacion' => $programacion,
                            'id_lectivo' => $id_lectivo,
                            'fecha' => date("Y-m-d H:i:s"),
                            'ruta' => $nueva_ruta,
                            'edicion' => $ultima->num_version,
                        ];

                        $this->pProgramacionModelo->no_cambia_programacion($programacion, $id_modulo);
                        redireccionar('/PProgramacion'.'/'.$id_modulo);
                    } else {
                        echo "Error al copiar el archivo desde la programación anterior.";
                    }

                } else {
                    $this->pProgramacionModelo->es_mismo_anio($id_modulo, $id_lectivo);
                    redireccionar('/PProgramacion'.'/'.$id_modulo);
                }

            }

    }
    
}






// descargar y ver la programacion
public function descargar_programacion($id_modulo){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $ruta_archivo = $_POST['ruta_archivo'];
        if ($ruta_archivo){

            // Ruta del archivo en el servidor
            //$rutaArchivo = "ruta/a/tu/archivo/$ruta_archivo"; 
            if (file_exists($ruta_archivo)) {
                header('Content-Type: application/pdf'); 
                header('Content-Disposition: attachment; filename="' . basename($ruta_archivo) . '"');
                header('Content-Length: ' . filesize($ruta_archivo));
                readfile($ruta_archivo);
                exit();
            } else {
                echo "El archivo no existe en el servidor.";
            }
        } else {
            echo "No se encontró el archivo en la base de datos.";
        }
        redireccionar('/PProgramacion'.'/'.$id_modulo);
    }
}





// codigo verificacion programacion
public function enviar_codigo_verificacion($id_modulo){
    if($_SERVER['REQUEST_METHOD'] =='POST'){
        $this->pProgramacionModelo->enviar_codigo_verificacion($id_modulo);
        redireccionar('/PProgramacion'.'/'.$id_modulo);
    }else{
        $this->vista('profeSegui/datos_modulo/'.$id_modulo,$this->datos);
    }
}





}