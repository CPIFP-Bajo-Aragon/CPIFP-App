<?php

class Acciones extends Controlador{

    private $accionModelo;


    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
       // $this->datos['rolesPermitidos'] = [50];          // Definimos los roles que tendran acceso
        
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->accionModelo = $this->modelo('AccionM');

    }





    public function index(){
        $this->datos['lectivo'] = $this->accionModelo->obtener_lectivo();
        $this->datos['tipos'] = $this->accionModelo->obtener_tipos(); //tipos de acciones
        $this->datos['causas_soluciones'] = $this->accionModelo->obtener_causas_soluciones(); //todas causas y soluciones y tipo al que pertenecen
        $this->vista('direccion/acciones/causas_soluciones', $this->datos);
    }




/************************ NUEVO ****************************/

public function nueva_causa_solucion(){

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }

    if($_SERVER['REQUEST_METHOD'] =='POST'){
        $nuevo=(object) [
            'accion' => $_POST['accion'],
            'descripcion' => $_POST['descripcion']
        ];
        if($this->accionModelo->nueva_causa_solucion($nuevo)){
            redireccionar('/acciones');
        }else{
            die('Algo ha fallado!!');
        }
    }else{
        $this->datos['causas_soluciones'] = (object)[
            'accion'=>'',
            'descripcion'=>''
        ];
        $this->vista('direccion/acciones/causas_soluciones',$this->datos);
    }
}



/************************ BAJA ****************************/

public function borrar_causa_solucion($id){

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($this->accionModelo->borrar_causa_solucion($id)) {
            redireccionar('/acciones');
        }else{
            die('Algo ha fallado!!!');
        }
    }else{
        $this->vista('direccion/acciones/causas_soluciones', $this->datos);
    }
}



/************************ MODIFICACION ****************************/

public function editar_causa_solucion($id){

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $editar = [
            'accion' => $_POST['accion'],
            'descripcion' => $_POST['descripcion']
            ];
        if($this->accionModelo->editar_causa_solucion($editar,$id)){
            redireccionar('/acciones');
        }else{
            die('Algo ha fallado!!');
        }
    }else{
        $this->vista('direccion/acciones/causas_soluciones',$this->datos);
    }
}

        







}





