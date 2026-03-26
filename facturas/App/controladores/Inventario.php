<?php
/**
 * Inventario.php — Controlador de inventario
 * Ruta:  facturas/App/controladores/Inventario.php
 *
 * URLs generadas (igual que el resto del MVC facturas):
 *   /facturas/Inventario/index          → home
 *   /facturas/Inventario/alta           → formulario nueva alta
 *   /facturas/Inventario/guardarAlta    → POST guardar alta
 *   /facturas/Inventario/altaOk/NE      → confirmación
 *   /facturas/Inventario/consulta       → listado filtrable
 *   /facturas/Inventario/modificar      → listado para editar
 *   /facturas/Inventario/editarDetalle/id
 *   /facturas/Inventario/guardarModificacion
 *   /facturas/Inventario/bajas          → listado para dar de baja
 *   /facturas/Inventario/ajaxFactura?asiento=N   → JSON
 *   /facturas/Inventario/ajaxArticulos?cat=N     → JSON
 *   /facturas/Inventario/ajaxBaja       → POST JSON
 *   /facturas/Inventario/ajaxReactivar  → POST JSON
 *
 * Roles (igual que GestionFacturas):
 *   300 = Jefe de Departamento  (modifica/baja solo su destino)
 *   500 = Equipo Directivo      (acceso total, incluida el alta)
 */
class Inventario extends Controlador {

    private object $sesion;
    private int    $rol;

    public function __construct() {

        Sesion::iniciarSesion($this->datos);

        $this->datos['menuActivo'] = 'inventario';

        $this->facturaModelo = $this->modelo('FacturaModelo');

        // Carga roles (igual que GestionFacturas)
        $this->datos['usuarioSesion']->roles  =
            $this->facturaModelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol =
            obtenerRol($this->datos['usuarioSesion']->roles);

        $this->rol = (int)$this->datos['usuarioSesion']->id_rol;

        // Solo roles 300 y 500 pueden acceder al inventario
        if (!tienePrivilegios($this->rol, [300, 500])) {
            echo 'No tienes privilegios para acceder al inventario.';
            exit();
        }

        $this->sesion = $this->datos['usuarioSesion'];
    }

    // ── Helpers de autorización ─────────────────────────────────────────

    private function esED(): bool         { return $this->rol === 500; }
    private function esJD(): bool         { return $this->rol === 300; }
    private function puedeGestionar(): bool { return $this->rol >= 300; }

    private function requireED(): void {
        if (!$this->esED()) {
            header('Location: ' . RUTA_URL . '/Inventario/index');
            exit();
        }
    }

    /** Destino_Id del JD actual (primer destino asignado) */
    private function destinoJD(): ?int {
        if ($this->esED()) return null;
        $destinos = $this->facturaModelo->getDestinos($this->sesion->id_profesor);
        return !empty($destinos) ? (int)$destinos[0]->Destino_Id : null;
    }

    private function puedeEditarDetalle(object $det): bool {
        if ($this->esED()) return true;
        // JD solo puede tocar registros de su destino
        $destinoJD = $this->destinoJD();
        return $destinoJD !== null && (int)$det->Dep_Responsable === $destinoJD;
    }

    // ── Datos comunes para formularios ──────────────────────────────────

    private function datosFormulario(): array {
        return [
            'facturasInv'  => $this->facturaModelo->invGetFacturasInventariables(),
            'categorias'   => $this->facturaModelo->invGetCategorias(),
            'ubicaciones'  => $this->facturaModelo->invGetUbicaciones(),
            'destinos'     => $this->esED()
                ? $this->facturaModelo->getDestinosEquipoDirectivo()
                : $this->facturaModelo->getDestinos($this->sesion->id_profesor),
        ];
    }

    // ────────────────────────────────────────────────────────────────────
    // HOME
    // ────────────────────────────────────────────────────────────────────

    public function index(): void {
        $this->datos['esED']         = $this->esED();
        $this->datos['puedeGest']    = $this->puedeGestionar();
        $this->vista('inventario/index', $this->datos);
    }

    // ────────────────────────────────────────────────────────────────────
    // ALTA
    // ────────────────────────────────────────────────────────────────────

