<?php

class Inicio extends Controlador{

    private $incidenciaModelo;

    public function __construct(){

        Sesion::iniciarSesion($this->datos);

        $this->datos["menuActivo"] = "home";

        $this->incidenciaModelo = $this->modelo('IncidenciaModelo');

        $this->datos["usuarioSesion"]->roles = $this->incidenciaModelo->getRolesProfesor($this->datos["usuarioSesion"]->id_profesor);
        
        $this->datos["usuarioSesion"]->id_rol = obtenerRol($this->datos["usuarioSesion"]->roles);
       
        $this->datos['rolesPermitidos'] = [100,200,300];         // Definimos los roles que tendran acceso, en este caso todos
                                                            // Comprobamos si tiene privilegios                            
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol,$this->datos['rolesPermitidos'])) {
            echo "No tienes privilegios!!!";
            exit();
            // redireccionar('/');
        }

    }

    public function index(){
        
        $this->datos["incidenciasActivas"] = $this->incidenciaModelo->getIncidenciasActivas();

        foreach($this->datos["incidenciasActivas"] as $incidencia){
            $incidencia->acciones = $this->incidenciaModelo->getAccionesIncidencia($incidencia->id_incidencia);
        }

        $this->vista("index",$this->datos);
    }
}
