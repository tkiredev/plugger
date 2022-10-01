<?PHP
#CONEXION
require('pdo/class.users.php');
#llamar la clase 
$classU = new\users();
session_start();
//msg index 
$msg_index = "
<div class='notif'><p>{$classU->msg_index}</p></div>";

//session_destroy();
#iniciar sesión.
if($_SESSION['rol'] > 0 AND $classU->access == false OR !empty($_SESSION['username']) AND $classU->access == true){

//test
header('location: home');
switch($_SESSION['rol'] > 0 AND $classU->access == true){
 #ACCESO A ADMINISTRACIÓN.
 CASE true:
  header('location: admin');
 BREAK;

 CASE false:
 header('location: home');
 BREAK;
}

}else{

?>
<!DOCTYPE html>
<html lang="html">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content=":Plugger te conecta con tus viejos amigos.">
<link rel="icon" href="icon.png" type="image/x-icon" sizes="50x50">
<title>regístrate | :plugger</title>
<!--//
EL CODIGO FUE ECHO APARTIR DE 20 DE OBTUBRE DEL 2019 ALAS 4:04pm.
//-->
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body style="background:#ECECEC">

<?PHP
if(isset($_POST['login'])){
#RESEBIR LAS VARIABLES PARA LIMPIARLAS.
DEFINE("NUMERS", cleaner($_POST['numer']));

DEFINE("PASSWORD", cleaner($_POST['password']));
#PETICION Y RESPUESTA DE CONSULTA.
$user = $classU->get_users(NUMERS, PASSWORD);

$row = $user->fetch(PDO::FETCH_OBJ);
$return = '';
#VALIDAR SI EXISTE EL USUARIO O EL ADMINISTRADOR o hay acceso a toda la matriz.
if($user->rowcount() > 0 AND $row->id >0 AND $classU->access == false){
$return .= "<div class='notif'><p>{$classU->msg_index}</p></div>";
echo $classU->access;
$_SESSION['username'] = $row->id;
$_SESSION['rol'] = $row->rol;
switch($_SESSION['rol'] > 0){
 #ACCESO A ADMINISTRACIÓN.
 CASE true:
  header('location: admin');
 BREAK;
 }
}else if($user->rowcount() > 0 AND $row->id > 1 AND $classU->access == true){

   //LOGIN CORRECTO!
   #COMPROBAR SI EL USUARIO QUIERE GUARDAR SESSION(COOKIE).
 if(isset($_POST['rememberme'])){
//si acepto cookie.
#debido a los errores de inserción se cerró la COOKIES
setcookie("username",$row->id,time()+60*60*24*30);
$_SESSION['username'] = $_COOKIE['username'];
}

$_SESSION['username'] = $row->id;
$_SESSION['rol'] = $row->rol;
#RIRECIONAR AL USUARIO ALA PÁGINA DE INICIO.
 header('location: home');
}else{
//NO HAY USUARIO O ES INCORRECTO.
$msg_login = "<div class='notif'><p>Número de teléfono o/y contraseña incorrectos</p></div>";

  ($classU->access == false)? $return = $msg_index: $return = $msg_login;
}
}


//agregar alvertencia
 if($classU->access == false AND !isset($_POST['login']))
 {
   $return = $msg_index;
 }

?>
<header>
<div class="img-logo">
<img src="loggo/loggo.png" alt="icon/plugger"/> 
</div>
<?=$return;?>
<div class="login">
<form action="" method="post">
<h2>Iniciar sesión</h2>
<label>número celular</label>
<div class="var_1">
<input type="phone" value="<?=$_POST['numer']?>" name="numer" placeholder="10 dígitos" maxlength="10">
</div>
<div class="var_1">
<input type="password" name="password" placeholder="contraseña" value="<?=$_POST['password'];?>">
</div>
<div class="var_2">
<input type="submit" value="entrar" name="login" id="btn_disabled" class="btn btn-2 size-m bord">
<?php
 echo "<script>
 var btn = document.getElementById('btn_disaled');
 var access = '{$classU->access}';
 (access == false)? btn.disabled = true: btn.disabled = false;
 </script>";
?>
<input type="checkbox" name="rememberme" id="remember" value="true">
<label for="remember">recordar mis datos</label>
</div>
<a class="btn-1" href="recover.php?require=true';?>">¿Olvidaste tu contraseña?</a>
</form>
</div>
</header>
<div class="title">
<span>Únete a :plugger ahora!</span>
</div>
<div class="sinup">
<?PHP
DEFINE("PHONE", $_POST['phone']);
DEFINE("PASSW", cleaner($_POST['passpic']));
$response = "";
if(isset($_POST["sinup"])){

 if(empty(PHONE) AND empty(PASSW)){
  $response .= "<div class='notif'><p>completa los siguientes campos</p></div>";
  }else if(!preg_match('/^([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})$/',PHONE)){

 if(strlen(PHONE) < 10){
$response = "<div class='notif'><p>número de teléfono muy cortó</p></div>";
}else{
  $response .= "<div class='notif'><p>el teléfono es invalido</p></div>";
}
 }else if(strlen(PASSW) < 2){
  $response = "<div class='notif'><p>contraseña insegura. debe ser mallor a 20</p></div>";
  }else{
  #INCLUDE CONEXIÓN.
  $db = new\db();
  #COMPROVAR SI EXISTE LA CUENTA ANTES DE CREAR UNA NUEVA.
  $sql = $db->connect()->query("SELECT numer FROM usernames WHERE numer = {$_POST['phone']}");
 
  if($sql->rowcount('queryString') < 1){
  //CERRAR CURSOR.
  $sql->closeCursor();
  $sql == null;
  //NO EXISTE EL USUARIO(nuevo registro).
 $sql = $db->connect()->prepare("INSERT INTO usernames(token,name_user,filset,numer,password,state,avatar,sexo,my_info,date_fech,mail,rol) VALUES(:token,'usuario',':plugger',:numer,:passw,'','','','','','','0')");
  $sql->bindParam(':token', token(rand(5,6)),PDO::PARAM_STR);
  $sql->bindParam(':numer', cleaner(PHONE),PDO::PARAM_STR);
  $sql->bindParam(':passw', cleaner(PASSW),PDO::PARAM_STR);
  $sql->execute();

 switch($sql){
   CASE true:
$user = $classU->get_users(PHONE, PASSW);
$row = $user->fetch(PDO::FETCH_OBJ);
$return = '';
#VALIDAR SI EXISTE EL USUARIO O EL ADMINISTRADOR.
if($user->rowcount() > 0 AND $row->id > 1 OR $row->id != ""){
   //LOGIN CORRECTO!
   $_SESSION['username'] = $row->id;
   $_SESSION['rol'] = $row->rol;
   $response = "<div class='notif'><p>cuenta creada correctamente</p></div>";
}
   BREAK;
  }

  //CERRAR CURSOR
  $sql->closeCursor();
  $sql = null;
	}else{
	$response = "<div class='notif'><p>el número ya existe en otra cuenta</p></div>";
	}
 }
}
?>

<?=$response;?>
<form action="#bottom" method="post">
<div class="var_1">
<input type="phone" placeholder="número celular" maxlength="10" name="phone" value="<?=$_POST["phone"];?>">
</div>
<div class="var_1">
<input type="password" name="passpic" placeholder="contraseña" value="<?=$_POST["passpic"];?>">
</div>
<div class="var_2">
<input type="submit" value="Activar" class="btn btn-2 size-m bord" name="sinup">
</div>
<label>Al hacer clic en activar, aceptas Plugger y sus <a class="btn-1" href="#">condiciones de uso</a></label>
</form>
</div>

</body>
</html><? }?>