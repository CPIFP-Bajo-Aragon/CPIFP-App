<?php

class Indicadores extends Controlador{

    private $indicadorModelo;


    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
       // $this->datos['rolesPermitidos'] = [50];          // Definimos los roles que tendran acceso
        
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->indicadorModelo = $this->modelo('IndicadorM');

    }




/**********************************************************************************************/
/**********************************************************************************************/
/*********************************** INDICADORES **********************************************/
/**********************************************************************************************/
/**********************************************************************************************/

    public function index(){
        $this->datos['lectivo'] = $this->indicadorModelo->obtener_lectivo(); // curso actual
        $this->datos['indicadores'] = $this->indicadorModelo->obtener_indicadores(); // todos indicadores
        $this->vista('direccion/indicadores/indicadores', $this->datos);
    }




/**********************************************************************************************/
/**********************************************************************************************/
/*********************************** PREGUNTAS  ***********************************************/
/**********************************************************************************************/
/**********************************************************************************************/


public function preguntas($id_indicador){
    $this->datos['indicador'] = $this->indicadorModelo->obtener_indicador($id_indicador); // indicador concreto
    $this->datos['preguntas'] = $this->indicadorModelo->obtener_preguntas($id_indicador); // preguntas indicador concreto
    $this->vista('direccion/indicadores/preguntas', $this->datos);
}





}





