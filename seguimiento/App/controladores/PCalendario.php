<?php


class PCalendario extends Controlador{

    private $calendarioModelo;



    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        //$this->datos['rolesPermitidos'] = [10];          // Definimos los roles que tendran acceso
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->calendarioModelo = $this->modelo('CalendarioM');
    }




    public function index($id_modulo){

        // LECTIVO
        $this->datos['lectivo'] = $this->calendarioModelo->obtener_lectivo();
        $lectivo = $this->datos['lectivo'];
        $id_lectivo = $lectivo[0]->id_lectivo;

        // info del modulo
        $id_profe = $this->datos['usuarioSesion']->id_profesor;
        $this->datos['datos_modulo'] = $this->calendarioModelo->info_modulo($id_profe,$id_modulo, $id_lectivo);
        $info_modulo = $this->datos['datos_modulo'];

        // CALENDARIO DEL MODULO
        $this->datos['calendario']=$this->calendarioModelo->calendario_bueno_modulo($id_modulo,$id_lectivo);
     
        // BLOQUEAR ENLACES
        $this->datos['modulo'] = $this->calendarioModelo->obtener_modulos($id_profe, $id_lectivo);


        // BLOQUEAR ENLACES SI NO ESTA TODO OK
        $resultado = [];
        $modulo_profesor = $this->datos['datos_modulo'];
        $hay_temas = $this->calendarioModelo->hay_temas($modulo_profesor[0]->id_modulo);
        $hay_horas = $this->calendarioModelo->hay_horas($modulo_profesor[0]->id_modulo);
        $hay_seguimiento = $this->calendarioModelo->hay_seguimiento($modulo_profesor[0]->id_modulo);
        $hay_suma = $this->calendarioModelo->suma_temas($modulo_profesor[0]->id_modulo);
        $resultado[] = [
            'id_modulo' => $modulo_profesor[0]->id_modulo,
            'hay_temas' => $hay_temas[0]->hay_temas,
            'hay_horas' => $hay_horas[0]->hay_horas,
            'hay_seguimiento' => $hay_seguimiento[0]->hay_seguimiento,
            'hay_suma' => $hay_suma[0]->suma,
            'horas_totales_modulo' => $modulo_profesor[0]->horas_totales
        ];
        $this->datos['resultado'] = $resultado;


        // VISTA PRINCIPAL
        $this->vista('profesores/calendario',$this->datos);
    }




}