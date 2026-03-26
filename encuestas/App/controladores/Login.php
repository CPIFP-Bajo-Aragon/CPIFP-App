<?php

class Login extends Controlador {

    public function index(){
        if(Sesion::sesionCreada()){
            redireccionar('/');
        }
        $this->vista('login');
    }

    public function logout(){
        Sesion::cerrarSesion();
        redireccionar('/');
    }
}
