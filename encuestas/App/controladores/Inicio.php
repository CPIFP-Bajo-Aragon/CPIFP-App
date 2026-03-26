<?php

class Inicio extends Controlador {

    private $encuestaModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->encuestaModelo = $this->modelo('EncuestaModelo');
        $this->datos['usuarioSesion']->roles  = $this->encuestaModelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        $this->datos['menuActivo'] = 'inicio';
    }

    public function index(){
        $id_profesor = $this->datos['usuarioSesion']->id_profesor;
        $id_rol      = $this->datos['usuarioSesion']->id_rol;

        // Estadísticas rápidas para el dashboard
        if($id_rol >= 200){
            $this->datos['encuestas'] = $this->encuestaModelo->getEncuestas([], 0, 5);
        } else {
            $this->datos['encuestas'] = $this->encuestaModelo->getEncuestasByProfesor($id_profesor, 0, 5);
        }

        $this->datos['cursos'] = $this->encuestaModelo->getCursosAcademicos();
        $this->vista('index', $this->datos);
    }
}