    public function alta(): void {
        $this->requireED();
        $this->datos = array_merge($this->datos, $this->datosFormulario());
        $this->datos['error'] = '';
        $this->datos['post']  = [];
        $this->vista('inventario/alta', $this->datos);
    }

    /** AJAX → datos de factura */
    public function ajaxFactura(): void {
        header('Content-Type: application/json; charset=utf-8');
        $asiento = (int)($_GET['asiento'] ?? 0);
        $f = $asiento ? $this->facturaModelo->invGetFacturaPorAsiento($asiento) : null;
        echo json_encode($f);
    }

    /** AJAX → artículos de una categoría */
    public function ajaxArticulos(): void {
        header('Content-Type: application/json; charset=utf-8');
        $cat = (int)($_GET['cat'] ?? 0);
        echo json_encode($cat ? $this->facturaModelo->invGetArticulosPorCategoria($cat) : []);
    }

    /** POST → guardar alta */
    public function guardarAlta(): void {
        $this->requireED();

        $origen = trim($_POST['origen'] ?? '');
        $post   = $_POST;
        $error  = '';

        // Validación
        if (!in_array($origen, ['factura', 'donacion'])) {
            $error = 'Selecciona el origen del bien (Factura o Donación).';
        } elseif ($origen === 'factura' && empty($post['N_Asiento'])) {
            $error = 'Debes seleccionar el número de asiento de la factura.';
        } elseif ($origen === 'donacion' && empty(trim($post['Procedencia'] ?? ''))) {
            $error = 'Indica la procedencia de la donación.';
        } elseif (empty($post['CodCat'])) {
            $error = 'Selecciona una categoría.';
        } elseif (empty($post['CodArt'])) {
            $error = 'Selecciona un artículo.';
        } elseif ((int)($post['Unidades'] ?? 0) < 1) {
            $error = 'Las unidades deben ser 1 o más.';
        }

        if ($error) {
            $this->datos = array_merge($this->datos, $this->datosFormulario());
            $this->datos['error'] = $error;
            $this->datos['post']  = $post;
            $this->vista('inventario/alta', $this->datos);
            return;
        }

        // Guardar cabecera
        $nEntrada = $this->facturaModelo->invAltaCabecera([
            'N_Asiento'     => $origen === 'factura' ? (int)$post['N_Asiento'] : null,
            'NFactura'      => $post['NFactura']      ?? null,
            'CIF'           => $post['CIF']           ?? null,
            'Procedencia'   => $origen === 'donacion' ? trim($post['Procedencia']) : null,
            'Observaciones' => trim($post['Observaciones'] ?? ''),
        ]);

        // Guardar detalle
        $this->facturaModelo->invAltaDetalle([
            'NEntrada'       => $nEntrada,
            'CodCat'         => (int)$post['CodCat'],
            'CodArt'         => (int)$post['CodArt'],
            'Unidades'       => (int)$post['Unidades'],
            'Individual'     => ($post['Individual'] ?? 'B') === 'I' ? 'I' : 'B',
            'Dep_Responsable'=> !empty($post['Dep_Responsable']) ? (int)$post['Dep_Responsable'] : null,
            'Local_Ini'      => !empty($post['Local_Ini'])       ? (int)$post['Local_Ini']       : null,
            'Descripcion'    => trim($post['Descripcion'] ?? ''),
        ]);

        header('Location: ' . RUTA_URL . '/Inventario/altaOk/' . $nEntrada);
        exit();
    }

    public function altaOk(int $nEntrada = 0): void {
        $this->datos['nEntrada'] = $nEntrada;
        $this->vista('inventario/alta_ok', $this->datos);
    }

    // ────────────────────────────────────────────────────────────────────
    // CONSULTA
    // ────────────────────────────────────────────────────────────────────

