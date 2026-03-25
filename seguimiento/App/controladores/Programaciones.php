<?php

class Programaciones extends Controlador{

    private $programacionModelo;


    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
       // $this->datos['rolesPermitidos'] = [50];          // Definimos los roles que tendran acceso
        
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->programacionModelo = $this->modelo('ProgramacionM');
    }





    public function index(){
        $this->datos['lectivo'] = $this->programacionModelo->obtener_lectivo();
        if(!empty( $this->datos['lectivo'])){
            $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;
            $this->datos['nuevas'] = $this->programacionModelo->nuevas_por_departamento($id_lectivo); // trae las nuevas por cada departamento
            $this->datos['verificadas'] = $this->programacionModelo->verificadas_por_departamento($id_lectivo); 
        } else{
            $this->datos['nuevas'] = '';
        }
        $this->datos['departamentos'] = $this->programacionModelo->departamentos_formacion(); // trae los departamentos de formacion
        $this->vista('direccion/programaciones/programaciones', $this->datos);
    }




//*****************************************/
// VISTA: CICLOS de un departamento
//***************************************/

    public function departamento($id_dep){
        $this->datos['lectivo'] = $this->programacionModelo->obtener_lectivo();
        $dep = $this->programacionModelo->departamento_por_id($id_dep); 
        if(!empty( $this->datos['lectivo'])){
            $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;
            $this->datos['ciclos'] = $this->programacionModelo->obtener_ciclos($id_dep); // trae los ciclos de un departamento
            $this->datos['nuevas'] = $this->programacionModelo->nuevas_por_ciclo($id_dep, $id_lectivo); // trae las nuevas por ciclo
            $this->datos['verificadas'] = $this->programacionModelo->verificadas_por_ciclo($id_dep, $id_lectivo);
        } else{
            $this->datos['nuevas'] = '';
        }
        $this->vista('direccion/programaciones/prog_departamento', $this->datos);
    }



//*****************************************/
// VISTAS: todos MODULOS de un ciclo
//***************************************/

    public function ciclo($id_ciclo){

        $this->datos['lectivo'] = $this->programacionModelo->obtener_lectivo();
        $this->datos['modulo'] = $this->programacionModelo->modulos_ciclo($id_ciclo); // todos los modulos de un ciclo incluido FOL y LEO

        if(!empty( $this->datos['lectivo'])){
            $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;
            $this->datos['profesor_modulo_ciclo'] = $this->programacionModelo->profesor_modulo_ciclo($id_ciclo, $id_lectivo); 
            $this->datos['profesor_anterior'] = $this->programacionModelo->profesor_anterior($id_ciclo, $id_lectivo); 
            $this->datos['programaciones_anio_anterior'] = $this->programacionModelo->programaciones_modulos_anio_anterior($id_ciclo, $id_lectivo); 
            $this->datos['numero_programaciones_ciclo'] = $this->programacionModelo->numero_programaciones_ciclo($id_ciclo, $id_lectivo); // todas las programaciones de un ciclo
        } else{
            $this->datos['nuevas'] = '';
        }

        $this->datos['programaciones_ediciones_anteriores'] = $this->programacionModelo->programaciones_ediciones_anteriores($id_ciclo); // todas las programaciones de un departamento
        $this->datos['programaciones_modulos_activas'] = $this->programacionModelo->programaciones_modulos_activas($id_ciclo);
        $this->vista('direccion/programaciones/prog_ciclo', $this->datos);
    }




//*********************************************/
// VISTA: PROGRAMACIONES un MODULO concreto
//*********************************************/

    public function modulo($id_modulo) {

        $this->datos['lectivo'] = $this->programacionModelo->obtener_lectivo();

        $this->datos['modulo'] = $this->programacionModelo->un_modulo($id_modulo);
        $programaciones_modulo = $this->programacionModelo->programaciones_modulo($id_modulo);
        $filtradas = array_filter($programaciones_modulo, function($p) {
            return $p->id_programacion !== null; 
        });

        $this->datos['programaciones_modulo'] = !empty($filtradas) ? $filtradas : [];
        $this->vista('direccion/programaciones/prog_modulo', $this->datos);
    }






/*********************** CODIGO VERIFICACION ********************************/

public function codigo_verificacion($id_modulo){

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $codigo_verificacion = $_POST['codigo_verificacion'];
        $id_ciclo = $_POST['id_ciclo'];

        if ($this->programacionModelo->codigo_verificacion($id_modulo, $codigo_verificacion)) {
            redireccionar('/programaciones/ciclo/'.$id_ciclo);
        }else{
            die('Algo ha fallado!!!');
        }
    }else{
        $this->vista('direccion/programaciones/prog_ciclo', $this->datos);
    }
}




/*********************** SUBIR PROGRAMACION ********************************/

public function subir_programacion($id_modulo)
{
    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $lectivo = $this->programacionModelo->obtener_lectivo();
        $nombre_lectivo = trim($lectivo[0]->lectivo);
        $id_lectivo = $lectivo[0]->id_lectivo;

        $profesor = $this->programacionModelo->profesor_modulo($id_modulo, $id_lectivo);
        $modulo = $this->programacionModelo->un_modulo($id_modulo);

        $num_version = $_POST['num_version'];
        $nueva = 0;

        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === 0) {
            $nombre_original = $_FILES['archivo']['name'];
            $tipo = $_FILES['archivo']['type'];
            $tmp = $_FILES['archivo']['tmp_name'];


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
            $nombre_archivo = $id_modulo . '-' . $num_version . '-' . pathinfo($nombre_original, PATHINFO_FILENAME) . '.' . $extension;
            $ruta = $directorio_destino . $nombre_archivo;

            $permitidos = [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];

            if (!in_array($tipo, $permitidos)) {
                die("Tipo de archivo no permitido. Solo .doc y .docx.");
            }

            if (file_exists($ruta)) {
                die("El archivo ya existe en el servidor.");
            }

            if (!move_uploaded_file($tmp, $ruta)) {
                die("Error al mover el archivo al destino.");
            }

            // Si todo fue bien, insertar en la base de datos
            $programacion = [
                'id_modulo' => $id_modulo,
                'codigo_programacion' => $modulo[0]->codigo_programacion,
                'id_lectivo' => $id_lectivo,
                'fecha' => date("Y-m-d H:i:s"),
                'ruta' => $ruta,
                'num_version' => $num_version,
                'nueva' => $nueva
            ];

            $this->programacionModelo->subir_programacion($programacion);
            redireccionar('/programaciones/modulo/' . $id_modulo);
        } else {
            echo "Error al subir el archivo o no se seleccionó ningún archivo.";
        }
    }
}




/*********************** DESCARGAR PROGRAMACION ********************************/

public function descargar_programacion(){

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $ruta_archivo = $_POST['ruta_archivo'];

        if ($ruta_archivo){

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
        redireccionar('/programaciones');
    } else{
        $this->vista('programaciones', $this->datos);
    }
}



/*********************** BORRAR PROGRAMACION ********************************/

public function borrar_programacion($id_programacion){

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }

    $ruta = $_POST['ruta_archivo'];
    $id_modulo = $_POST['id_modulo'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        if (file_exists($ruta)) {
            unlink($ruta);
        }

        if ($this->programacionModelo->borrar_programacion($id_programacion)) {
            redireccionar('/programaciones/modulo/'.$id_modulo);
        }else{
            die('Algo ha fallado!!!');
        }
    }else{
        $this->vista('programaciones/modulo/'.$id_modulo, $this->datos);
    }
}










}





