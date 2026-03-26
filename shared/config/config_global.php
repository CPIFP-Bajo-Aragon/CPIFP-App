<?php
/**
 * ============================================================
 * config_global.php — Configuración central CPIFP Bajo Aragón
 * ============================================================
 * Ubicación en servidor: /var/www/html/shared/config/config_global.php
 *
 * Se incluye al FINAL de cada configurar.php individual:
 *   require_once '/var/www/html/shared/config/config_global.php';
 *
 * Usa if(!defined) en BD para que el MVC principal pueda definir
 * sus propios valores antes de incluir este fichero.
 * ============================================================
 */

// ── Base de datos ─────────────────────────────────────────────
// El MVC principal sobreescribe estos valores en su propio configurar.php
// antes de llamar a este fichero. El resto de MVCs los heredan de aquí.
if (!defined('DB_HOST'))     define('DB_HOST',     'localhost');
if (!defined('DB_USUARIO'))  define('DB_USUARIO',  'interno_ubdcalidapp');
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', 'k41c-?55K5UcyJ8^');
if (!defined('DB_NOMBRE'))   define('DB_NOMBRE',   'interno_calidapp');

// ── URL raíz del servidor ─────────────────────────────────────
if (!defined('RUTA_CPIFP'))  define('RUTA_CPIFP', 'http://192.168.1.197');

// ── Recursos públicos compartidos (única copia de logos e iconos) ─
if (!defined('RUTA_LOGOS'))  define('RUTA_LOGOS', RUTA_CPIFP . '/public/img/logos/');
if (!defined('RUTA_Icon'))   define('RUTA_Icon',  RUTA_CPIFP . '/public/img/icons/');

// ── Autenticación ─────────────────────────────────────────────
if (!defined('RUTA_LOGOUT')) define('RUTA_LOGOUT', RUTA_CPIFP . '/login/logout');

// ── CSS compartido del MVC seguimiento ────────────────────────
if (!defined('RUTA_SEGUIMIENTO_CSS'))
    define('RUTA_SEGUIMIENTO_CSS', RUTA_CPIFP . '/seguimiento/public/css/estilos_seguimiento.css');

// ── Footer con logos compartido ───────────────────────────────
if (!defined('RUTA_FOOTER_LOGOS'))
    define('RUTA_FOOTER_LOGOS', '/var/www/html/shared/vistas/footer_logos.php');

// ── Sesión y paginación ───────────────────────────────────────
if (!defined('TMP_SESION'))        define('TMP_SESION',        2 * 60 * 60);
if (!defined('NUM_ITEMS_BY_PAGE')) define('NUM_ITEMS_BY_PAGE', 20);
if (!defined('TAM_PAGINA'))        define('TAM_PAGINA',        20);

// ── Correo SMTP ───────────────────────────────────────────────
if (!defined('EmailEmisor')) define('EmailEmisor', 'noreply@cpifpbajoaragon.com');
if (!defined('EmailPass'))   define('EmailPass',   'kvAPuHCKX9NSDZts$$py');
if (!defined('Emisor'))      define('Emisor',      'CPIFP Bajo Aragón');
if (!defined('Host'))        define('Host',        'smtp.ionos.es');
if (!defined('SMTPSecure'))  define('SMTPSecure',  'TLS');
if (!defined('Puerto'))      define('Puerto',      587);
