<?php

class Empresas extends Controlador {

    private $empresaModelo;
    private $encuestaModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->empresaModelo  = $this->modelo('EmpresaModelo');
        $this->encuestaModelo = $this->modelo('EncuestaModelo');
        $this->datos['usuarioSesion']->roles  = $this->encuestaModelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        $this->datos['menuActivo'] = 'empresas';

        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            echo "No tienes privilegios para gestionar empresas.";
            exit();
        }
    }

    public function index(){
        $this->datos['empresas'] = $this->empresaModelo->getEmpresas();
        $this->vista('empresas/index', $this->datos);
    }

    public function nueva($error = 0){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id = $this->empresaModelo->addEmpresa($_POST);
            if($id){
                redireccionar('/empresas');
            } else {
                redireccionar('/empresas/nueva/1');
            }
        } else {
            $this->datos['error'] = $error;
            $this->vista('empresas/nueva', $this->datos);
        }
    }

    public function editar($id_empresa){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $datos = $_POST;
            $datos['id_empresa'] = $id_empresa;
            if($this->empresaModelo->editEmpresa($datos)){
                redireccionar('/empresas');
            } else {
                echo "Error al guardar.";
            }
        } else {
            $this->datos['empresa'] = $this->empresaModelo->getEmpresa($id_empresa);
            $this->vista('empresas/editar', $this->datos);
        }
    }

    public function eliminar(){
        $this->datos['rolesPermitidos'] = [300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            $this->vistaApi(false);
            return;
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->vistaApi($this->empresaModelo->delEmpresa($_POST['id_empresa']));
        }
    }

    public function regenerar_token(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $token = $this->empresaModelo->regenerarToken($_POST['id_empresa']);
            $this->vistaApi($token ? ['token' => $token] : false);
        }
    }

    public function enviar_encuesta(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id_encuesta = $_POST['id_encuesta'];
            $encuesta    = $this->encuestaModelo->getEncuesta($id_encuesta);
            if($encuesta && $encuesta->email_empresa){
                $enlace = RUTA_CPIFP . RUTA_URL . '/responder/' . $encuesta->token_publico;
                $ok = email_encuesta_empresa($encuesta->email_empresa, $encuesta->nombre_empresa, $enlace);
                $this->vistaApi($ok);
            } else {
                $this->vistaApi(false);
            }
        }
    }
}
