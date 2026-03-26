<?php

class Otras_encuestas extends Controlador {

    private $encuestaModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->encuestaModelo = $this->modelo('EncuestaModelo');
        $this->datos['usuarioSesion']->roles  = $this->encuestaModelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        $this->datos['menuActivo'] = 'otras_encuestas';
    }

    // ── Página principal: tipos + listado de encuestas creadas ────────────
    public function index(){
        $this->datos['tipos']            = $this->encuestaModelo->getTiposEncuestaOtras();
        $this->datos['evaluaciones']     = $this->encuestaModelo->getEvaluaciones();
        $this->datos['curso_actual']     = cursoAcademicoActual();
        $this->datos['evaluacion_actual']= $this->encuestaModelo->getEvaluacionActual();

        // Filtros desde GET
        $filtro = [
            'curso_academico'  => $_GET['curso']   ?? cursoAcademicoActual(),
            'id_tipo_encuesta' => $_GET['tipo']    ?? '',
            'trimestre'        => $_GET['eval']    ?? '',
            'activa'           => $_GET['estado']  ?? '',
        ];
        $this->datos['filtro'] = $filtro;
        $this->datos['lista']  = $this->encuestaModelo->getEncuestasOtras($filtro, 0, 999);
        $this->vista('otras_encuestas/index', $this->datos);
    }

    // ── Crear nueva encuesta (POST desde formulario inline) ───────────────
    public function nueva(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            redireccionar('/otras_encuestas');
        }

        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            echo "Sin privilegios."; exit();
        }

        $id = $this->encuestaModelo->addEncuestaOtra($_POST, $this->datos['usuarioSesion']->id_profesor);
        if($id){
            redireccionar('/otras_encuestas/ver/'.$id);
        } else {
            redireccionar('/otras_encuestas');
        }
    }

    // ── Ver resultados ─────────────────────────────────────────────────────
    public function ver($id_encuesta){
        $encuesta = $this->encuestaModelo->getEncuesta($id_encuesta);
        if(!$encuesta){ redireccionar('/otras_encuestas'); }

        // Preparar enlace público
        $datos_encuesta = $encuesta;
        $enlace_publico = RUTA_CPIFP . '/encuestas/responder/' . $encuesta->token_publico;

        $this->datos['encuesta']              = $encuesta;
        $this->datos['enlace_publico']        = $enlace_publico;
        $this->datos['preguntas']             = $this->encuestaModelo->getPreguntasEncuesta($id_encuesta);
        $this->datos['resumen']               = $this->encuestaModelo->getResumenEncuesta($id_encuesta);
        $this->datos['distribucion']          = $this->encuestaModelo->getDistribucionPuntuaciones($id_encuesta);
        $this->datos['distribucion_opciones'] = $this->encuestaModelo->getDistribucionOpciones($id_encuesta);
        $this->datos['comentarios']           = $this->encuestaModelo->getComentariosEncuesta($id_encuesta);
        $this->datos['puedeEliminarForzado']  = $this->encuestaModelo->puedeEliminarForzado(
                                                    $this->datos['usuarioSesion']->roles);
        $this->vista('otras_encuestas/ver', $this->datos);
    }

    // ── Eliminar (solo sin respuestas) ─────────────────────────────────────
    public function eliminar(){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            $this->vistaApi(['ok'=>false,'msg'=>'Sin privilegios']); return;
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $result = $this->encuestaModelo->delEncuestaSegura((int)$_POST['id_encuesta']);
            $this->vistaApi([
                'ok'  => $result === 'ok',
                'msg' => $result === 'ok' ? 'Encuesta eliminada.'
                       : ($result === 'tiene_respuestas' ? 'No se puede eliminar: tiene respuestas.'
                                                        : 'Error al eliminar.'),
            ]);
        }
    }

    // ── Eliminar forzado (con respuestas) — solo calidad / directivo ──────
    public function eliminar_forzado(){
        $puede = $this->encuestaModelo->puedeEliminarForzado(
                     $this->datos['usuarioSesion']->roles);
        if(!$puede){
            $this->vistaApi(['ok'=>false,'msg'=>'Sin privilegios para eliminación forzada.']);
            return;
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $result = $this->encuestaModelo->delEncuestaForzado((int)$_POST['id_encuesta']);
            $this->vistaApi([
                'ok'  => $result === 'ok',
                'msg' => $result === 'ok' ? 'Encuesta y todos sus datos eliminados.'
                                          : 'Error al eliminar.',
            ]);
        }
    }


    public function cerrar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST')
            $this->vistaApi($this->encuestaModelo->cerrarEncuesta((int)$_POST['id_encuesta']));
    }
    public function abrir(){
        if($_SERVER['REQUEST_METHOD'] === 'POST')
            $this->vistaApi($this->encuestaModelo->abrirEncuesta((int)$_POST['id_encuesta']));
    }
}
