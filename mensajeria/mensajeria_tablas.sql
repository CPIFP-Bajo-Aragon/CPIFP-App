-- =====================================================================
-- TABLAS MENSAJERIA INTERNA  prefijo mensj_
-- Base de datos: calidapp
-- =====================================================================

CREATE TABLE IF NOT EXISTS `mensj_mensaje` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `id_remitente`  INT          NOT NULL,
  `asunto`        VARCHAR(200) NOT NULL,
  `cuerpo`        TEXT         NOT NULL,
  `fecha_envio`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tiene_adjunto` TINYINT(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_remitente` (`id_remitente`),
  KEY `idx_fecha`     (`fecha_envio`),
  CONSTRAINT `fk_mensj_remitente`
    FOREIGN KEY (`id_remitente`) REFERENCES `cpifp_profesor` (`id_profesor`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

CREATE TABLE IF NOT EXISTS `mensj_destinatario` (
  `id`            INT        NOT NULL AUTO_INCREMENT,
  `id_mensaje`    INT        NOT NULL,
  `id_profesor`   INT        NOT NULL,
  `leido`         TINYINT(1) NOT NULL DEFAULT 0,
  `fecha_lectura` DATETIME   DEFAULT NULL,
  `eliminado`     TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_dest_mensaje`  (`id_mensaje`),
  KEY `idx_dest_profesor` (`id_profesor`),
  CONSTRAINT `fk_mensj_dest_mensaje`
    FOREIGN KEY (`id_mensaje`)  REFERENCES `mensj_mensaje`  (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mensj_dest_profesor`
    FOREIGN KEY (`id_profesor`) REFERENCES `cpifp_profesor` (`id_profesor`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

CREATE TABLE IF NOT EXISTS `mensj_adjunto` (
  `id`           INT          NOT NULL AUTO_INCREMENT,
  `id_mensaje`   INT          NOT NULL,
  `nombre_orig`  VARCHAR(255) NOT NULL,
  `nombre_disco` VARCHAR(255) NOT NULL,
  `mime`         VARCHAR(100) NOT NULL DEFAULT 'application/octet-stream',
  `tamanio`      INT          NOT NULL DEFAULT 0,
  `fecha_subida` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_adj_mensaje` (`id_mensaje`),
  CONSTRAINT `fk_mensj_adj_mensaje`
    FOREIGN KEY (`id_mensaje`) REFERENCES `mensj_mensaje` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

CREATE TABLE IF NOT EXISTS `mensj_config` (
  `clave`       VARCHAR(60)  NOT NULL,
  `valor`       VARCHAR(255) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO `mensj_config` (`clave`, `valor`, `descripcion`) VALUES
  ('dias_borrado_adjuntos',  '30',  'Dias que se conservan los ficheros adjuntos en disco'),
  ('max_tam_adjunto_mb',     '10',  'Tamano maximo por fichero adjunto en MB'),
  ('extensiones_permitidas', 'pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,txt', 'Extensiones permitidas')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);
