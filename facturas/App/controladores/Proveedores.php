<?php

class Proveedores extends Controlador {

    private $facturaModelo;

    public function __construct() {

        Sesion::iniciarSesion($this->datos);

        $this->facturaModelo = $this->modelo('FacturaModelo');

        $this->datos['usuarioSesion']->roles  = $this->facturaModelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);

        $this->datos['rolesPermitidos'] = [300, 500];

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            echo 'No tienes privilegios!!!';
            exit();
        }

        // Cargar destinos según rol (necesario para el destino en la navbar)
        if ($this->datos['usuarioSesion']->id_rol == 300) {
            $this->datos['destinos'] = $this->facturaModelo->getDestinos($this->datos['usuarioSesion']->id_profesor);
        } elseif ($this->datos['usuarioSesion']->id_rol == 500) {
            $this->datos['destinos'] = $this->facturaModelo->getDestinosEquipoDirectivo();
        }

        $this->datos['menuActivo'] = 'proveedores';
    }


    public function index() {
        $this->vista('proveedores/indiceProveedores', $this->datos);
    }


    /************************ LISTA DE PROVEEDORES ****************************/

    public function listaProveedores() {
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $cif    = isset($_POST['CIF'])    ? trim($_POST['CIF'])    : '';
        $nombre = isset($_POST['Nombre']) ? trim($_POST['Nombre']) : '';

        $this->datos['proveedores']    = $this->facturaModelo->getProveedoresConValoraciones(-1, $pagina, $cif, $nombre);
        $this->datos['paginaAcual']    = $pagina;
        $this->datos['totalPaginas']   = ceil($this->facturaModelo->getNumProveedores(-1, $cif, $nombre) / NUM_ITEMS_BY_PAGE);
        $this->datos['busquedaCIF']    = $cif;
        $this->datos['busquedaNombre'] = $nombre;

        $this->vista('proveedores/listaProveedores', $this->datos);
    }


    /************************ VER PROVEEDOR (AJAX) ****************************/

    public function verProveedor() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['CIF'])) {
            $proveedor = $this->facturaModelo->getProveedor(trim($_POST['CIF']));
            header('Content-Type: application/json');
            echo json_encode($proveedor);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'CIF requerido']);
        }
        exit();
    }


    /************************ EDITAR PROVEEDOR ****************************/

    public function editarProveedor() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['CIF'])) {
            $cif = trim($_POST['CIF']);
            $datos = [
                'Nombre'    => $_POST['Nombre']    ?? '',
                'Alias'     => $_POST['Alias']     ?? '',
                'Telefono'  => $_POST['Telefono']  ?? '',
                'Direccion' => $_POST['Direccion'] ?? '',
                'CP'        => $_POST['CP']        ?? '',
                'Localidad' => $_POST['Localidad'] ?? '',
                'Provincia' => $_POST['Provincia'] ?? '',
                'Pais'      => $_POST['Pais']      ?? '',
                'Externo'   => $_POST['Externo']   ?? 'S',
            ];

            if ($this->facturaModelo->editarProveedor($cif, $datos)) {
                echo "<script>
                    alert('Proveedor actualizado correctamente.');
                    window.location.href = '" . RUTA_URL . "/Proveedores/listaProveedores';
                </script>";
            } else {
                die('Error al actualizar el proveedor.');
            }
        } else {
            redireccionar('/Proveedores/listaProveedores');
        }
    }


    /************************ BORRAR PROVEEDOR ****************************/

    public function borrarProveedor() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['CIF'])) {
            $cif = trim($_POST['CIF']);

            if ($this->facturaModelo->borrarProveedor($cif)) {
                redireccionar('/Proveedores/listaProveedores');
            } else {
                die('Error al borrar el proveedor. Es posible que tenga facturas asociadas.');
            }
        } else {
            redireccionar('/Proveedores/listaProveedores');
        }
    }


    /************************ AÑADIR PROVEEDOR ****************************/
    // Solo el Equipo Directivo (rol 500) puede crear proveedores.
    // El resto ve un mensaje invitándole a contactar con Secretaría.

    public function addProveedor() {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Bloqueo en servidor aunque manipulen el HTML
            if ($this->datos['usuarioSesion']->id_rol !== 500) {
                $this->vista('proveedores/addProveedor', $this->datos);
                return;
            }

            $cif = trim($_POST['CIF'] ?? '');

            if (empty($cif)) {
                $this->datos['errorCif'] = 'El CIF es obligatorio.';
                $this->vista('proveedores/addProveedor', $this->datos);
                return;
            }

            if (!empty($this->facturaModelo->getProveedor($cif))) {
                $this->datos['errorCif'] = 'Ya existe un proveedor con el CIF <strong>' . htmlspecialchars($cif) . '</strong>.';
                $this->vista('proveedores/addProveedor', $this->datos);
                return;
            }

            $datos = [
                'CIF'       => $cif,
                'Nombre'    => trim($_POST['Nombre']    ?? ''),
                'Alias'     => trim($_POST['Alias']     ?? ''),
                'Externo'   => trim($_POST['Externo']   ?? 'S'),
                'Direccion' => trim($_POST['Direccion'] ?? ''),
                'CP'        => trim($_POST['CP']        ?? ''),
                'Localidad' => trim($_POST['Localidad'] ?? ''),
                'Provincia' => trim($_POST['Provincia'] ?? ''),
                'Pais'      => trim($_POST['Pais']      ?? ''),
                'Telefono'  => trim($_POST['Telefono']  ?? ''),
            ];
            $this->facturaModelo->addProveedor($datos);
            redireccionar('/Proveedores/listaProveedores');

        } else {
            $this->vista('proveedores/addProveedor', $this->datos);
        }
    }

}
