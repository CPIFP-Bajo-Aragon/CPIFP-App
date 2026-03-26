<?php

/**
 * Portal público de encuestas.
 * No requiere login. Permite a los alumnos navegar hasta su encuesta
 * seleccionando ciclo formativo → grupo → encuesta.
 */
class Portal extends Controlador {

    private $encuestaModelo;

    public function __construct(){
        // Sin Sesion::iniciarSesion — acceso público
        $this->encuestaModelo = $this->modelo('EncuestaModelo');
        $this->datos['menuActivo'] = '';

        // Generar o renovar la cookie anónima en TODOS los métodos del portal,
        // incluidas las llamadas AJAX, para que enc_uid siempre esté disponible
        // al calcular ya_respondida en encuestas().
        $this->_asegurarUidAnonimo();
    }

    public function index(){
        $curso = $_GET['curso'] ?? cursoAcademicoActual();
        $this->datos['curso_actual'] = $curso;
        $this->datos['ciclos']       = $this->encuestaModelo->getCiclosPublico($curso);
        $this->datos['cursos_acad']  = $this->_getCursosAcademicosPublicos();
        $this->vista('portal/index', $this->datos);
    }

    // AJAX: cursos de un ciclo
    public function cursos($id_ciclo){
        $curso = $_GET['curso'] ?? cursoAcademicoActual();
        $this->vistaApi($this->encuestaModelo->getCursosPublico($id_ciclo, $curso));
    }

    // AJAX: encuestas de un grupo — incluye estado ya_respondida por cookie
    public function encuestas($id_curso){
        $curso     = $_GET['curso'] ?? cursoAcademicoActual();
        $encuestas = $this->encuestaModelo->getEncuestasActivasGrupo($id_curso, $curso);

        // La cookie ya está garantizada por __construct → _asegurarUidAnonimo()
        $uid = $_COOKIE['enc_uid'] ?? '';

        foreach($encuestas as $enc){
            $token_anonimo      = $uid ? hash('sha256', $uid . '|' . $enc->id_encuesta) : null;
            $enc->ya_respondida = $token_anonimo
                ? $this->encuestaModelo->yaRespondio($enc->id_encuesta, $token_anonimo)
                : false;
        }

        $this->vistaApi($encuestas);
    }

    // ── Helpers privados ─────────────────────────────────────────────

    /**
     * Asegura que la cookie enc_uid existe y está renovada.
     * Mismo algoritmo que Responder.php para que los hashes coincidan.
     */
    private function _asegurarUidAnonimo(){
        $cookie = 'enc_uid';
        if(!empty($_COOKIE[$cookie]) && preg_match('/^[a-f0-9]{64}$/', $_COOKIE[$cookie])){
            $uid = $_COOKIE[$cookie];
        } else {
            $uid = bin2hex(random_bytes(32));
        }
        // Renovar duración (1 año). setcookie() debe llamarse antes de cualquier salida HTML.
        setcookie($cookie, $uid, time() + 365 * 24 * 3600, '/', '', false, true);
        $_COOKIE[$cookie] = $uid; // disponible en la misma petición (llamadas AJAX)
    }

    private function _getCursosAcademicosPublicos(){
        try {
            $db = new Base;
            $db->query("SELECT DISTINCT curso_academico
                          FROM en_encuesta
                         WHERE activa = 1 AND id_tipo_encuesta = 1
                         ORDER BY curso_academico DESC");
            return $db->registros();
        } catch(Exception $e){ return []; }
    }
}
