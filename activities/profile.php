<?PHP
#CONEXION
include('../pdo/class.users.php');
#iniciar session
session_start();
#incluir clases 
$classU = new\users();
$db = new\db();
$user = $classU->get_user($_SESSION["username"]);
$get_user = $classU->get_user($_GET['profile']);
#SESSION
$classU->session($_SESSION['username'],$_COOKIE['username'],$_SESSION['rol']);

//repuestas(get) para agregar una nueva solicitud.
$new_resquest = http_build_query([
 "profile" => $_GET['profile'],
 "add_friend" => $get_user->id,
 "accs" => true
]);

//cancelar solicitud 
$cancel_resquest  = http_build_query([
 "profile" => $_GET['profile'],
 "resquest" => $get_user->id,
 "cancel_resquest" => true
]);

//aceptar solicitud de forma inmediata
$accept_resquest = http_build_query([
 "profile" => $_GET['profile'],
 "accept" => $get_user->id,
 "accept_resquest" => true
]);

 //validar si ya se envió la solicitud y verificar si ya está en la lista de amigos.
 $valid_solicitud = $db->connect()->query("SELECT id_user FROM friends WHERE id_user = '{$user->id}' AND id_users= '{$_GET['profile']}' AND value = '0' || id_user = '{$user->id}' AND id_users = '{$_GET['profile']}' AND value = '1'");

//si se preciono el botón de enviar solicitud y verificar si ya existe la solicitud.
switch(isset($_GET['add_friend']) AND isset($_GET['accs'])){
 CASE true:  
 if($valid_solicitud->rowcount() > 0){
 #ya existe la solicitud.
 }else{
 #solicitud enviada.
 $add_friend = $db->connect()->prepare("INSERT INTO friends(id_user,id_users,value) values(:id,:id_users,:value)")->execute([":id" => $user->id, ":id_users" => $_GET['add_friend'], ":value" => 0]);

 }
BREAK;
}

 //verificar si el usuario quiere cancelar la solicitud enviada.
if(isset($_GET['resquest']) AND $_GET['cancel_resquest'] == true){
//si el usuario preciono el botton de cancelar solicitud.
// if($valid_solicitud->fetchall());
 $cancel = $db->connect()->query("DELETE FROM friends WHERE id_user = '{$user->id}' AND id_users = '{$get_user->id}' AND value = '0'");
}
#aceptar solicitud de forma inmediata.
//verificar si se aceptó la solicitud.
 if(isset($_GET['accept']) AND $_GET['accept_resquest'] == true){
   $accep = $db->connect()->prepare("UPDATE friends SET value = :value WHERE id_user = :id")->execute([':value' => true, ':id' => cleaner($get_user->id)]);
  $resqued_accepted = $db->connect()->prepare("INSERT INTO public(id_token,id_user,new_friend, timestamp) VALUES(:token,:id_user,:new_friend,:timestamp)")->execute([":token" => token(5,6),":id_user" => $user->id,":new_friend" => "<a class='btn-1' href='../activities/profile.php? profile={$get_user->id}'>{$get_user->name_user}&nbsp;{$get_user->filset}</a>", ":timestamp" => time()]);
 //header("location:{$_SERVER['PHP_SELF']}");

 }