    public function consulta(): void {
        $f = $this->getFiltro(['dep', 'cat', 'buscar', 'baja']);

        // JD solo ve su destino si no ha filtrado por otro
        if ($this->esJD() && empty($f['dep'])) {
            $f['dep'] = (string)($this->destinoJD() ?? '');
        }

        $total  = $this->facturaModelo->invCount($f);
        $porPag = NUM_ITEMS_BY_PAGE;
        $numPag = max(1, (int)ceil($total / $porPag));
        $pag    = max(1, min((int)($_GET['pagina'] ?? 1), $numPag));

        $this->datos = array_merge($this->datos, [
            'registros'   => $this->facturaModelo->invGetPaginado($f, $pag, $porPag),
            'filtro'      => $f,
            'destinos'    => $this->esED()
                ? $this->facturaModelo->getDestinosEquipoDirectivo()
                : $this->facturaModelo->getDestinos($this->sesion->id_profesor),
            'categorias'  => $this->facturaModelo->invGetCategorias(),
            'total'       => $total,
            'paginaActual'=> $pag,
            'numPaginas'  => $numPag,
            'cadenaGet'   => $this->buildQuery($f),
            'esED'        => $this->esED(),
            'puedeGest'   => $this->puedeGestionar(),
        ]);

        $this->vista('inventario/consulta', $this->datos);
    }

    // ────────────────────────────────────────────────────────────────────
    // MODIFICAR
    // ────────────────────────────────────────────────────────────────────

    public function modificar(): void {
        $f = $this->getFiltro(['dep', 'buscar']);
        $f['baja'] = '0';
        $f['cat']  = '';

        if ($this->esJD()) $f['dep'] = (string)($this->destinoJD() ?? '');

        $total  = $this->facturaModelo->invCount($f);
        $porPag = NUM_ITEMS_BY_PAGE;
        $numPag = max(1, (int)ceil($total / $porPag));
        $pag    = max(1, min((int)($_GET['pagina'] ?? 1), $numPag));

        $this->datos = array_merge($this->datos, [
            'registros'   => $this->facturaModelo->invGetPaginado($f, $pag, $porPag),
            'filtro'      => $f,
            'destinos'    => $this->esED()
                ? $this->facturaModelo->getDestinosEquipoDirectivo()
                : $this->facturaModelo->getDestinos($this->sesion->id_profesor),
            'total'       => $total,
            'paginaActual'=> $pag,
            'numPaginas'  => $numPag,
            'cadenaGet'   => $this->buildQuery($f),
            'esED'        => $this->esED(),
        ]);

        $this->vista('inventario/modificar', $this->datos);
    }

    public function editarDetalle(int $id = 0): void {
        $det = $this->facturaModelo->invGetDetalleById($id);
        if (!$det || !$this->puedeEditarDetalle($det)) {
            header('Location: ' . RUTA_URL . '/Inventario/modificar');
            exit();
        }

        $this->datos = array_merge($this->datos, [
            'detalle'    => $det,
            'categorias' => $this->facturaModelo->invGetCategorias(),
            'articulos'  => $this->facturaModelo->invGetArticulosPorCategoria((int)$det->CodCat),
            'ubicaciones'=> $this->facturaModelo->invGetUbicaciones(),
            'destinos'   => $this->esED()
                ? $this->facturaModelo->getDestinosEquipoDirectivo()
                : $this->facturaModelo->getDestinos($this->sesion->id_profesor),
            'error'      => '',
            'esED'       => $this->esED(),
        ]);

        $this->vista('inventario/editar', $this->datos);
    }

