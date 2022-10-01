<?PHP
#CONEXION
require('../pdo/class.users.php');
#iniciar session
session_start();
//session_destroy();
#incluir clases 
$classU = new\users();
$user = $classU->get_user($_SESSION['username']);
$db = new\db();
#SESSION
$classU->session($_SESSION['username'],$_COOKIE['username'],$_SESSION['rol']);
#capturar repuesta del get.
$http = http_build_query([
 "token" => $_GET['token']
]);
#verificar si el usuario existe .
 $get_user = $classU->get_user($_GET['token']);
switch($get_user){
CASE false:
 header("location:/");
 BREAK;
}

//verificar si está en la lista de amigos. y si existe un chat con la persona.
$friend_valid = $db->connect()->query("SELECT id FROM friends WHERE id_user = '{$user->id}' AND id_users = '{$_GET['token']}' AND value > '0' OR id_users = '{$user->id}' AND id_user = '{$_GET['token']}' AND value > '0'");

switch ($friend_valid->rowcount('QueryString') > 0){
 CASE false:
 header("location:../activities/profile.php?profile={$_GET['token']}");
 BREAK;
 }


$chat_valid = $db->connect()->query("SELECT id,status FROM grou_message WHERE id_user = '{$user->id}' AND token = '{$_GET['token']}' OR id_user = '{$_GET['token']}' AND token = '{$user->id}' ");


 
//marcar mensaje como leído.
if($classU->messages($user->id)->rowcount() > 0){

 $update_chats = $db->connect()->prepare("
 UPDATE grou_message SET
 status = :status
 WHERE token_id = '{$user->id}' AND id_user = '{$_GET['token']}' AND status = 0 || token_id = '{$user->id}' AND id_user = '{$user->id}' AND status = 0 ")->execute([
 ":status" => true
 ]);

 $update_messages = $db->connect()->query("UPDATE messages SET
 view = true
 WHERE token_user = '{$user->id}' AND id_user = '{$_GET['token']}' AND view = 0");

}

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
<script type="text/javascript">
   var uid = '281738';
   var wid = '578613';
   var pop_fback = 'up';
   var pop_tag = document.createElement('script');pop_tag.src='//cdn.popcash.net/show.js';document.body.appendChild(pop_tag);
   pop_tag.onerror = function() {pop_tag = document.createElement('script');pop_tag.src='//cdn2.popcash.net/show.js';document.body.appendChild(pop_tag)};
</script>
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
 <li><a href="../friends">AMIGOS<?=$status_add;?></a></li>
</ul>
</header>
<!--//Notifi//-->
<div class="notif">
<p>
<?=$classU->notif();?>
</p>
</div>
<div class="str borderlan">
<a class="btn-1" href="../profile/" arial-labelledby="none">← Lista de conversaciónes</a>
</div>
<!------FORM MESSAGE---//-->
<?php
$status_chat =
$chat_valid->fetch(PDO::FETCH_OBJ)->status;
 if(isset($_POST['submit'])){
 
switch ($friend_valid->rowcount('QueryString') > 0){
 CASE false:
   header("location:../activities/profile.php?profile={$_GET['token']}");
 BREAK;
 CASE true:

$submit_message = $db->connect()->prepare("
INSERT INTO messages(
 token_user,
 id_user,
 name_user,
 asunto,
 view,
 timestamp
)
VALUES(
 :token,
 :id_user,
 :name_user,
 :asunto,
 :view,
 :timestamp
)")->execute([
 ":token" => $_GET['token'],
 ":id_user" => $user->id,
 ":name_user" => $user->name_user,
 ":asunto" => $_POST['message'],
 ":view" => 0,
 ":timestamp" => time()
 ]);

 if($chat_valid->rowcount() > 0){
 //actualizar mensaje. 
 //verificar si está en la lista de amigos antes de enviar mensaje.
 $update_message = $db->connect()->prepare("
 UPDATE grou_message SET
 token = :token,
 token_id = :token_id,
 id_user = :id_user,
 message = :message,
 timestamp = :time,
 status = :status
 WHERE id_user = '{$user->id}' AND token = '{$_GET['token']}' OR id_user = '{$_GET['token']}' AND token = '{$user->id}'
 ")->execute([
 ":token"=> $_GET['token'],
 ":token_id" => $_GET["token"],
 ":time" => time(),
 ":message" => $_POST['message'],
 ":status" => 0,
 ":id_user"=> $user->id
 ]);
 
 }else{
 
 $new_chat = $db->connect()->prepare("
 INSERT INTO grou_message(
 token,
 token_id,
 message,
 timestamp,
 id_user
 )VALUES(
 :token,
 :token_id,
 :message,
 :timestamp,
 :id_user
 )")->execute([
 ":token" => $_GET["token"],
 ":token_id" => $_GET['token'],
 ":message" => $_POST['message'],
 ":timestamp" => time(),
 ":id_user" => $user->id
 ]);
 
 } 
BREAK;
 } 
}
?>

<div class="cont-profile">
<div class="profile">
 <div class="var_1">
<?php
$return = "";
$return .= (strlen($get_user->avatar) >= 1) ?
"<img class='img-message' src='../pictures/{$get_user->avatar}' alt='avatar/{$get_user->name_user}'>":
 "<img class='img-message' src='../avatar.png' alt='{$get_user->name_user}'>";
