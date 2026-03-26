<?php

/**
 * Controlador PÚBLICO – no requiere login.
 *
 * Control anti-duplicado anónimo:
 *   - Al primer acceso se genera una cookie `enc_uid` con un UUID aleatorio
 *     que no contiene ningún dato personal.
 *   - Al guardar una respuesta se almacena SHA256(uid + id_encuesta) en la BD.
 *   - Si ese hash ya existe, se muestra la pantalla "ya respondiste".
 *   - El hash es irreversible: imposible saber quién respondió qué.
 */
class Responder extends Controlador {

    private $encuestaModelo;

    public function __construct(){
        $this->encuestaModelo = $this->modelo('EncuestaModelo');
        $this->datos['menuActivo'] = '';
    }

    // /responder/{token}
    public function index($token = null){
        if(!$token){
            // Sin token → portal de selección de encuestas
            $this->portal();
            return;
        }

        $encuesta = $this->encuestaModelo->getEncuestaByToken($token);

        if(!$encuesta){
            $this->vista('encuestas/encuesta_no_encontrada', $this->datos);
            return;
        }

        if(!$encuesta->activa){
            $this->datos['encuesta'] = $encuesta;
            $this->vista('encuestas/encuesta_cerrada', $this->datos);
            return;
        }

        // ── Sesión y cookie siempre primero ───────────────────────────
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        // Obtener o generar el UUID anónimo del navegador
        $uid = $this->_obtenerUidAnonimo();

        // Hash irreversible para esta encuesta concreta
        $token_anonimo = hash('sha256', $uid . '|' . $encuesta->id_encuesta);

        // ── Verificación del código de acceso ─────────────────────────
        $requiere_codigo   = !empty($encuesta->codigo_acceso);
        $codigo_verificado = !empty($_SESSION['codigo_ok_' . $encuesta->id_encuesta]);

        if($requiere_codigo && !$codigo_verificado){
            if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['codigo_acceso'])){
                if(trim($_POST['codigo_acceso']) === trim($encuesta->codigo_acceso)){
                    $_SESSION['codigo_ok_' . $encuesta->id_encuesta] = true;
                    header('Location: ' . RUTA_CPIFP . RUTA_URL . '/responder/' . $token);
                    exit();
                } else {
                    $this->datos['encuesta']     = $encuesta;
                    $this->datos['error_codigo'] = 'Código incorrecto. Inténtalo de nuevo.';
                    $this->vista('encuestas/codigo_acceso', $this->datos);
                    return;
                }
            }
            $this->datos['encuesta']     = $encuesta;
            $this->datos['error_codigo'] = null;
            $this->vista('encuestas/codigo_acceso', $this->datos);
            return;
        }

        // ── Comprobar si ya respondió ─────────────────────────────────
        if($this->encuestaModelo->yaRespondio($encuesta->id_encuesta, $token_anonimo)){
            $this->datos['encuesta']   = $encuesta;
            $this->datos['pendientes'] = $this->_getPendientesGrupo($encuesta, $token_anonimo);
            $this->vista('encuestas/ya_respondida', $this->datos);
            return;
        }

        // ── POST: guardar respuesta ───────────────────────────────────
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $puntuaciones = $_POST['preguntas'] ?? [];
            $comentarios  = [
                'mejor' => $_POST['comentario_mejor'] ?? '',
                'peor'  => $_POST['comentario_peor']  ?? '',
                'libre' => $_POST['comentario_libre']  ?? '',
            ];
            $ip = $_SERVER['REMOTE_ADDR'];

            $preguntas = $this->encuestaModelo->getPreguntasEncuesta($encuesta->id_encuesta);
            $valido    = true;
            foreach($preguntas as $p){
                $tipo = $p->tipo_respuesta ?? 'puntuacion';
                $val  = $puntuaciones[$p->id_pregunta] ?? null;

                if($tipo === 'puntuacion'){
                    if($val === null || (int)$val < 1 || (int)$val > 10){
                        $valido = false; break;
                    }
                } elseif($tipo === 'opciones'){
                    $opts = !empty($p->opciones_json) ? json_decode($p->opciones_json, true) : [];
                    if($val === null || (int)$val < 0 || (int)$val >= count($opts)){
                        $valido = false; break;
                    }
                } elseif($tipo === 'numerica'){
                    if($val === null || !is_numeric($val) || (int)$val < 0){
                        $valido = false; break;
                    }
                }
            }

            if(!$valido){
                $this->datos['encuesta']  = $encuesta;
                $this->datos['preguntas'] = $preguntas;
                $this->datos['error']     = 'Por favor, responde a todas las preguntas.';
                $this->datos['post']      = $_POST;
                $this->vista('encuestas/formulario', $this->datos);
                return;
            }

            $id_respuesta = $this->encuestaModelo->addRespuesta(
                $encuesta->id_encuesta, $puntuaciones, $comentarios, $ip, $token_anonimo
            );

            if($id_respuesta){
                $this->datos['encuesta']   = $encuesta;
                $this->datos['pendientes'] = $this->_getPendientesGrupo($encuesta, $token_anonimo);
                $this->vista('encuestas/gracias', $this->datos);
            } else {
                $this->datos['encuesta']  = $encuesta;
                $this->datos['preguntas'] = $preguntas;
                $this->datos['error']     = 'Ha ocurrido un error. Inténtalo de nuevo.';
                $this->datos['post']      = $_POST;
                $this->vista('encuestas/formulario', $this->datos);
            }

        } else {
            $this->datos['encuesta']  = $encuesta;
            $this->datos['preguntas'] = $this->encuestaModelo->getPreguntasEncuesta($encuesta->id_encuesta);
            $this->datos['error']     = null;
            $this->datos['post']      = [];
            $this->vista('encuestas/formulario', $this->datos);
        }
    }

    // ─── Helpers privados ────────────────────────────────────────────

    /**
     * Portal público de selección de encuestas.
     * Muestra acordeón Departamento → Ciclo → Curso → Módulos.
     */
    public function portal(){
        if(session_status() === PHP_SESSION_NONE) session_start();

        // UID anónimo para saber qué encuestas ya respondió este navegador
        $uid = $this->_obtenerUidAnonimo();

        $curso_academico = cursoAcademicoActual();

        // Cargar jerarquía completa en una sola query
        $filas = $this->encuestaModelo->getJerarquiaEncuestasActivas($curso_academico);

        // Construir árbol: [dept][ciclo][curso][] = encuesta
        $arbol = [];
        foreach($filas as $f){
            $did = $f->id_departamento;
            $cid = $f->id_ciclo;
            $uid_curso = $f->id_curso;
            if(!isset($arbol[$did])){
                $arbol[$did] = [
                    'label'  => $f->departamento,
                    'ciclos' => []
                ];
            }
            if(!isset($arbol[$did]['ciclos'][$cid])){
                $arbol[$did]['ciclos'][$cid] = [
                    'label'  => $f->ciclo,
                    'corto'  => $f->ciclo_corto,
                    'cursos' => []
                ];
            }
            if(!isset($arbol[$did]['ciclos'][$cid]['cursos'][$uid_curso])){
                $arbol[$did]['ciclos'][$cid]['cursos'][$uid_curso] = [
                    'label'     => $f->nombre_curso,
                    'encuestas' => []
                ];
            }
            // Calcular token_anonimo para esta encuesta y ver si ya fue respondida
            $token_anonimo = hash('sha256', $uid . '|' . $f->id_encuesta);
            $respondida = $this->encuestaModelo->yaRespondio($f->id_encuesta, $token_anonimo);

            $arbol[$did]['ciclos'][$cid]['cursos'][$uid_curso]['encuestas'][] = [
                'id_encuesta'       => $f->id_encuesta,
                'token_publico'     => $f->token_publico,
                'codigo_acceso'     => $f->codigo_acceso,
                'nombre_evaluacion' => $f->nombre_evaluacion,
                'nombre_profesor'   => $f->nombre_profesor,
                'nombre_modulo'     => $f->nombre_modulo,
                'nombre_corto'      => $f->nombre_corto,
                'respondida'        => $respondida,
            ];
        }

        $this->datos['arbol']           = $arbol;
        $this->datos['curso_academico'] = $curso_academico;
        $this->vista('encuestas/portal', $this->datos);
    }

    private function _obtenerUidAnonimo(){
        $cookie = 'enc_uid';
        if(!empty($_COOKIE[$cookie]) && preg_match('/^[a-f0-9]{64}$/', $_COOKIE[$cookie])){
            $uid = $_COOKIE[$cookie];
        } else {
            $uid = bin2hex(random_bytes(32));
        }
        setcookie($cookie, $uid, time() + 365 * 24 * 3600, '/', '', false, true);
        return $uid;
    }

    private function _getPendientesGrupo($encuesta, $token_anonimo){
        $infopm = $this->encuestaModelo->getInfoProfesorModulo($encuesta->id_profesor_modulo ?? 0);
        if(!$infopm) return [];
        return $this->encuestaModelo->getEncuestasPendientesGrupo(
            $infopm->id_curso, $encuesta->curso_academico, $token_anonimo
        );
    }
}
