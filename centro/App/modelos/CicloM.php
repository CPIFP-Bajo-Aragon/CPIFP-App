<?php

class CicloM{

    private $db;

    public function __construct(){
        $this->db = new Base;
    }




// TODOS LOS CICLOS
public function obtener_ciclos(){
    $this->db->query("SELECT cpifp_ciclos.id_ciclo, ciclo, ciclo_corto, cpifp_departamento.id_departamento, 
    departamento, cpifp_grados.id_grado, nombre, cpifp_turnos.id_turno, turno
    FROM cpifp_ciclos, cpifp_departamento, cpifp_grados, cpifp_turnos
    WHERE cpifp_departamento.id_departamento=cpifp_ciclos.id_departamento
    AND cpifp_grados.id_grado=cpifp_ciclos.id_grado
    AND cpifp_turnos.id_turno=cpifp_ciclos.id_turno;");
    return $this->db->registros();
}



// TODOS LOS TURNOS
public function obtener_turnos(){
    $this->db->query("SELECT * FROM cpifp_turnos;");
    return $this->db->registros();
}



// TODOS LOS GRADOS
public function obtener_grados(){
    $this->db->query("SELECT * FROM cpifp_grados 
    WHERE nombre != 'Especializacion';");
    return $this->db->registros();
}


// SOLO DEPARTAMENTOS DE FORMACION CON CICLOS ASOCIADOS
public function obtener_departamentos_formacion(){
    $this->db->query("SELECT * FROM cpifp_departamento 
    WHERE isFormacion = 1 
    AND sin_ciclo = 0;");
    return $this->db->registros();
}



// INFO DE UN CICLO CONCRETO
public function un_ciclo_concreto($id_ciclo){
    $this->db->query("SELECT cpifp_ciclos.id_ciclo, ciclo, ciclo_corto, cpifp_departamento.id_departamento, 
    departamento, cpifp_grados.id_grado, nombre, cpifp_turnos.id_turno, turno
    FROM cpifp_ciclos, cpifp_departamento, cpifp_grados, cpifp_turnos
    WHERE cpifp_departamento.id_departamento=cpifp_ciclos.id_departamento
    AND cpifp_grados.id_grado=cpifp_ciclos.id_grado
    AND cpifp_turnos.id_turno=cpifp_ciclos.id_turno
    AND cpifp_ciclos.id_ciclo=:id_ciclo;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    return $this->db->registros();
}


// TODOS LOS CURSOS DE UN CICLO CONCRETO
public function cursos_ciclo_concreto($id_ciclo){
    $this->db->query("SELECT * FROM `cpifp_curso`, cpifp_ciclos, cpifp_departamento, seg_numero
    WHERE cpifp_curso.id_ciclo=cpifp_ciclos.id_ciclo 
    AND seg_numero.id_numero=cpifp_curso.id_numero
    AND cpifp_ciclos.id_departamento=cpifp_departamento.id_departamento
    AND cpifp_curso.id_ciclo=:id_ciclo 
    ORDER BY cpifp_curso.id_numero;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    return $this->db->registros();
}


// TRAE LOS CURSOS DE SEG_NUMERO
public function obtener_seg_numero(){
    $this->db->query("SELECT * FROM seg_numero;");
    return $this->db->registros();
}

// TODOS LOS DEPARTAMENTOS
public function obtener_departamentos(){
    $this->db->query("SELECT * FROM cpifp_departamento;");
    return $this->db->registros();
}



//****************************************************************************/
//************************* REFERENTE A CICLOS *******************************/
//****************************************************************************/


//------------------------ NUEVO CICLO -------------------------------

public function nuevo_ciclo($nuevo, $cursos){

    // insertamos en cpifp_ciclos
    $this->db->query("INSERT INTO cpifp_ciclos (ciclo, ciclo_corto, id_departamento, id_grado, id_turno) 
    VALUES (:ciclo, :ciclo_corto, :id_departamento, :id_grado, :id_turno)");
    $this->db->bind(':ciclo', $nuevo['ciclo']);
    $this->db->bind(':ciclo_corto', $nuevo['ciclo_corto']);
    $this->db->bind(':id_departamento', $nuevo['id_departamento']);
    $this->db->bind(':id_grado', $nuevo['id_grado']);
    $this->db->bind(':id_turno', $nuevo['id_turno']);
    if (!$this->db->execute()) {
        return false; 
    }

    $id_ciclo=$this->db->ultimoIndice();

    // despues insertamos en cpifp_curso
    foreach($cursos as $alta){
        $this->db->query("INSERT INTO cpifp_curso (curso, id_numero, id_ciclo) 
        VALUES (:curso,:id_numero,:id_ciclo)");
        $this->db->bind(':curso', $alta['curso']);
        $this->db->bind(':id_numero', $alta['id_numero']);
        $this->db->bind(':id_ciclo', $id_ciclo);
        if (!$this->db->execute()) {
            return false; 
        }
    }

    return true;           
}



//------------------------ BORRAR CICLO -------------------------------

public function borrar_ciclo($id_ciclo){

    //PRIMERO BORRAMOS EN TABLA CURSO
    for($i=0;$i<2;$i++){
        $this->db->query("DELETE FROM cpifp_curso 
        WHERE id_ciclo=:id_ciclo;");
        $this->db->bind(':id_ciclo',$id_ciclo);
        $this->db->execute();
    };

    //DESPUES BORRAMOS EN TABLA CICLOS
    $this->db->query("DELETE FROM cpifp_ciclos 
    WHERE id_ciclo=:id_ciclo;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }
}



//------------------------ EDITAR CICLO -------------------------------

public function editar_ciclo($editar,$id_ciclo){
    $this->db->query("UPDATE cpifp_ciclos 
    SET ciclo=:ciclo, ciclo_corto=:ciclo_corto, id_departamento=:id_dep, id_grado=:id_grad, id_turno=:id_turno 
    WHERE id_ciclo=:id_ciclo;"); 
    $this->db->bind(':ciclo', $editar['ciclo']);
    $this->db->bind(':ciclo_corto', $editar['ciclo_corto']);
    $this->db->bind(':id_dep', $editar['id_departamento']);
    $this->db->bind(':id_grad', $editar['id_grado']);
    $this->db->bind(':id_turno', $editar['id_turno']);
    $this->db->bind(':id_ciclo',$id_ciclo);
    if($this->db->execute()){
        return true;
    }else{
        return false;
    }
}


//****************************************************************************/
//************************* REFERENTE A CURSOS *******************************/
//****************************************************************************/


//------------------------ NUEVO CURSO CICLO -------------------------------

public function nuevo_curso($curso, $id_numero, $id_ciclo){
    $this->db->query("INSERT INTO cpifp_curso (curso, id_numero, id_ciclo) 
    VALUES (:curso, :id_numero, :id_ciclo);");
    $this->db->bind(':curso', $curso);
    $this->db->bind(':id_numero', $id_numero);
    $this->db->bind(':id_ciclo', $id_ciclo);
    if($this->db->execute()){
        return true;
    }else{
        return false;
    } 
}



//------------------------ BORRAR CURSO CICLO -------------------------------

public function borrar_curso_ciclo($id_curso){
    $this->db->query("DELETE FROM cpifp_curso WHERE id_curso=:id_curso");
    $this->db->bind(':id_curso',$id_curso);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }
}


//------------------------ EDITAR CURSO CICLO -------------------------------

public function editar_curso_ciclo($curso,$id_curso){
    $this->db->query("UPDATE cpifp_curso SET curso=:curso WHERE id_curso=:id_curso;"); 
    $this->db->bind(':curso', $curso);
    $this->db->bind(':id_curso',$id_curso);
    if($this->db->execute()){
        return true;
    }else{
        return false;
    }
}






}