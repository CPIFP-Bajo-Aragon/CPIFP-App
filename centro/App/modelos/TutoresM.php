<?php

class TutoresM {

    private $db;

    public function __construct() {
        $this->db = new Base;
    }


    // ════════════════════════════════════════════════════════
    // CURSOS agrupados por departamento y ciclo
    // ════════════════════════════════════════════════════════

    /**
     * Devuelve todos los cursos con su ciclo y departamento,
     * ordenados por departamento → ciclo → curso.
     */
    public function obtener_cursos_agrupados() {
        $this->db->query(
            "SELECT
                cu.id_curso,
                cu.curso,
                ci.id_ciclo,
                ci.ciclo,
                ci.ciclo_corto,
                d.id_departamento,
                d.departamento,
                d.departamento_corto
             FROM cpifp_curso cu
             JOIN cpifp_ciclos      ci ON ci.id_ciclo        = cu.id_ciclo
             JOIN cpifp_departamento d  ON d.id_departamento  = ci.id_departamento
             WHERE d.isFormacion = 1
             ORDER BY d.departamento, ci.ciclo, cu.curso;"
        );
        return $this->db->registros();
    }


    // ════════════════════════════════════════════════════════
    // TUTORES asignados a un curso
    // ════════════════════════════════════════════════════════

    /** Devuelve los tutores asignados a un curso concreto */
    public function obtener_tutores_curso(int $id_curso): array {
        $this->db->query(
            "SELECT p.id_profesor, p.nombre_completo, p.login
             FROM cpifp_curso_tutor ct
             JOIN cpifp_profesor p ON p.id_profesor = ct.id_profesor
             WHERE ct.id_curso = :id_curso
             ORDER BY p.nombre_completo;"
        );
        $this->db->bind(':id_curso', $id_curso);
        return $this->db->registros();
    }

    /**
     * Devuelve todos los tutores de todos los cursos en un solo query,
     * para evitar N+1 al renderizar la lista completa.
     * Resultado: array indexado por id_curso.
     */
    public function obtener_todos_tutores(): array {
        $this->db->query(
            "SELECT ct.id_curso, p.id_profesor, p.nombre_completo, p.login
             FROM cpifp_curso_tutor ct
             JOIN cpifp_profesor p ON p.id_profesor = ct.id_profesor
             ORDER BY ct.id_curso, p.nombre_completo;"
        );
        $rows = $this->db->registros();
        $map  = [];
        foreach ($rows as $r) {
            $map[$r->id_curso][] = $r;
        }
        return $map;
    }


    // ════════════════════════════════════════════════════════
    // PROFESORES disponibles para asignar como tutor
    // ════════════════════════════════════════════════════════

    /** Todos los profesores activos, ordenados por nombre */
    public function obtener_profesores_activos(): array {
        $this->db->query(
            "SELECT id_profesor, nombre_completo, login
             FROM cpifp_profesor
             WHERE activo = 1
             ORDER BY nombre_completo;"
        );
        return $this->db->registros();
    }


    // ════════════════════════════════════════════════════════
    // ASIGNAR / QUITAR tutor
    // ════════════════════════════════════════════════════════

    /** Asigna un tutor a un curso (ignora si ya existe) */
    public function asignar_tutor(int $id_curso, int $id_profesor): bool {
        $this->db->query(
            "INSERT IGNORE INTO cpifp_curso_tutor (id_curso, id_profesor)
             VALUES (:id_curso, :id_profesor);"
        );
        $this->db->bind(':id_curso',    $id_curso);
        $this->db->bind(':id_profesor', $id_profesor);
        return $this->db->execute();
    }

    /** Quita un tutor de un curso */
    public function quitar_tutor(int $id_curso, int $id_profesor): bool {
        $this->db->query(
            "DELETE FROM cpifp_curso_tutor
             WHERE id_curso = :id_curso AND id_profesor = :id_profesor;"
        );
        $this->db->bind(':id_curso',    $id_curso);
        $this->db->bind(':id_profesor', $id_profesor);
        return $this->db->execute();
    }

    /** Reemplaza todos los tutores de un curso de una vez (usado en el form POST) */
    public function sincronizar_tutores(int $id_curso, array $ids_profesores): void {
        // Borrar todos los tutores actuales del curso
        $this->db->query(
            "DELETE FROM cpifp_curso_tutor WHERE id_curso = :id_curso;"
        );
        $this->db->bind(':id_curso', $id_curso);
        $this->db->execute();

        // Insertar los nuevos
        foreach ($ids_profesores as $id_prof) {
            $id_prof = (int)$id_prof;
            if ($id_prof > 0) {
                $this->db->query(
                    "INSERT INTO cpifp_curso_tutor (id_curso, id_profesor)
                     VALUES (:id_curso, :id_profesor);"
                );
                $this->db->bind(':id_curso',    $id_curso);
                $this->db->bind(':id_profesor', $id_prof);
                $this->db->execute();
            }
        }
    }


    // ════════════════════════════════════════════════════════
    // INFO de un curso concreto
    // ════════════════════════════════════════════════════════

    public function info_curso(int $id_curso): ?object {
        $this->db->query(
            "SELECT cu.id_curso, cu.curso,
                    ci.ciclo, ci.ciclo_corto,
                    d.departamento, d.departamento_corto
             FROM cpifp_curso cu
             JOIN cpifp_ciclos       ci ON ci.id_ciclo       = cu.id_ciclo
             JOIN cpifp_departamento d  ON d.id_departamento = ci.id_departamento
             WHERE cu.id_curso = :id_curso;"
        );
        $this->db->bind(':id_curso', $id_curso);
        return $this->db->registro();
    }
}
