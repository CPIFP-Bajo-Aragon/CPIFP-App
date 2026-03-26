<?php

class AsesoriaModelo {
    private $db;

    public function __construct(){
        $this->db = new Base;
    }


    public function addAsesoria($datos,$id_profesor){
        $this->db->query("INSERT INTO ori_asesoria (nombre_as, dni_as, titulo_as, 
                                    telefono_as, email_as, descripcion_as, 
                                    domicilio_as, fecha_inicio, id_estado) 
                            VALUES (:nombre_as, :dni_as, :titulo_as, :telefono_as, 
                                    :email_as, :descripcion_as, :domicilio_as, 
                                    NOW(), 1)");

        //vinculamos los valores
        $this->db->bind(':nombre_as',trim($datos['nombre_as']));
        $this->db->bind(':dni_as',trim($datos['dni_as']));
        $this->db->bind(':titulo_as',trim($datos['titulo_as']));
        $this->db->bind(':telefono_as',trim($datos['telefono_as']));
        $this->db->bind(':email_as',trim($datos['email_as']));
        $this->db->bind(':domicilio_as',trim($datos['domicilio_as']));
        $this->db->bind(':descripcion_as',trim($datos['descripcion_as']));

        $id_asesoria = $this->db->executeLastId();


        $this->db->query("INSERT INTO ori_reg_acciones (fecha_reg,accion,automatica,
                                id_asesoria, id_profesor) 
                            VALUES (NOW(),'Inicia', 1, :id_asesoria,:id_profesor)");

        $this->db->bind(':id_asesoria',$id_asesoria);
        $this->db->bind(':id_profesor',$id_profesor);

        //ejecutamos
        try {
            return $this->db->execute();
        } catch (Exception $e) {
            return false;
            // $this->db->query_error($e);
        }
    }



