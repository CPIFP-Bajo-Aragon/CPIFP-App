<?php

class Inicio extends Controlador{

    private $asesoriaModelo;

    public function __construct(){

        Sesion::iniciarSesion($this->datos);

        $this->datos["menuActivo"] = "home";

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
        
        $this->datos["asesoriasActivas"] = $this->asesoriaModelo->getAsesoriasActivas();

        foreach($this->datos["asesoriasActivas"] as $asesoria){
            $asesoria->acciones = $this->asesoriaModelo->getAccionesAsesoria($asesoria->id_asesoria);
        }

        $this->vista("index",$this->datos);
    }
}
