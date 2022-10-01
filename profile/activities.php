<?PHP
#CONEXION
include('../pdo/class.users.php');
#iniciar session
session_start();
ob_start();
#incluir clases 
$classU = new\users();
$db = new\db();
$user = $classU->get_user($_SESSION["username"]);
$get_user = $classU->get_user($_GET['profile']);
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
<div class="cont-profile">
<div class="profile">
<?PHP
//limitar carácteres.
function limit_caract($str){
 $return =  '';
 $length = strlen($str);
 $return .= ($length >= 84)? 
 str_ireplace(substr($str,84,4),'...',substr($str,0,88)): 
 $str;
 return $return;
}
?>
<div class="var_1">
<?php
$return = "";
$return .= (strlen($user->avatar) >= 1) ?
"<img width='500' height='50' src='../pictures/{$user->avatar}' alt='avatar/{$user->name_user}'>":
 "<img src='../avatar.png' alt='{$user->name_user}'>";
echo $return;
?>
</div>
<div class="var_2">
<span><?=$user->name_user.'&nbsp'.$user->filset;?></span>
<abbr><?=limit_caract($user->state)?></abbr>
<abbr><a class="btn-1" href="state.php">Actualiza tu estado</a></abbr>
</div>
</div>
<div class="str">
<input class="btn btn-2" type="button" value="+ Comparte tus fotos"/>
</div>
<ul class="mini-menu">
  <li><strong>actividades</strong></li>
  <li><a class="btn-1" href="../profile">conversiones</a></li>
  <li><a class="btn-1" href="#">fotos y videos</a></li>
  <li><a class="btn-1" href="info-profile.php">información de perfil</a></li>
 </ul>
<!--//muestra toda las publicaciones//-->

<?PHP
#conexion(DB).
$db = new\db();

#crear query.
$sql = $db->connect()->prepare("SELECT * FROM public WHERE id_user = :param");
#preparar la consulta.
$sql->bindParam(':param',$user->id,PDO::PARAM_STR);
#ejectutar la consulta.
$sql->execute();
#incluir paginación.

	$row = $sql->rowcount();
	//número de páginas
	$page_rows = 5;
  //nombre de dominio.
  $domain = "";

	$last = ceil($row/$page_rows);
 
	if($last < 1){
		$last = 1;
	}

	$pagenum = 1;

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
	
	$query = $db->connect()->query("SELECT * FROM public WHERE id_user = '{$user->id}' ORDER BY Id DESC {$limit}");
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
	$paginationCtrls .= "<abbr>  {$pagenum}&nbsp;/&nbsp;{$n}</abb>";

    if ($pagenum != $last) {
        $next = $pagenum + 1;
        $paginationCtrls .= '<a href="'.$domain.'?'.$http.'&pn='.$next.'" class="btn-1 pg-next">Siguiente &rarr;</a> ';
    }

#recorrer todos los datos (foreach)
 foreach($query->FETCHALL(PDO::FETCH_OBJ) AS $rows){
//--Validar si existe un me gusta en el post--//
#validar si ya esxiste el like.
$like = $db->connect()->query("SELECT id_user FROM likes WHERE id_token = '{$rows->id_token}' AND id_user  = '{$user->id}'")->rowcount('queryString');

$get_like = ($like >= 1)? "Ya no me gusta" : "Me gusta";
?>
<!--//imprimir todos los datos del siglo foreach.//-->
<div class="cont-profile active borderlan">
<div class="profile">

<div class="var_1">
<!--//muestra una IMAGEN//-->
<?PHP
//comprobar a si existe un avatar.
$return = "";
$return .= strlen($user->avatar) >= 1 ?
"<img class='img' src='../pictures/{$user->avatar}' alt='avatar/{$user->name_user}'>":
 "<img class='img' src='../avatar.png' alt='avatar/{$user->name_user}'>";

echo $return;
?>
</div>

<div class="var_2">
<!--//muestra nombre//-->
<div class="float-r">
<?PHP
//--eliminar publicación--//

#captura el post que se eliminará.
if(isset($_GET['post'])){
#validar si el usuario que clockeo existe.
$validUser = $db->connect()->query("SELECT id FROM usernames WHERE id = '{$user->id}'");

 if($validUser->rowcount('queryString') >= 1){
 #validar si el post es de el usuario que clockeo.
 $delete = $db->connect()->query("DELETE FROM public WHERE id_token = '{$_GET['post']}' AND id_user = '{$user->id}'");

  switch($delete){
   CASE true: 
    header("location:{$_SERVER['PHP_SELF']}"); 
   BREAK;
   CASE false:
    echo "ocurrió un error";
   BREAK;
   DEFAULT: 
    header("location'/");
   BREAK;
  }
 }
}

?>
<a class="btn-1" href="?delete_post=true&post=<?=$rows->id_token;?>">&times;</a>
</div>
<span><a class="btn-1" href="#defaul"><?= $user->name_user.'&nbsp'.$user->filset;?></a></span>
<abbr>
<?PHP
//comprobar si hay texto o imagen.
 $img_text = "";
 if(strlen($rows->img) >= 1 && strlen($rows->texts) >= 1){
 //hay texto y imagen
 echo "<a href='../activities/?plugger={$rows->id_token}'><img class='img-cont' src='../pictures/{$rows->img}'/></a>";
 print "<br>";
 echo $rows->texts;
 }else IF(strlen($rows->texts) >= 1){
 //hay texto
 echo hastah($rows->texts);
 }else{
 //hay imágen.
 echo "<a href='../activities/?plugger={$rows->id_token}'><img class='img-cont' src='../pictures/{$rows->img}' alt=''/></a>";
 }
?>
</abbr>
<abbr>

<a class="btn-1" href="?like=<?=$rows->id_token;?>"><?=$get_like;?></a>&nbsp;|&nbsp;<a class="btn-1" href="../activities/?plugger=<?=$rows->id_token;?>">comentar&nbsp;(<?=$classU->get_coment($rows->id_token)->rowcount();?>)</a>
</abbr>
<!--//fecha//-->
<div class="float-r">
<abbr>
<?=timestamp($rows->timestamp);?>
</abbr>
<img src="../icon.png" alt="icon-time">
</div>
</div>

</div>
</div>

<?PHP } //se cierra las llaves del siclo 
//--Validar si existe un me gusta en el post--//
#validar si ya esxiste el like.
$like = $db->connect()->query("SELECT id_user FROM likes WHERE id_token = '{$_GET['like']}' AND id_user  = '{$user->id}'")->rowcount('queryString');

#capturar el like
if(isset($_GET['like'])){
 if(!empty($_GET["like"])){
 if($like >= 1){
  $update_like = $db->connect()->query("DELETE FROM likes WHERE id_token = '{$_GET['like']}' AND id_user = {$user->id}");
  switch($update_like){
   CASE true:
   header("location:{$_SERVER['PHP_SELF']}");
   BREAK;
   DEFAULT:
  }
 }else{
 $insert_like = $db->connect()->query("INSERT INTO likes(id_token,user_token,id_user) VALUES('{$_GET['like']}','{$user->token}','{$user->id}')");
  switch($insert_like){
   CASE true:
     header("location:{$_SERVER['PHP_SELF']}");
   BREAK;
   DEFAULT:
  }
 }
}
}

?>
<!--//paginación.//-->
<div class="pg">
<?=$paginationCtrls;?>
</div>
</body>
</html>