<?php

class Asesorias extends Controlador{

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        
        $this->datos["menuActivo"] = "asesorias";

        $this->asesoriaModelo = $this->modelo('AsesoriaModelo');
        
        $this->datos["usuarioSesion"]->roles = $this->asesoriaModelo->getRolesProfesor($this->datos["usuarioSesion"]->id_profesor);
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


    public function add_asesoria($error=0){
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $asesoria = $_POST;

            if (!$_POST["nombre_as"] && !$_POST["dni_as"] && !$_POST["titulo_as"] && !$_POST["telefono_as"] && !$_POST["email_as"] && !$_POST["domicilio_as"] && !$_POST["descripcion_as"]){        // Comprobamos que haya datos por lo menos en un campo
                redireccionar('/asesorias/add_asesoria/1');
            } else {
                email_informativo_asesoria($asesoria['email_as'],$asesoria['nombre_as']);   // Envia email al cliente confirmando que ha sido atendido
                
                $asesores = $this->asesoriaModelo->getAsesores();   // Optenemos todos los asesores disponibles

                $emails = array_column($asesores, 'email');
                $nombres = array_column($asesores, 'nombre_completo');

                email_aviso_asesores($emails,$nombres);     // Enviamos aviso a los asesores para que revisen las nuevas asesorias creadas

                if($this->asesoriaModelo->addAsesoria($asesoria,$this->datos["usuarioSesion"]->id_profesor)) {
                    redireccionar('/');
                } else {
                    echo "Se ha producido un Error!!!";
                }
            }

        } else {
            $this->datos["menuActivo"] = "";
            $this->datos["error"] = $error;

            $this->vista("asesorias/add_asesoria",$this->datos);
        }
    }


    public function ver_asesoria($id_asesoria){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $asesoria = $_POST;

            if($this->asesoriaModelo->asesoriaCerrada($id_asesoria)){
                exit();
            }

            if ($this->asesoriaModelo->editAsesoria($asesoria,$id_asesoria)){
                redireccionar("/asesorias/ver_asesoria/$id_asesoria");
            } else {
                echo "Se ha producido un Error!!!";
            }
        } else {
            $this->datos["asesoria"] = $this->asesoriaModelo->getAsesoria($id_asesoria);
            $this->datos["asesoria"]->acciones = $this->asesoriaModelo->getAccionesAsesoria($id_asesoria);
            
            $this->vista("asesorias/ver_asesoria",$this->datos);
        }
    }

    public function add_accion(){
        $this->datos['rolesPermitidos'] = [100,200,300];
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol,$this->datos['rolesPermitidos'])) {
            echo "No tienes privilegios!!!";
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $accion = $_POST;
            $accion['id_profesor'] = $this->datos["usuarioSesion"]->id_profesor;
            
            if($this->asesoriaModelo->asesoriaCerrada($accion["id_asesoria"])){
                exit();
            }

            // print_r($accion);
            if ($this->asesoriaModelo->addAccion($accion)){
                redireccionar("/asesorias/ver_asesoria/".$accion["id_asesoria"]);
            } else {
                echo "Se ha producido un Error!!!";
            }
        } else {

        }
    }


    public function cerrar_asesoria(){
        $this->datos['rolesPermitidos'] = [200,300];
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol,$this->datos['rolesPermitidos'])) {
            echo "No tienes privilegios!!!";
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $accion = $_POST;
            $accion['id_profesor'] = $this->datos["usuarioSesion"]->id_profesor;

            if($this->asesoriaModelo->asesoriaCerrada($accion["id_asesoria"])){
                exit();
            }

            // print_r($accion);
            if ($this->asesoriaModelo->cerrarAsesoria($accion)){
                redireccionar("/asesorias/ver_asesoria/".$accion["id_asesoria"]);
            } else {
                echo "Se ha producido un Error!!!";
            }
        } else {

        }
    }


    public function abrir_asesoria(){
        $this->datos['rolesPermitidos'] = [300];
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol,$this->datos['rolesPermitidos'])) {
            echo "No tienes privilegios!!!";
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $accion = $_POST;
            $accion['id_profesor'] = $this->datos["usuarioSesion"]->id_profesor;

            if ($this->asesoriaModelo->abrirAsesoria($accion)){
                redireccionar("/asesorias/ver_asesoria/".$accion["id_asesoria"]);
            } else {
                echo "Se ha producido un Error!!!";
            }
        } else {

        }
    }


    public function get_accion($id_reg_acciones){

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {         // Solo acceso GET
            $datos = $this->asesoriaModelo->getAccion($id_reg_acciones);    // No necesitamos la informacion que nos aporta $this->datos
            $this->vistaApi($datos);
        }
    }


    public function set_accion(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {         // Solo acceso POST
            $accion = $_POST;
            if ($this->asesoriaModelo->setAccion($accion)){
                $this->vistaApi(true);
            } else {
                $this->vistaApi(false);
            }
        }
    }


    public function del_accion(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_reg_acciones = $_POST["id_reg_acciones"];

            if ($this->asesoriaModelo->delAccion($id_reg_acciones)){
                $this->vistaApi(true);
            } else {
                $this->vistaApi(false);
            }
        } else {

        }
    }



    public function del_asesoria(){

        $this->datos['rolesPermitidos'] = [300];

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol,$this->datos['rolesPermitidos'])) {
            $this->vistaApi(false);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id_asesoria = $_POST['id_asesoria'];

            if ($this->asesoriaModelo->delAsesoria($id_asesoria)){
                $this->vistaApi(true);
            } else {
                $this->vistaApi(false);
            }
        } else {

        }
    }


    public function filtro(){
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $datos = $_GET;

            if (!isset($datos['fecha_ini']) || empty($datos['fecha_ini'])){     // Si fecha_ini esta vacio, le pongo la fecha de 6 meses atras
                $datos['fecha_ini'] = hoyMenos6Meses();
            }

            if (!isset($datos['fecha_fin']) || empty($datos['fecha_fin'])){     // Si fecha_fin esta vacio, le pongo fecha de hoy
                $datos['fecha_fin'] = date("Y-m-d");
            }


            $this->datos["estados"] = $this->asesoriaModelo->getEstados($datos);    // Cogemos todos los estados existentes
            if (!isset($datos['buscar'])){              // con esto vemos que entramos a filtro por primera vez, los selecciono todos
                $datos['estado'] = array_column($this->datos['estados'], 'id_estado');
            }

            if (!isset($datos['estado'])){              // Inicializo si esta vacio para que no de error
                $datos['estado'] = [];
            }

            if(!isset($datos['buscar'])){   // Si no esta definido buscar, lo defino con la cadena vacia
                $datos['buscar'] = '';
            }

            $this->datos["filtro"] = $datos;
            $this->datos["asesorias"] = $this->asesoriaModelo->getAsesoriasFiltro($datos);
            foreach($this->datos["asesorias"] as $asesoria){
                $asesoria->acciones = $this->asesoriaModelo->getAccionesAsesoria($asesoria->id_asesoria);
            }

            $this->vista("asesorias/index",$this->datos);
        } else {

        }
    }

}
