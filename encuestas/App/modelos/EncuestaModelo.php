<?php

class EncuestaModelo {
    private $db;

    public function __construct(){
        $this->db = new Base;
    }

    // ─────────────────────────────────────────────────────────────────
    // ROLES / USUARIOS
    // ─────────────────────────────────────────────────────────────────
    public function getRolesProfesor($id_profesor){
        $this->db->query("SELECT *
                            FROM cpifp_profesor_departamento
                                NATURAL JOIN cpifp_rol
                                NATURAL JOIN cpifp_departamento
                           WHERE id_profesor = :id_profesor");
        $this->db->bind(':id_profesor', $id_profesor);
        return $this->db->registros();
    }

    // ─────────────────────────────────────────────────────────────────
    // TIPOS DE ENCUESTA
    // ─────────────────────────────────────────────────────────────────
    public function getTiposEncuesta(){
        $this->db->query("SELECT t.*,
                                 COUNT(p.id_plantilla_pregunta) AS total_preguntas
                            FROM en_tipo_encuesta t
                            LEFT JOIN en_plantilla_pregunta p ON p.id_tipo_encuesta = t.id_tipo_encuesta
                           GROUP BY t.id_tipo_encuesta
                           ORDER BY t.id_tipo_encuesta");
        return $this->db->registros();
    }

    /** Solo los tipos que NO son de alumnos (id != 1) */
    public function getTiposEncuestaOtras(){
        $this->db->query("SELECT * FROM en_tipo_encuesta WHERE id_tipo_encuesta != 1 ORDER BY id_tipo_encuesta");
        return $this->db->registros();
    }

    public function getTipoEncuesta($id){
        $this->db->query("SELECT * FROM en_tipo_encuesta WHERE id_tipo_encuesta = :id");
        $this->db->bind(':id', $id);
        return $this->db->registro();
    }

    public function addTipoEncuesta($datos){
        $this->db->query("INSERT INTO en_tipo_encuesta (tipo_encuesta, descripcion)
                           VALUES (:nombre, :desc)");
        $this->db->bind(':nombre', trim($datos['tipo_encuesta']));
        $this->db->bind(':desc',   trim($datos['descripcion'] ?? ''));
        return $this->db->executeLastId();
    }

    public function editTipoEncuesta($datos){
        $this->db->query("UPDATE en_tipo_encuesta
                             SET tipo_encuesta = :nombre, descripcion = :desc
                           WHERE id_tipo_encuesta = :id");
        $this->db->bind(':nombre', trim($datos['tipo_encuesta']));
        $this->db->bind(':desc',   trim($datos['descripcion'] ?? ''));
        $this->db->bind(':id',     $datos['id_tipo_encuesta']);
        return $this->db->execute();
    }

    public function delTipoEncuesta($id){
        // No borrar el tipo 1 (alumnos) nunca
        if((int)$id === 1) return false;
        // Solo se puede borrar si no tiene encuestas creadas
        $this->db->query("SELECT COUNT(*) AS total FROM en_encuesta WHERE id_tipo_encuesta = :id");
        $this->db->bind(':id', $id);
        if($this->db->registro()->total > 0) return 'tiene_encuestas';

        // Borrar preguntas de plantilla primero
        $this->db->query("DELETE FROM en_plantilla_pregunta WHERE id_tipo_encuesta = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();

        $this->db->query("DELETE FROM en_tipo_encuesta WHERE id_tipo_encuesta = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute() ? 'ok' : 'error';
    }

    /**
     * Crear encuesta de tipo != 1.
     * Completamente anónima: sin profesor_modulo, sin empresa, sin datos personales.
     */
    public function addEncuestaOtra($datos, $id_creador){
        $token = generarToken();
        $this->db->query("INSERT INTO en_encuesta
                            (id_tipo_encuesta, titulo, descripcion, curso_academico,
                             trimestre, fecha_inicio, fecha_fin, activa,
                             token_publico, mostrar_mejor_peor, mostrar_observaciones,
                             creada_por, fecha_creacion)
                           VALUES
                            (:tipo, :titulo, :desc, :curso,
                             :trimestre, :fecha_ini, :fecha_fin, 1,
                             :token, :mejor_peor, :observaciones,
                             :creador, NOW())");
        $this->db->bind(':tipo',          $datos['id_tipo_encuesta']);
        $this->db->bind(':titulo',        trim($datos['titulo']));
        $this->db->bind(':desc',          trim($datos['descripcion'] ?? ''));
        $this->db->bind(':curso',         $datos['curso_academico']);
        $this->db->bind(':trimestre',     !empty($datos['trimestre']) ? $datos['trimestre'] : null);
        $this->db->bind(':fecha_ini',     !empty($datos['fecha_inicio']) ? $datos['fecha_inicio'] : null);
        $this->db->bind(':fecha_fin',     !empty($datos['fecha_fin'])    ? $datos['fecha_fin']    : null);
        $this->db->bind(':token',         $token);
        $this->db->bind(':mejor_peor',    isset($datos['mostrar_mejor_peor'])    ? 1 : 0);
        $this->db->bind(':observaciones', isset($datos['mostrar_observaciones']) ? 1 : 0);
        $this->db->bind(':creador',       $id_creador);
        $id = $this->db->executeLastId();

        if($id){
            $preguntas = $this->getPlantillaPreguntas($datos['id_tipo_encuesta']);
            foreach($preguntas as $p){
                $this->db->query("INSERT INTO en_pregunta
                                    (id_encuesta, pregunta, orden, tipo_respuesta, opciones_json)
                                   VALUES (:id, :preg, :orden, :tipo_resp, :opciones)");
                $this->db->bind(':id',        $id);
                $this->db->bind(':preg',      $p->pregunta);
                $this->db->bind(':orden',     $p->orden);
                $this->db->bind(':tipo_resp', $p->tipo_respuesta ?? 'puntuacion');
                $this->db->bind(':opciones',  $p->opciones_json ?? null);
                $this->db->execute();
            }
        }
        return $id;
    }

    // ─────────────────────────────────────────────────────────────────
    // PLANTILLA DE PREGUNTAS
    // ─────────────────────────────────────────────────────────────────
    public function getPlantillaPreguntas($id_tipo_encuesta){
        $this->db->query("SELECT * FROM en_plantilla_pregunta
                           WHERE id_tipo_encuesta = :id_tipo AND activo = 1
                           ORDER BY orden");
        $this->db->bind(':id_tipo', $id_tipo_encuesta);
        return $this->db->registros();
    }

    public function getAllPlantillaPreguntas($id_tipo_encuesta){
        $this->db->query("SELECT * FROM en_plantilla_pregunta
                           WHERE id_tipo_encuesta = :id_tipo
                           ORDER BY orden");
        $this->db->bind(':id_tipo', $id_tipo_encuesta);
        return $this->db->registros();
    }

    public function addPlantillaPregunta($datos){
        $opciones = !empty($datos['opciones_json']) ? $datos['opciones_json'] : null;
        $this->db->query("INSERT INTO en_plantilla_pregunta
                            (id_tipo_encuesta, orden, pregunta, tipo_respuesta, opciones_json, activo)
                           VALUES (:id_tipo, :orden, :pregunta, :tipo_resp, :opciones, 1)");
        $this->db->bind(':id_tipo',   $datos['id_tipo_encuesta']);
        $this->db->bind(':orden',     $datos['orden']);
        $this->db->bind(':pregunta',  trim($datos['pregunta']));
        $this->db->bind(':tipo_resp', $datos['tipo_respuesta'] ?? 'puntuacion');
        $this->db->bind(':opciones',  $opciones);
        return $this->db->execute();
    }

    public function editPlantillaPregunta($datos){
        $opciones = !empty($datos['opciones_json']) ? $datos['opciones_json'] : null;
        $this->db->query("UPDATE en_plantilla_pregunta
                             SET pregunta       = :pregunta,
                                 orden          = :orden,
                                 activo         = :activo,
                                 tipo_respuesta = :tipo_resp,
                                 opciones_json  = :opciones
                           WHERE id_plantilla_pregunta = :id");
        $this->db->bind(':pregunta',  trim($datos['pregunta']));
        $this->db->bind(':orden',     $datos['orden']);
        $this->db->bind(':activo',    $datos['activo']);
        $this->db->bind(':tipo_resp', $datos['tipo_respuesta'] ?? 'puntuacion');
        $this->db->bind(':opciones',  $opciones);
        $this->db->bind(':id',        $datos['id_plantilla_pregunta']);
        return $this->db->execute();
    }

    public function delPlantillaPregunta($id){
        $this->db->query("DELETE FROM en_plantilla_pregunta
                           WHERE id_plantilla_pregunta = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // ─────────────────────────────────────────────────────────────────
    // ENCUESTAS
    // ─────────────────────────────────────────────────────────────────
    // ── Encuestas de alumnos (tipo 1) ─────────────────────────────────
    public function getEncuestas($filtro = [], $pagina = 0, $tamPagina = TAM_PAGINA){
        $where  = "WHERE e.id_tipo_encuesta = 1"; // siempre solo alumnos
        $params = [];

        if(!empty($filtro['curso_academico'])){
            $where .= " AND e.curso_academico = :curso";
            $params[':curso'] = $filtro['curso_academico'];
        }
        if(!empty($filtro['activa']) && $filtro['activa'] !== ''){
            $where .= " AND e.activa = :activa";
            $params[':activa'] = $filtro['activa'];
        }
        if(!empty($filtro['id_departamento'])){
            $where .= " AND ci.id_departamento = :id_dept";
            $params[':id_dept'] = $filtro['id_departamento'];
        }
        if(!empty($filtro['id_ciclo'])){
            $where .= " AND ci.id_ciclo = :id_ciclo";
            $params[':id_ciclo'] = $filtro['id_ciclo'];
        }
        if(!empty($filtro['id_curso'])){
            $where .= " AND cu.id_curso = :id_curso";
            $params[':id_curso'] = $filtro['id_curso'];
        }
        if(!empty($filtro['buscar'])){
            $where .= " AND (e.titulo LIKE :buscar OR p.nombre_completo LIKE :buscar2)";
            $params[':buscar']  = '%'.$filtro['buscar'].'%';
            $params[':buscar2'] = '%'.$filtro['buscar'].'%';
        }

        $joins = "LEFT JOIN en_tipo_encuesta te ON e.id_tipo_encuesta = te.id_tipo_encuesta
                  LEFT JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                  LEFT JOIN cpifp_profesor p  ON pm.id_profesor = p.id_profesor
                  LEFT JOIN cpifp_modulo m    ON pm.id_modulo   = m.id_modulo
                  LEFT JOIN cpifp_curso cu    ON m.id_curso     = cu.id_curso
                  LEFT JOIN cpifp_ciclos ci   ON cu.id_ciclo    = ci.id_ciclo
                  LEFT JOIN cpifp_departamento dp ON ci.id_departamento = dp.id_departamento";

        $this->db->query("SELECT COUNT(*) as total FROM en_encuesta e $joins $where");
        foreach($params as $k => $v) $this->db->bind($k, $v);
        $total = $this->db->registro()->total;

        $offset = $pagina * $tamPagina;
        $this->db->query("SELECT e.*,
                                 te.tipo_encuesta,
                                 p.nombre_completo AS nombre_profesor,
                                 m.modulo          AS nombre_modulo,
                                 cu.curso          AS nombre_curso,
                                 ci.ciclo          AS nombre_ciclo,
                                 dp.departamento   AS nombre_departamento,
                                 dp.departamento_corto,
                                 ce.evaluacion     AS nombre_evaluacion,
                                 (SELECT COUNT(*) FROM en_respuesta r WHERE r.id_encuesta = e.id_encuesta) AS total_respuestas
                            FROM en_encuesta e
                            $joins
                            LEFT JOIN cpifp_evaluaciones ce ON ce.id_evaluacion = e.trimestre
                            $where
                           ORDER BY e.fecha_creacion DESC
                           LIMIT :offset, :tamPagina");
        foreach($params as $k => $v) $this->db->bind($k, $v);
        $this->db->bind(':offset',    $offset,    PDO::PARAM_INT);
        $this->db->bind(':tamPagina', $tamPagina, PDO::PARAM_INT);
        $registros = $this->db->registros();

        $result = new stdClass();
        $result->registros    = $registros;
        $result->total        = $total;
        $result->totalPaginas = ceil($total / $tamPagina);
        return $result;
    }

    // ── Otras encuestas (todo excepto tipo 1) ─────────────────────────
    public function getEncuestasOtras($filtro = [], $pagina = 0, $tamPagina = TAM_PAGINA){
        $where  = "WHERE e.id_tipo_encuesta != 1";
        $params = [];

        if(!empty($filtro['curso_academico'])){
            $where .= " AND e.curso_academico = :curso";
            $params[':curso'] = $filtro['curso_academico'];
        }
        if(isset($filtro['activa']) && $filtro['activa'] !== ''){
            $where .= " AND e.activa = :activa";
            $params[':activa'] = $filtro['activa'];
        }
        if(!empty($filtro['id_tipo_encuesta'])){
            $where .= " AND e.id_tipo_encuesta = :id_tipo";
            $params[':id_tipo'] = $filtro['id_tipo_encuesta'];
        }
        if(isset($filtro['trimestre']) && $filtro['trimestre'] !== ''){
            if($filtro['trimestre'] === '0'){
                $where .= " AND (e.trimestre IS NULL OR e.trimestre = 0)";
            } else {
                $where .= " AND e.trimestre = :trimestre";
                $params[':trimestre'] = $filtro['trimestre'];
            }
        }
        if(!empty($filtro['buscar'])){
            $where .= " AND e.titulo LIKE :buscar";
            $params[':buscar'] = '%'.$filtro['buscar'].'%';
        }

        $this->db->query("SELECT COUNT(*) as total FROM en_encuesta e
                           LEFT JOIN en_tipo_encuesta te ON e.id_tipo_encuesta = te.id_tipo_encuesta
                           $where");
        foreach($params as $k => $v) $this->db->bind($k, $v);
        $total = $this->db->registro()->total;

        $offset = $pagina * $tamPagina;
        $this->db->query("SELECT e.*,
                                 te.tipo_encuesta,
                                 ce.evaluacion AS nombre_evaluacion,
                                 (SELECT COUNT(*) FROM en_respuesta r WHERE r.id_encuesta = e.id_encuesta) AS total_respuestas
                            FROM en_encuesta e
                            LEFT JOIN en_tipo_encuesta te ON e.id_tipo_encuesta = te.id_tipo_encuesta
                            LEFT JOIN cpifp_evaluaciones ce ON ce.id_evaluacion = e.trimestre
                            $where
                           ORDER BY e.fecha_creacion DESC
                           LIMIT :offset, :tamPagina");
        foreach($params as $k => $v) $this->db->bind($k, $v);
        $this->db->bind(':offset',    $offset,    PDO::PARAM_INT);
        $this->db->bind(':tamPagina', $tamPagina, PDO::PARAM_INT);
        $registros = $this->db->registros();

        $result = new stdClass();
        $result->registros    = $registros;
        $result->total        = $total;
        $result->totalPaginas = ceil($total / $tamPagina);
        return $result;
    }

    // ── Auxiliares para filtros ───────────────────────────────────────
    /** Departamentos que tienen ciclos con encuestas de alumnos */
    public function getDepartamentos(){
        $this->db->query("SELECT DISTINCT dp.id_departamento, dp.departamento
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_modulo m   ON m.id_modulo  = pm.id_modulo
                            JOIN cpifp_curso cu   ON cu.id_curso  = m.id_curso
                            JOIN cpifp_ciclos ci  ON ci.id_ciclo  = cu.id_ciclo
                            JOIN cpifp_departamento dp ON dp.id_departamento = ci.id_departamento
                           WHERE e.id_tipo_encuesta = 1
                           ORDER BY dp.departamento");
        return $this->db->registros();
    }

    /**
     * Carga toda la jerarquía dept→ciclo→curso→encuestas activas de alumnos
     * en una sola query, para el portal público de selección.
     * Solo departamentos con sin_ciclo = 0.
     */
    public function getJerarquiaEncuestasActivas($curso_academico){
        $this->db->query("SELECT
                            dp.id_departamento,
                            dp.departamento,
                            ci.id_ciclo,
                            ci.ciclo,
                            ci.ciclo_corto,
                            cu.id_curso,
                            cu.curso          AS nombre_curso,
                            e.id_encuesta,
                            e.token_publico,
                            e.codigo_acceso,
                            e.trimestre,
                            ev.evaluacion     AS nombre_evaluacion,
                            p.nombre_completo AS nombre_profesor,
                            m.modulo          AS nombre_modulo,
                            m.nombre_corto,
                            m.id_modulo
                          FROM en_encuesta e
                          JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                          JOIN cpifp_profesor p  ON p.id_profesor  = pm.id_profesor
                          JOIN cpifp_modulo m    ON m.id_modulo    = pm.id_modulo
                          JOIN cpifp_curso cu    ON cu.id_curso    = m.id_curso
                          JOIN cpifp_ciclos ci   ON ci.id_ciclo    = cu.id_ciclo
                          JOIN cpifp_departamento dp ON dp.id_departamento = ci.id_departamento
                          LEFT JOIN cpifp_evaluaciones ev ON ev.id_evaluacion = e.trimestre
                         WHERE e.activa = 1
                           AND e.id_tipo_encuesta = 1
                           AND e.curso_academico = :curso
                           AND dp.sin_ciclo = 0
                         ORDER BY dp.departamento, ci.ciclo, cu.curso,
                                  ev.id_evaluacion, m.modulo, p.nombre_completo");
        $this->db->bind(':curso', $curso_academico);
        return $this->db->registros();
    }

    /** Ciclos de un departamento que tienen encuestas de alumnos */
    public function getCiclosByDepartamento($id_departamento){
        $this->db->query("SELECT DISTINCT ci.id_ciclo, ci.ciclo
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_modulo m   ON m.id_modulo = pm.id_modulo
                            JOIN cpifp_curso cu   ON cu.id_curso = m.id_curso
                            JOIN cpifp_ciclos ci  ON ci.id_ciclo = cu.id_ciclo
                           WHERE e.id_tipo_encuesta = 1
                             AND ci.id_departamento = :id_dept
                           ORDER BY ci.ciclo");
        $this->db->bind(':id_dept', $id_departamento);
        return $this->db->registros();
    }

    public function getEncuestasByProfesor($id_profesor, $pagina = 0, $tamPagina = TAM_PAGINA){
        $offset = $pagina * $tamPagina;
        $this->db->query("SELECT COUNT(*) as total
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                           WHERE pm.id_profesor = :id_profesor AND e.id_tipo_encuesta = 1");
        $this->db->bind(':id_profesor', $id_profesor);
        $total = $this->db->registro()->total;

        $this->db->query("SELECT e.*,
                                 te.tipo_encuesta,
                                 p.nombre_completo AS nombre_profesor,
                                 m.modulo AS nombre_modulo,
                                 cu.curso AS nombre_curso,
                                 ci.ciclo AS nombre_ciclo,
                                 ce.evaluacion AS nombre_evaluacion,
                                 (SELECT COUNT(*) FROM en_respuesta r WHERE r.id_encuesta = e.id_encuesta) AS total_respuestas
                            FROM en_encuesta e
                            JOIN en_tipo_encuesta te ON e.id_tipo_encuesta = te.id_tipo_encuesta
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_profesor p  ON pm.id_profesor = p.id_profesor
                            JOIN cpifp_modulo m    ON pm.id_modulo   = m.id_modulo
                            JOIN cpifp_curso cu    ON m.id_curso     = cu.id_curso
                            JOIN cpifp_ciclos ci   ON cu.id_ciclo    = ci.id_ciclo
                            LEFT JOIN cpifp_evaluaciones ce ON ce.id_evaluacion = e.trimestre
                           WHERE pm.id_profesor = :id_profesor AND e.id_tipo_encuesta = 1
                           ORDER BY e.fecha_creacion DESC
                           LIMIT :offset, :tamPagina");
        $this->db->bind(':id_profesor', $id_profesor);
        $this->db->bind(':offset',    $offset,    PDO::PARAM_INT);
        $this->db->bind(':tamPagina', $tamPagina, PDO::PARAM_INT);
        $registros = $this->db->registros();

        $result = new stdClass();
        $result->registros    = $registros;
        $result->total        = $total;
        $result->totalPaginas = ceil($total / $tamPagina);
        return $result;
    }

    public function getEncuesta($id_encuesta){
        $this->db->query("SELECT e.*,
                                 te.tipo_encuesta,
                                 p.nombre_completo AS nombre_profesor,
                                 p.id_profesor,
                                 m.modulo AS nombre_modulo,
                                 m.id_modulo,
                                 cu.curso AS nombre_curso,
                                 ci.ciclo AS nombre_ciclo,
                                 (SELECT COUNT(*) FROM en_respuesta r WHERE r.id_encuesta = e.id_encuesta) AS total_respuestas
                            FROM en_encuesta e
                            LEFT JOIN en_tipo_encuesta te ON e.id_tipo_encuesta = te.id_tipo_encuesta
                            LEFT JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            LEFT JOIN cpifp_profesor p  ON pm.id_profesor = p.id_profesor
                            LEFT JOIN cpifp_modulo m    ON pm.id_modulo   = m.id_modulo
                            LEFT JOIN cpifp_curso cu    ON m.id_curso     = cu.id_curso
                            LEFT JOIN cpifp_ciclos ci   ON cu.id_ciclo    = ci.id_ciclo
                           WHERE e.id_encuesta = :id_encuesta");
        $this->db->bind(':id_encuesta', $id_encuesta);
        return $this->db->registro();
    }

    public function getEncuestaByToken($token){
        $this->db->query("SELECT e.*,
                                 te.tipo_encuesta,
                                 p.nombre_completo AS nombre_profesor,
                                 m.modulo AS nombre_modulo,
                                 cu.curso AS nombre_curso,
                                 ci.ciclo AS nombre_ciclo
                            FROM en_encuesta e
                            LEFT JOIN en_tipo_encuesta te ON e.id_tipo_encuesta = te.id_tipo_encuesta
                            LEFT JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            LEFT JOIN cpifp_profesor p  ON pm.id_profesor = p.id_profesor
                            LEFT JOIN cpifp_modulo m    ON pm.id_modulo   = m.id_modulo
                            LEFT JOIN cpifp_curso cu    ON m.id_curso     = cu.id_curso
                            LEFT JOIN cpifp_ciclos ci   ON cu.id_ciclo    = ci.id_ciclo
                           WHERE e.token_publico = :token");
        $this->db->bind(':token', $token);
        return $this->db->registro();
    }

    public function addEncuesta($datos, $id_creador){
        $token = generarToken();
        // Código de acceso: solo para encuestas de alumnos (tipo 1)
        $codigo_acceso = null;
        if((int)$datos['id_tipo_encuesta'] === 1){
            $codigo_acceso = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        $this->db->query("INSERT INTO en_encuesta
                            (id_tipo_encuesta, titulo, descripcion, curso_academico,
                             trimestre, id_profesor_modulo,
                             fecha_inicio, fecha_fin, activa, token_publico, codigo_acceso,
                             mostrar_mejor_peor, mostrar_observaciones,
                             creada_por, fecha_creacion)
                           VALUES
                            (:id_tipo, :titulo, :descripcion, :curso,
                             :trimestre, :id_pm,
                             :fecha_ini, :fecha_fin, 1, :token, :codigo,
                             :mejor_peor, :observaciones,
                             :creador, NOW())");
        $this->db->bind(':id_tipo',       $datos['id_tipo_encuesta']);
        $this->db->bind(':titulo',        trim($datos['titulo']));
        $this->db->bind(':descripcion',   trim($datos['descripcion'] ?? ''));
        $this->db->bind(':curso',         $datos['curso_academico']);
        $this->db->bind(':trimestre',     !empty($datos['trimestre'])          ? $datos['trimestre']          : null);
        $this->db->bind(':id_pm',         !empty($datos['id_profesor_modulo']) ? $datos['id_profesor_modulo'] : null);
        $this->db->bind(':fecha_ini',     $datos['fecha_inicio']);
        $this->db->bind(':fecha_fin',     !empty($datos['fecha_fin'])          ? $datos['fecha_fin']          : null);
        $this->db->bind(':token',         $token);
        $this->db->bind(':codigo',        $codigo_acceso);
        $this->db->bind(':mejor_peor',    isset($datos['mostrar_mejor_peor'])    ? 1 : 0);
        $this->db->bind(':observaciones', isset($datos['mostrar_observaciones']) ? 1 : 0);
        $this->db->bind(':creador',       $id_creador);

        $id_encuesta = $this->db->executeLastId();

        if($id_encuesta){
            // Copiar preguntas de la plantilla
            $preguntas = $this->getPlantillaPreguntas($datos['id_tipo_encuesta']);
            foreach($preguntas as $p){
                $this->db->query("INSERT INTO en_pregunta
                                    (id_encuesta, orden, pregunta, tipo_respuesta, opciones_json)
                                   VALUES (:id, :orden, :preg, :tipo_resp, :opciones)");
                $this->db->bind(':id',        $id_encuesta);
                $this->db->bind(':orden',     $p->orden);
                $this->db->bind(':preg',      $p->pregunta);
                $this->db->bind(':tipo_resp', $p->tipo_respuesta ?? 'puntuacion');
                $this->db->bind(':opciones',  $p->opciones_json ?? null);
                $this->db->execute();
            }
        }

        return $id_encuesta;
    }

    public function editEncuesta($datos){
        $this->db->query("UPDATE en_encuesta
                             SET titulo       = :titulo,
                                 descripcion  = :descripcion,
                                 fecha_inicio = :fecha_ini,
                                 fecha_fin    = :fecha_fin,
                                 activa       = :activa
                           WHERE id_encuesta = :id");
        $this->db->bind(':titulo',      trim($datos['titulo']));
        $this->db->bind(':descripcion', trim($datos['descripcion'] ?? ''));
        $this->db->bind(':fecha_ini',   $datos['fecha_inicio']);
        $this->db->bind(':fecha_fin',   !empty($datos['fecha_fin']) ? $datos['fecha_fin'] : null);
        $this->db->bind(':activa',      $datos['activa']);
        $this->db->bind(':id',          $datos['id_encuesta']);
        return $this->db->execute();
    }

    public function cerrarEncuesta($id_encuesta){
        $this->db->query("UPDATE en_encuesta SET activa = 0 WHERE id_encuesta = :id");
        $this->db->bind(':id', $id_encuesta);
        return $this->db->execute();
    }

    public function abrirEncuesta($id_encuesta){
        $this->db->query("UPDATE en_encuesta SET activa = 1 WHERE id_encuesta = :id");
        $this->db->bind(':id', $id_encuesta);
        return $this->db->execute();
    }

    public function delEncuesta($id_encuesta){
        $this->db->query("DELETE FROM en_encuesta WHERE id_encuesta = :id");
        $this->db->bind(':id', $id_encuesta);
        return $this->db->execute();
    }

    // ─────────────────────────────────────────────────────────────────
    // PREGUNTAS DE UNA ENCUESTA CONCRETA
    // ─────────────────────────────────────────────────────────────────
    public function getPreguntasEncuesta($id_encuesta){
        $this->db->query("SELECT * FROM en_pregunta
                           WHERE id_encuesta = :id
                           ORDER BY orden");
        $this->db->bind(':id', $id_encuesta);
        return $this->db->registros();
    }

    public function addPreguntaEncuesta($id_encuesta, $pregunta, $orden){
        $this->db->query("INSERT INTO en_pregunta (id_encuesta, pregunta, orden)
                           VALUES (:id, :preg, :orden)");
        $this->db->bind(':id',    $id_encuesta);
        $this->db->bind(':preg',  trim($pregunta));
        $this->db->bind(':orden', (int)$orden);
        return $this->db->executeLastId();
    }

    public function editPreguntaEncuesta($id_pregunta, $pregunta, $orden){
        $this->db->query("UPDATE en_pregunta
                             SET pregunta = :preg, orden = :orden
                           WHERE id_pregunta = :id");
        $this->db->bind(':preg',  trim($pregunta));
        $this->db->bind(':orden', (int)$orden);
        $this->db->bind(':id',    $id_pregunta);
        return $this->db->execute();
    }

    public function delPreguntaEncuesta($id_pregunta){
        // Solo se puede borrar si la encuesta no tiene respuestas
        $this->db->query("SELECT COUNT(*) AS total
                            FROM en_respuesta_detalle d
                            JOIN en_respuesta r ON r.id_respuesta = d.id_respuesta
                           WHERE d.id_pregunta = :id");
        $this->db->bind(':id', $id_pregunta);
        if($this->db->registro()->total > 0) return 'tiene_respuestas';

        $this->db->query("DELETE FROM en_pregunta WHERE id_pregunta = :id");
        $this->db->bind(':id', $id_pregunta);
        return $this->db->execute() ? 'ok' : 'error';
    }

    /**
     * Lista todas las encuestas (todos los tipos) para el gestor.
     * Con filtros de curso, tipo, estado y búsqueda.
     */
    public function getAllEncuestasGestor($filtro = [], $pagina = 0, $tamPagina = TAM_PAGINA){
        $where  = "WHERE 1=1";
        $params = [];

        if(!empty($filtro['curso_academico'])){
            $where .= " AND e.curso_academico = :curso";
            $params[':curso'] = $filtro['curso_academico'];
        }
        if(!empty($filtro['id_tipo_encuesta'])){
            $where .= " AND e.id_tipo_encuesta = :id_tipo";
            $params[':id_tipo'] = $filtro['id_tipo_encuesta'];
        }
        if(isset($filtro['activa']) && $filtro['activa'] !== ''){
            $where .= " AND e.activa = :activa";
            $params[':activa'] = $filtro['activa'];
        }
        if(!empty($filtro['buscar'])){
            $where .= " AND (e.titulo LIKE :buscar OR p.nombre_completo LIKE :buscar2
                              OR m.modulo LIKE :buscar3)";
            $params[':buscar']  = '%'.$filtro['buscar'].'%';
            $params[':buscar2'] = '%'.$filtro['buscar'].'%';
            $params[':buscar3'] = '%'.$filtro['buscar'].'%';
        }

        $joins = "LEFT JOIN en_tipo_encuesta te ON e.id_tipo_encuesta = te.id_tipo_encuesta
                  LEFT JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                  LEFT JOIN cpifp_profesor p  ON pm.id_profesor = p.id_profesor
                  LEFT JOIN cpifp_modulo m    ON pm.id_modulo   = m.id_modulo
                  LEFT JOIN cpifp_curso cu    ON m.id_curso     = cu.id_curso
                  LEFT JOIN cpifp_ciclos ci   ON cu.id_ciclo    = ci.id_ciclo";

        $this->db->query("SELECT COUNT(*) as total FROM en_encuesta e $joins $where");
        foreach($params as $k => $v) $this->db->bind($k, $v);
        $total = $this->db->registro()->total;

        $offset = $pagina * $tamPagina;
        $this->db->query("SELECT e.*,
                                 te.tipo_encuesta,
                                 p.nombre_completo AS nombre_profesor,
                                 m.modulo          AS nombre_modulo,
                                 cu.curso          AS nombre_curso,
                                 ci.ciclo          AS nombre_ciclo,
                                 (SELECT COUNT(*) FROM en_respuesta r
                                  WHERE r.id_encuesta = e.id_encuesta) AS total_respuestas,
                                 (SELECT COUNT(*) FROM en_pregunta q
                                  WHERE q.id_encuesta = e.id_encuesta) AS total_preguntas
                            FROM en_encuesta e $joins
                            $where
                           ORDER BY e.fecha_creacion DESC
                           LIMIT :offset, :tam");
        foreach($params as $k => $v) $this->db->bind($k, $v);
        $this->db->bind(':offset', $offset,    PDO::PARAM_INT);
        $this->db->bind(':tam',    $tamPagina, PDO::PARAM_INT);

        $result = new stdClass();
        $result->registros    = $this->db->registros();
        $result->total        = $total;
        $result->totalPaginas = $tamPagina > 0 ? ceil($total / $tamPagina) : 1;
        return $result;
    }

    // ─────────────────────────────────────────────────────────────────
    // RESPUESTAS (FRONT-END PÚBLICO)
    // ─────────────────────────────────────────────────────────────────
    public function addRespuesta($id_encuesta, $puntuaciones, $comentarios, $ip, $token_anonimo = null){
        $this->db->query("INSERT INTO en_respuesta
                            (id_encuesta, fecha_respuesta, ip, token_anonimo,
                             comentario_mejor, comentario_peor, comentario_libre)
                           VALUES (:id, NOW(), :ip, :token,
                                   :mejor, :peor, :libre)");
        $this->db->bind(':id',    $id_encuesta);
        $this->db->bind(':ip',    $ip);
        $this->db->bind(':token', $token_anonimo);
        $this->db->bind(':mejor', trim($comentarios['mejor'] ?? ''));
        $this->db->bind(':peor',  trim($comentarios['peor']  ?? ''));
        $this->db->bind(':libre', trim($comentarios['libre'] ?? ''));
        $id_respuesta = $this->db->executeLastId();

        if($id_respuesta){
            // Obtener tipo de cada pregunta para guardar en la columna correcta
            $tipos = [];
            $this->db->query("SELECT id_pregunta, tipo_respuesta FROM en_pregunta
                               WHERE id_encuesta = :id");
            $this->db->bind(':id', $id_encuesta);
            foreach($this->db->registros() as $row){
                $tipos[$row->id_pregunta] = $row->tipo_respuesta ?? 'puntuacion';
            }

            foreach($puntuaciones as $id_pregunta => $valor){
                $tipo = $tipos[$id_pregunta] ?? 'puntuacion';

                if($tipo === 'opciones'){
                    $this->db->query("INSERT INTO en_respuesta_detalle
                                        (id_respuesta, id_pregunta, valor_opcion)
                                       VALUES (:id_r, :id_p, :val)");
                    $this->db->bind(':val', (int)$valor);
                } elseif($tipo === 'numerica'){
                    $this->db->query("INSERT INTO en_respuesta_detalle
                                        (id_respuesta, id_pregunta, valor_numerico)
                                       VALUES (:id_r, :id_p, :val)");
                    $this->db->bind(':val', (int)$valor);
                } else {
                    $this->db->query("INSERT INTO en_respuesta_detalle
                                        (id_respuesta, id_pregunta, puntuacion)
                                       VALUES (:id_r, :id_p, :val)");
                    $this->db->bind(':val', (int)$valor);
                }
                $this->db->bind(':id_r', $id_respuesta);
                $this->db->bind(':id_p', $id_pregunta);
                $this->db->execute();
            }
        }

        return $id_respuesta;
    }

    /**
     * Comprueba si el token anónimo ya respondió esta encuesta.
     * Devuelve true si ya existe una respuesta con ese token.
     */
    public function yaRespondio($id_encuesta, $token_anonimo){
        if(!$token_anonimo) return false;
        $this->db->query("SELECT COUNT(*) AS total FROM en_respuesta
                           WHERE id_encuesta = :id AND token_anonimo = :token");
        $this->db->bind(':id',    $id_encuesta);
        $this->db->bind(':token', $token_anonimo);
        return $this->db->registro()->total > 0;
    }

    /**
     * Devuelve las encuestas activas del mismo grupo (id_curso) que aún NO
     * han sido respondidas por este token anónimo. Se usa en la página de
     * "gracias" para sugerir las encuestas pendientes.
     */
    public function getEncuestasPendientesGrupo($id_curso, $curso_academico, $token_anonimo){
        $this->db->query("SELECT e.id_encuesta, e.titulo, e.token_publico,
                                 e.codigo_acceso,
                                 p.nombre_completo AS nombre_profesor,
                                 m.modulo AS nombre_modulo, m.nombre_corto,
                                 ev.evaluacion AS nombre_evaluacion
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_profesor p  ON p.id_profesor = pm.id_profesor
                            JOIN cpifp_modulo m    ON m.id_modulo   = pm.id_modulo
                            JOIN cpifp_curso cu    ON cu.id_curso   = m.id_curso
                            LEFT JOIN cpifp_evaluaciones ev ON ev.id_evaluacion = e.trimestre
                           WHERE e.activa = 1
                             AND e.id_tipo_encuesta = 1
                             AND e.curso_academico  = :curso
                             AND cu.id_curso        = :id_curso
                             AND NOT EXISTS (
                                 SELECT 1 FROM en_respuesta r
                                  WHERE r.id_encuesta    = e.id_encuesta
                                    AND r.token_anonimo  = :token
                             )
                           ORDER BY ev.id_evaluacion, m.modulo");
        $this->db->bind(':curso',    $curso_academico);
        $this->db->bind(':id_curso', $id_curso);
        $this->db->bind(':token',    $token_anonimo);
        return $this->db->registros();
    }

    // ─────────────────────────────────────────────────────────────────
    // RESULTADOS / ESTADÍSTICAS (BACK-END)
    // ─────────────────────────────────────────────────────────────────
    public function getResumenEncuesta($id_encuesta){
        $this->db->query("SELECT p.id_pregunta,
                                 p.orden,
                                 p.pregunta,
                                 p.tipo_respuesta,
                                 p.opciones_json,
                                 COUNT(d.id_respuesta_detalle)      AS total_respuestas,
                                 -- Puntuación 1-10
                                 ROUND(AVG(d.puntuacion), 2)        AS media,
                                 MIN(d.puntuacion)                  AS minimo,
                                 MAX(d.puntuacion)                  AS maximo,
                                 -- Numérica abierta
                                 ROUND(AVG(d.valor_numerico), 2)    AS media_num,
                                 MIN(d.valor_numerico)              AS minimo_num,
                                 MAX(d.valor_numerico)              AS maximo_num
                            FROM en_pregunta p
                            LEFT JOIN en_respuesta_detalle d ON p.id_pregunta = d.id_pregunta
                           WHERE p.id_encuesta = :id
                           GROUP BY p.id_pregunta, p.orden, p.pregunta,
                                    p.tipo_respuesta, p.opciones_json
                           ORDER BY p.orden");
        $this->db->bind(':id', $id_encuesta);
        return $this->db->registros();
    }

    public function getDistribucionPuntuaciones($id_encuesta){
        // Distribución de puntuaciones 1-10 (solo tipo puntuacion)
        $this->db->query("SELECT p.id_pregunta,
                                 p.orden,
                                 p.pregunta,
                                 d.puntuacion,
                                 COUNT(*) AS cantidad
                            FROM en_pregunta p
                            JOIN en_respuesta_detalle d ON p.id_pregunta = d.id_pregunta
                           WHERE p.id_encuesta = :id
                             AND p.tipo_respuesta = 'puntuacion'
                             AND d.puntuacion IS NOT NULL
                           GROUP BY p.id_pregunta, p.orden, p.pregunta, d.puntuacion
                           ORDER BY p.orden, d.puntuacion");
        $this->db->bind(':id', $id_encuesta);
        return $this->db->registros();
    }

    public function getDistribucionOpciones($id_encuesta){
        // Distribución de opciones elegidas (tipo opciones)
        $this->db->query("SELECT p.id_pregunta,
                                 p.orden,
                                 p.pregunta,
                                 p.opciones_json,
                                 d.valor_opcion,
                                 COUNT(*) AS cantidad
                            FROM en_pregunta p
                            JOIN en_respuesta_detalle d ON p.id_pregunta = d.id_pregunta
                           WHERE p.id_encuesta = :id
                             AND p.tipo_respuesta = 'opciones'
                             AND d.valor_opcion IS NOT NULL
                           GROUP BY p.id_pregunta, p.orden, p.pregunta,
                                    p.opciones_json, d.valor_opcion
                           ORDER BY p.orden, d.valor_opcion");
        $this->db->bind(':id', $id_encuesta);
        return $this->db->registros();
    }

    public function getComentariosEncuesta($id_encuesta){
        $this->db->query("SELECT id_respuesta, fecha_respuesta,
                                 comentario_mejor, comentario_peor, comentario_libre
                            FROM en_respuesta
                           WHERE id_encuesta = :id
                             AND (comentario_mejor IS NOT NULL AND comentario_mejor != ''
                               OR comentario_peor  IS NOT NULL AND comentario_peor  != ''
                               OR comentario_libre IS NOT NULL AND comentario_libre != '')
                           ORDER BY fecha_respuesta DESC");
        $this->db->bind(':id', $id_encuesta);
        return $this->db->registros();
    }

    public function getRespuestasEncuesta($id_encuesta){
        $this->db->query("SELECT * FROM en_respuesta
                           WHERE id_encuesta = :id
                           ORDER BY fecha_respuesta DESC");
        $this->db->bind(':id', $id_encuesta);
        return $this->db->registros();
    }

    // Medias globales por profesor (todas sus encuestas de un curso académico)
    public function getMediasProfesorCurso($id_profesor, $curso_academico){
        $this->db->query("SELECT p.nombre_completo AS nombre_profesor,
                                 m.modulo AS nombre_modulo,
                                 e.trimestre,
                                 e.id_encuesta,
                                 ROUND(AVG(d.puntuacion), 2) AS media_global,
                                 COUNT(DISTINCT r.id_respuesta) AS total_respuestas
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_profesor p  ON pm.id_profesor = p.id_profesor
                            JOIN cpifp_modulo m    ON pm.id_modulo   = m.id_modulo
                            JOIN en_respuesta r    ON e.id_encuesta  = r.id_encuesta
                            JOIN en_respuesta_detalle d ON r.id_respuesta = d.id_respuesta
                           WHERE pm.id_profesor = :id_profesor
                             AND e.curso_academico = :curso
                             AND e.id_tipo_encuesta = 1
                           GROUP BY p.nombre_completo, m.modulo, e.trimestre, e.id_encuesta
                           ORDER BY e.trimestre, m.modulo");
        $this->db->bind(':id_profesor', $id_profesor);
        $this->db->bind(':curso',       $curso_academico);
        return $this->db->registros();
    }

    // ─────────────────────────────────────────────────────────────────
    // DATOS AUXILIARES – CASCADA PARA CREAR ENCUESTAS DE ALUMNOS
    // Ciclo → Curso → Módulo → Profesor
    // ─────────────────────────────────────────────────────────────────

    /** Todos los ciclos que tienen profesor_modulo activo en el lectivo vigente */
    public function getCiclosConProfesor(){
        $this->db->query("SELECT DISTINCT ci.id_ciclo, ci.ciclo, ci.ciclo_corto
                            FROM cpifp_ciclos ci
                            JOIN cpifp_curso cu   ON cu.id_ciclo    = ci.id_ciclo
                            JOIN cpifp_modulo m   ON m.id_curso     = cu.id_curso
                            JOIN cpifp_profesor_modulo pm ON pm.id_modulo = m.id_modulo
                            JOIN cpifp_profesor p ON p.id_profesor  = pm.id_profesor
                           WHERE p.activo = 1
                           ORDER BY ci.ciclo");
        return $this->db->registros();
    }

    /** Cursos del ciclo seleccionado que tienen profesor_modulo activo */
    public function getCursosByCiclo($id_ciclo){
        $this->db->query("SELECT DISTINCT cu.id_curso, cu.curso
                            FROM cpifp_curso cu
                            JOIN cpifp_modulo m   ON m.id_curso     = cu.id_curso
                            JOIN cpifp_profesor_modulo pm ON pm.id_modulo = m.id_modulo
                            JOIN cpifp_profesor p ON p.id_profesor  = pm.id_profesor
                           WHERE cu.id_ciclo = :id_ciclo
                             AND p.activo = 1
                           ORDER BY cu.curso");
        $this->db->bind(':id_ciclo', $id_ciclo);
        return $this->db->registros();
    }

    /** Módulos del curso seleccionado que tienen profesor activo */
    public function getModulosByCurso($id_curso){
        $this->db->query("SELECT DISTINCT m.id_modulo, m.modulo, m.nombre_corto
                            FROM cpifp_modulo m
                            JOIN cpifp_profesor_modulo pm ON pm.id_modulo = m.id_modulo
                            JOIN cpifp_profesor p ON p.id_profesor = pm.id_profesor
                           WHERE m.id_curso = :id_curso
                             AND p.activo = 1
                           ORDER BY m.modulo");
        $this->db->bind(':id_curso', $id_curso);
        return $this->db->registros();
    }

    /** Profesores que imparten el módulo (con su id_profesor_modulo) */
    public function getProfesoresByModulo($id_modulo){
        $this->db->query("SELECT pm.id_profesor_modulo,
                                 p.id_profesor,
                                 p.nombre_completo
                            FROM cpifp_profesor_modulo pm
                            JOIN cpifp_profesor p ON p.id_profesor = pm.id_profesor
                           WHERE pm.id_modulo = :id_modulo
                             AND p.activo = 1
                           ORDER BY p.nombre_completo");
        $this->db->bind(':id_modulo', $id_modulo);
        return $this->db->registros();
    }

    // ─────────────────────────────────────────────────────────────────
    // EVALUACIÓN ACTUAL (trimestre) desde seg_evaluaciones + seg_calendario
    // Devuelve el registro de cpifp_evaluaciones cuya fecha de corte en
    // seg_calendario sea la más próxima (posterior) a hoy.
    // ─────────────────────────────────────────────────────────────────
    public function getEvaluacionActual(){
        // Obtenemos la evaluación cuya fecha de corte (en seg_calendario)
        // sea igual o posterior a hoy, tomando la más próxima.
        // seg_evaluaciones.id_calendario marca el último día de esa evaluación.
        $this->db->query("SELECT ce.id_evaluacion,
                                 ce.evaluacion,
                                 sc.fecha AS fecha_corte
                            FROM seg_evaluaciones se
                            JOIN cpifp_evaluaciones ce ON ce.id_evaluacion = se.id_evaluacion
                            JOIN seg_calendario sc     ON sc.id_calendario  = se.id_calendario
                           WHERE sc.fecha >= CURDATE()
                           ORDER BY sc.fecha ASC
                           LIMIT 1");
        return $this->db->registro();
    }

    /** Todas las evaluaciones disponibles (para selector manual) */
    public function getEvaluaciones(){
        $this->db->query("SELECT * FROM cpifp_evaluaciones ORDER BY id_evaluacion");
        return $this->db->registros();
    }

    // ─────────────────────────────────────────────────────────────────
    // LECTIVO ACTIVO
    // ─────────────────────────────────────────────────────────────────
    public function getLectivoActivo(){
        $this->db->query("SELECT * FROM seg_lectivos WHERE cerrado = 0 ORDER BY id_lectivo DESC LIMIT 1");
        return $this->db->registro();
    }

    // ─────────────────────────────────────────────────────────────────
    // GENERACIÓN MASIVA DE ENCUESTAS
    // Un código compartido por todas las encuestas del mismo curso_academico
    // ─────────────────────────────────────────────────────────────────

    /**
     * Devuelve todos los pares profesor-módulo activos (para previsualizar
     * qué se va a generar antes de confirmar).
     */
    public function getProfesorModulosParaGeneracion(){
        $this->db->query("SELECT pm.id_profesor_modulo,
                                 p.id_profesor,
                                 p.nombre_completo AS nombre_profesor,
                                 m.id_modulo,
                                 m.modulo          AS nombre_modulo,
                                 cu.id_curso,
                                 cu.curso          AS nombre_curso,
                                 ci.id_ciclo,
                                 ci.ciclo          AS nombre_ciclo
                            FROM cpifp_profesor_modulo pm
                            JOIN cpifp_profesor p ON p.id_profesor  = pm.id_profesor
                            JOIN cpifp_modulo m   ON m.id_modulo    = pm.id_modulo
                            JOIN cpifp_curso cu   ON cu.id_curso    = m.id_curso
                            JOIN cpifp_ciclos ci  ON ci.id_ciclo    = cu.id_ciclo
                           WHERE p.activo = 1
                           ORDER BY ci.ciclo, cu.curso, m.modulo, p.nombre_completo");
        return $this->db->registros();
    }

    /** Info del par profesor-módulo con id_curso, nombre_curso y nombre_ciclo */
    public function getInfoProfesorModulo($id_pm){
        $this->db->query("SELECT pm.id_profesor_modulo,
                                 m.id_curso,
                                 cu.curso AS nombre_curso,
                                 ci.ciclo AS nombre_ciclo
                            FROM cpifp_profesor_modulo pm
                            JOIN cpifp_modulo m  ON m.id_modulo  = pm.id_modulo
                            JOIN cpifp_curso cu  ON cu.id_curso  = m.id_curso
                            JOIN cpifp_ciclos ci ON ci.id_ciclo  = cu.id_ciclo
                           WHERE pm.id_profesor_modulo = :id_pm");
        $this->db->bind(':id_pm', $id_pm);
        return $this->db->registro();
    }

    /**
     * Si ya existe una encuesta del mismo grupo (id_curso) en esta evaluación/curso,
     * devuelve su código para reutilizarlo (garantiza código único por grupo).
     */
    public function getCodigoGrupo($id_curso, $id_evaluacion, $curso_academico){
        $this->db->query("SELECT e.codigo_acceso
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_modulo m ON m.id_modulo = pm.id_modulo
                           WHERE m.id_curso       = :id_curso
                             AND e.trimestre      = :trimestre
                             AND e.curso_academico= :curso
                             AND e.id_tipo_encuesta = 1
                             AND e.codigo_acceso IS NOT NULL
                           LIMIT 1");
        $this->db->bind(':id_curso',  $id_curso);
        $this->db->bind(':trimestre', $id_evaluacion);
        $this->db->bind(':curso',     $curso_academico);
        $r = $this->db->registro();
        return $r ? $r->codigo_acceso : null;
    }

    /** Ciclos que tienen encuestas activas (para la página pública) */
    public function getCiclosPublico($curso_academico){
        $this->db->query("SELECT DISTINCT ci.id_ciclo, ci.ciclo, ci.ciclo_corto
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_modulo m   ON m.id_modulo = pm.id_modulo
                            JOIN cpifp_curso cu   ON cu.id_curso = m.id_curso
                            JOIN cpifp_ciclos ci  ON ci.id_ciclo = cu.id_ciclo
                           WHERE e.activa = 1
                             AND e.id_tipo_encuesta = 1
                             AND e.curso_academico = :curso
                           ORDER BY ci.ciclo");
        $this->db->bind(':curso', $curso_academico);
        return $this->db->registros();
    }

    /** Cursos (grupos) de un ciclo con encuestas activas */
    public function getCursosPublico($id_ciclo, $curso_academico){
        $this->db->query("SELECT DISTINCT cu.id_curso, cu.curso
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_modulo m   ON m.id_modulo = pm.id_modulo
                            JOIN cpifp_curso cu   ON cu.id_curso = m.id_curso
                           WHERE e.activa = 1
                             AND e.id_tipo_encuesta = 1
                             AND e.curso_academico = :curso
                             AND cu.id_ciclo = :id_ciclo
                           ORDER BY cu.curso");
        $this->db->bind(':curso',    $curso_academico);
        $this->db->bind(':id_ciclo', $id_ciclo);
        return $this->db->registros();
    }

    /** Encuestas activas de un grupo concreto */
    public function getEncuestasActivasGrupo($id_curso, $curso_academico){
        $this->db->query("SELECT e.id_encuesta, e.titulo, e.token_publico,
                                 e.codigo_acceso, e.descripcion,
                                 p.nombre_completo AS nombre_profesor,
                                 m.modulo AS nombre_modulo, m.nombre_corto,
                                 ev.evaluacion AS nombre_evaluacion
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_profesor p  ON p.id_profesor = pm.id_profesor
                            JOIN cpifp_modulo m    ON m.id_modulo   = pm.id_modulo
                            JOIN cpifp_curso cu    ON cu.id_curso   = m.id_curso
                            LEFT JOIN cpifp_evaluaciones ev ON ev.id_evaluacion = e.trimestre
                           WHERE e.activa = 1
                             AND e.id_tipo_encuesta = 1
                             AND e.curso_academico = :curso
                             AND cu.id_curso = :id_curso
                           ORDER BY ev.id_evaluacion, m.modulo, p.nombre_completo");
        $this->db->bind(':curso',    $curso_academico);
        $this->db->bind(':id_curso', $id_curso);
        return $this->db->registros();
    }

    /** Ciclos que tienen al menos una encuesta (para filtro) */
    public function getCiclosConEncuestas(){
        $this->db->query("SELECT DISTINCT ci.id_ciclo, ci.ciclo
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_modulo m   ON m.id_modulo  = pm.id_modulo
                            JOIN cpifp_curso cu   ON cu.id_curso  = m.id_curso
                            JOIN cpifp_ciclos ci  ON ci.id_ciclo  = cu.id_ciclo
                           ORDER BY ci.ciclo");
        return $this->db->registros();
    }

    /** Cursos (grupos) de un ciclo que tienen encuestas (para filtro AJAX) */
    public function getCursosConEncuestasByCiclo($id_ciclo){
        $this->db->query("SELECT DISTINCT cu.id_curso, cu.curso
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_modulo m   ON m.id_modulo = pm.id_modulo
                            JOIN cpifp_curso cu   ON cu.id_curso = m.id_curso
                           WHERE cu.id_ciclo = :id_ciclo
                           ORDER BY cu.curso");
        $this->db->bind(':id_ciclo', $id_ciclo);
        return $this->db->registros();
    }

    /**
     * Comprueba si ya existe una encuesta para ese profesor-módulo,
     * evaluación y curso académico.
     * Devuelve el registro existente o false.
     */
    public function existeEncuesta($id_profesor_modulo, $id_evaluacion, $curso_academico){
        $this->db->query("SELECT e.id_encuesta, e.titulo,
                                 p.nombre_completo AS nombre_profesor,
                                 m.modulo          AS nombre_modulo,
                                 cu.curso          AS nombre_curso,
                                 ci.ciclo          AS nombre_ciclo,
                                 ev.evaluacion     AS nombre_evaluacion
                            FROM en_encuesta e
                            JOIN cpifp_profesor_modulo pm ON e.id_profesor_modulo = pm.id_profesor_modulo
                            JOIN cpifp_profesor p  ON p.id_profesor = pm.id_profesor
                            JOIN cpifp_modulo m    ON m.id_modulo   = pm.id_modulo
                            JOIN cpifp_curso cu    ON cu.id_curso   = m.id_curso
                            JOIN cpifp_ciclos ci   ON ci.id_ciclo   = cu.id_ciclo
                            LEFT JOIN cpifp_evaluaciones ev ON ev.id_evaluacion = e.trimestre
                           WHERE e.id_profesor_modulo = :id_pm
                             AND e.trimestre          = :trimestre
                             AND e.curso_academico    = :curso
                             AND e.id_tipo_encuesta   = 1
                           LIMIT 1");
        $this->db->bind(':id_pm',     $id_profesor_modulo);
        $this->db->bind(':trimestre', $id_evaluacion);
        $this->db->bind(':curso',     $curso_academico);
        return $this->db->registro(); // false si no existe
    }

    /**
     * Eliminar encuesta solo si no tiene respuestas.
     * Devuelve: 'ok', 'tiene_respuestas', o 'error'
     */
    /**
     * Comprueba si el usuario pertenece al departamento de calidad
     * o tiene rol de equipo directivo (id_rol=1).
     * $roles = array de objetos devuelto por getRolesProfesor()
     */
    public function puedeEliminarForzado($roles){
        foreach($roles as $rol){
            // Equipo directivo
            if((int)$rol->id_rol === 1) return true;
            // Departamento de calidad (nombre contiene "calidad", case-insensitive)
            if(isset($rol->departamento) &&
               stripos($rol->departamento, 'calidad') !== false) return true;
        }
        return false;
    }

    public function delEncuestaForzado($id_encuesta){
        // Borra respuesta_detalle → respuesta → pregunta → encuesta
        $this->db->query("DELETE rd FROM en_respuesta_detalle rd
                           JOIN en_respuesta r ON r.id_respuesta = rd.id_respuesta
                           WHERE r.id_encuesta = :id");
        $this->db->bind(':id', $id_encuesta);
        $this->db->execute();

        $this->db->query("DELETE FROM en_respuesta WHERE id_encuesta = :id");
        $this->db->bind(':id', $id_encuesta);
        $this->db->execute();

        $this->db->query("DELETE FROM en_pregunta WHERE id_encuesta = :id");
        $this->db->bind(':id', $id_encuesta);
        $this->db->execute();

        $this->db->query("DELETE FROM en_encuesta WHERE id_encuesta = :id");
        $this->db->bind(':id', $id_encuesta);
        return $this->db->execute() ? 'ok' : 'error';
    }

    public function delEncuestaSegura($id_encuesta){
        $this->db->query("SELECT COUNT(*) AS total FROM en_respuesta WHERE id_encuesta = :id");
        $this->db->bind(':id', $id_encuesta);
        $resp = $this->db->registro()->total;
        if($resp > 0) return 'tiene_respuestas';

        // Borrar preguntas primero (FK)
        $this->db->query("DELETE FROM en_pregunta WHERE id_encuesta = :id");
        $this->db->bind(':id', $id_encuesta);
        $this->db->execute();

        $this->db->query("DELETE FROM en_encuesta WHERE id_encuesta = :id");
        $this->db->bind(':id', $id_encuesta);
        return $this->db->execute() ? 'ok' : 'error';
    }

    /**
     * Genera una encuesta individual dentro del proceso masivo.
     * Recibe el código ya calculado para que sea el mismo para todo el curso.
     */
    public function addEncuestaMasiva($id_profesor_modulo, $id_evaluacion,
                                      $curso_academico, $nombre_evaluacion,
                                      $codigo_acceso, $id_creador,
                                      $fecha_inicio, $fecha_fin = null,
                                      $mostrar_mejor_peor = 1, $mostrar_observaciones = 1){
        // Obtener datos del profesor y módulo para el título
        $this->db->query("SELECT p.nombre_completo, m.modulo, m.nombre_corto
                            FROM cpifp_profesor_modulo pm
                            JOIN cpifp_profesor p ON p.id_profesor = pm.id_profesor
                            JOIN cpifp_modulo m   ON m.id_modulo   = pm.id_modulo
                           WHERE pm.id_profesor_modulo = :id_pm");
        $this->db->bind(':id_pm', $id_profesor_modulo);
        $info = $this->db->registro();
        if(!$info) return false;

        $modulo_txt = $info->nombre_corto ?: $info->modulo;
        $titulo = "Encuesta {$nombre_evaluacion} – {$modulo_txt} – {$info->nombre_completo} ({$curso_academico})";
        $token  = generarToken();

        $this->db->query("INSERT INTO en_encuesta
                            (id_tipo_encuesta, titulo, descripcion, curso_academico,
                             trimestre, id_profesor_modulo,
                             fecha_inicio, fecha_fin, activa, token_publico, codigo_acceso,
                             mostrar_mejor_peor, mostrar_observaciones,
                             creada_por, fecha_creacion)
                           VALUES
                            (1, :titulo, '', :curso,
                             :trimestre, :id_pm,
                             :fecha_ini, :fecha_fin, 1, :token, :codigo,
                             :mejor_peor, :observaciones,
                             :creador, NOW())");
        $this->db->bind(':titulo',        $titulo);
        $this->db->bind(':curso',         $curso_academico);
        $this->db->bind(':trimestre',     $id_evaluacion);
        $this->db->bind(':id_pm',         $id_profesor_modulo);
        $this->db->bind(':fecha_ini',     $fecha_inicio);
        $this->db->bind(':fecha_fin',     $fecha_fin);
        $this->db->bind(':token',         $token);
        $this->db->bind(':codigo',        $codigo_acceso);
        $this->db->bind(':mejor_peor',    (int)$mostrar_mejor_peor);
        $this->db->bind(':observaciones', (int)$mostrar_observaciones);
        $this->db->bind(':creador',       $id_creador);

        $id_encuesta = $this->db->executeLastId();
        if(!$id_encuesta) return false;

        // Copiar preguntas de la plantilla de alumnos (tipo 1)
        $preguntas = $this->getPlantillaPreguntas(1);
        foreach($preguntas as $p){
            $this->db->query("INSERT INTO en_pregunta
                                (id_encuesta, orden, pregunta, tipo_respuesta, opciones_json)
                               VALUES (:id, :orden, :preg, :tipo_resp, :opciones)");
            $this->db->bind(':id',        $id_encuesta);
            $this->db->bind(':orden',     $p->orden);
            $this->db->bind(':preg',      $p->pregunta);
            $this->db->bind(':tipo_resp', $p->tipo_respuesta ?? 'puntuacion');
            $this->db->bind(':opciones',  $p->opciones_json ?? null);
            $this->db->execute();
        }
        return $id_encuesta;
    }

    public function getCursosAcademicos(){
        $this->db->query("SELECT DISTINCT curso_academico FROM en_encuesta ORDER BY curso_academico DESC");
        return $this->db->registros();
    }
}
