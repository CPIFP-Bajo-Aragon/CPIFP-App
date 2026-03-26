-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 15-10-2024 a las 14:40:34
-- Versión del servidor: 8.0.39-0ubuntu0.22.04.1
-- Versión de PHP: 8.1.2-1ubuntu2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


DROP SCHEMA interno_calidapp;
CREATE SCHEMA interno_calidapp;
USE interno_calidapp;




-- ----------------- ROLES -----------------------------

CREATE TABLE `cpifp_rol` (
  `id_rol` int NOT NULL,
  `rol` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


INSERT INTO `cpifp_rol` (`id_rol`, `rol`) VALUES
(5, 'Conserje'),
(10, 'Profesor'),
(20, 'Tutor'),
(30, 'Jefe de departamento'),
(40, 'Técnico'),
(50, 'Equipo directivo');



-- ----------------- GRADOS -----------------------------


CREATE TABLE `cpifp_grados` (
  `id_grado` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_grado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `cpifp_grados` (`id_grado`, `nombre`) VALUES
(1, 'Basica'),
(2, 'Medio'),
(3, 'Superior'),
(4, 'Especializacion');


-- ----------------- TURNOS -----------------------------

CREATE TABLE `cpifp_turnos` (
  `id_turno` int NOT NULL AUTO_INCREMENT,
  `turno` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY(`id_turno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `cpifp_turnos` (`id_turno`, `turno`) VALUES
(1, 'Mañana'),
(2, 'Tarde'),
(3, 'Distancia');


-- ----------------- EVALUACIONES -----------------------------

CREATE TABLE `cpifp_evaluaciones` (
  `id_evaluacion` int NOT NULL AUTO_INCREMENT,
  `evaluacion` varchar(45) NOT NULL,
  PRIMARY KEY (`id_evaluacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


-- ----------------- CICLOS -----------------------------

CREATE TABLE `cpifp_ciclos` (
  `id_ciclo` int NOT NULL,
  `ciclo` varchar(100) NOT NULL,
  `ciclo_corto` varchar(8) DEFAULT NULL,
  `id_departamento` int NOT NULL,
  `id_grado` int NOT NULL,
  `id_turno` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;



-- ----------------- CURSO -----------------------------

CREATE TABLE `cpifp_curso` (
  `id_curso` int NOT NULL,
  `curso` varchar(45) NOT NULL,
  `id_numero` int NOT NULL,
  `id_ciclo` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;



-- --------------------- DEPARTAMENTOS ---------------------------

CREATE TABLE `cpifp_departamento` (
  `id_departamento` int NOT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `departamento_corto` varchar(10) DEFAULT NULL,
  `isFormacion` tinyint(1),
  `sin_ciclo` tinyint(1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


INSERT INTO `cpifp_departamento` (`id_departamento`, `departamento`, `departamento_corto`, `isFormacion`, `sin_ciclo`) 
VALUES (1, 'Dirección', 'DIR', 0, 1);


-- ------------------- MODULO -----------------------------


CREATE TABLE `cpifp_modulo` (
  `id_modulo` int NOT NULL,
  `modulo` varchar(100) DEFAULT NULL,
  `nombre_corto` varchar(10) DEFAULT NULL,
  `horas_totales` int NOT NULL,
  `cuerpo` varchar(10) DEFAULT NULL,
  `id_curso` int NOT NULL,
  `horas_semanales` int DEFAULT NULL,
  `id_departamento` int NOT NULL,
  `codigo_programacion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;



-- ------------------ PROFESOR ------------------------------

CREATE TABLE `cpifp_profesor` (
  `id_profesor` int NOT NULL,
  `login` varchar(45) NOT NULL,
  `password` varchar(70) DEFAULT NULL,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `activo` tinyint NOT NULL,
  `cod_pass` text,
  `isAdmin` tinyint(1) DEFAULT '0',
  `cuerpo` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


INSERT INTO `cpifp_profesor` (`id_profesor`,`login`, `password`, `nombre_completo`, `email`, `activo`, `cod_pass`, `isAdmin`, `cuerpo`) VALUES
(1,'admin01', '$2y$10$E4NqyQkNsWjKZy3vNWKdU.vl16hoBdgDGC9WdmLH.AAIqTTwyR10S', 'Administrador CPIFP BAJO ARAGON', 'admin01', 1, 'admin01', 1, 'pt');




-- ------------------------------------------------


CREATE TABLE `cpifp_profesor_departamento` (
  `id_profesor_departamento` int NOT NULL,
  `id_profesor` int NOT NULL,
  `id_departamento` int NOT NULL,
  `id_rol` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


INSERT INTO `cpifp_profesor_departamento` (`id_profesor_departamento`, `id_profesor`, `id_departamento`, `id_rol`) 
VALUES (1, 1, 1, 50);



-- ------------------------------------------------


CREATE TABLE `cpifp_profesor_modulo` (
  `id_profesor_modulo` int NOT NULL,
  `id_lectivo` int DEFAULT NULL,
  `id_profesor` int NOT NULL,
  `id_modulo` int NOT NULL,
  `horas_profesor` int NOT NULL, 
  `cambia_programacion` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;







-- -------------------------------------------
-- -------------------------------------------
-- ------------  SEGUIMIENTO -----------------
-- -------------------------------------------
-- -------------------------------------------


-- ------------------- NUMERO CURSO --------------------

CREATE TABLE `seg_numero` (
  `id_numero` int NOT NULL,
  `numero` int NOT NULL,
  `nombre_curso` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `seg_numero` (`id_numero`, `numero`,`nombre_curso`) 
VALUES (1,1,'Primero'), (2,2,'Segundo'), (3,3,'Tercero'), (4,4,'Cuarto'), (5,5,'Quinto'), (6,6,'Sexto');


-- ------------------- LECTIVO --------------------

CREATE TABLE `seg_lectivos` (
  `id_lectivo` int NOT NULL,
  `lectivo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `cerrado` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ------------------- CALENDARIO --------------------

CREATE TABLE `seg_calendario` (
  `id_calendario` int NOT NULL,
  `id_lectivo` int NOT NULL,
  `fecha` date DEFAULT NULL,
  `dia_semana` varchar(1) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- ------------------- EVALUACIONES --------------------

CREATE TABLE `seg_evaluaciones` (
  `id_seg_evaluacion` int NOT NULL,
  `id_evaluacion` int NOT NULL,
  `id_calendario` int NOT NULL,
  `id_grado` int NOT NULL,
  `id_turno` int NOT NULL,
  `id_numero` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- ------------------- FESTIVOS --------------------

CREATE TABLE `seg_festivos` (
  `id_festivo` int NOT NULL,
  `id_calendario` int NOT NULL,
  `festivo` varchar(100) NOT NULL, 
  `dia_semana` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




-- ------------------- DIAS SEMANA / HORARIO --------------------

CREATE TABLE `seg_dias_semana` (
  `id_dia_semana` int NOT NULL,
  `dia_semana` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `dia_corto` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `seg_dias_semana` (`id_dia_semana`, `dia_semana`, `dia_corto`) VALUES
(1,'Lunes','L'), (2,'Martes','M'), (3,'Miercoles','X'), (4,'Jueves','J'), (5,'Viernes','V'), (6,'Sabado','S'), (7,'Domingo','D');


CREATE TABLE `seg_horario_modulo` (
  `id_dia_semana` int NOT NULL,
  `id_modulo` int NOT NULL,
  `horas_dia` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- ------------------- INDICADORES --------------------

CREATE TABLE `seg_indicadores` (
  `id_indicador` int NOT NULL,
  `indicador` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `indicador_corto` varchar(20) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `seg_indicadores` (`id_indicador`, `indicador`, `indicador_corto`) VALUES
(1,'Asistencia de los alumnos', 'AA'),
(2, 'Grado de cumplimiento de la programacion', 'EP2'),
(3, 'Horas de docencia impartidas', 'HI'),
(4, 'Indicador de aprobados', 'AP'),
(5, 'Ambiente de trabajo', 'AT'),
(6, 'Contenidos impartidos', 'EP1'),
(7, 'Aprobados en 2ª convocatoria', 'AP2');


CREATE TABLE `seg_indicadores_grados` (
  `id_indicador` int NOT NULL,
  `id_grado` int NOT NULL,
  `id_lectivo` int NOT NULL,
  `porcentaje` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




-- ------------------- PREGUNTAS INDICADOR EP2 --------------------

CREATE TABLE `seg_preguntas` (
  `id_pregunta` int NOT NULL,
  `pregunta` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `id_indicador` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `seg_preguntas` (`id_pregunta`, `pregunta`, `id_indicador`) VALUES
(1, '¿Consideras que se están cumpliendo los objetivos al ritmo que deberían?', 2),
(2, '¿Estás cumpliendo con la ogranización y secuenciación de contenidos prevista en la programación?', 2),
(3, '¿Estás cumpliendo con la temporalización prevista en la programación?', 2),
(4, 'Tras lo que llevamos de curso, ¿te parece adecuada la secuenciación  de contenidos de la programación?', 2),
(5, 'Tras lo que llevamos de curso, ¿te parece adecuada la temporalización de contenidos de la programación?', 2),
(6, '¿Se está siguiendo la metodología programada?', 2),
(7, 'Tras lo que llevamos de curso, ¿Te parece adecuada la metodología  descrita en la programación?', 2),
(8, '¿Te está dando buenos resultados el sistema que realmente llevas en clase?', 2),
(9, '¿Has tenido en cuenta los criterios de evaluación mínimos previstos en la programación?', 2),
(10, '¿Se están aplicando el resto de criterios de evaluación previstos en la progamación?', 2),
(11, 'Tras esta evaluación ¿Te parecen  adecuados los criterios de evaluación que hay en la programación?', 2),
(12, '¿Están alcanzando los alumnos los resultados de aprendizaje previstos en el currículo?', 2),
(13, '¿Has aplicado  los criterios de calilficación previstos en la progamación?', 2),
(14, '¿Han resultado fácil de aplicar estos criterios?', 2),
(15, 'Tras ver los resultados, ¿te parecen justas calificaciones obtenidas por tus alumnos?', 2),
(16, '¿Crees que debes mantener los mismos criterios de calificación para posteriores evaluaciones?', 2),
(17, '¿Se están utilizando todos los procedimientos e instrumentos reen la progamación?', 2),
(18, 'Se esté siguiendo la programación o no, ¿Te parece adecuado lo que hay en ella?', 2),
(19, 'Para los alumnos de este grupo en concreto ¿Te parece adecuado lo previsto en la programación?', 2),
(20, 'Sea o no la programado, ¿Te parece adecuado lo que realmente estás llevando a cabo?', 2),
(21, '¿Se dispone de los materiales y recursos didácticos necesarios para las clases teóricas?', 2),
(22, '¿Se dispone de los materiales y recursos didácticos necesarios para las clases prácticas?', 2),
(23, '¿Se han empleado todos los materiales que aparecen en la programación?', 2),
(24, '¿Están resultando de utilidad los materiales y recursos de los que se dispone?', 2),
(25, '¿Se están  empleando los mecanismos de seguimiento que aparecen en la progamación?', 2),
(26, '¿Has dado a conocer a los alumnos la programación y los criterios de evaluación?', 2),
(27, '¿Lo has hecho conforme estaba en la programación?', 2),
(28, '¿Se están realizando las actividades previstas en la programación?', 2),
(29, 'En general, ¿esas actividades parecen adecuadas?', 2),
(30, '¿Están sirviendo para que los alumnos pendientes recuperen?', 2),
(31, '¿Se ha aplicado el plan tay como está previsto en la programación?', 2),
(32, '¿Consideras que en el futuro debe mantenerse el mismo plan de contigencia?', 2),
(33, 'Alumnos matriculados (A)',1),
(34, 'Alumnos que solo han asistido a clase excepcionalmente (B)',1),
(35, 'Alumnos efectivos (A-B=C)',1),
(36, 'Horas docencia previstas (F) x alumnos efectivos (C)',1),
(37, 'Nº total de faltas consignadas (sin contar alumnos B)',1),
(38, 'Horas de docencia previstas (F)',3),
(39, 'Horas faltadas por el profesor/a',3),
(40, 'Horas perdidas por otros motivos',3),
(41, 'Total alumnos evaluados',4),
(42, 'Total alumnos aprobados',4),
(43, 'Interés/atención del grupo',5),
(44, 'Comportamiento general, respeto, trato',5),
(45, 'Puntualidad',5),
(46, 'Limpieza/orden aula/taller, equipos, etc',5),
(47, 'Octubre', 6),
(48, 'Noviembre', 6),
(49, 'Diciembre', 6),
(50, 'Enero', 6),
(51, 'Febrero', 6),
(52, 'Marzo', 6),
(53, 'Abril', 6),
(54, 'Mayo', 6),
(55, 'Junio', 6),
(56, 'Total alumnos evaluados',7),
(57, 'Total alumnos aprobados',7);



-- ------------------- CATEGORIAS INDICADORES --------------------

CREATE TABLE `seg_categorias` (
  `id_categoria` int NOT NULL,
  `categoria` varchar(500) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `seg_categorias` (`id_categoria`, `categoria`) VALUES
(1, 'Objetivos del modulo profesional'),
(2, 'Organizacion, secuenciacion y temporalizacion'),
(3, 'Principios metodologicos'),
(4, 'Resultados de aprendizaje y critreios de evaluacion'),
(5, 'Criterios de calificacion'),
(6, 'Procedimiento e instrumentos de evaluacion'),
(7, 'Materiales y recursos didacticos'),
(8, 'Mecanismos de seguimiento y valoracion. Derecho a conocer la programacion'),
(9, 'Actividades de orientacion y apoyo a alumnos pendientes'),
(10, 'Plan de contingencia')
;

CREATE TABLE `seg_preguntas_categorias`(
  `id_pregunta` int NOT NULL ,
  `id_categoria` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `seg_preguntas_categorias` (`id_pregunta`, `id_categoria`) VALUES
(1,1),
(2,2),
(3,2),
(4,2),
(5,2),
(6,3),
(7,3),
(8,3),
(9,4),
(10,4),
(11,4),
(12,4),
(13,5),
(14,5),
(15,5),
(16,5),
(17,6),
(18,6),
(19,6),
(20,6),
(21,7),
(22,7),
(23,7),
(24,7),
(25,8),
(26,8),
(27,8),
(28,9),
(29,9),
(30,9),
(31,10),
(32,10);


-- ------------------- ACCIONES / SOLUCIONES --------------------

CREATE TABLE `seg_acciones` (
  `id_accion` int NOT NULL,
  `accion` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `seg_acciones` (`id_accion`, `accion`) VALUES
(1, 'Causa'), (2, 'Solucion (trimestral)'), (3, 'Solucion (final)');


CREATE TABLE `seg_soluciones` (
  `id_solucion` int NOT NULL,
  `solucion` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `id_accion` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `seg_soluciones` (`id_solucion`, `solucion`, `id_accion`) VALUES
(1,'Motivos laborales de los alumnos',1),
(2,'Motivos enfermedad algún alumno',1),
(3,'Ausencias injusticadas de los alumnos',1),
(4,'Huelgas de alumnos',1),
(5,'Huelgas de profesores',1),
(6,'Permisos del profesor sin nombramiento de sustituto',1),
(7,'Otras ausencias justificadas del profesor',1),
(8,'Acumulación de días festivos en los que había clase del módulo',1),
(9,'Acumulación de fenómenos atmosféricos adversos',1),
(10,'Coincidencia con actividades extraaescolares',1),
(11,'Poco interés de los alumnos por el módulo',1),
(12,'Existencia de grupos de alborotadores que impiden el correcto desarrollo de las clases',1),
(13,'Poca atención de los alumnos en clase',1),
(14,'Falta de interés y motivación del grupo',1),
(15,'Comportamiento general, respeto, trato incorrecto',1),
(16,'Falta de puntualidad',1),
(17,'Limpieza/orden aula/taller, equipos, etc inadecuada',1),
(18,'Falta de feeling alumnos-profesor',1),
(19,'Animadversión alumnos-profesor',1),
(20,'Desmotivación del profesor',1),
(21,'Falta de experiencia del profesor',1),
(22,'Falta de expriencia del profesor con este módulo',1),
(23,'No resulta adecuado un apartado de la programación',1),
(24,'Insuficiencia de materiales para la correcta impartición del programa',1),
(25,'Insuficiencia de espacio',1),
(26,'Problemas con los materiales o recursos didácticos (Concretar)',1),
(27,'Incumplimiento de lo marcado en algún apartado de la programación por otros motivos (Concretar)',1),
(28,'Dificultad propia de los contenidos',1),
(29,'Bajo nivel de partida de los alumnos',1),
(30,'Se ha insisitido mucho en algunos contenidos y no se ha llegado a dar otros…',1),
(31,'…porque los alumnos lo demandaban',1),
(32,'… porque los alumnos  no los entendían',1),
(33,'…porque se ha considereado más interesante',1),
(34,'Otros motivos (Indicar)',1),
(35,'Aplicar el plan de contigencia',2),
(36,'Modificar el plan de contingencia',2),
(37,'Preparar más actividades para el plan  de contigencia',2),
(38,'Entregar a los alumnos documentación que permita el auto aprendizaje',2),
(39,'Pedir a los alumnos la realización de actividades que compensen las ausencias',2),
(40,'Utilización de la plataforma moodle para la realización de actividades',2),
(41,'Uso del  correo electrónico para la comunicación con los alumnos que faltan de forma justificada',2),
(42,'Aconsejar a los ( ) alumnos que se den de baja para evitar la pérdida de convocatoria ante la práctica imposibilidad de superar el módulo',2),
(43,'Medidas disciplinarias (Concretar)',2),
(44,'Amonestación a los alumnos',2),
(45,'Comunicación con  las familias',2),
(46,'Mantener con los alumnos ( ) una charla de concienciación sobre la importancia de que asistan a clase para no quedar descolgados',2),
(47,'Concienciarles  la repercusión de las faltas en la imposibilidad de realizar ( ) prácticas',2),
(48,'Avisar a los alumnos que de seguir faltando perderán el derecho a la evaluación continua',2),
(49,'Aplicar la pérdida de la evaluación continua',2),
(50,'Actualizar el banco de actividades para las guardias con ejercicios sobre…',2),
(51,'Aprovechar la plataforma Moodle para colgar actividades que puedan realizar los alumnos',2),
(52,'Recuperación de horas con cambios de horas con otros módulos en que se vaya más avanzado',2),
(53,'Coger horas a otro profesor que le sobren',2),
(54,'Realizar dinámicas de grupo orientadas a la resolución de conflictos',2),
(55,'Abordar con los alumnos, la importancia de un ambiente de trabajo respetuoso para facilitar el provechamiento de la clase',2),
(56,'Elaborar planes de mantenimiento/limpieza/organización de las aulas/talleres/equipos',2),
(57,'Proponer desdobles en el módulo en la tercera evaluación',2),
(58,'Proponer apoyos en el módulo  en la tercera evaluación',2),
(59,'Cambiar la distribución de las mesas en el aula',2),
(60,'Cambiar a los alumnos de sitio',2),
(61,'Adaptación de los contenidos',2),
(62,'Hacer las clases más prácticas',2),
(63,'Hacer las clases más agradables',2),
(64,'Hacer las clases más serias',2),
(65,'Preparación de actividades motivadoras',2),
(66,'Salidas del centro',2),
(67,'Actividades de motivación para alumnos con alta capacidad',2),
(68,'Medidas disciplinarias',2),
(69,'Modificar la programación',2),
(70,'Adquisición de nuevos materiales. Concretar',2),
(71,'Emplear otros materiales',2),
(72,'Cambiar la metodología que se está aplicando',2),
(73,'Adaptación de los contenidos',2),
(74,'Planificar actividades de recuperación para el tercer trimestre',2),
(75,'División de la materia en más exámenes para que resulte más fácil su superación',2),
(76,'Preparar actividades de refuerzo para alumnos con menor nivel',2),
(77,'Poner a disposición de los alumnos documentación o ejercicios o ... sobre ( ) tema para que puedan comprenderlo mejor',2),
(78,'Dividir la materia entre varios exámenes',2),
(79,'Otras',2),
(80,'No son necesarias medidas adicionales, porque los alumnos han cumplido con los criterios de evaluación',3),
(81,'No son necesarias medidas adicionales, porque se han tomado ya medidas disciplinarias',3),
(82,'No son necesarias medidas adicionales, porque se ha aplicado el plan de contingencia',3),
(83,'Con las medidas tomadas anteriormente ya se recondujo la situación',3),
(84,'Los alumnos han suspendido por esta causa  y se les programan actividades obligatorias para el tercer trimestre',3),
(85,'Entregar a los alumnos documentación que permita el auto aprendizaje',3),
(86,'Compensar las horas no impartidas con la entrega de documentación que permita el auto aprendizaje',3),
(87,'Actualizar el banco de actividades para las guardias con ejercicios sobre…',3),
(88,'Aprovechar la plataforma Moodle para colgar actividades que puedan realizar los alumnos',3),
(89,'Impartir alguna sesión adicional durante el tercer trimestre en los días de visita al centro',3),
(90,'Proponer  cambios en la programación',3),
(91,'Insistir en la conveniencia de atender al consejo orientador',3),
(92,'Elaborar planes de mantenimiento/limpieza/   organización de las aulas/talleres/equipos',3),
(93,'Proponer desdobles en el módulo para el curso siguiente',3),
(94,'Proponer apoyos en el módulo para el curso siguientes',3),
(95,'Cambiar la distribución de las mesas en el aula en el curso siguiente',3),
(96,'Modificar la programación',3),
(97,'Adquisición de nuevos materiales. Concretar', 3),
(98,'Cambio de libro de texto',3),
(99,'Adaptación de los contenidos',3),
(100,'Hacer las clases más prácticas en el curso siguiente',3),
(101,'Hacer las clases más agradables en el curso siguiente',3),
(102,'Hacer las clases más serias. En el curso siguiente',3),
(103,'Preparación de actividades motivadoras para el curso siguiente',3),
(104,'Salidas del centro al curso siguiente',3),
(105,'Rotación de los profesores por las materias y niveles',3),
(106,'Modificar la programación',3),
(107,'Planificar actividades de recuperación para el tercer trimestre',3),
(108,'Impartir clases de recuperación durante el tercer trimestre',3),
(109,'División de la materia en más exámenes para que resulte más fácil su superación',3),
(110,'Cambiar la asignación de módulos a  los profesores',3),
(111,'Preparar actividades de refuerzo para alumnos con menor nivel',3),
(112,'Preparar actividades de motivación para alumnos con alta capacidad',3),
(113,'Poner a disposición de los alumnos documentación o ejercicios o ... sobre ( ) tema para que puedan comprenderlo mejor',3),
(114,'Incrementar en la programación las horas destinadas a ( ) tema en detrimento de las destinadas a ( )',3),
(115,'Reestructuración de los contenidos, manteniendo los mínimos',3),
(116,'Modificación de los contenidos mínimos',3),
(117,'Dividir la materia entre varios exámenes',3),
(118,'Planificar actividades de recuperación para el tercer trimestre',3),
(119,'Otras (Indicar)',3);




-- ------------------- TEMAS / SEGUIMIENTO --------------------

CREATE TABLE `seg_temas` (
  `id_tema` int NOT NULL,
  `tema` int NOT NULL,
  `id_modulo` int NOT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_horas` float NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `seg_seguimiento_temas` (
  `id_profesor` int NOT NULL,
  `id_tema` int NOT NULL,
  `id_modulo` int NOT NULL,
  `fecha` date NOT NULL,
  `horas_dia` float DEFAULT NULL,
  `plan` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `actividad` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `observaciones` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `seg_seguimiento_modulo` (
  `id_seguimiento` int NOT NULL,
  `id_seg_evaluacion` int NOT NULL,
  `id_modulo` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `seg_seguimiento_preguntas` (
  `id_seguimiento` int NOT NULL,
  `id_pregunta` int NOT NULL,
  `respuesta` float NOT NULL, 
  `observaciones` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




-- ---------------------- TABLA EDITABLE Y REAL DEL INDICADOR EP1 ---------------------------


CREATE TABLE seg_ep1_mes (
    id_ep1_mes INT AUTO_INCREMENT PRIMARY KEY,
    id_seguimiento INT NOT NULL,
    id_modulo INT NOT NULL,
    id_pregunta INT NOT NULL,
    ajustes FLOAT DEFAULT 0,
    contenidos_impartidos FLOAT DEFAULT 0,
    ep1 FLOAT DEFAULT 0, 
    edicion_mes tinyint
);

CREATE TABLE seg_ep1_tema (
    id_ep1_tema INT AUTO_INCREMENT PRIMARY KEY,
    id_ep1_mes INT NOT NULL,
    id_tema INT NOT NULL,
    id_pregunta INT NOT NULL,
    horas_acumuladas FLOAT DEFAULT 0, 
    edicion_tema tinyint
);


CREATE TABLE seg_ep1_real (
    id_ep1_real INT AUTO_INCREMENT PRIMARY KEY,
    id_seguimiento INT NOT NULL,
    id_modulo INT NOT NULL,
    id_pregunta INT NOT NULL,
    ep1 FLOAT
);


CREATE TABLE seg_ep1(
    id_ep1 INT AUTO_INCREMENT PRIMARY KEY,
    id_seguimiento INT NOT NULL,
    id_modulo INT NOT NULL,
    id_pregunta INT NOT NULL,
    ep1 FLOAT
);




-- ---------------------- TABLAS RESUMEN E HISTORICAS ---------------------------

CREATE TABLE `seg_totales` (
  `id_total` int AUTO_INCREMENT PRIMARY KEY,
  `id_seguimiento` int NOT NULL,
  `id_modulo` int NOT NULL,
  `id_indicador` int NOT NULL,
  `total` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `his_total_modulo` (
  `id_total_modulo` int AUTO_INCREMENT PRIMARY KEY, 
  `id_lectivo` int NOT NULL,
  `lectivo` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_ciclo` int NOT NULL,
  `ciclo` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ciclo_corto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_departamento` int NOT NULL,
  `departamento` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `departamento_corto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_grado` int NOT NULL,
  `grado` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_turno` int NOT NULL,
  `turno` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_modulo` int NOT NULL,
  `modulo` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nombre_corto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_departamento_modulo` int NOT NULL,
  `departamento_modulo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_curso` int NOT NULL,
  `curso` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_numero` int NOT NULL,
  `numero` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nombre_curso` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_profesor` int NOT NULL,
  `profesor` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_indicador` int NOT NULL,
  `indicador` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `indicador_corto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total` float NOT NULL,
  `modulo_conforme` tinyint
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `his_total_curso` (
  `id_total_curso` int AUTO_INCREMENT PRIMARY KEY, 
  `id_lectivo` int NOT NULL,
  `lectivo` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_ciclo` int NOT NULL,
  `ciclo` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ciclo_corto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_departamento` int NOT NULL,
  `departamento` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `departamento_corto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_grado` int NOT NULL,
  `grado` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_turno` int NOT NULL,
  `turno` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_curso` int NOT NULL,
  `curso` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_numero` int NOT NULL,
  `numero` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nombre_curso` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_indicador` int NOT NULL,
  `indicador` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `indicador_corto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total` float NOT NULL, 
  `conforme` tinyint
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `his_anual` (
    `id_anual` INT NOT NULL,
    `id_lectivo` INT NOT NULL,
    `id_indicador` INT NOT NULL,
    `promedio` DECIMAL(5,2) NOT NULL
);



-- ---------------------- PROGRAMACIONES ---------------------------


CREATE TABLE `seg_programaciones` (
  `id_programacion` int NOT NULL,
  `id_modulo` int NOT NULL,
  `id_profesor` int DEFAULT NULL,
  `id_lectivo` int DEFAULT NULL,
  `codigo_programacion` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `ruta` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `num_version` int NOT NULL,
  `id_programacion_base` int DEFAULT NULL,
  `nueva` tinyint NOT NULL,
  `editada` int NOT NULL,
  `activa` tinyint NOT NULL, 
  `codigo_verificacion` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `verificada_profesor` tinyint NOT NULL,
  `verificada_jefe_dep` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- ---------------------- VALORACIONES DEL SEGUIMIENTO (PROFESOR - ANALISIS) ---------------------------

CREATE TABLE `seg_valoraciones` (
  `id_valoracion` INT NOT NULL AUTO_INCREMENT,         
  `id_lectivo` INT NOT NULL,                           
  `id_seguimiento` INT NOT NULL,                      
  `id_modulo` INT NOT NULL,                           
  `causa` INT DEFAULT NULL,                            
  `causa2` INT DEFAULT NULL,                           
  `otro1` VARCHAR(255) DEFAULT NULL,                   
  `otro2` VARCHAR(255) DEFAULT NULL,                    
  `otro3` VARCHAR(255) DEFAULT NULL,                    
  `solucion` INT DEFAULT NULL,                         
  `solucion2` INT DEFAULT NULL,                        
  `solucion3` INT DEFAULT NULL,                        
  `observaciones` VARCHAR(1000) COLLATE utf8mb4_general_ci DEFAULT NULL, 
  PRIMARY KEY (`id_valoracion`),                       
  KEY `FK_seg_valoraciones_id_lectivo` (`id_lectivo`),  
  KEY `FK_seg_valoraciones_id_seguimiento` (`id_seguimiento`), 
  KEY `FK_seg_valoraciones_id_modulo` (`id_modulo`),   
  KEY `FK_seg_valoraciones_id_causa` (`causa`),         
  KEY `FK_seg_valoraciones_id_causa2` (`causa2`),       
  KEY `FK_seg_valoraciones_id_solucion` (`solucion`),  
  KEY `FK_seg_valoraciones_id_solucion2` (`solucion2`), 
  KEY `FK_seg_valoraciones_id_solucion3` (`solucion3`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;






--
-- Estructura Stand-in para la vista `segui_profesor_departamento`
--

CREATE TABLE `segui_profesor_departamento` (
`departamento` varchar(100)
,`id_departamento` int
,`id_profesor` int
,`nombre_completo` varchar(100)
);



--
-- Estructura Stand-in para la vista `segui_departamento_modulo`
--

DROP TABLE IF EXISTS `segui_departamento_modulo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `segui_departamento_modulo` AS
    SELECT `cpifp_departamento`.`id_departamento` AS `id_departamento`, 
    `cpifp_departamento`.`departamento` AS `departamento`,
    `cpifp_ciclos`.`id_ciclo` AS `id_ciclo`, 
    `cpifp_ciclos`.`ciclo` AS `ciclo`,
    `cpifp_ciclos`.`ciclo_corto`,
    `cpifp_grados`.`nombre` AS `grado`,
    `cpifp_grados`.`id_grado`,
    `cpifp_turnos`.`id_turno`,
    `cpifp_turnos`.`turno`,
    `cpifp_curso`.`id_curso` AS `id_curso`,
    `cpifp_curso`.`curso` AS `curso`, 
    `cpifp_curso`.`id_numero`, 
    `seg_numero`.`numero`, 
    `seg_numero`.`nombre_curso`,
    `cpifp_modulo`.`id_modulo` AS `id_modulo`, 
    `cpifp_modulo`.`nombre_corto` AS `nombre_corto`,
    `cpifp_modulo`.`modulo` AS `modulo`, 
    `cpifp_modulo`.`codigo_programacion`,
    `cpifp_modulo`.`horas_totales` AS `horas_totales`, 
    `cpifp_modulo`.`horas_semanales` AS `horas_semanales`, 
    `cpifp_modulo`.`id_departamento` AS `departamento_modulo`
    FROM `cpifp_modulo`
    JOIN `cpifp_curso` ON `cpifp_curso`.`id_curso` = `cpifp_modulo`.`id_curso`
    JOIN `cpifp_ciclos` ON `cpifp_ciclos`.`id_ciclo` = `cpifp_curso`.`id_ciclo`
    JOIN `cpifp_grados` ON `cpifp_ciclos`.`id_grado` = `cpifp_grados`.`id_grado`
    JOIN `cpifp_turnos` ON `cpifp_ciclos`.`id_turno` = `cpifp_turnos`.`id_turno`
    JOIN `cpifp_departamento` ON `cpifp_departamento`.`id_departamento` = `cpifp_ciclos`.`id_departamento`
    JOIN `seg_numero` ON `seg_numero`.`id_numero` = `cpifp_curso`.`id_numero`;




--
-- Estructura para la vista `segui_profesor_departamento`
--
DROP TABLE IF EXISTS `segui_profesor_departamento`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `segui_profesor_departamento`  AS
    SELECT `cpifp_departamento`.`id_departamento` AS `id_departamento`, 
    `cpifp_departamento`.`departamento` AS `departamento`,
    `cpifp_profesor`.`id_profesor` AS `id_profesor`, 
    `cpifp_profesor`.`nombre_completo` AS `nombre_completo`
    FROM ((`cpifp_profesor` join `cpifp_departamento`) join `cpifp_profesor_departamento`)
    WHERE ((`cpifp_profesor_departamento`.`id_profesor` = `cpifp_profesor`.`id_profesor`)
    AND (`cpifp_profesor_departamento`.`id_departamento` = `cpifp_departamento`.`id_departamento`)) ;



--
-- Estructura para la vista `segui_diario_temas`
--
DROP TABLE IF EXISTS `segui_diario_temas`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `segui_diario_temas`  AS
    SELECT id_profesor, seg_seguimiento_temas.id_modulo, 
    fecha, horas_dia, plan, actividad, observaciones, 
    seg_temas.id_tema, tema, descripcion, total_horas, estado
    FROM seg_temas, seg_seguimiento_temas
    WHERE seg_temas.id_tema=seg_seguimiento_temas.id_tema 
    AND seg_temas.id_modulo=seg_seguimiento_temas.id_modulo
    ORDER BY fecha;






-- -------------------------------------------------------
-- -------------------------------------------------------
-- ----------------- INDICES -----------------------------
-- -------------------------------------------------------
-- -------------------------------------------------------


-- ---------- CPIFP -----------------------


ALTER TABLE `cpifp_ciclos`
  ADD PRIMARY KEY (`id_ciclo`),
  ADD KEY `FK_id_grado_cpifp_ciclos` (`id_grado`),
  ADD KEY `FK_id_departamento_cpifp_ciclos` (`id_departamento`),
  ADD KEY `FK_id_turno_cpifp_ciclos` (`id_turno`);


ALTER TABLE `cpifp_curso`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `fk_id_numero_seg_numero` (`id_numero`),
  ADD KEY `fk_curso_ciclo1_idx` (`id_ciclo`);


ALTER TABLE `cpifp_departamento`
  ADD PRIMARY KEY (`id_departamento`);


ALTER TABLE `cpifp_modulo`
  ADD PRIMARY KEY (`id_modulo`),
  ADD KEY `fk_cpifp_modulo_curso1_idx` (`id_curso`),
  ADD KEY `fk_cpifp_modulo_cpifp_departamento1` (`id_departamento`);


ALTER TABLE `cpifp_profesor`
  ADD PRIMARY KEY (`id_profesor`),
  ADD UNIQUE KEY `login_UNIQUE` (`login`);


ALTER TABLE `cpifp_profesor_departamento`
  ADD PRIMARY KEY (`id_profesor_departamento`),
  ADD KEY `fk_cpifp_profesor_departamento_cpifp_departamento1_idx` (`id_departamento`),
  ADD KEY `fk_cpifp_profesor_departamento_man_rol1_idx` (`id_rol`),
  ADD KEY `fk_cpifp_profesor_departamento_cpifp_profesor1_idx` (`id_profesor`);


ALTER TABLE `cpifp_profesor_modulo`
  ADD PRIMARY KEY (`id_profesor_modulo`),
  ADD KEY `FK_prof_modulo_idx` (`id_profesor`),
  ADD KEY `FK_modulo_idx` (`id_modulo`),
  ADD KEY `FK_id_lectivo_idx` (`id_lectivo`);


ALTER TABLE `cpifp_rol`
  ADD PRIMARY KEY (`id_rol`);





-- ---------- SEGUIMIENTO -----------------------


ALTER TABLE `seg_lectivos`
  ADD PRIMARY KEY (`id_lectivo`);


ALTER TABLE `seg_calendario`
  ADD PRIMARY KEY (`id_calendario`),
  ADD KEY `FK_seg_calendario_seg_lectivo` (`id_lectivo`);


ALTER TABLE `seg_evaluaciones`
  ADD PRIMARY KEY (`id_seg_evaluacion`),
  ADD KEY `FK_seg_evaluaciones_cpifp_evaluaciones` (`id_evaluacion`),
  ADD KEY `FK_seg_evaluaciones_seg_calendario` (`id_calendario`),
  ADD KEY `FK_seg_evaluaciones_cpifp_grados` (`id_grado`),
  ADD KEY `FK_seg_evaluaciones_cpifp_turnos` (`id_turno`),
  ADD KEY `FK_seg_evaluaciones_seg_numero` (`id_numero`);


ALTER TABLE `seg_festivos`
  ADD PRIMARY KEY (`id_festivo`),
  ADD KEY `FK_seg_festivos_seg_calendario` (`id_calendario`);


ALTER TABLE `seg_dias_semana`
  ADD PRIMARY KEY (`id_dia_semana`);


ALTER TABLE `seg_horario_modulo`
  ADD PRIMARY KEY(`id_dia_semana`,`id_modulo`),
  ADD KEY `FK_seg_horario_modulo_seg_dias_semana` (`id_dia_semana`),
  ADD KEY `FK_seg_horario_modulo_cpifp_modulo` (`id_modulo`);


ALTER TABLE `seg_seguimiento_modulo`
  ADD PRIMARY KEY (`id_seguimiento`,`id_seg_evaluacion`,`id_modulo`),
  ADD KEY `FK_seg_seguimiento_modulo_seg_evaluaciones` (`id_seg_evaluacion`),
  ADD KEY `FK_seg_seguimiento_modulo_cpifp_modulo` (`id_modulo`);


ALTER TABLE `seg_seguimiento_preguntas`
  ADD PRIMARY KEY (`id_seguimiento`,`id_pregunta`),
  ADD KEY `FK_seg_seguimiento_preguntas_seg_seguimiento_modulo` (`id_seguimiento`),
  ADD KEY `FK_seg_seguimiento_preguntas_seg_preguntas` (`id_pregunta`);


ALTER TABLE `seg_numero`
  ADD PRIMARY KEY (`id_numero`);


ALTER TABLE `seg_indicadores`
  ADD PRIMARY KEY (`id_indicador`);


ALTER TABLE `seg_indicadores_grados` 
  ADD PRIMARY KEY(`id_indicador`,`id_grado`,`id_lectivo`),
  ADD KEY `FK_id_indicador_seg_indicadores_grados` (`id_indicador`),
  ADD KEY `FK_id_grado_seg_indicadores_gradOS` (`id_grado`),
  ADD KEY `FK_id_lectivo_seg_indicadores_gradOS` (`id_lectivo`);


ALTER TABLE `seg_preguntas`
  ADD PRIMARY KEY (`id_pregunta`);


ALTER TABLE `seg_categorias`
  ADD PRIMARY KEY (`id_categoria`);


ALTER TABLE `seg_preguntas_categorias`
  ADD PRIMARY KEY(`id_pregunta`,`id_categoria`),
  ADD KEY `FK_id_pregunta_seg_preguntas_categorias`  (`id_pregunta`),
  ADD KEY `FK_id_categoria_seg_preguntas_categorias`  (`id_categoria`);


ALTER TABLE `seg_acciones`
  ADD PRIMARY KEY (`id_accion`);


ALTER TABLE `seg_soluciones`
  ADD PRIMARY KEY (`id_solucion`),
  ADD KEY `FK_id_accion_seg_soluciones` (`id_accion`);


ALTER TABLE `seg_totales`
  ADD KEY `FK_id_seguimiento_seg_totales` (`id_seguimiento`),
  ADD KEY `FK_id_modulo_seg_totales` (`id_modulo`),
  ADD KEY `FK_id_indicador_seg_totales` (`id_indicador`);


ALTER TABLE `seg_programaciones`
  ADD PRIMARY KEY (`id_programacion`),
  ADD KEY `FK_id_modulo_cpifp_modulo` (`id_modulo`),
  ADD KEY `FK_id_profesor_cpifp_profesor` (`id_profesor`),
  ADD KEY `FK_id_lectivo_seg_lectivos` (`id_lectivo`),
  ADD KEY `FK_id_programacion_base_seg_programaciones` (`id_programacion`);


ALTER TABLE `seg_temas`
  ADD PRIMARY KEY (`id_tema`);


ALTER TABLE `seg_seguimiento_temas`
  ADD PRIMARY KEY (`id_profesor`,`id_tema`,`id_modulo`,`fecha`),
  ADD KEY `FK_id_profesor_seg_profesor_tema` (`id_profesor`),
  ADD KEY `FK_id_tema_seg_profesor_tema` (`id_tema`),
  ADD KEY `FK_id_modulo_seg_profesor_tema` (`id_modulo`);


ALTER TABLE `his_anual`
  ADD PRIMARY KEY (`id_anual`),
  ADD KEY `FK_id_lectivo_his_anual` (`id_lectivo`),
  ADD KEY `FK_id_indicador_his_anual` (`id_indicador`);




-- -------------------------------------------------------
-- -------------------------------------------------------
-- ----------------- AUTOINCREMENT -----------------------
-- -------------------------------------------------------
-- -------------------------------------------------------



-- ---------- CPIFP -----------------------

ALTER TABLE `cpifp_ciclos`
  MODIFY `id_ciclo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

ALTER TABLE `cpifp_curso`
  MODIFY `id_curso` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

ALTER TABLE `cpifp_departamento`
  MODIFY `id_departamento` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

ALTER TABLE `cpifp_modulo`
  MODIFY `id_modulo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

ALTER TABLE `cpifp_profesor`
  MODIFY `id_profesor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

ALTER TABLE `cpifp_profesor_departamento`
  MODIFY `id_profesor_departamento` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

ALTER TABLE `cpifp_profesor_modulo`
  MODIFY `id_profesor_modulo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;




-- ---------- SEGUIMIENTO -----------------------


ALTER TABLE `seg_lectivos`
  MODIFY `id_lectivo` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `seg_calendario`
  MODIFY `id_calendario` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `seg_evaluaciones`
  MODIFY `id_seg_evaluacion` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `seg_festivos`
  MODIFY `id_festivo` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `seg_seguimiento_modulo`
  MODIFY `id_seguimiento` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `seg_indicadores`
  MODIFY `id_indicador` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
  

ALTER TABLE `seg_preguntas`
  MODIFY `id_pregunta` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `seg_categorias`
  MODIFY `id_categoria` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `seg_acciones`
  MODIFY `id_accion` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `seg_soluciones`
  MODIFY `id_solucion` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `seg_programaciones`
  MODIFY `id_programacion` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `seg_temas`
  MODIFY `id_tema` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `seg_valoraciones`
  MODIFY `id_valoracion` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `his_anual`
  MODIFY `id_anual` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;




-- -------------------------------------------------------
-- -------------------------------------------------------
-- ---------------------- FILTROS ------------------------
-- -------------------------------------------------------
-- -------------------------------------------------------


-- ---------- CPIFP -----------------------

ALTER TABLE `cpifp_ciclos`
  ADD CONSTRAINT `FK_id_departamento_cpifp_ciclos` FOREIGN KEY (`id_departamento`) REFERENCES `cpifp_departamento` (`id_departamento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_id_grado_cpifp_ciclos` FOREIGN KEY (`id_grado`) REFERENCES `cpifp_grados` (`id_grado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_id_turno_cpifp_ciclos` FOREIGN KEY (`id_turno`) REFERENCES `cpifp_turnos` (`id_turno`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `cpifp_curso`
  ADD CONSTRAINT `fk_curso_ciclo1` FOREIGN KEY (`id_ciclo`) REFERENCES `cpifp_ciclos` (`id_ciclo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_numero_seg_numero` FOREIGN KEY (`id_numero`) REFERENCES `seg_numero` (`id_numero`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `cpifp_modulo`
  ADD CONSTRAINT `fk_cpifp_modulo_cpifp_departamento1` FOREIGN KEY (`id_departamento`) REFERENCES `cpifp_departamento` (`id_departamento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cpifp_modulo_curso1` FOREIGN KEY (`id_curso`) REFERENCES `cpifp_curso` (`id_curso`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `cpifp_profesor_departamento`
  ADD CONSTRAINT `fk_cpifp_profesor_departamento_cpifp_departamento1` FOREIGN KEY (`id_departamento`) REFERENCES `cpifp_departamento` (`id_departamento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cpifp_profesor_departamento_cpifp_profesor1` FOREIGN KEY (`id_profesor`) REFERENCES `cpifp_profesor` (`id_profesor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cpifp_profesor_departamento_man_rol1` FOREIGN KEY (`id_rol`) REFERENCES `cpifp_rol` (`id_rol`);


ALTER TABLE `cpifp_profesor_modulo`
  ADD CONSTRAINT `FK_id_lectivo` FOREIGN KEY (`id_lectivo`) REFERENCES `seg_lectivos` (`id_lectivo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `cpifp_modulo` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_prof_modulo` FOREIGN KEY (`id_profesor`) REFERENCES `cpifp_profesor` (`id_profesor`) ON DELETE CASCADE ON UPDATE CASCADE;





-- ---------- SEGUIMIENTO -----------------------



ALTER TABLE `seg_indicadores_grados`
  ADD CONSTRAINT `FK_seg_indicadores_grados_seg_indicadores` FOREIGN KEY (`id_indicador`) REFERENCES `seg_indicadores` (`id_indicador`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_seg_indicadores_grados_cpifp_grados` FOREIGN KEY (`id_grado`) REFERENCES `cpifp_grados` (`id_grado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_seg_indicadores_grados_seg_lectivos` FOREIGN KEY (`id_lectivo`) REFERENCES `seg_lectivos` (`id_lectivo`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `seg_preguntas`
  ADD CONSTRAINT `fk_id_indicador` FOREIGN KEY (`id_indicador`) REFERENCES `seg_indicadores` (`id_indicador`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `seg_preguntas_categorias`
  ADD CONSTRAINT `FK_id_pregunta` FOREIGN KEY (`id_pregunta`) REFERENCES `seg_preguntas`(`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_id_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `seg_categorias`(`id_categoria`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `seg_soluciones` 
  ADD CONSTRAINT `FK_id_accion_seg_soluciones` FOREIGN KEY (`id_accion`) REFERENCES `seg_acciones` (`id_accion`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `seg_calendario`
  ADD CONSTRAINT `FK_seg_calendario_seg_lectivos` FOREIGN KEY (`id_lectivo`) REFERENCES `seg_lectivos` (`id_lectivo`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `seg_festivos`
  ADD CONSTRAINT `FK_seg_festivos_seg_calendario` FOREIGN KEY (`id_calendario`) REFERENCES `seg_calendario` (`id_calendario`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `seg_evaluaciones`
  ADD CONSTRAINT `FK_seg_evaluaciones_cpifp_evaluaciones` FOREIGN KEY (`id_evaluacion`) REFERENCES `cpifp_evaluaciones` (`id_evaluacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_seg_evaluaciones_seg_calendario` FOREIGN KEY (`id_calendario`) REFERENCES `seg_calendario` (`id_calendario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_seg_evaluaciones_cpifp_grados` FOREIGN KEY (`id_grado`) REFERENCES `cpifp_grados` (`id_grado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_seg_evaluaciones_cpifp_turnos` FOREIGN KEY (`id_turno`) REFERENCES `cpifp_turnos` (`id_turno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_seg_evaluaciones_seg_numero` FOREIGN KEY (`id_numero`) REFERENCES `seg_numero` (`id_numero`) ON DELETE CASCADE ON UPDATE CASCADE;
  


ALTER TABLE `seg_seguimiento_modulo`
  ADD CONSTRAINT `FK_seg_seguimiento_modulo_seg_evaluaciones` FOREIGN KEY (`id_seg_evaluacion`) REFERENCES `seg_evaluaciones` (`id_seg_evaluacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_seg_seguimiento_modulo_cpifp_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `cpifp_modulo` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `seg_horario_modulo`
  ADD CONSTRAINT `FK_seg_horario_modulo_seg_dias_semana` FOREIGN KEY (`id_dia_semana`) REFERENCES `seg_dias_semana` (`id_dia_semana`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_seg_horario_modulo_cpifp_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `cpifp_modulo` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `seg_seguimiento_preguntas`
  ADD CONSTRAINT `FK_seg_seguimiento_preguntas_seg_seguimiento_modulo` FOREIGN KEY (`id_seguimiento`) REFERENCES `seg_seguimiento_modulo` (`id_seguimiento`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `seg_temas`
  ADD CONSTRAINT `FK_id_modulo_seg_tema` FOREIGN KEY (`id_modulo`) REFERENCES `cpifp_modulo` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;



ALTER TABLE `seg_programaciones`
  ADD CONSTRAINT `FK_id_modulo_seg_programanciones` FOREIGN KEY (`id_modulo`) REFERENCES `cpifp_modulo` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_id_lectivo_seg_programaciones` FOREIGN KEY (`id_lectivo`) REFERENCES `seg_lectivos` (`id_lectivo`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `seg_seguimiento_temas`
  ADD CONSTRAINT `FK_id_modulo_seg_profesor_tema` FOREIGN KEY (`id_modulo`) REFERENCES `cpifp_modulo` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_id_profesor_seg_profesor_tema` FOREIGN KEY (`id_profesor`) REFERENCES `cpifp_profesor` (`id_profesor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_id_tema_seg_profesor_tema` FOREIGN KEY (`id_tema`) REFERENCES `seg_temas` (`id_tema`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `seg_totales`
  ADD CONSTRAINT `FK_id_seguimiento_seg_totales` FOREIGN KEY (`id_seguimiento`) REFERENCES `seg_seguimiento_modulo` (`id_seguimiento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_id_modulo_seg_totales` FOREIGN KEY (`id_modulo`) REFERENCES `cpifp_modulo` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_indicador_seg_totales` FOREIGN KEY (`id_indicador`) REFERENCES `seg_indicadores` (`id_indicador`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `seg_valoraciones`
  ADD CONSTRAINT `FK_seg_valoraciones_id_lectivo` FOREIGN KEY (`id_lectivo`) REFERENCES `seg_lectivos`(`id_lectivo`),
  ADD CONSTRAINT `FK_seg_valoraciones_id_seguimiento` FOREIGN KEY (`id_seguimiento`) REFERENCES `seg_seguimiento_modulo`(`id_seguimiento`),
  ADD CONSTRAINT `FK_seg_valoraciones_id_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `cpifp_modulo`(`id_modulo`),
  ADD CONSTRAINT `FK_seg_valoraciones_id_causa` FOREIGN KEY (`causa`) REFERENCES `seg_soluciones`(`id_solucion`),
  ADD CONSTRAINT `FK_seg_valoraciones_id_causa2` FOREIGN KEY (`causa2`) REFERENCES `seg_soluciones`(`id_solucion`),
  ADD CONSTRAINT `FK_seg_valoraciones_id_solucion` FOREIGN KEY (`solucion`) REFERENCES `seg_soluciones`(`id_solucion`),
  ADD CONSTRAINT `FK_seg_valoraciones_id_solucion2` FOREIGN KEY (`solucion2`) REFERENCES `seg_soluciones`(`id_solucion`),
  ADD CONSTRAINT `FK_seg_valoraciones_id_solucion3` FOREIGN KEY (`solucion3`) REFERENCES `seg_soluciones`(`id_solucion`);




-- Claves foráneas para seg_ep1_mes
ALTER TABLE seg_ep1_mes
  ADD CONSTRAINT FK_seg_ep1_mes_id_seguimiento FOREIGN KEY (id_seguimiento)
      REFERENCES seg_seguimiento_modulo(id_seguimiento)
      ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FK_seg_ep1_mes_id_modulo FOREIGN KEY (id_modulo)
      REFERENCES cpifp_modulo(id_modulo)
      ON DELETE CASCADE ON UPDATE CASCADE;


-- Claves foráneas para seg_ep1_tema
ALTER TABLE seg_ep1_tema
  ADD CONSTRAINT FK_seg_ep1_tema_id_ep1_mes FOREIGN KEY (id_ep1_mes)
      REFERENCES seg_ep1_mes(id_ep1_mes)
      ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FK_seg_ep1_tema_id_tema FOREIGN KEY (id_tema)
      REFERENCES seg_temas(id_tema)
      ON DELETE CASCADE ON UPDATE CASCADE;


-- Claves foráneas para seg_ep1_real
ALTER TABLE seg_ep1_real
  ADD CONSTRAINT FK_seg_ep1_real_id_seguimiento FOREIGN KEY (id_seguimiento)
      REFERENCES seg_seguimiento_modulo(id_seguimiento)
      ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FK_seg_ep1_real_id_modulo FOREIGN KEY (id_modulo)
      REFERENCES cpifp_modulo(id_modulo)
      ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FK_seg_ep1_real_id_pregunta FOREIGN KEY (id_pregunta)
      REFERENCES seg_preguntas(id_pregunta)
      ON DELETE CASCADE ON UPDATE CASCADE;


  -- Claves foráneas para seg_ep1
ALTER TABLE seg_ep1
  ADD CONSTRAINT FK_seg_ep1_id_seguimiento FOREIGN KEY (id_seguimiento)
      REFERENCES seg_seguimiento_modulo(id_seguimiento)
      ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FK_seg_ep1_id_modulo FOREIGN KEY (id_modulo)
      REFERENCES cpifp_modulo(id_modulo)
      ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FK_seg_ep1_id_pregunta FOREIGN KEY (id_pregunta)
      REFERENCES seg_preguntas(id_pregunta)
      ON DELETE CASCADE ON UPDATE CASCADE;