    public function getAsesoriasActivas(){
        $this->db->query("SELECT * FROM ori_asesoria
                                    NATURAL JOIN ori_estados
                                WHERE id_estado=1 OR id_estado=2
                                ORDER BY fecha_inicio DESC");

        return $this->db->registros();
    }


    public function getProfesor($id_profesor){
        $this->db->query("SELECT * FROM cpifp_profesor
                                WHERE id_profesor=:id_profesor");

        $this->db->bind(':id_profesor',$id_profesor);

        return $this->db->registro();
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


    public function getAccionesAsesoria($id_asesoria){
        $this->db->query("SELECT * FROM ori_reg_acciones
                                    NATURAL JOIN cpifp_profesor
                                WHERE id_asesoria=:id_asesoria
                                ORDER BY fecha_reg");

        $this->db->bind(':id_asesoria',$id_asesoria);

        return $this->db->registros();
    }


    public function getAsesoria($id_asesoria){
        $this->db->query("SELECT * FROM ori_asesoria
                                    NATURAL JOIN ori_estados
                                WHERE id_asesoria=:id_asesoria");

        $this->db->bind(':id_asesoria',$id_asesoria);

        return $this->db->registro();
    }


    public function editAsesoria($datos,$id_asesoria){

        $this->db->query("UPDATE ori_asesoria SET nombre_as=:nombre_as, dni_as=:dni_as, titulo_as=:titulo_as, 
                                            telefono_as=:telefono_as, email_as=:email_as, domicilio_as=:domicilio_as, 
                                            descripcion_as=:descripcion_as
                                    WHERE id_asesoria=:id_asesoria");

        $this->db->bind(':nombre_as',$datos['nombre_as']);
        $this->db->bind(':dni_as',$datos['dni_as']);
        $this->db->bind(':titulo_as',$datos['titulo_as']);
        $this->db->bind(':telefono_as',$datos['telefono_as']);
        $this->db->bind(':email_as',$datos['email_as']);
        $this->db->bind(':domicilio_as',$datos['domicilio_as']);
        $this->db->bind(':descripcion_as',$datos['descripcion_as']);
        $this->db->bind(':id_asesoria',$id_asesoria);

        // print_r($datos);

        // exit();
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }


    public function addAccion($datos){

        // Cambiamos estado de la asesoria: 2 -- Procesando
        $this->db->query("UPDATE ori_asesoria SET id_estado=2
                                            WHERE id_asesoria=:id_asesoria");

        $this->db->bind(':id_asesoria',$datos['id_asesoria']);
        $this->db->execute();

        $this->db->query("INSERT INTO ori_reg_acciones (fecha_reg,accion,automatica,
                                id_asesoria, id_profesor) 
                            VALUES (NOW(),:accion, 0, :id_asesoria,:id_profesor)");

        $this->db->bind(':id_asesoria',$datos['id_asesoria']);
        $this->db->bind(':id_profesor',$datos['id_profesor']);
        $this->db->bind(':accion',$datos['accion']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }


    public function cerrarAsesoria($datos){

        // Cambiamos estado de la asesoria: 3 -- Cerrado
        $this->db->query("UPDATE ori_asesoria SET id_estado=3, fecha_fin=NOW()
                                            WHERE id_asesoria=:id_asesoria");

        $this->db->bind(':id_asesoria',$datos['id_asesoria']);
        $this->db->execute();

        $this->db->query("INSERT INTO ori_reg_acciones (fecha_reg,accion,automatica,
                                id_asesoria, id_profesor) 
                            VALUES (NOW(),'Cierra', 1, :id_asesoria,:id_profesor)");

        $this->db->bind(':id_asesoria',$datos['id_asesoria']);
        $this->db->bind(':id_profesor',$datos['id_profesor']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }


    public function abrirAsesoria($datos){

        // Cambiamos estado de la asesoria: 2 -- Procesando
        $this->db->query("UPDATE ori_asesoria SET id_estado=2, fecha_fin=null
                                            WHERE id_asesoria=:id_asesoria");

        $this->db->bind(':id_asesoria',$datos['id_asesoria']);
        $this->db->execute();

        $this->db->query("INSERT INTO ori_reg_acciones (fecha_reg,accion,automatica,
                                id_asesoria, id_profesor) 
                            VALUES (NOW(),'Abre', 1, :id_asesoria,:id_profesor)");

        $this->db->bind(':id_asesoria',$datos['id_asesoria']);
        $this->db->bind(':id_profesor',$datos['id_profesor']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }


    public function asesoriaCerrada($id_asesoria){
        $this->db->query("SELECT * FROM ori_asesoria
                                WHERE id_asesoria = $id_asesoria
                                    AND id_estado=3");

        return $this->db->rowCount();
    }


    public function getAsesorias(){
        $this->db->query("SELECT * FROM ori_asesoria NATURAL JOIN ori_estados ORDER BY fecha_inicio DESC");

        return $this->db->registros();
    }


    public function getAccion($id_reg_acciones){
        $this->db->query("SELECT * FROM ori_reg_acciones
                                WHERE id_reg_acciones=:id_reg_acciones");

        $this->db->bind(':id_reg_acciones',$id_reg_acciones);

        return $this->db->registro();
    }


    public function setAccion($datos){

        $this->db->query("UPDATE ori_reg_acciones SET accion=:accion
                                            WHERE id_reg_acciones=:id_reg_acciones");

        $this->db->bind(':accion',trim($datos['accion']));
        $this->db->bind(':id_reg_acciones',$datos['id_reg_acciones']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }


    public function delAsesoria($id_asesoria){
        $this->db->query("DELETE FROM ori_asesoria WHERE id_asesoria = :id_asesoria");
        
        $this->db->bind(':id_asesoria',$id_asesoria);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }


    public function delAccion($id_reg_acciones){
        $this->db->query("DELETE FROM ori_reg_acciones WHERE id_reg_acciones = :id_reg_acciones");
        
        $this->db->bind(':id_reg_acciones',$id_reg_acciones);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function getAsesoriasFiltro($datos){
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

        $this->db->query("SELECT * FROM ori_asesoria NATURAL JOIN ori_estados 
                                    WHERE (LOWER(nombre_as) LIKE '%$buscar%'
                                        OR LOWER(dni_as) LIKE '%$buscar%'
                                        OR LOWER(titulo_as) LIKE '%$buscar%'
                                        OR LOWER(email_as) LIKE '%$buscar%'
                                        OR LOWER(descripcion_as) LIKE '%$buscar%'
                                        OR LOWER(domicilio_as) LIKE '%$buscar%')
                                        AND DATE(fecha_inicio) >= DATE('$fecha_ini')
                                        AND DATE(fecha_inicio) <= DATE('$fecha_fin')
                                        $subConsultaEstado
                                    ORDER BY fecha_inicio DESC");

        return $this->db->registros();
    }


    public function getEstados(){
        $this->db->query("SELECT * FROM ori_estados");

        return $this->db->registros();
    }



    public function getAsesores(){

        $this->db->query("SELECT * FROM cpifp_profesor");

        $profesores = $this->db->registros();

        foreach($profesores as $profesor){
            $profesor->roles = $this->getRolesProfesor($profesor->id_profesor);
        }

        $asesores = [];
        foreach($profesores as $profesor){
            $rolProfesor = obtenerRol($profesor->roles);
            if ($rolProfesor == 200){
                $asesores[] = $profesor;
            }
        }
        return $asesores;
    }


}
