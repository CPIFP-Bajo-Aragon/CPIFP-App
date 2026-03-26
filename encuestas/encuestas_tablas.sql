-- ============================================================
--  MÓDULO DE ENCUESTAS - CPIFP Bajo Aragón
--  Prefijo: en_
--  Fecha: 2025
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


-- ============================================================
-- TIPOS DE ENCUESTA
-- 1 = Encuesta de alumnos (por profesor y módulo)
-- 2 = Encuesta de empresas
-- ============================================================
CREATE TABLE `en_tipo_encuesta` (
  `id_tipo_encuesta` int NOT NULL AUTO_INCREMENT,
  `tipo_encuesta`    varchar(100) NOT NULL,
  `descripcion`      text,
  PRIMARY KEY (`id_tipo_encuesta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `en_tipo_encuesta` VALUES
  (1, 'Encuesta de alumnos', 'Encuesta de satisfacción de alumnos sobre un profesor y módulo'),
  (2, 'Encuesta de empresas', 'Encuesta anual de satisfacción de empresas colaboradoras');


-- ============================================================
-- PLANTILLA DE PREGUNTAS (preguntas editables por tipo)
-- Cuando se crea una encuesta concreta se COPIAN las preguntas
-- para preservar el histórico
-- ============================================================
CREATE TABLE `en_plantilla_pregunta` (
  `id_plantilla_pregunta` int NOT NULL AUTO_INCREMENT,
  `id_tipo_encuesta`      int NOT NULL,
  `orden`                 int NOT NULL DEFAULT 1,
  `pregunta`              text NOT NULL,
  `activo`                tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_plantilla_pregunta`),
  KEY `fk_plantilla_tipo_idx` (`id_tipo_encuesta`),
  CONSTRAINT `fk_plantilla_tipo` FOREIGN KEY (`id_tipo_encuesta`)
    REFERENCES `en_tipo_encuesta` (`id_tipo_encuesta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Preguntas por defecto para encuesta de alumnos (tipo 1)
INSERT INTO `en_plantilla_pregunta` (`id_tipo_encuesta`,`orden`,`pregunta`) VALUES
  (1, 1, 'Ambiente de trabajo en clase'),
  (1, 2, 'Comprensión y amenidad de las explicaciones (teoría)'),
  (1, 3, 'Trabajo desarrollado en clase (práctica)'),
  (1, 4, 'Forma de calificar y evaluar'),
  (1, 5, 'Dominio de la materia por parte del profesor'),
  (1, 6, 'Atención y disponibilidad del profesor fuera del horario de clase'),
  (1, 7, 'Puntualidad y aprovechamiento del tiempo de clase'),
  (1, 8, 'Claridad en la explicación de los objetivos y contenidos del módulo');

-- Preguntas por defecto para encuesta de empresas (tipo 2)
INSERT INTO `en_plantilla_pregunta` (`id_tipo_encuesta`,`orden`,`pregunta`) VALUES
  (2, 1, 'Grado de satisfacción general con el centro educativo'),
  (2, 2, 'Nivel de formación de los alumnos en prácticas recibidos'),
  (2, 3, 'Coordinación con los tutores del centro durante la FCT/FP Dual'),
  (2, 4, 'Adecuación del perfil de los alumnos a las necesidades de la empresa'),
  (2, 5, 'Disposición del centro para atender sus necesidades'),
  (2, 6, 'Probabilidad de colaborar de nuevo con el centro el próximo curso');


-- ============================================================
-- ENCUESTA CONCRETA
-- Para alumnos: una por profesor, módulo y trimestre
-- Para empresas: una por empresa y año
-- ============================================================
CREATE TABLE `en_encuesta` (
  `id_encuesta`      int NOT NULL AUTO_INCREMENT,
  `id_tipo_encuesta` int NOT NULL,
  `titulo`           varchar(200) NOT NULL,
  `descripcion`      text,
  `curso_academico`  varchar(20) NOT NULL COMMENT 'Ej: 2024-2025',
  `trimestre`        tinyint DEFAULT NULL COMMENT '1, 2 o 3 (solo para tipo alumno)',
  `id_profesor_modulo` int DEFAULT NULL COMMENT 'Ref a cpifp_profesor_modulo (solo tipo alumno)',
  `id_empresa`       int DEFAULT NULL COMMENT 'Ref a en_empresa (solo tipo empresa)',
  `fecha_inicio`     date NOT NULL,
  `fecha_fin`        date DEFAULT NULL,
  `activa`           tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=abierta, 0=cerrada',
  `token_publico`    varchar(64) DEFAULT NULL COMMENT 'Token para acceso público sin login',
  `codigo_acceso`    varchar(6) DEFAULT NULL COMMENT 'Código de 6 dígitos que el alumno debe conocer para responder (solo tipo alumno)',
  `creada_por`       int NOT NULL COMMENT 'id_profesor que crea la encuesta',
  `fecha_creacion`   datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_encuesta`),
  UNIQUE KEY `uk_token_publico` (`token_publico`),
  KEY `fk_enc_tipo_idx` (`id_tipo_encuesta`),
  KEY `fk_enc_profmod_idx` (`id_profesor_modulo`),
  KEY `fk_enc_empresa_idx` (`id_empresa`),
  KEY `fk_enc_creador_idx` (`creada_por`),
  CONSTRAINT `fk_enc_tipo` FOREIGN KEY (`id_tipo_encuesta`)
    REFERENCES `en_tipo_encuesta` (`id_tipo_encuesta`) ON UPDATE CASCADE,
  CONSTRAINT `fk_enc_profmod` FOREIGN KEY (`id_profesor_modulo`)
    REFERENCES `cpifp_profesor_modulo` (`id_profesor_modulo`) ON UPDATE CASCADE,
  CONSTRAINT `fk_enc_creador` FOREIGN KEY (`creada_por`)
    REFERENCES `cpifp_profesor` (`id_profesor`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- PREGUNTAS COPIADAS A CADA ENCUESTA CONCRETA
-- (histórico inmutable aunque cambien las plantillas)
-- ============================================================
CREATE TABLE `en_pregunta` (
  `id_pregunta`  int NOT NULL AUTO_INCREMENT,
  `id_encuesta`  int NOT NULL,
  `orden`        int NOT NULL DEFAULT 1,
  `pregunta`     text NOT NULL,
  PRIMARY KEY (`id_pregunta`),
  KEY `fk_preg_encuesta_idx` (`id_encuesta`),
  CONSTRAINT `fk_preg_encuesta` FOREIGN KEY (`id_encuesta`)
    REFERENCES `en_encuesta` (`id_encuesta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- RESPUESTAS A LAS ENCUESTAS
-- Una fila por encuesta respondida (un alumno o empresa)
-- ============================================================
CREATE TABLE `en_respuesta` (
  `id_respuesta`   int NOT NULL AUTO_INCREMENT,
  `id_encuesta`    int NOT NULL,
  `fecha_respuesta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip`             varchar(45) DEFAULT NULL COMMENT 'Para control anti-duplicados',
  `comentario_mejor` text DEFAULT NULL COMMENT 'Lo mejor del módulo/servicio',
  `comentario_peor`  text DEFAULT NULL COMMENT 'Lo peor del módulo/servicio',
  `comentario_libre` text DEFAULT NULL COMMENT 'Observaciones libres',
  PRIMARY KEY (`id_respuesta`),
  KEY `fk_resp_encuesta_idx` (`id_encuesta`),
  CONSTRAINT `fk_resp_encuesta` FOREIGN KEY (`id_encuesta`)
    REFERENCES `en_encuesta` (`id_encuesta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- DETALLE DE RESPUESTAS (una fila por pregunta respondida)
-- ============================================================
CREATE TABLE `en_respuesta_detalle` (
  `id_respuesta_detalle` int NOT NULL AUTO_INCREMENT,
  `id_respuesta` int NOT NULL,
  `id_pregunta`  int NOT NULL,
  `puntuacion`   tinyint NOT NULL COMMENT 'Valor del 1 al 10',
  PRIMARY KEY (`id_respuesta_detalle`),
  KEY `fk_det_respuesta_idx` (`id_respuesta`),
  KEY `fk_det_pregunta_idx` (`id_pregunta`),
  CONSTRAINT `fk_det_respuesta` FOREIGN KEY (`id_respuesta`)
    REFERENCES `en_respuesta` (`id_respuesta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_det_pregunta` FOREIGN KEY (`id_pregunta`)
    REFERENCES `en_pregunta` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- EMPRESAS (para encuestas de satisfacción de empresas)
-- ============================================================
CREATE TABLE `en_empresa` (
  `id_empresa`   int NOT NULL AUTO_INCREMENT,
  `empresa`      varchar(200) NOT NULL,
  `contacto`     varchar(100) DEFAULT NULL,
  `email`        varchar(150) DEFAULT NULL,
  `telefono`     varchar(20) DEFAULT NULL,
  `activa`       tinyint(1) NOT NULL DEFAULT 1,
  `token_acceso` varchar(64) DEFAULT NULL COMMENT 'Token único para que la empresa responda sin login',
  PRIMARY KEY (`id_empresa`),
  UNIQUE KEY `uk_empresa_token` (`token_acceso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Añadir FK a en_encuesta ahora que en_empresa existe
ALTER TABLE `en_encuesta`
  ADD CONSTRAINT `fk_enc_empresa`
    FOREIGN KEY (`id_empresa`) REFERENCES `en_empresa` (`id_empresa`) ON UPDATE CASCADE;


-- ============================================================
-- ROLES PARA EL MÓDULO DE ENCUESTAS
-- Se utilizan los mismos que el resto de la aplicación.
-- Los permisos por rol se definen en el controlador:
--   100 = Profesor (ve solo sus encuestas)
--   200 = Jefe de departamento (ve su dept.)
--   300 = Administrador / Dirección (acceso total)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- MIGRACIÓN: ejecutar solo si en_encuesta ya existía antes
-- ============================================================
-- ALTER TABLE `en_encuesta`
--   ADD COLUMN `codigo_acceso` varchar(6) DEFAULT NULL
--     COMMENT 'Código 6 dígitos para acceso alumno (solo tipo alumno)'
--     AFTER `token_publico`;
