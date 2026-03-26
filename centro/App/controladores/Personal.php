<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Personal extends Controlador{

    private $personalModelo;



    public function __construct(){

        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);

        // Definimos los roles que tendran acceso a todas las funciones del controlador
        $this->datos['rolesPermitidos'] = [50]; 
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }

        $this->personalModelo=$this->modelo('PersonalM');
    }


    
    public function index(){ 
        $this->datos['profesores']=$this->personalModelo->obtener_profesores();
        $this->datos['prof_dep'] = $this->personalModelo->obtener_prof_dep();
        $this->vista('personal', $this->datos);
    }



/************************ NUEVO MIEMBRO ****************************/

public function nuevo_profesor(){

    if($_SERVER['REQUEST_METHOD'] =='POST'){

        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $partes = explode('@', $email);
        $login = $partes[0];


        // validacion que no exista ese email
        $existe = $this->personalModelo->buscar_por_email($email);
        if ($existe) {
            echo "<script>
                    alert('El email ya está registrado para otro usuario.');
                    window.location.href = '" . RUTA_URL . "/personal';
                  </script>";
            exit;
        }

        $nuevo = [
            'nombre' => $nombre,
            'email' => $email,
            'activo' => trim($_POST['activo']),
            'admin' => trim($_POST['admin']),
            'login' => $login,
            'password' => 'acceso123'
        ];


        if($this->personalModelo->nuevo_profesor($nuevo)){

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
                $mail->addAddress($email, $nombre);

                $mail->isHTML(true);

                $mail->Subject = 'Acceso a la Plataforma';
                $mail->Body = "
                    <p>Hola <b>$nombre</b>,</p>
                    <p>Te damos la bienvenida a la nueva plataforma del centro. A continuación encontrarás tus credenciales de acceso:</p>
                    <ul>
                        <li><b>Usuario:</b> Puedes ingresar con <b>$email</b> o con tu nombre de usuario <b>$login</b></li>
                        <li><b>Contraseña inicial:</b> acceso123</li>
                    </ul>
                    <p>Te recomendamos cambiar tu contraseña al ingresar por primera vez.</p>
                    <p>Si tienes alguna duda, no dudes en contactarnos.</p>
                    <br>
                    <p>Saludos cordiales,<br><b>Equipo de Administración</b></p>
                ";
                $mail->AltBody = "Hola $nombre,\n\nTus credenciales de acceso:\nUsuario: $email\n
                Contraseña inicial: acceso123\n\nPor favor, cambia tu contraseña al iniciar sesión.\n\nSaludos,\nEquipo de Administración";
                
                $mail->send();

            } catch (Exception $e) {
                echo "No se pudo enviar el mensaje. Error de PHPMailer: {$mail->ErrorInfo}";
            }
            //  redireccionar('/personal');
            echo "<script>
                alert('Miembro dado de alta correctamente.');
                window.location.href = '" . RUTA_URL . "/personal';
            </script>";
        }else{
            die('Algo ha fallado!!');
        }
    }else{
        $this->vista('personal',$this->datos);
    }
}



/************************ CAMBIAR CONTRASEÑA (ADMIN) ****************************/

public function cambiar_password($id_profesor) {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $nueva_pass   = trim($_POST['nueva_password']);
        $confirmar    = trim($_POST['confirmar_password']);

        if (empty($nueva_pass) || strlen($nueva_pass) < 6) {
            echo "<script>
                    alert('La contraseña debe tener al menos 6 caracteres.');
                    window.location.href = '" . RUTA_URL . "/personal/personal_gestion/" . $id_profesor . "';
                  </script>";
            exit;
        }

        if ($nueva_pass !== $confirmar) {
            echo "<script>
                    alert('Las contraseñas no coinciden.');
                    window.location.href = '" . RUTA_URL . "/personal/personal_gestion/" . $id_profesor . "';
                  </script>";
            exit;
        }

        if ($this->personalModelo->cambiar_password($id_profesor, $nueva_pass)) {
            echo "<script>
                    alert('Contraseña actualizada correctamente.');
                    window.location.href = '" . RUTA_URL . "/personal/personal_gestion/" . $id_profesor . "';
                  </script>";
        } else {
            die('Algo ha fallado al cambiar la contraseña.');
        }

    } else {
        redireccionar('/personal/personal_gestion/' . $id_profesor);
    }
}



/************************ BORRAR MIEMBRO ****************************/

public function borrar_profesor($id_profesor){

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($this->personalModelo->borrar_profesor($id_profesor)) {
            redireccionar('/personal');
        }else{
            die('Algo ha fallado!!!');
        }
    }else{
        $this->vista('personal', $this->datos);
    }
}




    
/**********************************************************************************************/
/********************************** REFERENTE A UN SOLO MIEMBRO *******************************/
/**********************************************************************************************/


    public function personal_gestion($id_profesor){
        $this->datos['departamentos'] = $this->personalModelo->todos_departamentos();
        $this->datos['roles']=$this->personalModelo->obtener_roles();
        $this->datos['info_profe'] = $this->personalModelo->info_profe($id_profesor);
        $this->datos['todos_roles']=$this->personalModelo->todos_roles_profesor($id_profesor);
        $this->vista('personal_gestion', $this->datos);
    }



/************************ EDITAR MIEMBRO ****************************/


public function editar_profesor($id_profesor){

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $editar = [
            'nombre' => trim($_POST['nombre']),
            'email' => trim($_POST['email']),
            'activo' => trim($_POST['activo']),
            'admin' => trim($_POST['admin'])
            ];

        if($this->personalModelo->editar_profesor($editar, $id_profesor)){
            // redireccionar('/personal/personal_gestion/'.$id);
            echo "<script>
                alert('Datos guardados correctamente.');
                window.location.href = '" . RUTA_URL . "/personal/personal_gestion/".$id_profesor."'; 
            </script>";
        }else{
            die('Algo ha fallado!!');
        }

    }else{
        $this->vista('personal/personal_gestion',$this->datos);
    }
}




/************************ ASIGNAR DEPARTAMENTO ****************************/

public function asignar_departamento($id_profesor){

    if($_SERVER['REQUEST_METHOD'] =='POST'){

        $departamento = $_POST['departamento'];
        $rol = $_POST['rol'];

        if($this->personalModelo->asignar_departamento($id_profesor, $departamento, $rol)){
            // redireccionar('/personal/personal_gestion/'.$id_profe);
            echo "<script>
                alert('Asignacion al departamento correcta.');
                window.location.href = '" . RUTA_URL . "/personal/personal_gestion/".$id_profesor."'; 
            </script>";
        }else{
            die('Algo ha fallado!!');
        }
        
        
    }else{
        $this->vista('personal/personal_gestion',$this->datos);
    }
}



/************************ BORRAR DEPARTAMENTO ****************************/

public function borrar_departamento($id_profesor){

    $id_departamento = $_POST['id_departamento'];
    $id_rol = $_POST['id_rol'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($this->personalModelo->eliminar_de_departamento($id_profesor, $id_departamento, $id_rol)) {
            redireccionar('/personal/personal_gestion/'.$id_profesor);
        }else{
            die('Algo ha fallado!!!');
        }
    }else{
        $this->vista('personal/personal_gestion',$this->datos);;
    }
}



}