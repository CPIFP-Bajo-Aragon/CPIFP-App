<?php

class Incidencias extends Controlador{

    private $incidenciaModelo;
   
    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        
        $this->datos["menuActivo"] = "home";
        // print_r($this->datos);exit;
 
        $this->incidenciaModelo = $this->modelo('IncidenciaModelo');
        
        $this->datos["usuarioSesion"]->roles = $this->incidenciaModelo->getRolesProfesor($this->datos["usuarioSesion"]->id_profesor);
        $this->datos["usuarioSesion"]->id_rol = obtenerRol($this->datos["usuarioSesion"]->roles);

        $this->datos['rolesPermitidos'] = [100,200,300];         // Definimos los roles que tendran acceso
                                                            // Comprobamos si tiene privilegios
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol,$this->datos['rolesPermitidos'])) {
            echo "No tienes privilegios!!!";
            exit();
            // redireccionar('/');
        }
       
    }

    public function index(){

        // $this->datos["asesorias"] = $this->asesoriaModelo->getAsesorias();
        // foreach($this->datos["asesorias"] as $asesoria){
        //     $asesoria->acciones = $this->asesoriaModelo->getAccionesAsesoria($asesoria->id_asesoria);
        // }

        // $this->vista("asesorias/index",$this->datos);
    }


    public function add_incidencia($error=0){
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $incidencia = $_POST;

            if($this->incidenciaModelo->addIncidencia($incidencia,$this->datos["usuarioSesion"]->id_profesor)) {

                $tecnicos = $this->incidenciaModelo->getTecnicos();   // Optenemos todos los tecnicos disponibles

                $emails = array_column($tecnicos, 'email');
                $nombres = array_column($tecnicos, 'nombre_completo');

                email_aviso_tecnicos($emails,$nombres);     // Enviamos aviso a los tecnicos
                
                redireccionar('/');
            } else {
                echo "Se ha producido un Error!!!";
            }

        } else {
            $this->datos["menuActivo"] = "";
            $this->datos["error"] = $error;

            $this->datos["estadosUrgencia"] = $this->incidenciaModelo->getEstadosDeUrgencia();
            $this->datos["edificios"] = $this->incidenciaModelo->getEdificios();
            foreach($this->datos["edificios"] as $edificio){
                $edificio->ubicaciones = $this->incidenciaModelo->getUbicaciones($edificio->id_edificio);
            }
            $this->vista("incidencias/add_incidencia",$this->datos);
        }
    }



    public function del_incidencia(){

        $this->datos['rolesPermitidos'] = [100,200,300];        // por comodidad de momento no controlo en servidor que el privilegio 100 si no ha sido el creador o ya se ha iniciado la reparacion, no pueda eliminar

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol,$this->datos['rolesPermitidos'])) {
            $this->vistaApi(false);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id_incidencia = $_POST['id_incidencia'];

            if ($this->incidenciaModelo->delIncidencia($id_incidencia)){
                $this->vistaApi(true);
            } else {
                $this->vistaApi(false);
            }
        } else {

        }
    }



    public function ver_incidencia($id_incidencia){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $incidencia = $_POST;

            if($this->incidenciaModelo->incidenciaCerrada($id_incidencia)){
                exit();
            }

            if ($this->incidenciaModelo->editIncidencia($incidencia,$id_incidencia)){
                redireccionar("/incidencias/ver_incidencia/$id_incidencia");
            } else {
                echo "Se ha producido un Error!!!";
            }
        } else {
            $this->datos["incidencia"] = $this->incidenciaModelo->getIncidencia($id_incidencia);
            $this->datos["incidencia"]->acciones = $this->incidenciaModelo->getAccionesIncidencia($id_incidencia);
            $this->datos["incidencia"]->minTotales = 0;
            foreach ($this->datos["incidencia"]->acciones as $accion) {
                $this->datos["incidencia"]->minTotales += $accion->minutos;
            }
            
            $this->datos["estadosUrgencia"] = $this->incidenciaModelo->getEstadosDeUrgencia();
            $this->datos["edificios"] = $this->incidenciaModelo->getEdificios();
            foreach($this->datos["edificios"] as $edificio){
                $edificio->ubicaciones = $this->incidenciaModelo->getUbicaciones($edificio->id_edificio);
                if ($edificio->id_edificio == $this->datos["incidencia"]->id_edificio){
                    $this->datos["ubicacionesActivas"] = $edificio->ubicaciones;
                }
            }

            $this->vista("incidencias/ver_incidencia",$this->datos);
        }
    }

    

    public function add_accion(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $accion = $_POST;
            $accion['id_profesor'] = $this->datos["usuarioSesion"]->id_profesor;

            list($horas, $minutos) = explode(':', $accion['horas']);        // convertimos xx:xx a minutos
            $totalMinutos = ($horas * 60) + $minutos;
            $accion['totalMinutos'] = $totalMinutos;
            // $accion['totalMinutos'] = formatoHorasMinutosAMinutos($accion['horas']);    // Cambiar las tres lineas anteriores por esta

            if($this->incidenciaModelo->incidenciaCerrada($accion["id_incidencia"])){
                exit();
            }

            if ($this->incidenciaModelo->addAccion($accion)){
                redireccionar("/incidencias/ver_incidencia/".$accion["id_incidencia"]);
            } else {
                echo "Se ha producido un Error!!!";
            }
        } else {

        }
    }


    public function cerrar_incidencia(){
        $this->datos['rolesPermitidos'] = [200,300];
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol,$this->datos['rolesPermitidos'])) {
            echo "No tienes privilegios!!!";
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $accion = $_POST;
            $accion['id_profesor'] = $this->datos["usuarioSesion"]->id_profesor;

            if($this->incidenciaModelo->incidenciaCerrada($accion["id_incidencia"])){
                exit();
            }

            if ($this->incidenciaModelo->cerrarIncidencia($accion)){
                redireccionar("/incidencias/ver_incidencia/".$accion["id_incidencia"]);
            } else {
                echo "Se ha producido un Error!!!";
            }
        } else {

        }
    }



    public function abrir_incidencia(){
        $this->datos['rolesPermitidos'] = [200,300];
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol,$this->datos['rolesPermitidos'])) {
            echo "No tienes privilegios!!!";
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $accion = $_POST;
            $accion['id_profesor'] = $this->datos["usuarioSesion"]->id_profesor;
            
            if ($this->incidenciaModelo->abrirIncidencia($accion)){
                redireccionar("/incidencias/ver_incidencia/".$accion["id_incidencia"]);
            } else {
                echo "Se ha producido un Error!!!";
            }
        } else {

        }
    }



    public function del_accion(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_reg_acciones = $_POST["id_reg_acciones"];

            if ($this->incidenciaModelo->delAccion($id_reg_acciones)){
                $this->vistaApi(true);
            } else {
                $this->vistaApi(false);
            }
        } else {

        }
    }


    public function get_accion($id_reg_acciones){

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {         // Solo acceso GET
            $datos = $this->incidenciaModelo->getAccion($id_reg_acciones);    // No necesitamos la informacion que nos aporta $this->datos
            $this->vistaApi($datos);
        }
    }


    public function set_accion(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {         // Solo acceso POST
            $accion = $_POST;
            $accion['minutos'] = formatoHorasMinutosAMinutos($accion['horas_minutos']);
            if ($this->incidenciaModelo->setAccion($accion)){
                $accion['formatoHoras'] = formatoMinutosAHoras($accion['minutos']);         // Obtengo el formato de tiempo de la accion a imprimir en la salida

                $acciones = $this->incidenciaModelo->getAccionesIncidencia($accion['id_incidencia']);
                $minTotales = 0;
                foreach ($acciones as $act) {
                    $minTotales += $act->minutos;
                }
                $accion['horasTotales'] = formatoMinutosAHoras($minTotales); 

                $this->vistaApi($accion);
            } else {
                $this->vistaApi(false);
            }
        }
    }




    public function filtro($pagina = 0){
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->datos["menuActivo"] = "filtro";

            $cadena = $_SERVER['REQUEST_URI'];      // Cogemos la cadena de get para poder paginar sin reiniciar el filtro
            $posicion = strpos($cadena, "?");
            if ($posicion){
                $this->datos["cadenaGet"] = substr($cadena, $posicion + 1);
            } else {
                $this->datos["cadenaGet"] = '';
            }

            $datos = $_GET;

            if (!isset($datos['fecha_ini']) || empty($datos['fecha_ini'])){     // Si fecha_ini esta vacio, le pongo la fecha de 6 meses atras
                $datos['fecha_ini'] = hoyMenos6Meses();
            }

            if (!isset($datos['fecha_fin']) || empty($datos['fecha_fin'])){     // Si fecha_fin esta vacio, le pongo fecha de hoy
                $datos['fecha_fin'] = date("Y-m-d");
            }


            $this->datos["estados"] = $this->incidenciaModelo->getEstados($datos);      // Cogemos todos los estados existentes
            if (!isset($datos['buscar'])){                                              // con esto vemos que entramos a filtro por primera vez, los selecciono todos
                $datos['estado'] = array_column($this->datos['estados'], 'id_estado');
            }


            if (!isset($datos['estado'])){              // Inicializo si esta vacio para que no de error
                $datos['estado'] = [];
            }

            if(!isset($datos['buscar'])){   // Si no esta definido buscar, lo defino con la cadena vacia
                $datos['buscar'] = '';
            }

            $this->datos["filtro"] = $datos;
            // // $this->datos["incidencias"] = $this->incidenciaModelo->getIncidenciasFiltro($datos);
            $this->datos["incidencias"] = $this->incidenciaModelo->getIncidenciasFiltro($datos,$pagina,TAM_PAGINA);
            foreach($this->datos["incidencias"]->registros as $incidencia){
                $incidencia->acciones = $this->incidenciaModelo->getAccionesIncidencia($incidencia->id_incidencia);
                
                $minTotales = 0;
                foreach ($incidencia->acciones as $act) {
                    $minTotales += $act->minutos;
                }
                $incidencia->horasTotales = formatoMinutosAHoras($minTotales);
            }

            $this->vista("incidencias/index",$this->datos);
        } else {

        }
    }

}
