<?php

class Tutores extends Controlador {

    private $tutoresModelo;

    public function __construct() {
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);

        // Solo el Equipo Directivo (rol 50) puede gestionar tutores
        $this->datos['rolesPermitidos'] = [50];
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }

        $this->tutoresModelo = $this->modelo('TutoresM');
    }


    // ════════════════════════════════════════════════════════
    // INDEX — listado de todos los cursos con sus tutores
    // ════════════════════════════════════════════════════════

    public function index() {
        // Todos los cursos con su ciclo y departamento
        $cursos_planos = $this->tutoresModelo->obtener_cursos_agrupados();

        // Todos los tutores asignados en un solo query → map[id_curso]
        $tutores_map = $this->tutoresModelo->obtener_todos_tutores();

        // Agrupar cursos por departamento → ciclo para la vista
        $agrupado = [];
        foreach ($cursos_planos as $c) {
            $dep   = $c->id_departamento;
            $ciclo = $c->id_ciclo;
            if (!isset($agrupado[$dep])) {
                $agrupado[$dep] = [
                    'departamento'       => $c->departamento,
                    'departamento_corto' => $c->departamento_corto,
                    'ciclos'             => [],
                ];
            }
            if (!isset($agrupado[$dep]['ciclos'][$ciclo])) {
                $agrupado[$dep]['ciclos'][$ciclo] = [
                    'ciclo'       => $c->ciclo,
                    'ciclo_corto' => $c->ciclo_corto,
                    'cursos'      => [],
                ];
            }
            $agrupado[$dep]['ciclos'][$ciclo]['cursos'][] = [
                'id_curso' => $c->id_curso,
                'curso'    => $c->curso,
                'tutores'  => $tutores_map[$c->id_curso] ?? [],
            ];
        }

        $this->datos['agrupado']   = $agrupado;
        $this->datos['menuActivo'] = 'tutores';
        $this->vista('tutores', $this->datos);
    }


    // ════════════════════════════════════════════════════════
    // EDITAR — formulario de asignación de tutores a un curso
    // ════════════════════════════════════════════════════════

    public function editar($id_curso) {
        $id_curso = (int)$id_curso;
        $curso    = $this->tutoresModelo->info_curso($id_curso);

        if (!$curso) {
            redireccionar('/tutores');
        }

        $this->datos['curso']       = $curso;
        $this->datos['profesores']  = $this->tutoresModelo->obtener_profesores_activos();
        $this->datos['asignados']   = $this->tutoresModelo->obtener_tutores_curso($id_curso);
        $this->datos['menuActivo']  = 'tutores';
        $this->vista('tutores_editar', $this->datos);
    }


    // ════════════════════════════════════════════════════════
    // GUARDAR — recibe el POST del formulario de edición
    // ════════════════════════════════════════════════════════

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redireccionar('/tutores');
        }

        $id_curso      = (int)($_POST['id_curso'] ?? 0);
        $ids_profesores = isset($_POST['tutores']) ? (array)$_POST['tutores'] : [];

        if (!$id_curso) {
            redireccionar('/tutores');
        }

        $this->tutoresModelo->sincronizar_tutores($id_curso, $ids_profesores);

        echo "<script>
                alert('Tutores actualizados correctamente.');
                window.location.href = '" . RUTA_URL . "/tutores';
              </script>";
    }


    // ════════════════════════════════════════════════════════
    // AJAX — quitar tutor individual (desde el listado principal)
    // ════════════════════════════════════════════════════════

    public function ajax_quitar() {
        header('Content-Type: application/json; charset=utf-8');

        $id_curso    = (int)($_POST['id_curso']    ?? 0);
        $id_profesor = (int)($_POST['id_profesor'] ?? 0);

        if (!$id_curso || !$id_profesor) {
            echo json_encode(['ok' => false, 'msg' => 'Datos incompletos']);
            return;
        }

        $ok = $this->tutoresModelo->quitar_tutor($id_curso, $id_profesor);
        echo json_encode(['ok' => $ok]);
    }
}
