<?PHP
#CONEXION
require('../pdo/class.users.php');
#iniciar session
session_start();
#incluir clases 
$classU = new users();
$db = new\db();
$user = $classU->get_user($_SESSION['username']);
#SESSION
$classU->session($_SESSION['username'],$_COOKIE['username'],$_SESSION['rol']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type " content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content=":Plugger te conecta con tus amigos">
<link rel="icon" href="../icon.png" type="image/x-icon" sizes="50x50">
<title>Friends</title>
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
 <li><a href="../profile">PERFIL <?=$status_message;?></a></li>
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
 <li class="var_1"><a href="../friends">AMIGOS<?=$status_add;?></a></li>
</ul>
</header>
<!--//Notifi//-->
<div class="notif">
<p>
<?=$classU->notif();?>
</p>
</div>
 <ul class="mini-menu">
  <li><strong>MIS AMIGOS</strong></li>
  <li><a class="btn-1" href="?add_friend">SOLICITUDES DE AMISTAD <?= ($classU->solicitudes($user->id)->rowcount() >= 1)?"({$classU->solicitudes($user->id)->rowcount()})":null ;?></a></li>
 </ul>
<?PHP

//verificar si se aceptó la solicitud.
 if(isset($_GET['accep'])){
  switch($_GET['accep']){
   CASE true:
   //mapear la clave ID.
   $map = $classU->get_user($_GET['id']);
   $accep = $db->connect()->prepare("UPDATE friends SET value = :value WHERE id_user = :id")->execute([':value' => true, ':id' => cleaner($_GET['id'])]);

  //$resqued_accepted = $db->connect()->prepare("INSERT INTO public(id_token,id_user,new_friend, timestamp) VALUES(:token,:id_user,:new_friend,:timestamp)")->execute([":token" => token(5,6),":id_user" => $user->id,":new_friend" => "<a class='btn-1' href='../activities/profile.php? profile={$map->id}'>{$map->name_user}&nbsp;{$map->filset}</a>", ":timestamp" => time()]);
 header("location:{$_SERVER['PHP_SELF']}");
   BREAK;
   CASE false:
  echo "error";
   BREAK;
  }
 }
switch(isset($_GET["add_friend"])){
CASE true:
foreach($classU->solicitudes($user->id)->fetchall(PDO::FETCH_OBJ) AS $value){
//datos de tu amigo.
$friend = $classU->get_user($value->id_user);
?>
<div class="cont-profile">
<div class="profile">
 <div class="var_1">
 <?php
 $return = "";
$return .= (strlen($get_user->avatar) >= 1) ?
"<img src='../pictures/{$get_user->avatar}' alt='avatar/{$get_user->name_user}'>":
 "<img src='../avatar.png' alt='{$get_user->name_user}'>";
echo $return;
?>
 </div>
 <div class="var_2">
 <abbr><a class="btn-1" href="../activities/profile.php?profile=<?=$friend->id;?>"><?=$friend->name_user."&nbsp;".$friend->filset;?></a></abbr>
 <img class="icon" alt="logo" src="../icon.png"/>
 </div>
</div>
<a class="btn btn-2 size-m" href="?accep=true&id=<?=$friend->id;?>">aceptar</a>
</div>
<?PHP
}
BREAK;
CASE false:

//mostrar la lista de amigos.
$get_friend = $db->connect()->query("SELECT * FROM friends WHERE id_users = '{$user->id}' AND value > '0' || id_user = {$user->id} AND value > '0'");
?>
<div style="padding:10px 5px; background:#E6E6E6;">
 <a class="btn-1" href="search.php">Busca amigos</a>
</div>
<div class="str borderlan">
 <?php if($get_friend->rowcount('queryString') > 0){echo ($get_friend->rowcount('queryString') >= 2 )? "{$get_friend->rowcount('queryString')}&nbsp;amigos":"{$get_friend->rowcount('queryString')}&nbsp;amigo";}else echo "aún no tienes amigos"?> 
</div>
<?PHP
foreach($get_friend->fetchall(PDO::FETCH_OBJ) AS $value){
//datos de tu amigo.
$valid = ($value->id_user == $user->id)? $value->id_users: $value->id_user;
$friend = $classU->get_user($valid);
?>
<div class="cont-profile">
<div class="profile">
 <div class="var_1">
 <?php
 $return = "";
$return .= (strlen($friend->avatar) >= 1) ?
"<img src='../pictures/{$friend->avatar}' alt='avatar/{$friend->name_user}'>":
 "<img src='../avatar.png' alt='{$friend->name_user}'>";
echo $return;
?>
 </div>
 <div class="var_2">
 <abbr><a class="btn-1" href="../activities/profile.php?profile=<?=$friend->id;?>"><?=$friend->name_user."&nbsp;".$friend->filset;?></a></abbr>
 <img class="icon" alt="logo" src="../icon.png"/>
 </div>
</div>
 <a class=" btn-3 size-m" href="../profile/menssage.php?token=<?=$friend->id;?>">Mensaje</a>
</div>
<?PHP
}
BREAK;
}//endFor
?>
</body>
</html>