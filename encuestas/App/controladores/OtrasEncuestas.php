<?php

class OtrasEncuestas extends Controlador {

    private $encuestaModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->encuestaModelo = $this->modelo('EncuestaModelo');
        $this->datos['usuarioSesion']->roles  = $this->encuestaModelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        $this->datos['menuActivo'] = 'otras_encuestas';

        $this->datos['rolesPermitidos'] = [100, 200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            echo "No tienes privilegios."; exit();
        }
    }

    public function index($pagina = 0){
        $filtro    = ['curso_academico' => cursoAcademicoActual()];
        $tamPagina = TAM_PAGINA;

        $this->datos['lista']        = $this->encuestaModelo->getEncuestasOtras($filtro, $pagina, $tamPagina);
        $this->datos['cursos']       = $this->encuestaModelo->getCursosAcademicos();
        $this->datos['tipos']        = $this->encuestaModelo->getTiposEncuestaOtras();
        $this->datos['filtro']       = $filtro;
        $this->datos['tamPagina']    = $tamPagina;
        $this->datos['paginaActual'] = $pagina;
        $this->vista('otras_encuestas/index', $this->datos);
    }

    public function filtro($pagina = 0){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $filtro = $_GET;
            if(empty($filtro['curso_academico'])){
                $filtro['curso_academico'] = cursoAcademicoActual();
            }
            $opciones  = [20, 50, 100, 0];
            $tamPagina = isset($filtro['tam_pagina']) && in_array((int)$filtro['tam_pagina'], $opciones)
                         ? (int)$filtro['tam_pagina'] : TAM_PAGINA;
            $tamReal   = ($tamPagina === 0) ? 999999 : $tamPagina;

            $this->datos['lista']        = $this->encuestaModelo->getEncuestasOtras($filtro, $pagina, $tamReal);
            $this->datos['cursos']       = $this->encuestaModelo->getCursosAcademicos();
            $this->datos['tipos']        = $this->encuestaModelo->getTiposEncuestaOtras();
            $this->datos['filtro']       = $filtro;
            $this->datos['tamPagina']    = $tamPagina;
            $this->datos['paginaActual'] = $pagina;
            $this->vista('otras_encuestas/index', $this->datos);
        }
    }

    // Nueva encuesta (tipo != 1)
    public function nueva(){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            echo "Sin privilegios."; exit();
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $datos = $_POST;
            $id = $this->encuestaModelo->addEncuestaOtra($datos, $this->datos['usuarioSesion']->id_profesor);
            if($id){
                redireccionar('/otras_encuestas/ver/'.$id);
            } else {
                $this->datos['tipos']            = $this->encuestaModelo->getTiposEncuestaOtras();
                $this->datos['evaluaciones']     = $this->encuestaModelo->getEvaluaciones();
                $this->datos['curso_actual']     = cursoAcademicoActual();
                $this->datos['evaluacion_actual']= $this->encuestaModelo->getEvaluacionActual();
                $this->datos['error']            = 1;
                $this->datos['post']             = $_POST;
                $this->vista('otras_encuestas/nueva', $this->datos);
            }
        } else {
            $this->datos['tipos']            = $this->encuestaModelo->getTiposEncuestaOtras();
            $this->datos['evaluaciones']     = $this->encuestaModelo->getEvaluaciones();
            $this->datos['curso_actual']     = cursoAcademicoActual();
            $this->datos['evaluacion_actual']= $this->encuestaModelo->getEvaluacionActual();
            $this->datos['error']            = 0;
            $this->datos['post']             = [];
            $this->vista('otras_encuestas/nueva', $this->datos);
        }
    }

    public function ver($id_encuesta){
        $encuesta = $this->encuestaModelo->getEncuesta($id_encuesta);
        if(!$encuesta){ redireccionar('/otras_encuestas'); }
        $this->datos['encuesta']    = $encuesta;
        $this->datos['preguntas']   = $this->encuestaModelo->getPreguntasEncuesta($id_encuesta);
        $this->datos['resumen']     = $this->encuestaModelo->getResumenEncuesta($id_encuesta);
        $this->datos['comentarios'] = $this->encuestaModelo->getComentariosEncuesta($id_encuesta);
        $this->vista('otras_encuestas/ver', $this->datos);
    }

    public function eliminar_seguro(){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            $this->vistaApi(['ok'=>false,'msg'=>'Sin privilegios']); return;
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $result = $this->encuestaModelo->delEncuestaSegura((int)$_POST['id_encuesta']);
            if($result === 'ok')
                $this->vistaApi(['ok'=>true,  'msg'=>'Encuesta eliminada.']);
            elseif($result === 'tiene_respuestas')
                $this->vistaApi(['ok'=>false, 'msg'=>'No se puede eliminar: tiene respuestas.']);
            else
                $this->vistaApi(['ok'=>false, 'msg'=>'Error al eliminar.']);
        }
    }
}
