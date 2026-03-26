<?php

class GestionFacturas extends Controlador {

    public function __construct() {

        Sesion::iniciarSesion($this->datos);

        $this->datos['menuActivo'] = 'home';

        $this->facturaModelo = $this->modelo('FacturaModelo');

        $this->datos['usuarioSesion']->roles  = $this->facturaModelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);

        $this->datos['rolesPermitidos'] = [300, 500];

        if ($this->datos['usuarioSesion']->id_rol == 300)
            $this->datos['destinos'] = $this->facturaModelo->getDestinos($this->datos['usuarioSesion']->id_profesor);

        if ($this->datos['usuarioSesion']->id_rol == 500)
            $this->datos['destinos'] = $this->facturaModelo->getDestinosEquipoDirectivo();

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            echo 'No tienes privilegios!!!';
            exit();
        }
    }


    public function index() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['destino'])) {
                foreach ($this->datos['destinos'] as $destino) {
                    if ($_POST['destino'] == $destino->Destino_Id) {
                        Sesion::addPersistencia($this->datos, 'nombreDestinoSeleccionado', $destino->Depart_Servicio);
                        Sesion::addPersistencia($this->datos, 'idDestinoSeleccionado', $destino->Destino_Id);
                    }
                }
            }
        }
        $this->vista('facturacion/gestionFacturas', $this->datos);
    }


    /************************ CONFORMIDAD DE PAGO ****************************/

    public function conformidadPago() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['descripcion_as']) && isset($_POST['inventariable']) && isset($_POST['importe'])) {
                $this->datos['confirmarFactura']['NomProveedor'] = $this->facturaModelo->getProveedor($_POST['CIF'])->Nombre;
                $this->datos['confirmarFactura']['NomDestnino']  = $_POST['NomDestnino'];
                $this->datos['confirmarFactura']['responsable']  = $_POST['responsable'];
                $this->datos['confirmarFactura']['CIF']          = $_POST['CIF'];
                $this->datos['confirmarFactura']['NFactura']     = $_POST['NFactura'];
                $this->datos['confirmarFactura']['Fconformidad'] = $_POST['Fconformidad'];
                $this->datos['confirmarFactura']['Ffactura']     = $_POST['Ffactura'];
                $this->datos['confirmarFactura']['inventariable'] = $_POST['inventariable'];
                $this->datos['confirmarFactura']['descripcion']  = $_POST['descripcion_as'];
                $this->datos['confirmarFactura']['importe']      = $_POST['importe'];
                $this->datos['confirmarFactura']['Item1']        = $_POST['Item1'];
                $this->datos['confirmarFactura']['Item2']        = $_POST['Item2'];
                $this->datos['confirmarFactura']['Item3']        = $_POST['Item3'];
                $this->datos['confirmarFactura']['Item4']        = $_POST['Item4'];

                if (isset($_POST['guardar'])) {
                    $this->vista('facturacion/validarFactura', $this->datos);
                } elseif (isset($_POST['cancelar']) && $_POST['cancelar'] == 'cancelar') {
                    $this->datos['proveedores'] = $this->facturaModelo->getProveedores(-1);
                    $this->vista('facturacion/conformidadPago', $this->datos);
                } else {
                    echo 'OPERACIÓN NO PERMITIDA'; exit();
                }
            } else {
                echo 'OPERACIÓN NO PERMITIDA'; exit();
            }
        } else {
            $this->datos['proveedores'] = $this->facturaModelo->getProveedores(1);
            $this->vista('facturacion/conformidadPago', $this->datos);
        }
    }


    public function guardarFactura() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['Aceptar']) && isset($_POST['descripcion_as']) && isset($_POST['inventariable']) && isset($_POST['importe'])) {
                $this->datos['confirmarFactura']['NomProveedor']  = $this->facturaModelo->getProveedor($_POST['CIF'])->Nombre;
                $this->datos['confirmarFactura']['NomDestnino']   = $_POST['NomDestnino'];
                $this->datos['confirmarFactura']['responsable']   = $_POST['responsable'];
                $this->datos['confirmarFactura']['CIF']           = $_POST['CIF'];
                $this->datos['confirmarFactura']['NFactura']      = $_POST['NFactura'];
                $this->datos['confirmarFactura']['Fconformidad']  = $_POST['Fconformidad'];
                $this->datos['confirmarFactura']['Ffactura']      = $_POST['Ffactura'];
                $this->datos['confirmarFactura']['inventariable']  = $_POST['inventariable'];
                $this->datos['confirmarFactura']['descripcion']   = $_POST['descripcion_as'];
                $this->datos['confirmarFactura']['importe']       = $_POST['importe'];
                $this->datos['confirmarFactura']['Item1']         = $_POST['Item1'];
                $this->datos['confirmarFactura']['Item2']         = $_POST['Item2'];
                $this->datos['confirmarFactura']['Item3']         = $_POST['Item3'];
                $this->datos['confirmarFactura']['Item4']         = $_POST['Item4'];
                $this->datos['confirmarFactura']['facturaGuardada'] = false;

                if ($nAsiento = $this->facturaModelo->addFactura($this->datos)) {
                    $this->datos['confirmarFactura']['facturaGuardada'] = true;
                    $this->datos['confirmarFactura']['nAsiento']        = $nAsiento;
                }
                $this->vista('facturacion/validarFactura', $this->datos);
            } else {
                echo 'OPERACIÓN NO PERMITIDA'; exit();
            }
        } else {
            $this->datos['proveedores'] = $this->facturaModelo->getProveedores(1);
            $this->vista('facturacion/conformidadPago', $this->datos);
        }
    }


    public function justificanteFactura($nAsiento) {
        $this->datos['factura'] = $this->facturaModelo->getFactura($nAsiento);
        $this->vista('facturacion/justificanteFactura', $this->datos);
    }


    public function imprimirFactura() {
        if (isset($_POST['N_Asiento'])) {
            $this->datos['factura'] = $this->facturaModelo->getFactura($_POST['N_Asiento']);
            $this->vista('facturacion/facturaPdf', $this->datos);
        } else {
            echo 'ERROR AL IMPRIMIR FACTURA'; exit();
        }
    }


    public function verFactura() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nAsiento'])) {
            $this->datos['factura'] = $this->facturaModelo->getFactura($_POST['nAsiento']);
            $this->vista('facturacion/verFactura', $this->datos);
        } else {
            redireccionar('/');
        }
    }


    /************************ ABONO ****************************/
    /**
     * Flujo: GET → formulario de abono
     *        POST guardar → pantalla de confirmación (validarAbono)
     *        POST cancelar → vuelve al formulario con los datos
     */
    public function abono() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Recogemos siempre los datos del POST
            if (isset($_POST['CIF']) && isset($_POST['Importe'])) {

                $prov = $this->facturaModelo->getProveedor($_POST['CIF']);

                $this->datos['confirmarAbono']['NomProveedor'] = $prov ? $prov->Nombre : '';
                $this->datos['confirmarAbono']['NomDestino']   = $_POST['NomDestino']   ?? '';
                $this->datos['confirmarAbono']['Responsable']  = $_POST['Responsable']  ?? '';
                $this->datos['confirmarAbono']['CIF']          = $_POST['CIF'];
                $this->datos['confirmarAbono']['NAbono']       = $_POST['NAbono']        ?? '';
                $this->datos['confirmarAbono']['NFactura']     = $_POST['NFactura']      ?? '';
                $this->datos['confirmarAbono']['Importe']      = $_POST['Importe'];
                $this->datos['confirmarAbono']['Faprobacion']  = $_POST['Faprobacion']   ?? date('Y-m-d');
                $this->datos['confirmarAbono']['Motivos']      = $_POST['Motivos']        ?? '';

                if (isset($_POST['guardar'])) {
                    // → pantalla de confirmación
                    $this->vista('facturacion/validarAbono', $this->datos);

                } elseif (isset($_POST['cancelar'])) {
                    // → volver al formulario con datos pre-rellenos
                    $this->datos['proveedores'] = $this->facturaModelo->getProveedores(-1);
                    $this->vista('facturacion/abono', $this->datos);

                } else {
                    echo 'OPERACIÓN NO PERMITIDA'; exit();
                }

            } else {
                $this->datos['error'] = 1;
                $this->datos['proveedores'] = $this->facturaModelo->getProveedores(-1);
                $this->vista('facturacion/abono', $this->datos);
            }

        } else {
            // GET → formulario vacío
            $this->datos['proveedores'] = $this->facturaModelo->getProveedores(-1);
            $this->vista('facturacion/abono', $this->datos);
        }
    }


    /************************ GUARDAR ABONO ****************************/

    public function guardarAbono() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Aceptar'])) {

            $datosAbono = [
                'NAbono'      => $_POST['NAbono']      ?? '',
                'NFactura'    => $_POST['NFactura']    ?? '',
                'Importe'     => $_POST['Importe'],
                'CIF'         => $_POST['CIF'],
                'Faprobacion' => $_POST['Faprobacion'],
                'Destino_Id'  => $this->datos['persistencia']['idDestinoSeleccionado'],
                'Responsable' => $_POST['Responsable'],
                'Motivos'     => $_POST['Motivos']     ?? '',
            ];

            // Rellenar confirmarAbono para que la vista lo pueda mostrar
            $this->datos['confirmarAbono'] = $_POST;
            $this->datos['confirmarAbono']['NomProveedor'] = $this->facturaModelo->getProveedor($_POST['CIF'])->Nombre ?? '';
            $this->datos['confirmarAbono']['abonoGuardado'] = false;

            if ($idAbono = $this->facturaModelo->addAbono($datosAbono)) {
                $this->datos['confirmarAbono']['abonoGuardado'] = true;
                $this->datos['confirmarAbono']['idAbono']       = $idAbono;
            }

            $this->vista('facturacion/validarAbono', $this->datos);

        } elseif (isset($_POST['cancelar'])) {
            // Volver al formulario con los datos
            $this->datos['confirmarAbono']  = $_POST;
            $this->datos['proveedores']     = $this->facturaModelo->getProveedores(-1);
            $this->vista('facturacion/abono', $this->datos);

        } else {
            redireccionar('/GestionFacturas/abono');
        }
    }


    /************************ RETENCIÓN DE FACTURA (NCF) ****************************/
    /**
     * Flujo: GET → formulario
     *        POST guardar  → pantalla de confirmación (validarRetencion)
     *        POST cancelar → vuelve al formulario con los datos
     */
    public function retencion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['CIF']) && isset($_POST['Importe'])) {

                $prov = $this->facturaModelo->getProveedor($_POST['CIF']);

                $this->datos['confirmarRetencion']['NomProveedor'] = $prov ? $prov->Nombre : '';
                $this->datos['confirmarRetencion']['NomDestino']   = $_POST['NomDestino']   ?? '';
                $this->datos['confirmarRetencion']['Responsable']  = $_POST['Responsable']  ?? '';
                $this->datos['confirmarRetencion']['CIF']          = $_POST['CIF'];
                $this->datos['confirmarRetencion']['NFactura']     = $_POST['NFactura']      ?? '';
                $this->datos['confirmarRetencion']['Importe']      = $_POST['Importe'];
                $this->datos['confirmarRetencion']['Faprobacion']  = $_POST['Faprobacion']   ?? date('Y-m-d');
                $this->datos['confirmarRetencion']['Motivos']      = $_POST['Motivos']        ?? '';

                if (isset($_POST['guardar'])) {
                    $this->vista('facturacion/validarRetencion', $this->datos);

                } elseif (isset($_POST['cancelar'])) {
                    $this->datos['proveedores'] = $this->facturaModelo->getProveedores(-1);
                    $this->vista('facturacion/retencionFactura', $this->datos);

                } else {
                    echo 'OPERACIÓN NO PERMITIDA'; exit();
                }

            } else {
                $this->datos['error'] = 1;
                $this->datos['proveedores'] = $this->facturaModelo->getProveedores(-1);
                $this->vista('facturacion/retencionFactura', $this->datos);
            }

        } else {
            $this->datos['proveedores'] = $this->facturaModelo->getProveedores(-1);
            $this->vista('facturacion/retencionFactura', $this->datos);
        }
    }


    /************************ GUARDAR RETENCIÓN ****************************/

    public function guardarRetencion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Aceptar'])) {

            $datosNcf = [
                'NFactura'    => $_POST['NFactura']    ?? '',
                'Importe'     => $_POST['Importe'],
                'CIF'         => $_POST['CIF'],
                'Faprobacion' => $_POST['Faprobacion'],
                'Destino_Id'  => $this->datos['persistencia']['idDestinoSeleccionado'],
                'Responsable' => $_POST['Responsable'],
                'Motivos'     => $_POST['Motivos']     ?? '',
            ];

            $this->datos['confirmarRetencion'] = $_POST;
            $this->datos['confirmarRetencion']['NomProveedor']    = $this->facturaModelo->getProveedor($_POST['CIF'])->Nombre ?? '';
            $this->datos['confirmarRetencion']['retencionGuardada'] = false;

            if ($idNcf = $this->facturaModelo->addRetencion($datosNcf)) {
                $this->datos['confirmarRetencion']['retencionGuardada'] = true;
                $this->datos['confirmarRetencion']['idRetencion']       = $idNcf;
            }

            $this->vista('facturacion/validarRetencion', $this->datos);

        } elseif (isset($_POST['cancelar'])) {
            $this->datos['confirmarRetencion'] = $_POST;
            $this->datos['proveedores']        = $this->facturaModelo->getProveedores(-1);
            $this->vista('facturacion/retencionFactura', $this->datos);

        } else {
            redireccionar('/GestionFacturas/retencion');
        }
    }


    public function resumen() {
        if (!isset($this->datos['persistencia']['idDestinoSeleccionado'])) {
            echo 'No hay departamento seleccionado.'; exit();
        }

        $pagina     = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $proveedor  = trim($_POST['proveedor']  ?? $_GET['proveedor']  ?? '');
        $fechaIni   = trim($_POST['fechaIni']   ?? $_GET['fechaIni']   ?? '');
        $fechaFin   = trim($_POST['fechaFin']   ?? $_GET['fechaFin']   ?? '');
        $todosDestinos = isset($_POST['todosDestinos'])
                       ? ($_POST['todosDestinos'] === '1' ? 1 : 0)
                       : (int)($_GET['todosDestinos'] ?? 0);

        // Solo el Equipo Directivo puede ver todos los destinos
        if ($this->datos['usuarioSesion']->id_rol !== 500) {
            $todosDestinos = 0;
        }

        $idDestino = $todosDestinos ? -1 : $this->datos['persistencia']['idDestinoSeleccionado'];

        $this->datos['resumenFacturas'] = $this->facturaModelo->getFacturasPaginadas(
            $idDestino, $proveedor, $fechaIni, $fechaFin, $pagina
        );
        $this->datos['totalPaginas']  = ceil(
            $this->facturaModelo->getNumFacturas($idDestino, $proveedor, $fechaIni, $fechaFin)
            / NUM_ITEMS_BY_PAGE
        );
        $this->datos['paginaActual']  = $pagina;
        $this->datos['filtroProveedor']    = $proveedor;
        $this->datos['filtroFechaIni']     = $fechaIni;
        $this->datos['filtroFechaFin']     = $fechaFin;
        $this->datos['filtroTodosDestinos'] = $todosDestinos;

        $this->vista('facturacion/resumenOperaciones', $this->datos);
    }


    /************************ EXPORTAR RESUMEN A EXCEL (CSV) ****************************/

    public function exportarResumen() {
        if (!isset($this->datos['persistencia']['idDestinoSeleccionado'])) {
            echo 'No hay departamento seleccionado.'; exit();
        }

        $proveedor     = trim($_GET['proveedor']     ?? '');
        $fechaIni      = trim($_GET['fechaIni']      ?? '');
        $fechaFin      = trim($_GET['fechaFin']      ?? '');
        $todosDestinos = (int)($_GET['todosDestinos'] ?? 0);

        if ($this->datos['usuarioSesion']->id_rol !== 500) {
            $todosDestinos = 0;
        }

        $idDestino = $todosDestinos ? -1 : $this->datos['persistencia']['idDestinoSeleccionado'];

        // Obtener TODAS las filas (sin paginación)
        $facturas = $this->facturaModelo->getFacturasTodas($idDestino, $proveedor, $fechaIni, $fechaFin);

        $nombreDestino = $todosDestinos
            ? 'Todos_los_departamentos'
            : str_replace(' ', '_', $this->datos['persistencia']['nombreDestinoSeleccionado']);
        $nombreFichero = 'Facturas_' . $nombreDestino . '_' . date('Ymd') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreFichero . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // BOM UTF-8 para que Excel lo abra con tildes correctamente
        fputs($out, "ï»¿");

        // Cabecera
        fputcsv($out, ['Nº Asiento', 'Nº Factura', 'Proveedor', 'CIF', 'Destino',
                       'Fecha Factura', 'Fecha Aprobación', 'Importe (€)',
                       'Inventariable', 'Responsable'], ';');

        foreach ($facturas as $f) {
            fputcsv($out, [
                $f->N_Asiento,
                $f->NFactura,
                $f->Nombre,
                $f->CIF,
                $f->Depart_Servicio,
                transformarFecha($f->Ffactura),
                $f->Faprobacion ? transformarFecha($f->Faprobacion) : '',
                number_format((float)$f->Importe, 2, ',', '.'),
                $f->Inventariable,
                $f->Responsable,
            ], ';');
        }

        fclose($out);
        exit();
    }


    public function gestionFacturas() {
        $this->vista('facturacion/resumenOperaciones', $this->datos);
    }
}