    public function guardarModificacion(): void {
        $id  = (int)($_POST['id'] ?? 0);
        $det = $this->facturaModelo->invGetDetalleById($id);

        if (!$det || !$this->puedeEditarDetalle($det)) {
            header('Location: ' . RUTA_URL . '/Inventario/modificar');
            exit();
        }

        $error = '';
        if (empty($_POST['CodCat']))          $error = 'Selecciona categoría.';
        elseif (empty($_POST['CodArt']))      $error = 'Selecciona artículo.';
        elseif ((int)($_POST['Unidades'] ?? 0) < 1) $error = 'Unidades debe ser ≥ 1.';

        if ($error) {
            $this->datos = array_merge($this->datos, [
                'detalle'    => $det,
                'categorias' => $this->facturaModelo->invGetCategorias(),
                'articulos'  => $this->facturaModelo->invGetArticulosPorCategoria((int)$_POST['CodCat']),
                'ubicaciones'=> $this->facturaModelo->invGetUbicaciones(),
                'destinos'   => $this->esED()
                    ? $this->facturaModelo->getDestinosEquipoDirectivo()
                    : $this->facturaModelo->getDestinos($this->sesion->id_profesor),
                'error'      => $error,
                'esED'       => $this->esED(),
            ]);
            $this->vista('inventario/editar', $this->datos);
            return;
        }

        $this->facturaModelo->invModificarDetalle([
            'id'             => $id,
            'CodCat'         => (int)$_POST['CodCat'],
            'CodArt'         => (int)$_POST['CodArt'],
            'Unidades'       => (int)$_POST['Unidades'],
            'Individual'     => ($_POST['Individual'] ?? 'B'),
            'Dep_Responsable'=> !empty($_POST['Dep_Responsable']) ? (int)$_POST['Dep_Responsable'] : null,
            'Local_Ini'      => !empty($_POST['Local_Ini'])       ? (int)$_POST['Local_Ini']       : null,
            'Descripcion'    => trim($_POST['Descripcion'] ?? ''),
        ]);

        if ($this->esED() && isset($_POST['Observaciones'])) {
            $this->facturaModelo->invModificarCabecera(
                (int)$det->NEntrada,
                trim($_POST['Observaciones'])
            );
        }

        header('Location: ' . RUTA_URL . '/Inventario/modificar?ok=1');
        exit();
    }

    // ────────────────────────────────────────────────────────────────────
    // BAJAS
    // ────────────────────────────────────────────────────────────────────

    public function bajas(): void {
        $f = $this->getFiltro(['dep', 'buscar']);
        $f['baja'] = '0';
        $f['cat']  = '';

        if ($this->esJD()) $f['dep'] = (string)($this->destinoJD() ?? '');

        $total  = $this->facturaModelo->invCount($f);
        $porPag = NUM_ITEMS_BY_PAGE;
        $numPag = max(1, (int)ceil($total / $porPag));
        $pag    = max(1, min((int)($_GET['pagina'] ?? 1), $numPag));

        $this->datos = array_merge($this->datos, [
            'registros'   => $this->facturaModelo->invGetPaginado($f, $pag, $porPag),
            'filtro'      => $f,
            'destinos'    => $this->esED()
                ? $this->facturaModelo->getDestinosEquipoDirectivo()
                : $this->facturaModelo->getDestinos($this->sesion->id_profesor),
            'total'       => $total,
            'paginaActual'=> $pag,
            'numPaginas'  => $numPag,
            'cadenaGet'   => $this->buildQuery($f),
            'esED'        => $this->esED(),
        ]);

        $this->vista('inventario/bajas', $this->datos);
    }

    /** AJAX POST → dar de baja */
    public function ajaxBaja(): void {
        header('Content-Type: application/json; charset=utf-8');

        $id     = (int)($_POST['id']     ?? 0);
        $motivo = trim($_POST['motivo']  ?? '');

        if (!$id) { echo json_encode(['ok' => false, 'msg' => 'ID inválido']); return; }

        $det = $this->facturaModelo->invGetDetalleById($id);
        if (!$det || !$this->puedeEditarDetalle($det)) {
            echo json_encode(['ok' => false, 'msg' => 'Sin permisos']); return;
        }

        $ok = $this->facturaModelo->invDarDeBaja($id, $motivo);
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Baja registrada' : 'Error al dar de baja']);
    }

    /** AJAX POST → reactivar (solo ED) */
    public function ajaxReactivar(): void {
        header('Content-Type: application/json; charset=utf-8');
        $this->requireED();
        $id = (int)($_POST['id'] ?? 0);
        $ok = $id ? $this->facturaModelo->invReactivar($id) : false;
        echo json_encode(['ok' => $ok]);
    }

    // ── Helpers privados ────────────────────────────────────────────────

    private function getFiltro(array $keys): array {
        $f = [];
        foreach ($keys as $k) {
            $f[$k] = trim($_GET[$k] ?? $_POST[$k] ?? '');
        }
        return $f;
    }

    private function buildQuery(array $f): string {
        $parts = [];
        foreach ($f as $k => $v) {
            if ($v !== '') $parts[] = urlencode($k) . '=' . urlencode($v);
        }
        return implode('&', $parts);
    }
}
