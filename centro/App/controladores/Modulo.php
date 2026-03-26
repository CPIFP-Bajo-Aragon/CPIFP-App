<?php

class Modulo extends Controlador{

    private $moduloModelo;


    public function __construct(){

        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);

        // Definimos los roles que tendran acceso a todas las funciones del controlador
        $this->datos['rolesPermitidos'] = [50]; 
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }


        $this->moduloModelo=$this->modelo('ModuloM');
    }



    public function index($id){

        //SEPARAMOS TODO LO QUE NOS LLEGA POR LA URL
        $info = explode('-',$id);
        $id_ciclo = $info[0]; 
        $id_curso = $info[1];

        $this->datos['curso_ciclo'] = $this->moduloModelo->curso_ciclo_concreto($id_ciclo, $id_curso);
        $this->datos['modulos_un_curso'] = $this->moduloModelo->modulos_un_curso($id_curso);

        $id_departamento = $this->datos['curso_ciclo'][0]->id_departamento;
        $this->datos['departamentos'] = $this->moduloModelo->departamentos($id_departamento);

        $this->vista('ciclo_modulos', $this->datos);
    }






/************************ NUEVO MODULO ****************************/

public function nuevo_modulo($id_ciclo){

    if($_SERVER['REQUEST_METHOD'] =='POST'){

        $id_curso=$_POST['id_curso'];

        $nuevo = [
            'modulo' => trim($_POST['modulo']),
            'nombre_corto' => trim($_POST['modulo_codigo']),
            'horas_totales' => trim($_POST['horas_totales']),
            'id_curso' => $_POST['id_curso'],
            'horas_semanales' => trim($_POST['horas_semanales']),
            'codigo_programacion' => trim($_POST['codigo_programacion']),
            'id_departamento' => $_POST['id_departamento']
        ];

        if($this->moduloModelo->nuevo_modulo($nuevo)){
            // redireccionar('/modulo'.'/'.$id_ciclo.'-'.$id_curso);
            echo "<script>
                alert('Modulo dado de alta correctamente.');
                window.location.href = '" . RUTA_URL . "/modulo/".$id_ciclo."-".$id_curso."'; 
            </script>";
        }else{
            die('Algo ha fallado!!');
        }

    }else{
        $this->vista('ciclo_modulos/'.$id_ciclo,$this->datos);
    }
}



/************************ BORRAR MODULO ****************************/

public function borrar_modulo($id_modulo){

    $id_ciclo = $_POST['id_ciclo'];
    $id_curso = $_POST['id_curso'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($this->moduloModelo->borrar_modulo($id_modulo)) {
            redireccionar('/modulo'.'/'.$id_ciclo.'-'.$id_curso);
        }else{
            die('Algo ha fallado!!!');
        }
    }else{
        $this->vista('ciclo_modulos/'.$id_ciclo,$this->datos);
    }
}




/************************ EDITAR MODULO ****************************/

public function editar_modulo($id_modulo){

    $id_ciclo = $_POST['id_ciclo'];
    $id_curso = $_POST['id_curso'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $editar = [
            'modulo' => trim($_POST['modulo']),
            'nombre_corto' => trim($_POST['modulo_codigo']),
            'horas_totales' => trim($_POST['horas_totales']),
            'horas_semanales' => trim($_POST['horas_semanales']),
            'codigo_programacion' => trim($_POST['codigo_programacion']),
            'id_departamento' => trim($_POST['id_departamento'])
        ];

        if($this->moduloModelo->editar_modulo($editar, $id_modulo)){
            // redireccionar('/modulo'.'/'.$id_ciclo.'-'.$id_curso);
            echo "<script>
                alert('Datos guardados correctamente.');
                window.location.href = '" . RUTA_URL . "/modulo/".$id_ciclo."-".$id_curso."'; 
            </script>";
        }else{
            die('Algo ha fallado!!');
        }

    }else{
        $this->vista('ciclo_modulos/'.$id_ciclo,$this->datos);
    }
}




}