echo $return;
?>
 </div>
 <div class="var_2">
<a class="btn-1" href="../activities/profile.php?profile=<?=$get_user->id;?>"><?=$get_user->name_user.'&nbsp;'.$get_user->filset;?></a>
<abbr>null __contruct()</abbr>
<abbr><img class="icon"src="../icon.png"/></abbr>
 </div>
</div>
<a class="btn-3 btn-m" href="#not_foun">Mensaje</a>
<span>conversación entre tú y <?=$get_user->name_user;?></span>
</div>
<div class="coment bg-c">
<table border="0">
 <td>
 <?PHP
 if(!empty($user->avatar)){
 echo "<img class='img-m' src='../pictures/{$user->avatar}' alt='{$user->avatar}'/>";
 }else{
 echo "<img class='img-m' src='../avatar.png' alt='{$user->avatar}'/>";
 }
 ?>
 </td>
 <td>
  <a class="btn-1" href="../profile"><?=$user->name_user."&nbsp;".$user->filset;?></a>
 </td>
 </table>
<form action="?token=<?=$_GET['token'];?>" method="post">
 <div class="var_1">
<label for="comentpic">contestar</label>
 <textarea id="comentpic" rows="4" name="message" cols="10"></textarea>
 </div>
 <input type="submit" name="submit" class="btn btn-2 size-m" value="enviar">
<!--
<input type='file' name='img' id='upload_img'/>

 <?PHP
 if(isset($_POST['upload_image'])){
 echo $_POST["img_id"];
 }
?>
<!--<input type="hidden" name="img_id" value="<input type='file' id='open'>" />
<input type="submit" value="Agregar una foto" name="upload_image" style="background: #fff2e5; color: #ed7724; float:right; margin-right:15px;" />
-->
</form>
</div>
<!---VIEW MESAGGES---//-->
<div class="chat">
<?php
//incluir paginación.
$query = $db->connect()->query("SELECT * FROM messages WHERE id_user = '{$user->id}'   AND token_user = '{$_GET['token']}' UNION ALL SELECT * FROM messages WHERE token_user = '{$user->id}' AND id_user = '{$_GET['token']}' ORDER BY id DESC");

	$row = $query->rowcount();
	//número de páginas
	$page_rows = 5;
  //nombre de dominio.
  $domain = "";

	$last = ceil($row/$page_rows);
 
	if($last < 1){
		$last = 1;
	}

	$pagenum = null;

	if(isset($_GET['pn'])){
		$pagenum = preg_replace('#[^0-9]#', '', $_GET['pn']);
	}

	if ($pagenum < 1) { 
		$pagenum = 1; 
	} 

	else if ($pagenum > $last) { 
		$pagenum = $last; 
	}

	$limit = 'LIMIT ' .($pagenum - 1) * $page_rows .',' .$page_rows;
	
	$paginationCtrls = '';

	if($last != 1){
		
	if ($pagenum > 1) {
        $previous = $pagenum - 1;
		$paginationCtrls .= '<a href="'.$domain.'?'.$http.'&pn='.$previous.'" class="btn-1 pg-back">← Anterior</a>';
		
	    }
    }

	for($e = $pagenum; $e <= $last; $e++){
		$n = $e;
	}
if($last > 1){
	$paginationCtrls .= "<abbr>  {$pagenum}&nbsp;/&nbsp;{$n}</abb>";
}else{
$paginationCtrls = "";
}

    if ($pagenum != $last) {
        $next = $pagenum + 1;
        $paginationCtrls .= '<a href="'.$domain.'?'.$http.'&pn='.$next.'" class="btn-1 pg-next">Siguiente &rarr;</a> ';
    }
$my_chats = $db->connect()->query("SELECT * FROM messages WHERE id_user = '{$user->id}'   AND token_user = '{$_GET['token']}' UNION ALL SELECT * FROM messages WHERE token_user = '{$user->id}' AND id_user = '{$_GET['token']}' ORDER BY id DESC {$limit}");
 foreach($my_chats->fetchAll(PDO::FETCH_OBJ) AS $row){
//mostramos los usuarios en tiempo real.
$get_user = $classU->get_user($row->id_user);
#validar si el comentario es mio
if($row->id_user == $user->id){
$active = "active";
}else{
$active = "";
}
?>
<div class="coments <?=$active;?>">
<div class="mini-profile">
 <div class="var_1">
 <?=
  valid_avatar($get_user);
  ?>
 </div>
 <div class="var_2">
 <a class="btn-1" href="../activities/profile.php?profile=<?=$get_user->id;?>"><?=$get_user->name_user."&nbsp;".$get_user->filset;?></a> -
 <span>
 <?PHP
  if(!empty($row->asunto)){
  echo $row->asunto;
  }
 ?>
 </span>
 </div>
</div>
<div class="v">
<abbr>
<?=timestamp($row->timestamp);?>
</abbr>
</div>
</div>
<?PHP
}
?>
</div>
<!--<div class=""><a href="">&close; eliminar conversación</a></div>-->
<div class="pg">
<?=$paginationCtrls;?>
</div>
</body>
</html>