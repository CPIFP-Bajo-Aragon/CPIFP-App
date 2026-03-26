<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Login extends Controlador{


    public $loginModelo;


    public function __construct(){
        $this->loginModelo = $this->modelo('LoginModelo');
    }




    public function index($error = ''){


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->datos['email'] = trim($_POST['email']);
            $this->datos['passw'] = trim($_POST['passw']);

            $usuarioSesion = $this->loginModelo->loginEmail($this->datos['email'], $this->datos['passw']);  // obtenemos profesor

            if (isset($usuarioSesion) && !empty($usuarioSesion)) { 
                
                // comprobacion si usuario esta activo
                if ($usuarioSesion->activo == 0) {
                    if (ob_get_length()) ob_clean(); 
                    echo "<script>
                            alert('Tu cuenta está inactiva. Contacta con el administrador.');
                            window.location.href = '" . RUTA_URL . "/login';
                        </script>";
                    exit;
                }

                // Si está activo, continuar login normal
                $usuarioSesion->roles = $this->loginModelo->getRolesProfesor($usuarioSesion->id_profesor);  // obtenemos sus roles  
                Sesion::crearSesion($usuarioSesion);
                redireccionar('/');
            } else {
                redireccionar('/login/index/error_1');
            }
        } else {
            if (Sesion::sesionCreada($this->datos)) {    
                redireccionar('/inicio');
            } else {
                $this->datos['error'] = $error;
                $this->vista('login', $this->datos);
            }
        }
    }

    


public function logout(){
    Sesion::iniciarSesion($this->datos);        // controlamos si no esta iniciada la sesion y cogemos los datos de la sesion
    // $this->loginModelo->registroFinSesion($this->datos['usuarioSesion']->id_usuario);       // registramos fecha cierre de sesion
    Sesion::cerrarSesion();
    redireccionar('/');
}




public function recuperar() {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $email = trim($_POST['email_login']);

        // Validar que exista ese email en BD
        $usuario = $this->loginModelo->buscar_por_email($email);
        $id_usuario = $usuario->id_profesor;

        if (!$usuario) {
            // Email no registrado
            echo "<script>alert('El email no está registrado.'); window.history.back();</script>";
            exit;
        }


        $nueva_password = 'recuperar_' . bin2hex(random_bytes(3));
        // Actualiza la contraseña en la base de datos
        $this->loginModelo->recuperar_password($id_usuario, $nueva_password );

        // Enviar email con PHPMailer
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        try {


            $mail->isSMTP();
            $mail->Host = 'smtp.ionos.es';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply-calidapp@cpifpbajoaragon.com';  
            $mail->Password = '5Vti9D0U78Bio7pXfy4P';            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = 587;

            $mail->setFrom('noreply-calidapp@cpifpbajoaragon.com', 'CalidApp');
            $mail->addAddress($email, $usuario->nombre_completo);
            $mail->isHTML(true);


            $mail->Subject = 'Recuperación de contraseña';
            $mail->Body = "Hola {$usuario->nombre_completo},<br><br>Tu nueva contraseña es: <b>{$nueva_password}</b><br><br>Por favor, cambia esta contraseña cuando accedas.";
            $mail->send();

            echo "<script>alert('Se ha enviado la nueva contraseña a tu email.'); window.location.href='" . RUTA_URL . "/login';</script>";
            exit;

        } catch (Exception $e) {
            echo "<script>alert('No se pudo enviar el email. Error: {$mail->ErrorInfo}'); window.history.back();</script>";
            exit;
        }
    } else {
        redireccionar('/login');
    }
}



}
