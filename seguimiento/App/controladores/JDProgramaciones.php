<?php



class JDProgramaciones extends Controlador{

    private $jdProgramacionModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        // $this->datos['rolesPermitidos'] = [30];          // Definimos los roles que tendran acceso

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->jdProgramacionModelo = $this->modelo('jdProgramacionM');
    }

    
    


    // RESTO DE DEPARTAMENTOS DE FORMACION
    public function programaciones_modulos($id_ciclo){

        $this->datos['lectivo'] = $this->jdProgramacionModelo->obtener_lectivo();
        $id = $this->datos['usuarioSesion']->id_profesor;
        $datos = $this->jdProgramacionModelo->departamentos_formacion($id);
        $id_dep = $datos[0]->id_departamento;


        if(!empty($this->datos['lectivo'])){

            $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
            $this->datos['nuevas'] = $this->jdProgramacionModelo->nuevas_por_ciclo($id_dep, $id_lectivo); // PROGRAMACIONES NUEVAS
            $this->datos['profesor_anterior'] = $this->jdProgramacionModelo->profesor_anterior($id_ciclo, $id_lectivo); // profesor año anterior
            $this->datos['profesor_modulo_ciclo'] = $this->jdProgramacionModelo->profesor_modulo_ciclo($id_ciclo, $id_lectivo); //profesor actual

        };    

        $this->datos['modulos_ciclo'] = $this->jdProgramacionModelo->modulos_ciclo($id_ciclo, $id_dep); // info de un ciclo
        $this->datos['programaciones_modulos_activas'] = $this->jdProgramacionModelo->programaciones_modulos_activas($id_dep); // todas las programaciones activas de los modulos de un departamento
        $this->datos['programaciones_departamento'] = $this->jdProgramacionModelo->programaciones_departamento($id_dep); // todas las programaciones de un departamento
        $this->datos['programaciones_ediciones_anteriores'] = $this->jdProgramacionModelo->programaciones_ediciones_anteriores($id_dep); // todas las programaciones de un departamento
        $this->vista('jefeDep/programaciones/programaciones_modulos', $this->datos);   
    }
    



    // FOL Y LEO

    public function programaciones_fol_leo(){

        $this->datos['lectivo'] = $this->jdProgramacionModelo->obtener_lectivo();
        $id = $this->datos['usuarioSesion']->id_profesor;
        $datos = $this->jdProgramacionModelo->departamentos_formacion($id);
        $id_dep = $datos[0]->id_departamento;



        if(!empty($this->datos['lectivo'])){

            $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
            $this->datos['nuevas'] = $this->jdProgramacionModelo->nuevas_fol_leo($id_dep, $id_lectivo);
            $this->datos['profesor_modulo_ciclo'] = $this->jdProgramacionModelo->profesor_modulo_actual($id_lectivo, $id_dep); //profesor actual
            $this->datos['profesor_anterior'] = $this->jdProgramacionModelo->profesor_modulo_anterior($id_lectivo, $id_dep); // profesor año anterior
            $this->datos['programaciones_anio_anterior'] = $this->jdProgramacionModelo->programaciones_modulos_anio_anterior_fol($id_lectivo, $id_dep); 
        };    


        $this->datos['modulos'] = $this->jdProgramacionModelo->modulos($id_dep);
        $this->datos['programaciones_modulos_activas'] = $this->jdProgramacionModelo->programaciones_modulos_activas_fol_leo($id_dep); // todas las programaciones activas de los modulos de un departamento
        $this->datos['programaciones_departamento'] = $this->jdProgramacionModelo->programaciones_departamento_fol_leo($id_dep); // todas las programaciones de un departamento
        $this->datos['programaciones_ediciones_anteriores'] = $this->jdProgramacionModelo->programaciones_ediciones_anteriores_fol_leo($id_dep); // todas las programaciones de un departamento
        $this->vista('jefeDep/programaciones/programaciones_fol_leo', $this->datos);
     
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
    }
}






//****************************** VERIFICA FOL /LEO ********************************************/

public function verificar_programacion_fol_leo($id_modulo){

    $this->datos['rolesPermitidos'] = [30];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $verifica = $_POST['confirma'];
        if($this->jdProgramacionModelo->verifica_programacion($id_modulo, $verifica)){
            redireccionar('/JDProgramaciones/programaciones_fol_leo');
        }else{
            die('Algo ha fallado!!');
        }
        
    }else{
        $this->vista('jefeDep/programaciones/programaciones_fol_leo', $this->datos);  
    }  
}



//****************************** VERIFICA RESTO DEPS. ******************************************* */

public function verificar_programacion($id_modulo){

    $id_ciclo = $_POST['id_ciclo'];
    $this->datos['rolesPermitidos'] = [30];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $verifica = $_POST['confirma'];
        if($this->jdProgramacionModelo->verifica_programacion($id_modulo, $verifica)){
            redireccionar('/JDProgramaciones/programaciones_modulos/'.$id_ciclo);
        }else{
            die('Algo ha fallado!!');
        }

    }else{
        $this->vista('jefeDep/programaciones/programaciones_modulos', $this->datos);  
    }  
}








}


