<?php

class Departamento extends Controlador{

    private $departamentoModelo;


    public function __construct(){
        
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);

        // Definimos los roles que tendran acceso a todas las funciones del controlador
        $this->datos['rolesPermitidos'] = [50]; 
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }


        $this->departamentoModelo=$this->modelo('DepartamentoM');
    }




    public function index(){
        $this->datos['ges_departamentos'] = $this->departamentoModelo->obtener_departamentos(); // todos los departamentos
        $this->vista('departamentos', $this->datos);
    }


    
    public function departamento_miembros($id_departamento){
        $this->datos['info_departamento'] = $this->departamentoModelo->info_departamento($id_departamento); // info de un departamento concreto
        $this->datos['profesores'] = $this->departamentoModelo->profesores_x_departamento($id_departamento); // info todos los profes un departamento concreto
        $this->datos['profesores_agrupados'] = agrupar_profesores($this->datos['profesores']); // funcion agrupar profesores (helpers)
        $this->vista('departamento_miembros', $this->datos);
    }


    public function departamento_ciclos($id_departamento){
        $this->datos['departamento'] = $this->departamentoModelo->info_departamento($id_departamento);  // info de un departamento concreto
        $this->datos['ciclos_dep'] = $this->departamentoModelo->ciclos_x_departamento($id_departamento); // info todos los ciclos un departamento concreto
        $this->vista('departamento_ciclos', $this->datos);
    }




/************************ NUEVO DEPARTAMENTO ****************************/
        
    public function nuevo_departamento(){

        if($_SERVER['REQUEST_METHOD'] =='POST'){
            $nuevo = [
                'departamento' => trim($_POST['nombre']),
                'departamento_corto' => trim($_POST['nombre_corto']),
                'isFormacion' => trim($_POST['isFormacion']),
                'sin_ciclo' => trim($_POST['sin_ciclo'])
                ];


                if($this->departamentoModelo->nuevo_departamento($nuevo)){
                    // redireccionar('/departamento');
                    echo "<script>
                        alert('Departamento dado de alta correctamente.');
                        window.location.href = '" . RUTA_URL . "/departamento';
                    </script>";
                exit;
                    }else{
                        die('Algo ha fallado!!');
                    }

        }else{
            $this->vista('departamentos',$this->datos);
        }
    }



/************************ BORRAR DEPARTAMENTO ****************************/

    public function borrar_departamento($id_departamento){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->departamentoModelo->borrar_departamento($id_departamento)) {
                redireccionar('/departamento');
            }else{
                die('Algo ha fallado!!!');
            }
        }else{
            $this->vista('/departamentos', $this->datos);
        }
    }


/************************ EDITAR DEPARTAMENTO ****************************/

    public function editar_departamento($id_departamento){
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $editar = [
                'departamento' => trim($_POST['nombre']),
                'departamento_corto' => trim($_POST['nombre_corto']),
                'isFormacion' => trim($_POST['isFormacion']),
                'sin_ciclo' => trim($_POST['sin_ciclo'])
            ];

            if($this->departamentoModelo->editar_departamento($editar,$id_departamento)){
                // redireccionar('/departamento');
                echo "<script>
                    alert('Datos guardados correctamente.');
                    window.location.href = '" . RUTA_URL . "/departamento';
                </script>";
            }else{
                die('Algo ha fallado!!');
            }

        }else{
            $this->vista('departamentos',$this->datos);
        }
    }




}