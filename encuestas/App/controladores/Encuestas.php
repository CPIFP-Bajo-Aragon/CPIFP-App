<?php

class Encuestas extends Controlador {

    private $encuestaModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->encuestaModelo = $this->modelo('EncuestaModelo');
        $this->datos['usuarioSesion']->roles  = $this->encuestaModelo->getRolesProfesor($this->datos['usuarioSesion']->id_profesor);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        $this->datos['menuActivo'] = 'encuestas';

        // Cualquier usuario autenticado puede ver sus encuestas; mínimo rol 100
        $this->datos['rolesPermitidos'] = [100, 200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            echo "No tienes privilegios!!!";
            exit();
        }
    }

    // ─── Listado encuestas de alumnos (tipo 1) ──────────────────────
    public function index($pagina = 0){
        $id_rol      = $this->datos['usuarioSesion']->id_rol;
        $id_profesor = $this->datos['usuarioSesion']->id_profesor;
        $filtro      = ['curso_academico' => cursoAcademicoActual()];
        $tamPagina   = TAM_PAGINA;

        if($id_rol >= 200){
            $this->datos['lista'] = $this->encuestaModelo->getEncuestas($filtro, $pagina, $tamPagina);
        } else {
            $this->datos['lista'] = $this->encuestaModelo->getEncuestasByProfesor($id_profesor, $pagina, $tamPagina);
        }

        $this->datos['cursos']        = $this->encuestaModelo->getCursosAcademicos();
        $this->datos['departamentos'] = $this->encuestaModelo->getDepartamentos();
        $this->datos['ciclos_filtro'] = [];
        $this->datos['cursos_filtro'] = [];
        $this->datos['filtro']        = $filtro;
        $this->datos['tamPagina']     = $tamPagina;
        $this->datos['paginaActual']  = $pagina;
        $this->vista('encuestas/index', $this->datos);
    }


    // ─── Filtro encuestas de alumnos ─────────────────────────────────
    public function filtro($pagina = 0){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $id_rol      = $this->datos['usuarioSesion']->id_rol;
            $id_profesor = $this->datos['usuarioSesion']->id_profesor;
            $filtro      = $_GET;

            if(empty($filtro['curso_academico'])){
                $filtro['curso_academico'] = cursoAcademicoActual();
            }

            $opciones  = [20, 50, 100, 0];
            $tamPagina = isset($filtro['tam_pagina']) && in_array((int)$filtro['tam_pagina'], $opciones)
                         ? (int)$filtro['tam_pagina'] : TAM_PAGINA;
            $tamReal   = ($tamPagina === 0) ? 999999 : $tamPagina;

            if($id_rol >= 200){
                $this->datos['lista'] = $this->encuestaModelo->getEncuestas($filtro, $pagina, $tamReal);
            } else {
                $this->datos['lista'] = $this->encuestaModelo->getEncuestasByProfesor($id_profesor, $pagina, $tamReal);
            }

            $this->datos['filtro']        = $filtro;
            $this->datos['tamPagina']     = $tamPagina;
            $this->datos['cursos']        = $this->encuestaModelo->getCursosAcademicos();
            $this->datos['departamentos'] = $this->encuestaModelo->getDepartamentos();
            // Ciclos: si hay departamento seleccionado los filtramos por él
            $this->datos['ciclos_filtro'] = !empty($filtro['id_departamento'])
                ? $this->encuestaModelo->getCiclosByDepartamento($filtro['id_departamento'])
                : (!empty($filtro['id_ciclo']) ? $this->encuestaModelo->getCiclosConEncuestas() : []);
            $this->datos['cursos_filtro'] = !empty($filtro['id_ciclo'])
                ? $this->encuestaModelo->getCursosConEncuestasByCiclo($filtro['id_ciclo'])
                : [];
            $this->datos['paginaActual']  = $pagina;
            $this->vista('encuestas/index', $this->datos);
        }
    }

    // ─── AJAX: ciclos de un departamento (filtro) ────────────────────
    public function get_ciclos_filtro($id_departamento){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $this->vistaApi($this->encuestaModelo->getCiclosByDepartamento($id_departamento));
        }
    }

    // ─── AJAX: grupos de un ciclo (filtro) ───────────────────────────
    public function get_cursos_filtro($id_ciclo){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $this->vistaApi($this->encuestaModelo->getCursosConEncuestasByCiclo($id_ciclo));
        }
    }

    // ─── Borrado masivo de encuestas seleccionadas ───────────────────
    public function eliminar_masivo(){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            $this->vistaApi(['ok' => false, 'msg' => 'Sin privilegios']);
            return;
        }
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            $this->vistaApi(['ok' => false, 'msg' => 'Método no permitido']);
            return;
        }
        $ids        = $_POST['ids'] ?? [];
        $eliminadas = 0;
        $bloqueadas = 0;
        $errores    = 0;
        foreach($ids as $id){
            $result = $this->encuestaModelo->delEncuestaSegura((int)$id);
            if($result === 'ok')                   $eliminadas++;
            elseif($result === 'tiene_respuestas') $bloqueadas++;
            else                                   $errores++;
        }
        $this->vistaApi([
            'ok'         => true,
            'eliminadas' => $eliminadas,
            'bloqueadas' => $bloqueadas,
            'errores'    => $errores,
            'msg'        => "Eliminadas: {$eliminadas} · Con respuestas (no borradas): {$bloqueadas}" .
                            ($errores ? " · Errores: {$errores}" : ''),
        ]);
    }

    // ─── AJAX: ciclos con encuestas activas (vista pública) ──────────
    public function get_ciclos_publico(){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $curso = $_GET['curso'] ?? cursoAcademicoActual();
            $this->vistaApi($this->encuestaModelo->getCiclosPublico($curso));
        }
    }

    // ─── AJAX: cursos activos de un ciclo (vista pública) ────────────
    public function get_cursos_publico($id_ciclo){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $curso = $_GET['curso'] ?? cursoAcademicoActual();
            $this->vistaApi($this->encuestaModelo->getCursosPublico($id_ciclo, $curso));
        }
    }

    // ─── AJAX: encuestas activas de un grupo (vista pública) ─────────
    public function get_encuestas_publico($id_curso){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $curso = $_GET['curso'] ?? cursoAcademicoActual();
            $this->vistaApi($this->encuestaModelo->getEncuestasActivasGrupo($id_curso, $curso));
        }
    }


    // ─── Nueva encuesta ──────────────────────────────────────────────
    public function nueva(){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            echo "No tienes privilegios para crear encuestas.";
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $datos = $_POST;

            // Comprobar duplicado antes de crear (solo tipo alumno)
            if((int)$datos['id_tipo_encuesta'] === 1 && !empty($datos['id_profesor_modulo'])){
                $existente = $this->encuestaModelo->existeEncuesta(
                    $datos['id_profesor_modulo'],
                    $datos['trimestre'],
                    $datos['curso_academico']
                );
                if($existente){
                    // Volver al formulario con aviso de duplicado
                    $this->datos['error']         = 0;
                    $this->datos['error_duplicado'] = $existente;
                    $this->datos['tipos']         = $this->encuestaModelo->getTiposEncuesta();
                    $this->datos['ciclos']        = $this->encuestaModelo->getCiclosConProfesor();
                    $this->datos['evaluaciones']  = $this->encuestaModelo->getEvaluaciones();
                    $this->datos['curso_actual']  = cursoAcademicoActual();
                    $this->datos['evaluacion_actual'] = $this->encuestaModelo->getEvaluacionActual();
                    $this->datos['post']          = $datos; // para repoblar el form
                    $this->vista('encuestas/nueva', $this->datos);
                    return;
                }
            }

            $id_encuesta = $this->encuestaModelo->addEncuesta($datos, $this->datos['usuarioSesion']->id_profesor);
            if($id_encuesta){
                redireccionar('/encuestas/ver/' . $id_encuesta);
            } else {
                redireccionar('/encuestas/nueva/1');
            }
        } else {
            $this->datos['error']            = 0;
            $this->datos['error_duplicado']  = null;
            $this->datos['tipos']            = $this->encuestaModelo->getTiposEncuesta();
            $this->datos['ciclos']           = $this->encuestaModelo->getCiclosConProfesor();
            $this->datos['evaluaciones']     = $this->encuestaModelo->getEvaluaciones();
            $this->datos['curso_actual']     = cursoAcademicoActual();
            $this->datos['evaluacion_actual'] = $this->encuestaModelo->getEvaluacionActual();
            $this->datos['post']             = [];
            $this->vista('encuestas/nueva', $this->datos);
        }
    }

    // ─── Eliminar encuesta (solo si no tiene respuestas) ─────────────
    public function eliminar_seguro(){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            $this->vistaApi(['ok' => false, 'msg' => 'Sin privilegios']);
            return;
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id     = (int)$_POST['id_encuesta'];
            $result = $this->encuestaModelo->delEncuestaSegura($id);
            if($result === 'ok'){
                $this->vistaApi(['ok' => true,  'msg' => 'Encuesta eliminada correctamente.']);
            } elseif($result === 'tiene_respuestas'){
                $this->vistaApi(['ok' => false, 'msg' => 'No se puede eliminar: la encuesta ya tiene respuestas.']);
            } else {
                $this->vistaApi(['ok' => false, 'msg' => 'Error al eliminar la encuesta.']);
            }
        }
    }

    // ─── AJAX: cursos de un ciclo ─────────────────────────────────────
    public function get_cursos($id_ciclo){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $this->vistaApi($this->encuestaModelo->getCursosByCiclo($id_ciclo));
        }
    }

    // ─── AJAX: módulos de un curso ────────────────────────────────────
    public function get_modulos($id_curso){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $this->vistaApi($this->encuestaModelo->getModulosByCurso($id_curso));
        }
    }

    // ─── AJAX: profesores de un módulo ───────────────────────────────
    public function get_profesores($id_modulo){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $this->vistaApi($this->encuestaModelo->getProfesoresByModulo($id_modulo));
        }
    }


    // ─── Ver encuesta (detalle + resultados) ─────────────────────────
    public function ver($id_encuesta){
        $id_rol      = $this->datos['usuarioSesion']->id_rol;
        $id_profesor = $this->datos['usuarioSesion']->id_profesor;

        $encuesta = $this->encuestaModelo->getEncuesta($id_encuesta);
        if(!$encuesta){
            echo "Encuesta no encontrada.";
            exit();
        }

        // Comprobación: un profesor solo puede ver sus propias encuestas
        if($id_rol < 200 && $encuesta->id_profesor != $id_profesor){
            echo "No tienes acceso a esta encuesta.";
            exit();
        }

        $this->datos['encuesta']              = $encuesta;
        $this->datos['preguntas']             = $this->encuestaModelo->getPreguntasEncuesta($id_encuesta);
        $this->datos['resumen']               = $this->encuestaModelo->getResumenEncuesta($id_encuesta);
        $this->datos['distribucion']          = $this->encuestaModelo->getDistribucionPuntuaciones($id_encuesta);
        $this->datos['distribucion_opciones'] = $this->encuestaModelo->getDistribucionOpciones($id_encuesta);
        $this->datos['comentarios']           = $this->encuestaModelo->getComentariosEncuesta($id_encuesta);
        $this->datos['enlace_publico']        = RUTA_CPIFP . RUTA_URL . '/responder/' . $encuesta->token_publico;
        $this->datos['puedeEliminarForzado']  = $this->encuestaModelo->puedeEliminarForzado(
                                                    $this->datos['usuarioSesion']->roles);
        $this->vista('encuestas/ver', $this->datos);
    }


    // ─── Editar encuesta ─────────────────────────────────────────────
    public function editar($id_encuesta){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            echo "No tienes privilegios.";
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $datos = $_POST;
            $datos['id_encuesta'] = $id_encuesta;
            if($this->encuestaModelo->editEncuesta($datos)){
                redireccionar('/encuestas/ver/' . $id_encuesta);
            } else {
                echo "Error al guardar los cambios.";
            }
        } else {
            $this->datos['encuesta'] = $this->encuestaModelo->getEncuesta($id_encuesta);
            $this->vista('encuestas/editar', $this->datos);
        }
    }


    // ─── Cerrar / abrir encuesta (API) ───────────────────────────────
    public function cerrar(){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            $this->vistaApi(false);
            return;
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id = $_POST['id_encuesta'];
            $this->vistaApi($this->encuestaModelo->cerrarEncuesta($id));
        }
    }

    public function abrir(){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            $this->vistaApi(false);
            return;
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id = $_POST['id_encuesta'];
            $this->vistaApi($this->encuestaModelo->abrirEncuesta($id));
        }
    }

    public function eliminar(){
        $this->datos['rolesPermitidos'] = [300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            $this->vistaApi(false);
            return;
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id = $_POST['id_encuesta'];
            $this->vistaApi($this->encuestaModelo->delEncuesta($id));
        }
    }


    public function eliminar_forzado(){
        $puede = $this->encuestaModelo->puedeEliminarForzado(
                     $this->datos['usuarioSesion']->roles);
        if(!$puede){
            $this->vistaApi(['ok'=>false,'msg'=>'Sin privilegios para eliminación forzada.']);
            return;
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $result = $this->encuestaModelo->delEncuestaForzado((int)$_POST['id_encuesta']);
            $this->vistaApi([
                'ok'  => $result === 'ok',
                'msg' => $result === 'ok' ? 'Encuesta y todos sus datos eliminados.'
                                          : 'Error al eliminar.',
            ]);
        }
    }


    public function estadisticas(){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            echo "No tienes privilegios.";
            exit();
        }
        $this->datos['cursos'] = $this->encuestaModelo->getCursosAcademicos();
        $this->datos['curso_sel'] = $_GET['curso_academico'] ?? cursoAcademicoActual();

        if(!empty($this->datos['curso_sel'])){
            $this->datos['encuestas_curso'] = $this->encuestaModelo->getEncuestas(
                ['curso_academico' => $this->datos['curso_sel']]
            );
        }
        $this->vista('resultados/estadisticas', $this->datos);
    }


    // ─── AJAX: generar una encuesta individual (usado por generar_masiva) ──
    public function generar_uno(){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            $this->vistaApi(['estado' => 'error', 'msg' => 'Sin privilegios']);
            return;
        }
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            $this->vistaApi(['estado' => 'error', 'msg' => 'Método no permitido']);
            return;
        }

        $id_pm                = (int)$_POST['id_profesor_modulo'];
        $id_evaluacion        = (int)$_POST['id_evaluacion'];
        $curso_academico      = trim($_POST['curso_academico']);
        $fecha_inicio         = $_POST['fecha_inicio'];
        $fecha_fin            = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
        $mostrar_mejor_peor   = isset($_POST['mostrar_mejor_peor'])    ? (int)$_POST['mostrar_mejor_peor']    : 1;
        $mostrar_observaciones= isset($_POST['mostrar_observaciones']) ? (int)$_POST['mostrar_observaciones'] : 1;

        // Obtener nombre de la evaluación
        $evaluaciones = $this->encuestaModelo->getEvaluaciones();
        $nombre_eval  = '';
        foreach($evaluaciones as $ev){
            if($ev->id_evaluacion == $id_evaluacion){ $nombre_eval = $ev->evaluacion; break; }
        }

        // Comprobar si ya existe
        $existente = $this->encuestaModelo->existeEncuesta($id_pm, $id_evaluacion, $curso_academico);
        if($existente){
            $this->vistaApi([
                'estado'  => 'omitida',
                'msg'     => 'Ya existe',
                'id_encuesta' => $existente->id_encuesta,
            ]);
            return;
        }

        // Obtener el id_curso del profesor-módulo para asignar el código de grupo
        // El código de grupo se genera aquí (stateless); si ya había una encuesta del mismo
        // id_curso creada antes en esta tanda, el frontend reutiliza el código que recibió.
        // Generamos un código nuevo; el frontend es quien agrupa por id_curso.
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Pero si ya existe otra encuesta del mismo id_curso en esta evaluación/curso,
        // reutilizamos su código para que el grupo comparta código.
        // Obtener id_curso del pm para compartir código con el grupo
        $infopm = $this->encuestaModelo->getInfoProfesorModulo($id_pm);
        if($infopm){
            $codigoExistente = $this->encuestaModelo->getCodigoGrupo($infopm->id_curso, $id_evaluacion, $curso_academico);
            if($codigoExistente) $codigo = $codigoExistente;
        }

        $id_encuesta = $this->encuestaModelo->addEncuestaMasiva(
            $id_pm, $id_evaluacion, $curso_academico, $nombre_eval,
            $codigo,
            $this->datos['usuarioSesion']->id_profesor,
            $fecha_inicio, $fecha_fin,
            $mostrar_mejor_peor, $mostrar_observaciones
        );

        if($id_encuesta){
            $this->vistaApi([
                'estado'        => 'creada',
                'id_encuesta'   => $id_encuesta,
                'id_curso'      => $infopm ? $infopm->id_curso : 0,
                'nombre_curso'  => $infopm ? $infopm->nombre_curso : '',
                'nombre_ciclo'  => $infopm ? $infopm->nombre_ciclo : '',
                'codigo_acceso' => $codigo,
            ]);
        } else {
            $this->vistaApi(['estado' => 'error', 'msg' => 'Error al insertar']);
        }
    }
    public function generar_masiva($error = 0){
        $this->datos['rolesPermitidos'] = [200, 300];
        if(!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])){
            echo "No tienes privilegios.";
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id_evaluacion        = (int)$_POST['id_evaluacion'];
            $curso_academico      = trim($_POST['curso_academico']);
            $fecha_inicio         = $_POST['fecha_inicio'];
            $fecha_fin            = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
            $mostrar_mejor_peor   = isset($_POST['mostrar_mejor_peor'])    ? 1 : 0;
            $mostrar_observaciones= isset($_POST['mostrar_observaciones']) ? 1 : 0;

            // Obtener nombre de la evaluación
            $evaluaciones  = $this->encuestaModelo->getEvaluaciones();
            $nombre_eval   = '';
            foreach($evaluaciones as $ev){
                if($ev->id_evaluacion == $id_evaluacion){
                    $nombre_eval = $ev->evaluacion;
                    break;
                }
            }

            // Un código por grupo (id_curso): todos los módulos del mismo curso
            // comparten el mismo código de acceso, ya que son el mismo grupo de alumnos
            $codigos_por_grupo = []; // [id_curso => codigo_6_digitos]

            $pares      = $this->encuestaModelo->getProfesorModulosParaGeneracion();
            $creadas    = 0;
            $omitidas   = 0;
            $errores    = 0;
            foreach($pares as $par){
                // Saltar si ya existe
                if($this->encuestaModelo->existeEncuesta($par->id_profesor_modulo, $id_evaluacion, $curso_academico)){
                    $omitidas++;
                    continue;
                }
                // Reutilizar o generar el código para este grupo (id_curso)
                if(!isset($codigos_por_grupo[$par->id_curso])){
                    $codigos_por_grupo[$par->id_curso] = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                }
                $ok = $this->encuestaModelo->addEncuestaMasiva(
                    $par->id_profesor_modulo,
                    $id_evaluacion,
                    $curso_academico,
                    $nombre_eval,
                    $codigos_por_grupo[$par->id_curso],
                    $this->datos['usuarioSesion']->id_profesor,
                    $fecha_inicio,
                    $fecha_fin,
                    $mostrar_mejor_peor,
                    $mostrar_observaciones
                );
                if($ok) $creadas++;
                else    $errores++;
            }

            // Redirigir al listado con mensaje en sesión
            if(session_status() === PHP_SESSION_NONE) session_start();

            // Enriquecer codigos_por_grupo con el nombre del curso para mostrar en el mensaje
            $codigos_info = [];
            foreach($pares as $par){
                $id_curso = $par->id_curso;
                if(isset($codigos_por_grupo[$id_curso]) && !isset($codigos_info[$id_curso])){
                    $codigos_info[$id_curso] = [
                        'curso'  => $par->nombre_curso,
                        'ciclo'  => $par->nombre_ciclo,
                        'codigo' => $codigos_por_grupo[$id_curso],
                    ];
                }
            }

            $_SESSION['msg_masiva'] = [
                'creadas'       => $creadas,
                'omitidas'      => $omitidas,
                'errores'       => $errores,
                'codigos_grupo' => $codigos_info,
                'eval'          => $nombre_eval,
                'curso'         => $curso_academico,
            ];
            redireccionar('/encuestas');

        } else {
            $this->datos['error']            = $error;
            $this->datos['evaluaciones']     = $this->encuestaModelo->getEvaluaciones();
            $this->datos['evaluacion_actual'] = $this->encuestaModelo->getEvaluacionActual();
            $this->datos['curso_actual']     = cursoAcademicoActual();
            $this->datos['pares']            = $this->encuestaModelo->getProfesorModulosParaGeneracion();
            $this->vista('encuestas/generar_masiva', $this->datos);
        }
    }


    // ─── Datos de resumen en JSON (para gráficos AJAX) ───────────────
    public function get_resumen($id_encuesta){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $resumen      = $this->encuestaModelo->getResumenEncuesta($id_encuesta);
            $distribucion = $this->encuestaModelo->getDistribucionPuntuaciones($id_encuesta);
            $this->vistaApi(['resumen' => $resumen, 'distribucion' => $distribucion]);
        }
    }
}
