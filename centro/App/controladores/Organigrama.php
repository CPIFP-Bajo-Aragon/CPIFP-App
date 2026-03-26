<?php

class Organigrama extends Controlador {

    private $orgModelo;

    public function __construct() {
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);

        // Accesible para Jefes de dpto (30) y Equipo Directivo (50)
        $this->datos['rolesPermitidos'] = [30, 50];
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }

        $this->orgModelo = $this->modelo('OrganigramaM');
    }

    public function index() {

        // ── 1. Equipo directivo ──────────────────────────────
        $this->datos['equipo_directivo'] = $this->orgModelo->obtener_equipo_directivo();

        // ── 2 + 3. Departamentos de formación agrupados ──────
        //   Construimos: [ id_dep => [ 'info'=>..., 'jefes'=>[], 'profesores'=>[] ] ]
        $jefes      = $this->orgModelo->obtener_jefes_departamento();
        $profesores = $this->orgModelo->obtener_profesores();

        $departamentos = [];

        foreach ($jefes as $j) {
            $id = $j->id_departamento;
            if (!isset($departamentos[$id])) {
                $departamentos[$id] = [
                    'departamento'       => $j->departamento,
                    'departamento_corto' => $j->departamento_corto,
                    'jefes'              => [],
                    'profesores'         => [],
                    'tecnicos'           => [],
                ];
            }
            $departamentos[$id]['jefes'][] = $j->nombre_completo;
        }

        foreach ($profesores as $p) {
            $id = $p->id_departamento;
            if (!isset($departamentos[$id])) {
                $departamentos[$id] = [
                    'departamento'       => $p->departamento,
                    'departamento_corto' => $p->departamento_corto,
                    'jefes'              => [],
                    'profesores'         => [],
                    'tecnicos'           => [],
                ];
            }
            if ($p->id_rol == 40) {
                // Técnico (rol 40)
                if (!in_array($p->nombre_completo, $departamentos[$id]['tecnicos'])) {
                    $departamentos[$id]['tecnicos'][] = $p->nombre_completo;
                }
            } elseif ($p->id_rol != 30) {
                // Profesor normal (excluir jefes que ya están)
                if (!in_array($p->nombre_completo, $departamentos[$id]['profesores'])) {
                    $departamentos[$id]['profesores'][] = $p->nombre_completo;
                }
            }
        }

        // Ordenar por nombre de departamento
        uasort($departamentos, fn($a, $b) => strcmp($a['departamento'], $b['departamento']));

        $this->datos['departamentos'] = $departamentos;

        // ── 4. Tutores por grupo ─────────────────────────────
        $this->datos['tutores'] = $this->orgModelo->obtener_tutores_grupos();

        // ── Extra: estratégicos (calidad, IOPE…) ────────────
        $estrategicos_raw  = $this->orgModelo->obtener_estrategicos();
        $estrategicos      = [];
        foreach ($estrategicos_raw as $e) {
            $id = $e->id_departamento;
            if (!isset($estrategicos[$id])) {
                $estrategicos[$id] = [
                    'departamento'       => $e->departamento,
                    'departamento_corto' => $e->departamento_corto,
                    'miembros'           => [],
                ];
            }
            $estrategicos[$id]['miembros'][] = $e->nombre_completo;
        }
        $this->datos['estrategicos'] = $estrategicos;

        $this->datos['menuActivo'] = 'organigrama';
        $this->vista('organigrama', $this->datos);
    }
}
