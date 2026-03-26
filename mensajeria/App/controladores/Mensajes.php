<?php

class Mensajes extends Controlador {

    private $modelo;

    public function __construct() {
        Sesion::iniciarSesion($this->datos);
        $this->modelo = $this->modelo('MensajeriaModelo');

        $roles = $this->modelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($roles);

        // Conteo de no leidos para el badge del menu
        $this->datos['noLeidos'] = $this->modelo->getNumNoLeidos(
            $this->datos['usuarioSesion']->id_profesor
        );
    }


    // =====================================================================
    // BANDEJA DE ENTRADA
    // =====================================================================
    public function index() {
        $this->bandeja();
    }

    public function bandeja() {
        $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $id     = $this->datos['usuarioSesion']->id_profesor;

        $this->datos['mensajes']     = $this->modelo->getMensajesRecibidos($id, $pagina);
        $this->datos['totalPaginas'] = ceil($this->modelo->getNumMensajesRecibidos($id) / NUM_ITEMS_BY_PAGE);
        $this->datos['paginaActual'] = $pagina;
        $this->datos['menuActivo']   = 'bandeja';

        $this->vista('mensajes/bandeja', $this->datos);
    }


    // =====================================================================
    // MENSAJES ENVIADOS
    // =====================================================================
    public function enviados() {
        $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $id     = $this->datos['usuarioSesion']->id_profesor;

        $this->datos['mensajes']     = $this->modelo->getMensajesEnviados($id, $pagina);
        $this->datos['totalPaginas'] = ceil($this->modelo->getNumMensajesEnviados($id) / NUM_ITEMS_BY_PAGE);
        $this->datos['paginaActual'] = $pagina;
        $this->datos['menuActivo']   = 'enviados';

        $this->vista('mensajes/enviados', $this->datos);
    }


    // =====================================================================
    // VER MENSAJE
    // =====================================================================
    public function ver($id_mensaje = null) {
        if (!$id_mensaje) redireccionar('/Mensajes/bandeja');

        $id_prof = $this->datos['usuarioSesion']->id_profesor;

        if (!$this->modelo->tieneAcceso($id_mensaje, $id_prof)) {
            die('Acceso denegado.');
        }

        // Marcar como leido si es destinatario
        $this->modelo->marcarLeido($id_mensaje, $id_prof);

        $this->datos['mensaje']      = $this->modelo->getMensaje($id_mensaje);
        $this->datos['destinatarios'] = $this->modelo->getDestinatariosMensaje($id_mensaje);
        $this->datos['adjuntos']     = $this->modelo->getAdjuntos($id_mensaje);
        $this->datos['menuActivo']   = 'bandeja';
        $this->datos['esRemitente']  = ($this->datos['mensaje']->id_remitente == $id_prof);

        $this->vista('mensajes/ver', $this->datos);
    }


    // =====================================================================
    // DESCARGAR ADJUNTO
    // =====================================================================
    public function descargar($id_adjunto = null) {
        if (!$id_adjunto) redireccionar('/Mensajes/bandeja');

        $adj = $this->modelo->getAdjunto($id_adjunto);
        if (!$adj) die('Adjunto no encontrado.');

        // Verificar que el usuario tiene acceso al mensaje
        $id_prof = $this->datos['usuarioSesion']->id_profesor;
        if (!$this->modelo->tieneAcceso($adj->id_mensaje, $id_prof)) {
            die('Acceso denegado.');
        }

        $ruta = RUTA_ADJUNTOS . $adj->nombre_disco;
        if (!file_exists($ruta)) die('Fichero no disponible (puede haber sido eliminado por el sistema).');

        header('Content-Type: '        . $adj->mime);
        header('Content-Disposition: attachment; filename="' . $adj->nombre_orig . '"');
        header('Content-Length: '      . filesize($ruta));
        header('Cache-Control: private');
        readfile($ruta);
        exit();
    }


