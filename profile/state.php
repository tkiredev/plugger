<?PHP
#CONEXION
require('../pdo/class.users.php');
#iniciar session
session_start();
#incluir clases 
$classU = new\users();
$con = new\db();
$user = $classU->get_user($_SESSION['username']);
#SESSION
$classU->session($_SESSION['username'],$_COOKIE['username'],$_SESSION['rol']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content=":Plugger te conecta con tus amigos">
<link rel="icon" href="icon.png" type="image/x-icon" sizes="50x50">
<title>update state</title>
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
 <li class="var_1"><a href="../profile">PERFIL <?=$status_message;?></a></li>
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
 <li><a href="../friends/index">AMIGOS<?=$status_add;?></a></li>
</ul>
</header>
<body>
<?PHP

if(isset($_POST["submit"])){

#inlciur conexión.
 $array = array(
 "id_token" => token(rand(5,15)),
 "id_friend" => $user->id,
 "id_user" => $user->id,
 "texts" => cleaner($_POST['state']),
 "timestamp" => time()
 );
 
 $con = $con->connect();
 $sql1 = $con->query("UPDATE usernames SET state = '{$array['texts']}' WHERE id = '{$user->id}'");

 $sql = $con->query("INSERT INTO public(id_token,id_user,texts,timestamp,new_friend,img)
 VALUES('{$array['id_token']}','{$array['id_user']}','{$array['texts']}','{$array['timestamp']}','','')
 ");
 echo $sql? header('location:/'):"ELSE";
}

?>
<!--//update estate//-->
<div class="coment">
<strong>Actualizar estado</strong>
<form action="" method="post">
<div class="var_1">
<label for="statepic">¿Que estás pensando?</label>
<textarea id="statepic" rows="4" cols="10" name="state"></textarea>
</div>
<input class="btn btn-2 size-m" type="submit" name="submit" value="actualizar">
<a class="btn-1" href="/">cancelar</a>
</form>
</div>
</body>
</html>