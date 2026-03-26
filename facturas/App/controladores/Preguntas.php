<?php

    class Preguntas extends Controlador{

        public function __construct(){
            $this->preguntasModelo = $this->modelo('PreguntasModelo');
            $this->datos["numPreguntas"] = 0;
            
        }

        public function index($error = ''){
            // redireccionar('/..');                   // Redireccionamos para saltarnos el login al integrar con la aplicacion cpifp
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
              

            } else {
               
                $this->datos["preguntas"] = $this->preguntasModelo->getPreguntas();
                $this->vista('preguntas', $this->datos);
            }
        }

    

    }