    // =====================================================================
    // ELIMINAR MENSAJE (borrado logico para el receptor)
    // =====================================================================
    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_mensaje'])) {
            $id_prof = $this->datos['usuarioSesion']->id_profesor;
            $this->modelo->eliminarParaDestinatario((int)$_POST['id_mensaje'], $id_prof);
        }
        redireccionar('/Mensajes/bandeja');
    }


    // =====================================================================
    // NUEVO MENSAJE — formulario
    // =====================================================================
    public function nuevo() {
        $this->datos['profesores']   = $this->modelo->getProfesoresActivos();
        $this->datos['departamentos'] = $this->modelo->getDepartamentos();
        $this->datos['menuActivo']   = 'nuevo';
        $this->vista('mensajes/nuevo', $this->datos);
    }


    // =====================================================================
    // ENVIAR MENSAJE
    // =====================================================================
    public function enviar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redireccionar('/Mensajes/nuevo');
        }

        $id_remitente = $this->datos['usuarioSesion']->id_profesor;
        $asunto  = trim($_POST['asunto']  ?? '');
        $cuerpo  = trim($_POST['cuerpo']  ?? '');
        $tipo    = $_POST['tipo_destino'] ?? 'individual';  // individual | departamento | todos

        if (empty($asunto) || empty($cuerpo)) {
            $this->datos['error'] = 'El asunto y el cuerpo son obligatorios.';
            $this->datos['profesores']    = $this->modelo->getProfesoresActivos();
            $this->datos['departamentos'] = $this->modelo->getDepartamentos();
            $this->datos['menuActivo']    = 'nuevo';
            $this->datos['post']          = $_POST;
            $this->vista('mensajes/nuevo', $this->datos);
            return;
        }

        // Construir lista de destinatarios
        $destinatariosIds = [];

        if ($tipo === 'todos') {
            $profs = $this->modelo->getProfesoresActivos();
            foreach ($profs as $p) {
                if ($p->id_profesor != $id_remitente)
                    $destinatariosIds[] = (int)$p->id_profesor;
            }

        } elseif ($tipo === 'departamento') {
            // La vista muestra los miembros del departamento con checkboxes marcados.
            // El usuario puede desmarcar los que quiera excluir antes de enviar.
            // Se procesan igual que individual: solo los marcados llegan en destinatarios[].
            $seleccionados = $_POST['destinatarios'] ?? [];
            foreach ((array)$seleccionados as $id) {
                $id = (int)$id;
                if ($id && $id != $id_remitente) $destinatariosIds[] = $id;
            }

        } else {
            // individual o multiseleccion
            $seleccionados = $_POST['destinatarios'] ?? [];
            foreach ((array)$seleccionados as $id) {
                $id = (int)$id;
                if ($id && $id != $id_remitente) $destinatariosIds[] = $id;
            }
        }

        $destinatariosIds = array_unique($destinatariosIds);

        if (empty($destinatariosIds)) {
            $this->datos['error'] = 'Debes seleccionar al menos un destinatario.';
            $this->datos['profesores']    = $this->modelo->getProfesoresActivos();
            $this->datos['departamentos'] = $this->modelo->getDepartamentos();
            $this->datos['menuActivo']    = 'nuevo';
            $this->datos['post']          = $_POST;
            $this->vista('mensajes/nuevo', $this->datos);
            return;
        }

        // Gestionar adjuntos
        $adjuntosSubidos = [];
        $cfg = $this->modelo->getConfig();
        $maxMB = isset($cfg['max_tam_adjunto_mb']) ? (int)$cfg['max_tam_adjunto_mb']->valor : 10;
        $extPermitidas = isset($cfg['extensiones_permitidas'])
            ? explode(',', $cfg['extensiones_permitidas']->valor) : [];

        if (!empty($_FILES['adjuntos']['name'][0])) {
            foreach ($_FILES['adjuntos']['name'] as $i => $nombreOrig) {
                if ($_FILES['adjuntos']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $ext   = strtolower(pathinfo($nombreOrig, PATHINFO_EXTENSION));
                $tam   = $_FILES['adjuntos']['size'][$i];
                $mime  = $_FILES['adjuntos']['type'][$i];

                if (!empty($extPermitidas) && !in_array($ext, $extPermitidas)) continue;
                if ($tam > $maxMB * 1024 * 1024) continue;

                $nombreDisco = uniqid('adj_', true) . '.' . $ext;
                $destino     = RUTA_ADJUNTOS . $nombreDisco;

                if (move_uploaded_file($_FILES['adjuntos']['tmp_name'][$i], $destino)) {
                    $adjuntosSubidos[] = [
                        'nombre_orig'  => $nombreOrig,
                        'nombre_disco' => $nombreDisco,
                        'mime'         => $mime,
                        'tamanio'      => $tam,
                    ];
                }
            }
        }

        $tieneAdjunto = !empty($adjuntosSubidos) ? 1 : 0;

        // Insertar mensaje en BD
        $id_mensaje = $this->modelo->insertarMensaje($id_remitente, $asunto, $cuerpo, $tieneAdjunto);
        if (!$id_mensaje) {
            die('Error al guardar el mensaje en la base de datos.');
        }

        foreach ($adjuntosSubidos as $adj) {
            $this->modelo->insertarAdjunto(
                $id_mensaje,
                $adj['nombre_orig'],
                $adj['nombre_disco'],
                $adj['mime'],
                $adj['tamanio']
            );
        }

        // Insertar destinatarios y enviar email a cada uno
        $remitente = $this->datos['usuarioSesion'];
        foreach ($destinatariosIds as $id_dest) {
            $this->modelo->insertarDestinatario($id_mensaje, $id_dest);
            $prof = $this->modelo->getProfesor($id_dest);
            if ($prof && !empty($prof->email)) {
                $urlMensaje = RUTA_CPIFP . '/mensajeria/Mensajes/ver/' . $id_mensaje;
                $cuerpoEmail = "
                    <p>Tienes un nuevo mensaje interno de <strong>{$remitente->nombre_completo}</strong>.</p>
                    <p><strong>Asunto:</strong> " . htmlspecialchars($asunto) . "</p>
                    <hr>
                    <p>" . nl2br(htmlspecialchars($cuerpo)) . "</p>
                    <hr>
                    <p><a href='{$urlMensaje}'>Ver mensaje en la aplicacion</a></p>
                    <p><small>Este correo es una notificacion automatica. No respondas a este email.</small></p>
                ";
                EnviarEmail::sendEmail($prof->email, $prof->nombre_completo, '[Mensaje] ' . $asunto, $cuerpoEmail);
            }
        }

        $this->datos['okEnvio'] = count($destinatariosIds);
        $this->datos['menuActivo'] = 'nuevo';
        $this->vista('mensajes/enviado_ok', $this->datos);
    }


    // =====================================================================
    // CONFIGURACION (solo equipo directivo, rol 50)
    // =====================================================================
    public function configuracion() {
        if ($this->datos['usuarioSesion']->id_rol < 50) {
            die('Acceso restringido al Equipo Directivo.');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $claves = ['dias_borrado_adjuntos', 'max_tam_adjunto_mb', 'extensiones_permitidas'];
            foreach ($claves as $clave) {
                if (isset($_POST[$clave])) {
                    $this->modelo->setConfig($clave, trim($_POST[$clave]));
                }
            }
            $this->datos['okConfig'] = true;
        }

        $this->datos['config']     = $this->modelo->getConfig();
        $this->datos['menuActivo'] = 'config';
        $this->vista('mensajes/configuracion', $this->datos);
    }


    // =====================================================================
    // AJAX: obtener profesores de un departamento
    // =====================================================================
    public function profesoresDep() {
        header('Content-Type: application/json');
        $id_dep = (int)($_GET['id_dep'] ?? 0);
        $profs  = $id_dep ? $this->modelo->getProfesoresPorDepartamento($id_dep) : [];
        echo json_encode($profs);
        exit();
    }
}
