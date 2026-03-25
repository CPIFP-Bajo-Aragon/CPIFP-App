<?php

class Ciclo extends Controlador{

    private $cicloModelo;


    public function __construct(){

        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);

        // Definimos los roles que tendran acceso a todas las funciones del controlador
        $this->datos['rolesPermitidos'] = [50]; 
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }

        $this->cicloModelo=$this->modelo('CicloM');
    }




    public function index(){
        $this->datos['ciclos'] = $this->cicloModelo->obtener_ciclos();
        $this->datos['turnos'] = $this->cicloModelo->obtener_turnos();
        $this->datos['grados'] = $this->cicloModelo->obtener_grados();
        $this->datos['departamentos'] = $this->cicloModelo->obtener_departamentos_formacion(); // excepto FOL Y LEO
        $this->vista('ciclos', $this->datos);
    }




    public function ciclo_gestion($id_ciclo){
        $this->datos['un_ciclo'] = $this->cicloModelo->un_ciclo_concreto($id_ciclo);
        $this->datos['cursos_ciclo'] = $this->cicloModelo->cursos_ciclo_concreto($id_ciclo);
        $this->datos['seg_numero'] = $this->cicloModelo->obtener_seg_numero();
        $this->datos['departamentos'] = $this->cicloModelo->obtener_departamentos();
        $this->datos['turnos'] = $this->cicloModelo->obtener_turnos();
        $this->datos['grados'] = $this->cicloModelo->obtener_grados();
        $this->vista('ciclo_gestion', $this->datos);
    }





//------------------------ NUEVO CICLO -------------------------------

public function nuevo_ciclo(){


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $nuevo_ciclo = [
                'ciclo' => trim($_POST['ciclo']),
                'ciclo_corto' => trim($_POST['ciclo_corto']),
                'id_departamento' => trim($_POST['id_departamento']),
                'id_grado' => trim($_POST['id_grado']),
                'id_turno' => trim($_POST['id_turno'])
            ];

            // CURSOS DEL CICLO
            $cursos = [];
            $numCursos = intval($_POST['num_cursos']);
            for ($i = 1; $i <= $numCursos; $i++) {
                if (isset($_POST['codigos_cursos_' . $i])) {
                    $curso = [
                        'curso' => trim($_POST['codigos_cursos_' . $i]),
                        'id_numero' => $i
                    ];
                    $cursos[] = $curso;
                }
            }

            if ($this->cicloModelo->nuevo_ciclo($nuevo_ciclo, $cursos)) {
                // redireccionar('/ciclo');
                echo "<script>
                    alert('Ciclo dado de alta correctamente.');
                    window.location.href = '" . RUTA_URL . "/ciclo';
                </script>";
            } else {
                die('Algo ha fallado!!');
            }

        } else {
            $this->vista('ciclos',$this->datos);
        }

}



//------------------------ BORRAR CICLO -------------------------------

public function borrar_ciclo($id_ciclo){

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($this->cicloModelo->borrar_ciclo($id_ciclo)) {
            if(isset($_POST['id_departamento'])){
                redireccionar('/ciclo/departamento_ciclos/'.$_POST['id_departamento']);
            }else{
                redireccionar('/ciclo');
            }
        }else{
            die('Algo ha fallado!!!');
        }
    }else{
        $this->vista('ciclos',$this->datos);
    };
}



//------------------------EDITAR CICLO -------------------------------

public function editar_ciclo($id_ciclo){

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $editar = [
            'ciclo' => trim($_POST['ciclo']),
            'ciclo_corto' => trim($_POST['ciclo_corto']),
            'id_departamento' => trim($_POST['id_departamento']),
            'id_grado' => trim($_POST['id_grado']),
            'id_turno'=> trim($_POST['id_turno'])
            ];

        if($this->cicloModelo->editar_ciclo($editar,$id_ciclo)){
            // redireccionar('/ciclo/ciclo_gestion/'.$id_ciclo);
            echo "<script>
                alert('Datos guardados correctamente.');
                window.location.href = '" . RUTA_URL . "/ciclo/ciclo_gestion/" . $id_ciclo . "'; 
            </script>";
        }else{
            die('Algo ha fallado!!');
        }

    }else{
        $this->vista('ciclos',$this->datos);
    }
}




//------------------------ NUEVO CURSO CICLO -------------------------------

public function nuevo_curso($id_ciclo){

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $curso = trim($_POST['codigo_curso']);
            $id_numero = trim($_POST['id_numero']);
   
        if ($this->cicloModelo->nuevo_curso($curso, $id_numero, $id_ciclo)) {
            // redireccionar('/ciclo/ciclo_gestion/'.$id_ciclo);
            echo "<script>
                alert('Curso dado de alta correctamente.');
                window.location.href = '" . RUTA_URL . "/ciclo/ciclo_gestion/" . $id_ciclo . "'; 
            </script>";
        } else {
            die('Algo ha fallado!!');
        }

    } else {
        $this->vista('ciclos',$this->datos);
    }
}
        

//------------------------ BORRAR CURSO CICLO -------------------------------


public function borrar_curso_ciclo($id_curso){

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_ciclo = $_POST['id_ciclo'];

        if ($this->cicloModelo->borrar_curso_ciclo($id_curso)) {
            redireccionar('/ciclo/ciclo_gestion/'.$id_ciclo);
        }else{
            die('Algo ha fallado!!!');
        }
    }else{
        $this->vista('ciclos',$this->datos);
    };
}



//------------------------ EDITAR CURSO CICLO -------------------------------

public function editar_curso_ciclo($id_curso){

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $id_ciclo = $_POST['id_ciclo'];
        $curso = trim($_POST['curso']);
        
        if($this->cicloModelo->editar_curso_ciclo($curso,$id_curso)){
            // redireccionar('/ciclo/ciclo_gestion/'.$id_ciclo);
            echo "<script>
                alert('Datos guardados correctamente.');
                window.location.href = '" . RUTA_URL . "/ciclo/ciclo_gestion/" . $id_ciclo . "'; 
            </script>";
        }else{
            die('Algo ha fallado!!');
        }
        
    }else{
        $this->vista('ciclos',$this->datos);
    }
}



}