<?PHP
#CONEXION
require('pdo/class.users.php');
#llamar la clase 
$classU = new users();
session_start();

#iniciar sesión
$roles = (empty($_SESSION) != true)? $_SESSION:false;

if( $roles > 1 AND $classU->access === false ){
   //enviar al panel ADMIN
   $classU->view("admin");
 }
 
 if( !empty($_SESSION['username']) AND $classU->access){
  //enviar al inicio
  $classU->view("home");
 }else{
 //cargar vista de login
 $msg_index = "
 <div class='notif'><p>{$classU->msg_index}</p></ div>";
  require_once "template/view-login.php";
};
?>


