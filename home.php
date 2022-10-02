<?PHP 
#CONEXION
require('pdo/class.users.php');
#iniciar session
session_start();
#incluir clases 
$classU = new users();

//$user = $classU->get_user($_SESSION['username']);

#SESSION

  if(isset($_SESSION) && empty($_SESSION) != true){
 $user = $classU->get_user($_SESSION['username']);
$classU->sessionFilter($_SESSION['username'],$_COOKIE,$_SESSION['rol']);

//incluir plantilla de inicio
  require_once "template/view-home.php";
} else $classU->view("index.php");
?>
