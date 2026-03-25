<?php


class PSeguimiento extends Controlador{

    private $profeModelo;



    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        //$this->datos['rolesPermitidos'] = [10];          // Definimos los roles que tendran acceso
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->profeModelo = $this->modelo('ProfesorM');
    }


    

    public function index(){

        // LECTIVO ACTUAL
        $this->datos['lectivo'] = $this->profeModelo->obtener_lectivo();
        $id_profe = $this->datos['usuarioSesion']->id_profesor;


        if(!empty( $this->datos['lectivo'])){

            $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;       
            $this->datos['modulo'] = $this->profeModelo->obtener_modulos($id_profe, $id_lectivo);
            $this->datos['codigo_verificacion'] = $this->profeModelo->codigo_verificacion($id_profe, $id_lectivo);
            $this->datos['tiene_programacion'] = $this->profeModelo->tiene_programacion($id_profe, $id_lectivo);

            
                // funcion para bloquear enlaces si no hay horas o temas
                $resultado = [];
                $modulosProfesor = $this->datos['modulo'];

                foreach ($modulosProfesor as $modulo) {

                    $id_modulo_profesor = $modulo->id_modulo;

                    $hay_temas = $this->profeModelo->hay_temas($id_modulo_profesor);
                    $hay_horas = $this->profeModelo->hay_horas($id_modulo_profesor);
                    $hay_seguimiento = $this->profeModelo->hay_seguimiento($id_modulo_profesor);
                    $hay_suma = $this->profeModelo->suma_temas($id_modulo_profesor);

                    $resultado[] = [
                        'id_modulo' => $id_modulo_profesor,
                        'hay_temas' => $hay_temas[0]->hay_temas,
                        'hay_horas' => $hay_horas[0]->hay_horas, 
                        'hay_seguimiento' => $hay_seguimiento[0]->hay_seguimiento,
                        'hay_suma' => $hay_suma[0]->suma,
                        'horas_totales_modulo' => $modulo->horas_totales
                    ];

                }

                $this->datos['resultado'] =  $resultado;


        } else {

            $id_lectivo = '';
            $this->datos['modulo'] = [];
            $this->datos['resultado'] = [];

        }

        $this->vista('profesores/seguimiento',$this->datos);
    }





}