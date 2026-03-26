<?php

/**
 * Gestor de tipos de encuesta y plantillas de preguntas.
 * - CRUD de en_tipo_encuesta
 * - CRUD de en_plantilla_pregunta filtrado por tipo
 * Rol 300: puede crear/eliminar tipos + editar preguntas
 * Rol 200: solo puede editar preguntas de plantilla
 */
class Gestor_encuestas extends Controlador {

    private $encuestaModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->encuestaModelo = $this->modelo('EncuestaModelo');
        $this->datos['usuarioSesion']->roles  = $this->encuestaModelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        $this->datos['menuActivo'] = 'gestor';

        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            echo "No tienes privilegios."; exit();
        }
    }

    // ── Vista principal ──────────────────────────────────────────────────
    public function index(){
        $id_tipo = (int)($_GET['tipo'] ?? 1);
        $this->datos['tipos']     = $this->encuestaModelo->getTiposEncuesta();
        $this->datos['tipo_sel']  = $id_tipo;
        $this->datos['tipo_info'] = $this->encuestaModelo->getTipoEncuesta($id_tipo);
        $this->datos['preguntas'] = $this->encuestaModelo->getAllPlantillaPreguntas($id_tipo);
        $this->vista('gestor_encuestas/index', $this->datos);
    }

    // ── AJAX: preguntas de un tipo ───────────────────────────────────────
    public function get_preguntas(){
        $id_tipo   = (int)($_GET['tipo'] ?? 1);
        $tipo      = $this->encuestaModelo->getTipoEncuesta($id_tipo);
        $preguntas = $this->encuestaModelo->getAllPlantillaPreguntas($id_tipo);
        $this->vistaApi([
            'tipo'        => $tipo->tipo_encuesta ?? '',
            'descripcion' => $tipo->descripcion   ?? '',
            'bloqueado'   => (int)$id_tipo === 1,
            'preguntas'   => $preguntas,
        ]);
    }

    // ── CRUD Tipos ───────────────────────────────────────────────────────
    public function add_tipo(){
        if(!$this->_esAdmin()) return;
        if($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $id = $this->encuestaModelo->addTipoEncuesta($_POST);
        $this->vistaApi([
            'ok'           => (bool)$id,
            'id'           => $id,
            'tipo_encuesta'=> trim($_POST['tipo_encuesta']),
            'descripcion'  => trim($_POST['descripcion'] ?? ''),
        ]);
    }

    public function edit_tipo(){
        if(!$this->_esAdmin()) return;
        if($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $ok = $this->encuestaModelo->editTipoEncuesta($_POST);
        $this->vistaApi(['ok' => (bool)$ok]);
    }

    public function del_tipo(){
        if(!$this->_esAdmin()) return;
        if($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $result = $this->encuestaModelo->delTipoEncuesta((int)$_POST['id_tipo_encuesta']);
        $this->vistaApi([
            'ok'  => $result === 'ok',
            'msg' => $result === 'ok'              ? 'Tipo eliminado.'
                   : ($result === 'tiene_encuestas' ? 'No se puede eliminar: tiene encuestas creadas.'
                                                    : 'Error al eliminar.'),
        ]);
    }

    // ── CRUD Preguntas de plantilla ───────────────────────────────────────
    public function add_pregunta(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $ok = $this->encuestaModelo->addPlantillaPregunta($_POST);
        if($ok){
            // Devolver la última pregunta insertada para actualizar la fila en la UI
            $prgs  = $this->encuestaModelo->getAllPlantillaPreguntas($_POST['id_tipo_encuesta']);
            $ultima = end($prgs);
            $this->vistaApi(['ok' => true, 'id_plantilla_pregunta' => $ultima->id_plantilla_pregunta ?? 0]);
        } else {
            $this->vistaApi(['ok' => false]);
        }
    }

    public function edit_pregunta(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $ok = $this->encuestaModelo->editPlantillaPregunta($_POST);
        $this->vistaApi(['ok' => (bool)$ok]);
    }

    public function del_pregunta(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $ok = $this->encuestaModelo->delPlantillaPregunta($_POST['id_plantilla_pregunta']);
        $this->vistaApi(['ok' => (bool)$ok,
                         'msg'=> $ok ? '' : 'Error al eliminar.']);
    }

    private function _esAdmin(){
        if($this->datos['usuarioSesion']->id_rol < 200){
            $this->vistaApi(['ok' => false, 'msg' => 'Se requiere rol administrador.']);
            return false;
        }
        return true;
    }
}
