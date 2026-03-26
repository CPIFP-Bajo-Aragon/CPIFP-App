<?php

class UsuarioM {
    
    private $db;

    public function __construct(){
        $this->db = new Base;
    }



/************************ CAMBIAR PASSWORD ****************************/

// public function cambiar_password($id_profesor, $nuevo_pass){
//     $this->db->query("UPDATE cpifp_profesor 
//     SET password=:nuevo_pass 
//     WHERE id_profesor=:id_prof;"); 
//     $this->db->bind(':nuevo_pass',$nuevo_pass);
//     $this->db->bind(':id_prof',$id_profesor);
//     if ($this->db->execute()){
//         return true;
//     }else{
//         return false;
//     }        
// }
        

/************************ CAMBIAR PASSWORD ****************************/

public function cambiar_password($id_profesor, $nuevo_pass){

    $password_cifrada = password_hash($nuevo_pass, PASSWORD_DEFAULT);

    $this->db->query("UPDATE cpifp_profesor SET password=:nuevo_pass WHERE id_profesor=:id_prof;"); 
    $this->db->bind(':nuevo_pass',$password_cifrada);
    $this->db->bind(':id_prof',$id_profesor);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }        
}
        



}
