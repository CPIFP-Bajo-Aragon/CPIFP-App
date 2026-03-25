<?php


class Inicio extends Controlador{

   private $inicioModelo;

    public function __construct(){  
        Sesion::iniciarSesion($this->datos);
        $this->inicioModelo = $this->modelo('InicioModelo');
    }

    
    public function index(){
        $this->datos['accesos'] = new stdClass();
        $this->datos['accesos']->seguimiento = obtenerRol_seguimiento($this->datos['usuarioSesion']->roles);
        $this->datos['accesos']->mantenimiento = obtenerRol_mantenimiento($this->datos['usuarioSesion']->roles);
        $this->datos['accesos']->orientacion = obtenerRol_orientacion($this->datos['usuarioSesion']->roles);
        $this->vista('inicio', $this->datos);
    }



}



