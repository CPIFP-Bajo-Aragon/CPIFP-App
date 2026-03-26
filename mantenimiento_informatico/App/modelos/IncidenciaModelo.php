<?php

class IncidenciaModelo {
    private $db;

    public function __construct(){
        $this->db = new Base;
    }


    
    public function getRolesProfesor($id_profesor){
        $this->db->query("SELECT * 
                                    FROM cpifp_profesor_departamento
                                        NATURAL JOIN cpifp_rol
                                        NATURAL JOIN cpifp_departamento
                                    WHERE id_profesor=:id_profesor");

        $this->db->bind(':id_profesor',$id_profesor);

        return $this->db->registros();
    }



    public function getIncidenciasActivas(){
        $this->db->query("SELECT * FROM man_inf_incidencias
                                    NATURAL JOIN man_estado
                                    NATURAL JOIN man_urgencia
                                    NATURAL JOIN man_ubicacion
                                    NATURAL JOIN man_edificio
                                WHERE id_estado=1 OR id_estado=2
                                ORDER BY fecha_inicio DESC");

        return $this->db->registros();
    }


    public function getAccionesIncidencia($id_incidencia){
        $this->db->query("SELECT * FROM man_inf_reg_acciones
                                    NATURAL JOIN cpifp_profesor
                                WHERE id_incidencia=:id_incidencia
                                ORDER BY fecha_reg");

        $this->db->bind(':id_incidencia',$id_incidencia);

        return $this->db->registros();
    }

    
    public function getEstadosDeUrgencia(){
        $this->db->query("SELECT * FROM  man_urgencia");

        return $this->db->registros();
    }


    public function getEdificios(){
        $this->db->query("SELECT * FROM  man_edificio");

        return $this->db->registros();
    }


    public function getUbicaciones($id_edificio){
        $this->db->query("SELECT * FROM man_ubicacion
                                    
                                WHERE id_edificio=:id_edificio");

        $this->db->bind(':id_edificio',$id_edificio);

        return $this->db->registros();
    }


    public function addIncidencia($datos,$id_profesor){
        $this->db->query("INSERT INTO man_inf_incidencias 
                                    (titulo_in, descripcion_in, fecha_inicio, id_ubicacion, id_estado, id_urgencia) 
                            VALUES (:titulo_in, :descripcion_in, NOW(), :id_ubicacion, 1, :id_urgencia)");

        //vinculamos los valores
        $this->db->bind(':titulo_in',trim($datos['titulo_in']));
        $this->db->bind(':descripcion_in',trim($datos['descripcion_in']));
        $this->db->bind(':id_urgencia',$datos['id_urgencia']);
        $this->db->bind(':id_ubicacion',$datos['id_ubicacion']);

        $id_incidencia = $this->db->executeLastId();

        $this->db->query("INSERT INTO man_inf_reg_acciones
                                    (fecha_reg,accion,automatica, id_incidencia, id_profesor) 
                            VALUES (NOW(),'Inicia', 1, :id_incidencia, :id_profesor)");

        $this->db->bind(':id_incidencia',$id_incidencia);
        $this->db->bind(':id_profesor',$id_profesor);

        //ejecutamos
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }


    public function getTecnicos(){

        $this->db->query("SELECT * FROM cpifp_profesor");

        $profesores = $this->db->registros();

        foreach($profesores as $profesor){
            $profesor->roles = $this->getRolesProfesor($profesor->id_profesor);
        }

        $tecnicos = [];
        foreach($profesores as $profesor){
            $rolProfesor = obtenerRol($profesor->roles);
            if ($rolProfesor == 300){        // Seleccionamos a los tecnicos y administradores
                $tecnicos[] = $profesor;
            }
        }
        return $tecnicos;
    }


    public function delIncidencia($id_incidencia){
        $this->db->query("DELETE FROM man_inf_incidencias WHERE id_incidencia = :id_incidencia");
        
        $this->db->bind(':id_incidencia',$id_incidencia);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }


    public function getIncidencia($id_incidencia){
        $this->db->query("SELECT * FROM man_inf_incidencias
                                    NATURAL JOIN man_estado
                                    NATURAL JOIN man_urgencia
                                    NATURAL JOIN man_ubicacion
                                    NATURAL JOIN man_edificio
                                WHERE id_incidencia=:id_incidencia");

        $this->db->bind(':id_incidencia',$id_incidencia);

        return $this->db->registro();
    }



    public function incidenciaCerrada($id_incidencia){
        $this->db->query("SELECT * FROM man_inf_incidencias
                                WHERE id_incidencia = $id_incidencia
                                    AND id_estado=3");

        return $this->db->rowCount();
    }



    public function addAccion($datos){

        // Cambiamos estado de la asesoria: 2 -- Procesando
        $this->db->query("UPDATE man_inf_incidencias SET id_estado=2
                                            WHERE id_incidencia=:id_incidencia");

        $this->db->bind(':id_incidencia',$datos['id_incidencia']);
        $this->db->execute();

        $this->db->query("INSERT INTO man_inf_reg_acciones 
                                (fecha_reg,accion,automatica,id_incidencia,id_profesor,minutos) 
                            VALUES 
                                (NOW(),:accion, 0,:id_incidencia,:id_profesor,:minutos)");

        $this->db->bind(':id_incidencia',$datos['id_incidencia']);
        $this->db->bind(':id_profesor',$datos['id_profesor']);
        $this->db->bind(':accion',$datos['accion']);
        $this->db->bind(':minutos',$datos['totalMinutos']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }



    public function cerrarIncidencia($datos){

        // Cambiamos estado de la asesoria: 3 -- Cerrado
        $this->db->query("UPDATE man_inf_incidencias SET id_estado=3, fecha_fin=NOW()
                                            WHERE id_incidencia=:id_incidencia");

        $this->db->bind(':id_incidencia',$datos['id_incidencia']);
        $this->db->execute();

        $this->db->query("INSERT INTO man_inf_reg_acciones 
                                (fecha_reg,accion,automatica,id_incidencia, id_profesor) 
                            VALUES 
                                (NOW(),'Cierra', 1, :id_incidencia,:id_profesor)");

        $this->db->bind(':id_incidencia',$datos['id_incidencia']);
        $this->db->bind(':id_profesor',$datos['id_profesor']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }



    public function abrirIncidencia($datos){

        // Cambiamos estado de la asesoria: 2 -- Procesando
        $this->db->query("UPDATE man_inf_incidencias SET id_estado=2, fecha_fin=null
                                            WHERE id_incidencia=:id_incidencia");

        $this->db->bind(':id_incidencia',$datos['id_incidencia']);
        $this->db->execute();

        $this->db->query("INSERT INTO 
                                man_inf_reg_acciones (fecha_reg,accion,automatica,id_incidencia, id_profesor) 
                            VALUES 
                                (NOW(),'Abre', 1, :id_incidencia,:id_profesor)");

        $this->db->bind(':id_incidencia',$datos['id_incidencia']);
        $this->db->bind(':id_profesor',$datos['id_profesor']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }



    public function editIncidencia($datos,$id_incidencia){

        $this->db->query("UPDATE man_inf_incidencias 
                                    SET     titulo_in=:titulo_in, id_urgencia=:id_urgencia, id_ubicacion=:id_ubicacion, 
                                            descripcion_in=:descripcion_in
                                    WHERE id_incidencia=:id_incidencia");

        $this->db->bind(':titulo_in',$datos['titulo_in']);
        $this->db->bind(':id_urgencia',$datos['id_urgencia']);
        $this->db->bind(':id_ubicacion',$datos['id_ubicacion']);
        $this->db->bind(':descripcion_in',$datos['descripcion_in']);
        $this->db->bind(':id_incidencia',$id_incidencia);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }


    public function delAccion($id_reg_acciones){
        $this->db->query("DELETE FROM man_inf_reg_acciones WHERE id_reg_acciones = :id_reg_acciones");
        
        $this->db->bind(':id_reg_acciones',$id_reg_acciones);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }



    public function getAccion($id_reg_acciones){
        $this->db->query("SELECT * FROM man_inf_reg_acciones
                                WHERE id_reg_acciones=:id_reg_acciones");

        $this->db->bind(':id_reg_acciones',$id_reg_acciones);

        return $this->db->registro();
    }



    public function setAccion($datos){

        $this->db->query("UPDATE man_inf_reg_acciones SET accion=:accion, minutos=:minutos
                                            WHERE id_reg_acciones=:id_reg_acciones");

        $this->db->bind(':accion',trim($datos['accion']));
        $this->db->bind(':minutos',$datos['minutos']);
        $this->db->bind(':id_reg_acciones',$datos['id_reg_acciones']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }



    public function getEstados(){
        $this->db->query("SELECT * FROM man_estado");

        return $this->db->registros();
    }



    public function getIncidenciasFiltro($datos, $pagina = 0, $tam_pagina = 0){
        $buscar = strtolower($datos['buscar']);                 // convertimos a minusculas
        $fecha_ini = $datos['fecha_ini'];
        $fecha_fin = $datos['fecha_fin'];
        $estado = $datos['estado'];

        $subConsultaEstado = 'AND false';       // formamos la subconsulta de estados
        foreach($estado as $key=>$e){
            if($key==0){
                $subConsultaEstado = "AND (";
            }

            $subConsultaEstado .= "id_estado = ".$e;

            if($key==count($estado)-1) {
                $subConsultaEstado .= ")";
            } else {
                $subConsultaEstado .= " OR ";
            }
        }

        $consultaBase = "SELECT * FROM man_inf_incidencias 
                                        NATURAL JOIN man_estado
                                        NATURAL JOIN man_urgencia
                                        NATURAL JOIN man_ubicacion
                                        NATURAL JOIN man_edificio
                                    WHERE (LOWER(titulo_in) LIKE '%$buscar%'
                                        OR LOWER(descripcion_in) LIKE '%$buscar%')
                                        AND DATE(fecha_inicio) >= DATE('$fecha_ini')
                                        AND DATE(fecha_inicio) <= DATE('$fecha_fin')
                                        $subConsultaEstado
                                    ORDER BY fecha_inicio DESC";

        $this->db->query("$consultaBase");
        $numIncidenciasFiltro = $this->db->rowCount();            // Obtenemos el total de registros
        
        if ($tam_pagina == 0){                                  // Miramos si queremos la informacion paginada
            $limit_paginacion = "";
        } else {
            $registro_inicial = $pagina * $tam_pagina;
            $limit_paginacion = "LIMIT $registro_inicial, $tam_pagina";
        }

        $this->db->query("$consultaBase $limit_paginacion");

        return (object) [
            'registros' => $this->db->registros(),
            'numPaginas' => ceil($numIncidenciasFiltro/$tam_pagina),
            'paginaActual' => $pagina,
            'numTotalRegistros' => $numIncidenciasFiltro
        ];
    }

/*
    


    public function getProfesor($id_profesor){
        $this->db->query("SELECT * FROM cpifp_profesor
                                WHERE id_profesor=:id_profesor");

        $this->db->bind(':id_profesor',$id_profesor);

        return $this->db->registro();
    }



    public function getAsesorias(){
        $this->db->query("SELECT * FROM ori_asesoria NATURAL JOIN ori_estados ORDER BY fecha_inicio DESC");

        return $this->db->registros();
    }


*/

}