#comprobar si el usuario es amigo.
$sql = $db->connect()->query("SELECT * FROM friends WHERE id_users = '{$user->id}' AND id_user = '{$get_user->id}'||id_user = '{$user->id}' AND id_users = '{$_GET["profile"]}' AND value = '1' || id_user = '{$_GET['profile']}' AND id_users = '{$user->id}' AND value = '1'")->fetch(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content=":Plugger te conecta con tus amigos">
<link rel="icon" href="../icon.png" type="image/x-icon" sizes="50x50">
<title><?=$user->name_user;?></title>
<!--//
EL CODIGO FUE ECHO APARTIR DE 20 DE OBTUBRE DEL 2019 ALAS 4:04pm.
//-->
<link href="../css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<header>
<div class="img-logo">
<img src="../loggo/loggo.png" alt="icon/plugger"/> 
</div>
<ul class="submenu">
 <li><a href="/">INICIO</a></li>

  <?PHP
 $message_new = $classU->messages($user->id)->rowcount();
 if($message_new > 0){
  $status_message = ($message_new >= 9)? "&nbsp;(9+)":
  "&nbsp;({$message_new})"
   ;
  }else{
  $status_message = null;
  }
 ?>
 <li class="var_1"><a href="">PERFIL <?=$status_message;?></a></li>
<?PHP
//status solicitud.
$status = $classU->solicitudes($user->id)->rowcount();
 if($status > 0){
  $status_add = ($status >= 100)? "&nbsp;(99+)":
  "&nbsp;({$status})"
   ;
  }else{
  $status_add = null;
  }
?>
 <li><a href="../friends">AMIGOS<?=$status_add;?></a></li>
</ul>
</header>
<?PHP
switch($get_user){
CASE false:
header("location:/");
BREAK;
CASE $user->id == $get_user->id:
//enviar usuario al perfil.
 header("location:../profile/info-profile.php");
BREAK;
 DEFAULT:
}
?>
<!--//Notifi//-->
<div class="notif">
<p>
<?=$classU->notif();?>
</p>
</div>
<div class="cont-profile">
<div class="profile">
<div class="var_1">
<?php
$return = "";
$return .= (strlen($get_user->avatar) >= 1) ?
"<img class='img' src='../pictures/{$get_user->avatar}' alt='avatar/{$get_user->name_user}'>":
 "<img class='img' src='../avatar.png' alt='{$get_user->name_user}'>";
echo $return;
?>
</div>
<div class="var_2">
<a class="btn-1" href="#"><?=$get_user->name_user.'&nbsp;'.$get_user->filset;?></a>
<abbr>null __contruct()</abbr>
<img class="icon"src="../icon.png"/>
</div>
</div>
<?PHP
//validar si esta en la lista de amigos.
 switch($sql){
 CASE false:
//verificar si se envió la solicitud
 if($add_friend == true || $valid_solicitud->rowcount() > 0 AND $cancel == false ){
echo "<div class='notif'><p>solicitud de amistad enviada</p>
<p><a class='btn-1' href='?{$cancel_resquest}'>cancelar solicitud</a></p>
</div>";
 }else{
?>
<a class=" btn-3 btn-m" href="?<?=$new_resquest;?>">ser amigos</a>
</div>
<?PHP
}
BREAK;
CASE true:
 //aceptar solicitud.
$accep = $db->connect()->query("SELECT * FROM friends WHERE id_users ='{$user->id}' AND id_user = '{$get_user->id}' AND value = '0'")->rowcount();
 if($accep > 0){
 echo "<a class='btn-3 btn-m' href='?{$accept_resquest}'>aceptar solicitud</a>";
}else{
echo "<a class='btn-3 btn-m' href='../profile/menssage.php?token={$get_user->id}'>mensaje</a>";
?>
<a href="" class="btn-3 btn-m">Eliminar</a>
<ul class="mini-menu">

<?php
}
BREAK;
}
?>
  <ul class="mini-menu">
  <li><strong>información de perfil</strong></li>
  <li><a class="btn-1" href="#">amigos</a></li>
 </ul>
<!--//lista de información de usuario//-->

<ul class="profile_info">
<li><strong>Nombre(s)</strong></li>
<li><?=$get_user->name_user;?></li>
<li><strong>Apellido(s)</strong></li>
<li><?=$get_user->filset;?></li>
<li><strong>Soy</strong></li>
<li><?=$get_user->sexo;?></li>
<li><strong>Acerca de mi</strong></li>
<li><?=$get_user->my_info;?></li>
<li><strong>Fecha de nacimiento</strong></li>
<li><?=$get_user->date_fech;?></li>
<li><strong>Correo electrónico</strong></li>
<li><?=$get_user->mail;?></li>
</ul>




</body>
</html>