# MVC Mensajeria Interna — Instalacion

## 1. Crear las tablas en MySQL
    mysql -u root -p calidapp < mensajeria_tablas.sql

## 2. Copiar el directorio completo
    cp -r mensajeria/ /var/www/html/mensajeria/

## 3. Copiar librerias del framework desde facturas
    cp /var/www/html/facturas/App/librerias/Base.php       /var/www/html/mensajeria/App/librerias/
    cp /var/www/html/facturas/App/librerias/Controlador.php /var/www/html/mensajeria/App/librerias/
    cp /var/www/html/facturas/App/librerias/Core.php        /var/www/html/mensajeria/App/librerias/
    cp /var/www/html/facturas/App/librerias/Sesion.php      /var/www/html/mensajeria/App/librerias/
    cp /var/www/html/facturas/App/librerias/EnviarEmail.php /var/www/html/mensajeria/App/librerias/
    cp -r /var/www/html/facturas/App/librerias/externas/    /var/www/html/mensajeria/App/librerias/

## 4. Permisos de la carpeta de adjuntos
    chmod 775 /var/www/html/mensajeria/public/uploads/adjuntos/
    chown www-data:www-data /var/www/html/mensajeria/public/uploads/adjuntos/

## 5. Revisar configurar.php
    - RUTA_CPIFP: URL base del MVC principal (ej: http://192.168.1.197)
    - Datos SMTP (EmailEmisor, EmailPass, Host, Puerto, SMTPSecure)
    - Credentials BD

## 6. Configurar el cron de borrado de adjuntos
    crontab -e
    # Agregar la siguiente linea (cada noche a las 02:00):
    0 2 * * * /usr/bin/php /var/www/html/mensajeria/cron/limpiar_adjuntos.php >> /var/log/mensajeria_cron.log 2>&1

## 7. Anadir enlace en el menu del MVC principal (opcional)
    En principal/App/vistas/inc/header.php o la vista de inicio,
    anadir un enlace a http://<servidor>/mensajeria

## Notas
- El tiempo de borrado de adjuntos es configurable desde la pantalla
  Configuracion (solo Equipo Directivo, rol 50).
- Los adjuntos se guardan en public/uploads/adjuntos/ con nombre UUID.
- Los mensajes borrados por el destinatario son borrados logicamente
  (columna eliminado=1); el mensaje real permanece en BD.
