<?php

/**
 * Gestión de tipos de encuesta y sus plantillas de preguntas.
 * Solo rol 300 (equipo directivo / admin).
 */
class Preguntas extends Controlador {

    private $encuestaModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->encuestaModelo = $this->modelo('EncuestaModelo');
        $this->datos['usuarioSesion']->roles  = $this->encuestaModelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        $this->datos['menuActivo'] = 'preguntas';

        $this->datos['rolesPermitidos'] = [300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            echo "No tienes privilegios para editar tipos y plantillas de preguntas.";
            exit();
        }
    }

    // ─── Vista principal: lista de tipos + plantilla del tipo seleccionado ──
    public function index(){
        $this->datos['tipos']     = $this->encuestaModelo->getTiposEncuesta();
        $this->datos['tipo_sel']  = (int)($_GET['id_tipo_encuesta'] ?? 1);
        $this->datos['tipo_info'] = $this->encuestaModelo->getTipoEncuesta($this->datos['tipo_sel']);
        $this->datos['preguntas'] = $this->encuestaModelo->getAllPlantillaPreguntas($this->datos['tipo_sel']);
        $this->vista('preguntas/index', $this->datos);
    }

    // ─── CRUD Tipos ────────────────────────────────────────────────────────
    public function add_tipo(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id = $this->encuestaModelo->addTipoEncuesta($_POST);
            $this->vistaApi(['ok' => (bool)$id, 'id' => $id]);
        }
    }

    public function edit_tipo(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $ok = $this->encuestaModelo->editTipoEncuesta($_POST);
            $this->vistaApi(['ok' => (bool)$ok]);
        }
    }

    public function del_tipo(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $result = $this->encuestaModelo->delTipoEncuesta((int)$_POST['id_tipo_encuesta']);
            if($result === 'ok')
                $this->vistaApi(['ok' => true,  'msg' => 'Tipo eliminado.']);
            elseif($result === 'tiene_encuestas')
                $this->vistaApi(['ok' => false, 'msg' => 'No se puede eliminar: tiene encuestas creadas.']);
            else
                $this->vistaApi(['ok' => false, 'msg' => 'Error al eliminar.']);
        }
    }

    // ─── AJAX: preguntas de un tipo (para recargar la plantilla) ──────────
    public function get_preguntas(){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $id_tipo  = (int)($_GET['id_tipo_encuesta'] ?? 1);
            $tipo     = $this->encuestaModelo->getTipoEncuesta($id_tipo);
            $preguntas = $this->encuestaModelo->getAllPlantillaPreguntas($id_tipo);
            $this->vistaApi([
                'tipo'      => $tipo->tipo_encuesta ?? '',
                'preguntas' => $preguntas,
            ]);
        }
    }

    // ─── CRUD Preguntas de plantilla ───────────────────────────────────────
    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $ok = $this->encuestaModelo->addPlantillaPregunta($_POST);
            $this->vistaApi((bool)$ok);
        }
    }

    public function edit(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $ok = $this->encuestaModelo->editPlantillaPregunta($_POST);
            $this->vistaApi((bool)$ok);
        }
    }

    public function del(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $ok = $this->encuestaModelo->delPlantillaPregunta($_POST['id_plantilla_pregunta']);
            $this->vistaApi((bool)$ok);
        }
    }
}
