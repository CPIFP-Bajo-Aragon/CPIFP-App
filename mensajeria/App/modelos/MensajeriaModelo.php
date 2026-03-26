<?php

class MensajeriaModelo {

    private $db;

    public function __construct() {
        $this->db = new Base();
    }

    // ── USUARIOS ─────────────────────────────────────────────
    public function getProfesoresActivos() {
        $this->db->query("SELECT DISTINCT id_profesor, nombre_completo, email
                          FROM cpifp_profesor
                          WHERE activo = 1
                          ORDER BY nombre_completo");
        return $this->db->registros();
    }

    public function getProfesoresPorDepartamento($id_departamento) {
        $this->db->query("SELECT DISTINCT p.id_profesor, p.nombre_completo, p.email
                          FROM cpifp_profesor p
                          INNER JOIN cpifp_profesor_departamento pd ON pd.id_profesor = p.id_profesor
                          WHERE p.activo = 1
                          AND pd.id_departamento = :id_dep
                          ORDER BY p.nombre_completo");
        $this->db->bind(':id_dep', $id_departamento);
        return $this->db->registros();
    }

    public function getDepartamentos() {
        $this->db->query("SELECT id_departamento, departamento
                          FROM cpifp_departamento
                          ORDER BY departamento");
        return $this->db->registros();
    }

    public function getProfesor($id_profesor) {
        $this->db->query("SELECT id_profesor, nombre_completo, email
                          FROM cpifp_profesor WHERE id_profesor = :id");
        $this->db->bind(':id', $id_profesor);
        return $this->db->registro();
    }

    public function getRolesProfesor($id_profesor) {
        $this->db->query("SELECT id_rol FROM cpifp_profesor_departamento
                          WHERE id_profesor = :id");
        $this->db->bind(':id', $id_profesor);
        return $this->db->registros();
    }

    // ── BANDEJA DE ENTRADA ───────────────────────────────────
    public function getMensajesRecibidos($id_profesor, $pagina) {
        $offset = ($pagina - 1) * NUM_ITEMS_BY_PAGE;
        $this->db->query(
            "SELECT m.id, m.asunto, m.fecha_envio, m.tiene_adjunto,
                    p.nombre_completo AS remitente,
                    d.leido, d.id AS id_dest
             FROM mensj_destinatario d
             INNER JOIN mensj_mensaje m ON m.id = d.id_mensaje
             INNER JOIN cpifp_profesor p ON p.id_profesor = m.id_remitente
             WHERE d.id_profesor = :id AND d.eliminado = 0
             ORDER BY m.fecha_envio DESC
             LIMIT :lim OFFSET :off"
        );
        $this->db->bind(':id',  $id_profesor);
        $this->db->bind(':lim', NUM_ITEMS_BY_PAGE);
        $this->db->bind(':off', $offset);
        return $this->db->registros();
    }

    public function getNumMensajesRecibidos($id_profesor) {
        $this->db->query(
            "SELECT COUNT(*) AS total
             FROM mensj_destinatario d
             INNER JOIN mensj_mensaje m ON m.id = d.id_mensaje
             WHERE d.id_profesor = :id AND d.eliminado = 0"
        );
        $this->db->bind(':id', $id_profesor);
        $r = $this->db->registro();
        return $r ? (int)$r->total : 0;
    }

    public function getNumNoLeidos($id_profesor) {
        $this->db->query(
            "SELECT COUNT(*) AS total
             FROM mensj_destinatario
             WHERE id_profesor = :id AND leido = 0 AND eliminado = 0"
        );
        $this->db->bind(':id', $id_profesor);
        $r = $this->db->registro();
        return $r ? (int)$r->total : 0;
    }

    // ── MENSAJES ENVIADOS ────────────────────────────────────
    public function getMensajesEnviados($id_profesor, $pagina) {
        $offset = ($pagina - 1) * NUM_ITEMS_BY_PAGE;
        $this->db->query(
            "SELECT m.id, m.asunto, m.fecha_envio, m.tiene_adjunto,
                    GROUP_CONCAT(p.nombre_completo ORDER BY p.nombre_completo SEPARATOR ', ') AS destinatarios
             FROM mensj_mensaje m
             INNER JOIN mensj_destinatario d ON d.id_mensaje = m.id
             INNER JOIN cpifp_profesor p ON p.id_profesor = d.id_profesor
             WHERE m.id_remitente = :id
             GROUP BY m.id
             ORDER BY m.fecha_envio DESC
             LIMIT :lim OFFSET :off"
        );
        $this->db->bind(':id',  $id_profesor);
        $this->db->bind(':lim', NUM_ITEMS_BY_PAGE);
        $this->db->bind(':off', $offset);
        return $this->db->registros();
    }

    public function getNumMensajesEnviados($id_profesor) {
        $this->db->query(
            "SELECT COUNT(*) AS total FROM mensj_mensaje WHERE id_remitente = :id"
        );
        $this->db->bind(':id', $id_profesor);
        $r = $this->db->registro();
        return $r ? (int)$r->total : 0;
    }

    // ── VER MENSAJE ──────────────────────────────────────────
    public function getMensaje($id_mensaje) {
        $this->db->query(
            "SELECT m.*, p.nombre_completo AS remitente, p.email AS email_remitente
             FROM mensj_mensaje m
             INNER JOIN cpifp_profesor p ON p.id_profesor = m.id_remitente
             WHERE m.id = :id"
        );
        $this->db->bind(':id', $id_mensaje);
        return $this->db->registro();
    }

    public function getDestinatariosMensaje($id_mensaje) {
        $this->db->query(
            "SELECT p.nombre_completo, p.email, d.leido, d.fecha_lectura
             FROM mensj_destinatario d
             INNER JOIN cpifp_profesor p ON p.id_profesor = d.id_profesor
             WHERE d.id_mensaje = :id
             ORDER BY p.nombre_completo"
        );
        $this->db->bind(':id', $id_mensaje);
        return $this->db->registros();
    }

    public function getAdjuntos($id_mensaje) {
        $this->db->query(
            "SELECT * FROM mensj_adjunto WHERE id_mensaje = :id ORDER BY id"
        );
        $this->db->bind(':id', $id_mensaje);
        return $this->db->registros();
    }

    public function getAdjunto($id_adjunto) {
        $this->db->query("SELECT * FROM mensj_adjunto WHERE id = :id");
        $this->db->bind(':id', $id_adjunto);
        return $this->db->registro();
    }

    // Marca lectura cuando el receptor abre el mensaje
    public function marcarLeido($id_mensaje, $id_profesor) {
        $this->db->query(
            "UPDATE mensj_destinatario
             SET leido = 1, fecha_lectura = NOW()
             WHERE id_mensaje = :msg AND id_profesor = :prof AND leido = 0"
        );
        $this->db->bind(':msg',  $id_mensaje);
        $this->db->bind(':prof', $id_profesor);
        return $this->db->execute();
    }

    // Verifica que el usuario tiene acceso al mensaje (es remitente o destinatario)
    public function tieneAcceso($id_mensaje, $id_profesor) {
        $this->db->query(
            "SELECT COUNT(*) AS n FROM mensj_mensaje WHERE id = :msg AND id_remitente = :prof
             UNION ALL
             SELECT COUNT(*) FROM mensj_destinatario WHERE id_mensaje = :msg2 AND id_profesor = :prof2"
        );
        $this->db->bind(':msg',   $id_mensaje);
        $this->db->bind(':prof',  $id_profesor);
        $this->db->bind(':msg2',  $id_mensaje);
        $this->db->bind(':prof2', $id_profesor);
        $rows = $this->db->registros();
        foreach ($rows as $r) { if ((int)$r->n > 0) return true; }
        return false;
    }

    // Borrado lógico para el receptor
    public function eliminarParaDestinatario($id_mensaje, $id_profesor) {
        $this->db->query(
            "UPDATE mensj_destinatario SET eliminado = 1
             WHERE id_mensaje = :msg AND id_profesor = :prof"
        );
        $this->db->bind(':msg',  $id_mensaje);
        $this->db->bind(':prof', $id_profesor);
        return $this->db->execute();
    }

    // ── ENVIAR MENSAJE ───────────────────────────────────────
    public function insertarMensaje($id_remitente, $asunto, $cuerpo, $tieneAdjunto) {
        $this->db->query(
            "INSERT INTO mensj_mensaje (id_remitente, asunto, cuerpo, tiene_adjunto)
             VALUES (:rem, :asunto, :cuerpo, :adj)"
        );
        $this->db->bind(':rem',    $id_remitente);
        $this->db->bind(':asunto', $asunto);
        $this->db->bind(':cuerpo', $cuerpo);
        $this->db->bind(':adj',    $tieneAdjunto);
        return $this->db->executeLastId();
    }

    public function insertarDestinatario($id_mensaje, $id_profesor) {
        $this->db->query(
            "INSERT IGNORE INTO mensj_destinatario (id_mensaje, id_profesor)
             VALUES (:msg, :prof)"
        );
        $this->db->bind(':msg',  $id_mensaje);
        $this->db->bind(':prof', $id_profesor);
        return $this->db->execute();
    }

    public function insertarAdjunto($id_mensaje, $nombreOrig, $nombreDisco, $mime, $tamanio) {
        $this->db->query(
            "INSERT INTO mensj_adjunto (id_mensaje, nombre_orig, nombre_disco, mime, tamanio)
             VALUES (:msg, :orig, :disco, :mime, :tam)"
        );
        $this->db->bind(':msg',   $id_mensaje);
        $this->db->bind(':orig',  $nombreOrig);
        $this->db->bind(':disco', $nombreDisco);
        $this->db->bind(':mime',  $mime);
        $this->db->bind(':tam',   $tamanio);
        return $this->db->execute();
    }

    // ── CONFIGURACION ────────────────────────────────────────
    public function getConfig() {
        $this->db->query("SELECT clave, valor, descripcion FROM mensj_config ORDER BY clave");
        $rows = $this->db->registros();
        $cfg = [];
        foreach ($rows as $r) { $cfg[$r->clave] = $r; }
        return $cfg;
    }

    public function setConfig($clave, $valor) {
        $this->db->query(
            "INSERT INTO mensj_config (clave, valor)
             VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE valor = :v2"
        );
        $this->db->bind(':k',  $clave);
        $this->db->bind(':v',  $valor);
        $this->db->bind(':v2', $valor);
        return $this->db->execute();
    }

    // ── CRON: adjuntos caducados ─────────────────────────────
    public function getAdjuntosCaducados($dias) {
        $this->db->query(
            "SELECT * FROM mensj_adjunto
             WHERE fecha_subida < DATE_SUB(NOW(), INTERVAL :dias DAY)"
        );
        $this->db->bind(':dias', $dias);
        return $this->db->registros();
    }

    public function borrarAdjuntoBD($id) {
        $this->db->query("DELETE FROM mensj_adjunto WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
