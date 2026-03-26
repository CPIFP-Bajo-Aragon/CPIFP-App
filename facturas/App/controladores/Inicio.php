<?php

class Inicio extends Controlador {

    private $facturaModelo;

    public function __construct() {

        Sesion::iniciarSesion($this->datos);

        $this->facturaModelo = $this->modelo('FacturaModelo');

        // Recargamos los roles desde BD ya que la sesión del principal
        // puede no incluirlos con el formato que necesita facturas
        $this->datos['usuarioSesion']->roles  = $this->facturaModelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);

        // Roles permitidos: 300 = jefe departamento (30*10), 500 = equipo directivo (50*10)
        $this->datos['rolesPermitidos'] = [300, 500];

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }

        // Cargamos los destinos según el rol
        if ($this->datos['usuarioSesion']->id_rol == 300) {
            $this->datos['destinos'] = $this->facturaModelo->getDestinos($this->datos['usuarioSesion']->id_profesor);
        } elseif ($this->datos['usuarioSesion']->id_rol == 500) {
            $this->datos['destinos'] = $this->facturaModelo->getDestinosEquipoDirectivo();
        }

        $this->datos['menuActivo'] = 'home';
    }


    public function index() {
        // Si no hay destino seleccionado en sesión y hay destinos disponibles,
        // asignamos el primero automáticamente
        if (!isset($this->datos['persistencia']['idDestinoSeleccionado']) && !empty($this->datos['destinos'])) {
            Sesion::addPersistencia($this->datos, 'nombreDestinoSeleccionado', $this->datos['destinos'][0]->Depart_Servicio);
            Sesion::addPersistencia($this->datos, 'idDestinoSeleccionado',     $this->datos['destinos'][0]->Destino_Id);
        }

        $this->vista('index', $this->datos);
    }

}
