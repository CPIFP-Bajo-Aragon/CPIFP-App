<?php

class Inicio extends Controlador {

    private $modelo;

    public function __construct() {
        Sesion::iniciarSesion($this->datos);

        $this->modelo = $this->modelo('MensajeriaModelo');

        // Cargar rol del usuario
        $roles = $this->modelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($roles);

        // La mensajería es accesible para cualquier usuario activo con sesión.
        // Si por algún motivo el rol es 0 (sin asignación) también se permite:
        // el control fino de quién puede enviar a quién ya lo gestiona el modelo.

        // Conteo de no leídos para el badge del menú
        $this->datos['noLeidos'] = $this->modelo->getNumNoLeidos(
            $this->datos['usuarioSesion']->id_profesor
        );

        $this->datos['menuActivo'] = 'inicio';
    }


    public function index() {
        $id = $this->datos['usuarioSesion']->id_profesor;

        // Últimos 5 mensajes recibidos no leídos para mostrar en el panel
        $this->datos['ultimosNoLeidos'] = $this->modelo->getMensajesRecibidos($id, 1);
        $this->datos['totalNoLeidos']   = $this->datos['noLeidos'];
        $this->datos['totalEnviados']   = $this->modelo->getNumMensajesEnviados($id);

        $this->vista('index', $this->datos);
    }
}
