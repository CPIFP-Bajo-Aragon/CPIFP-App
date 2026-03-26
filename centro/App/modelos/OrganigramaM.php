<?php

class OrganigramaM {

    private $db;

    public function __construct() {
        $this->db = new Base;
    }

    // ════════════════════════════════════════════════════════
    // 1. Equipo directivo (rol 50, dpto DIR)
    // ════════════════════════════════════════════════════════
    public function obtener_equipo_directivo(): array {
        $this->db->query(
            "SELECT p.nombre_completo, d.departamento, d.departamento_corto
             FROM cpifp_profesor p
             JOIN cpifp_profesor_departamento pd ON pd.id_profesor    = p.id_profesor
             JOIN cpifp_departamento          d  ON d.id_departamento = pd.id_departamento
             WHERE pd.id_rol = 50
               AND d.departamento_corto = 'DIR'
               AND p.activo = 1
             ORDER BY p.nombre_completo;"
        );
        return $this->db->registros();
    }

    // ════════════════════════════════════════════════════════
    // 2. Jefes de departamento (rol 30) — solo dpts. formación
    // ════════════════════════════════════════════════════════
    public function obtener_jefes_departamento(): array {
        $this->db->query(
            "SELECT p.nombre_completo, d.id_departamento,
                    d.departamento, d.departamento_corto
             FROM cpifp_profesor p
             JOIN cpifp_profesor_departamento pd ON pd.id_profesor    = p.id_profesor
             JOIN cpifp_departamento          d  ON d.id_departamento = pd.id_departamento
             WHERE pd.id_rol = 30
               AND d.isFormacion = 1
               AND p.activo = 1
             ORDER BY d.departamento, p.nombre_completo;"
        );
        return $this->db->registros();
    }

    // ════════════════════════════════════════════════════════
    // 3. Todos los profesores con dpto y rol
    //    (solo dpts. isFormacion = 1)
    // ════════════════════════════════════════════════════════
    public function obtener_profesores(): array {
        $this->db->query(
            "SELECT p.nombre_completo, d.id_departamento,
                    d.departamento, d.departamento_corto,
                    pd.id_rol, r.rol
             FROM cpifp_profesor p
             JOIN cpifp_profesor_departamento pd ON pd.id_profesor    = p.id_profesor
             JOIN cpifp_departamento          d  ON d.id_departamento = pd.id_departamento
             LEFT JOIN cpifp_rol              r  ON r.id_rol          = pd.id_rol
             WHERE d.isFormacion = 1
               AND p.activo = 1
             ORDER BY d.departamento, pd.id_rol DESC, p.nombre_completo;"
        );
        return $this->db->registros();
    }

    // ════════════════════════════════════════════════════════
    // 4. Tutores asignados por grupo (requiere cpifp_curso_tutor)
    // ════════════════════════════════════════════════════════
    public function obtener_tutores_grupos(): array {
        try {
            $this->db->query(
                "SELECT p.nombre_completo, cu.curso,
                        ci.ciclo, ci.ciclo_corto,
                        dep.id_departamento, dep.departamento, dep.departamento_corto
                 FROM cpifp_curso_tutor ct
                 JOIN cpifp_profesor     p   ON p.id_profesor      = ct.id_profesor
                 JOIN cpifp_curso        cu  ON cu.id_curso         = ct.id_curso
                 JOIN cpifp_ciclos       ci  ON ci.id_ciclo         = cu.id_ciclo
                 JOIN cpifp_departamento dep ON dep.id_departamento = ci.id_departamento
                 ORDER BY dep.departamento, ci.ciclo, cu.curso, p.nombre_completo;"
            );
            return $this->db->registros();
        } catch (Exception $e) {
            return [];
        }
    }

    // ════════════════════════════════════════════════════════
    // Extra: departamentos estratégicos (isFormacion=0, excl. DIR)
    // ════════════════════════════════════════════════════════
    public function obtener_estrategicos(): array {
        $this->db->query(
            "SELECT p.nombre_completo, d.id_departamento,
                    d.departamento, d.departamento_corto, pd.id_rol
             FROM cpifp_profesor p
             JOIN cpifp_profesor_departamento pd ON pd.id_profesor    = p.id_profesor
             JOIN cpifp_departamento          d  ON d.id_departamento = pd.id_departamento
             WHERE d.isFormacion = 0
               AND d.departamento_corto NOT IN ('DIR')
               AND p.activo = 1
             ORDER BY d.departamento, p.nombre_completo;"
        );
        return $this->db->registros();
    }
}
