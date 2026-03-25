<?php


class Usuario extends Controlador{

   private $usuarioModelo;



    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->usuarioModelo = $this->modelo('UsuarioM');
    }



    
    public function index(){
        $this->vista('usuario', $this->datos);
    }





    public function cambiar_password($id_profesor) {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nuevo_pass = $_POST['pass_new'];

            if ($this->usuarioModelo->cambiar_password($id_profesor, $nuevo_pass)) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
    
                echo "<script>
                        alert('Contraseña cambiada con éxito. Se va a cerrar sesión.');
                        window.location.href = '" . RUTA_CPIFP . "'; 
                      </script>";
    
                session_unset(); // Elimina todas las variables de sesión
                session_destroy(); // Destruye la sesión
    
            } else {
                die('Algo ha fallado!!');
            }
        } else {
            $this->vista('usuario', $this->datos);
        }
    }
    


}



