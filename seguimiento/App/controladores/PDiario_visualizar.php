<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


class PDiario_visualizar extends Controlador{

    private $diarioModelo;



    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        //$this->datos['rolesPermitidos'] = [10];          // Definimos los roles que tendran acceso

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->diarioModelo = $this->modelo('DiarioM');
    }





    public function index($id_modulo){

        //LECTIVO
        $this->datos['lectivo']=$this->diarioModelo->obtener_lectivo();
        $lectivo=$this->datos['lectivo'];
        $id_lectivo=$this->datos['lectivo'][0]->id_lectivo;

        // info del modulo
        $id_profe = $this->datos['usuarioSesion']->id_profesor;
        $this->datos['datos_modulo']=$this->diarioModelo->info_modulo($id_profe,$id_modulo,$id_lectivo);

        // RECOGE TODOS LOS IDS DE SEGUIMIENTO PARA CADA EVALUACION DE UN MODULO CONCRETO
        $this->datos['evaluaciones']=$this->diarioModelo->obtener_id_seguimientos_evaluacion($id_modulo);
        $evaluaciones = $this->datos['evaluaciones'];
        $intervalos_evaluaciones = intervalos_evaluaciones($lectivo, $evaluaciones);

        // TODOS LOS TEMAS DEL MODULO
        $this->datos['temas']=$this->diarioModelo->temas_del_modulo($id_modulo);

        // DEVUELVE EL HORARIO DE LA SEMANA DEL MODULO
        $this->datos['horario_semana'] = $this->diarioModelo->obtener_horario_semana_modulo($id_modulo);

        $this->datos['suma_horas_temas'] = $this->diarioModelo->suma_horas_x_temas($id_modulo); // SUMA TODAS LAS HORAS IMPARTIDAS DE UN TEMA

        // CALENDARIO DEL MODULO
        $this->datos['calendario']=$this->diarioModelo->calendario_bueno_modulo($id_modulo,$id_lectivo);

        // OBTENER DIARIO
        $this->datos['diario']=$this->diarioModelo->obtener_diario($id_modulo);

        // para actualizar la pregunta 38 "HORAS DE DOCENCIA PREVISTAS" si cambia la fecha de evaluacion o el horario del modulo.....
        $this->diarioModelo->pregunta_38($id_modulo, $intervalos_evaluaciones);   

        $this->datos['modulo'] = $this->diarioModelo->obtener_modulos($id_profe, $id_lectivo);

        // BLOQUEAR ENLACES SI NO ESTA TODO OK
        $resultado = [];
        $modulo_profesor = $this->datos['datos_modulo'];
        $hay_temas = $this->diarioModelo->hay_temas($modulo_profesor[0]->id_modulo);
        $hay_horas = $this->diarioModelo->hay_horas($modulo_profesor[0]->id_modulo);
        $hay_seguimiento = $this->diarioModelo->hay_seguimiento($modulo_profesor[0]->id_modulo);
        $hay_suma = $this->diarioModelo->suma_temas($modulo_profesor[0]->id_modulo);
        $resultado[] = [
            'id_modulo' => $modulo_profesor[0]->id_modulo,
            'hay_temas' => $hay_temas[0]->hay_temas,
            'hay_horas' => $hay_horas[0]->hay_horas,
            'hay_seguimiento' => $hay_seguimiento[0]->hay_seguimiento,
            'hay_suma' => $hay_suma[0]->suma,
            'horas_totales_modulo' => $modulo_profesor[0]->horas_totales
        ];
        $this->datos['resultado'] = $resultado;


        $this->vista('profesores/diario_visualizar',$this->datos);

    }



}