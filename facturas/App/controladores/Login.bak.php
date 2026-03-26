<?php

    class Login extends Controlador{

        public function __construct(){
            $this->loginModelo = $this->modelo('LoginModelo');
        }

        public function index($error = ''){
            // redireccionar('/..');                   // Redireccionamos para saltarnos el login al integrar con la aplicacion cpifp
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $this->datos['usuario'] = trim($_POST['usuario']);
                $this->datos['pass'] = trim($_POST['pass']);
                
                $usuarioSesion = $this->loginModelo->loginUsuario($this->datos);
                
                if (isset($usuarioSesion) && !empty($usuarioSesion)){  // si tiene datos el objeto devuelto entramos
                    Sesion::crearSesion($usuarioSesion);
                    redireccionar('/');
                } else {
                    redireccionar('/login/index/error_1');
                }

                // print_r($usuarioSesion);
            } else {
     
                if (Sesion::sesionCreada()){    // si ya estamos logueados redirecciona a la raiz
                    redireccionar('/');
                }

		
                $this->datos['error'] = $error;

                $this->vista('login', $this->datos);
            }
        }

        public function logout(){
            Sesion::cerrarSesion();
            redireccionar('/');
        }

    }
