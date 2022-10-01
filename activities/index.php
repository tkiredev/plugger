<?PHP
#CONEXION
require('../pdo/class.users.php');
#iniciar session y conexión
session_start();
//session_destroy();
//var_dump($_COOKIE["username"]);
$db = new\db();
#incluir clases 
$classU = new users();
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
<title>comentarios</title>
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
 <li class="var_1"><a href="/">INICIO</a></li>
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
 <li><a href="../friends">AMIGOS<?=$status_add;?></a></li>
</ul>
</header>
<!--//Notifi//-->
<div class="notif">
<p>
<?=$classU->notif();?>
</p>
</div>
<?PHP
//comprobar si existe la petición.

//scape string

$scape = preg_replace('#[^0-9]#', '', $_GET['pn']);

$rows= $classU->coment($_GET["plugger"]);

if($rows == false){
echo "<div class='notif'><p>OPS! :'( lo sentimos {$user->name_user}, no se encontró ningún resultado, tal vez la publicación ya no está disponible.</p></div>";
exit();
}
 //verificar si el usuario que quiere hacer alguna acción existe en la lista de amigos.
/* switch ($classU->valid_friend($user->id,$rows->id_user)){
 CASE false:
 //no hay acceso para comentar.
 header("location: profile.php? profile={$rows->id_user}");
 BREAK;

}*/
?>
<!--//Notifi//-->
<div class="str borderlan">
<a class="btn-1" href="/" arial-labelledby="j">←Atras</a>
</div>
<div>
<div class="cont-profile borderlan">
<div class="profile">
<div class="var_1">
<?PHP
//comprobar a si existe un avatar

#MOSTRAR USUARIO.
$get_user = $classU->get_user($rows->id_user);

echo valid_avatar($get_user);
?>
</div>
<div class="var_2">
<span><a class="btn-1" href="profile.php?profile=<?=$rows->id_user;?>"><?=$get_user->name_user."&nbsp;".$get_user->filset;?></a></span>
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
<?php
//comprobar los comentarios.
$getc = $classU->get_coment($rows->id_token);
//http bouil
$http = http_build_query(array(
	"plugger" => $rows->id_token,
  "pn" => $scape
 // "back" => "activities/?show"
));

 //--INSERT INTO COMENT--//
 $resquet = json_encode(array(
 "id_token" => $rows->id_token,
 "id_user" =>$user->id,
 "texts" => cleaner($_POST['texts']),
 "timestamp" => time()
 ));

if(isset($_POST['submit'])){
 if(!empty($_POST["texts"])){
$classU->insert_coment($resquet);
 }
}
?>
<abbr>
<?php

//--Validar si existe un me gusta en el post--//
#validar si ya esxiste el like.
$like = $db->connect()->query("SELECT id_user FROM likes WHERE id_token = '{$_GET['plugger']}' AND id_user  = '{$user->id}'")->rowcount('queryString');

#capturar el like
if(isset($_GET['like'])){
 if(!empty($_GET["like"])){
if($classU->valid_friend($user->id,$rows->id_user) == true){
 if($like >= 1){
  $update_like = $db->connect()->query("DELETE FROM likes WHERE id_token = '{$_GET['like']}' AND id_user = {$user->id}");
  switch($update_like){
   CASE true:
   header("location:{$_SERVER['PHP_SELF']}?{$http}");
   BREAK;
   DEFAULT:
  }
 }else{
 $insert_like = $db->connect()->query("INSERT INTO likes(id_token,user_token,id_user) VALUES('{$_GET['like']}','{$user->token}','{$user->id}')");
  switch($insert_like){
   CASE true:
     header("location:{$_SERVER['PHP_SELF']}?{$http}");
   BREAK;
   DEFAULT:
  }
 }
 } # => ENDVALIDUSER.
}
}else{
$get_like = ($like >=1)? "ya no me gusta": "me gusta";
}
#GET LIKES
$get_likes = $classU->get_likes($rows->id_token);
 
//SISTEMA DE RELACIÓN DE KIKES.
$for =  "";
$maxrow  = 2;
$token_row = $rows->id_token;

$sql = $db->connect()->query("SELECT * FROM likes WHERE id_token = '{$token_row}' ORDER BY id DESC LIMIT {$maxrow}");

 foreach($sql->fetchAll(PDO:: FETCH_OBJ) as $row){

$get_user = $classU->get_user($row->id_user);

$row_file = $sql->rowcount();

$for .= "<a class='btn-1' href='profile.php?profile={$get_user->id}'>$get_user->name_user&nbsp;{$get_user->filset}</a>,";
 }
$row_c = $get_likes->rowcount();
$echo = "";
 $num = $row_c - $row_file;

#si el número de likes es mallor a 2, muestra a todos los que le gusta.
 if($row_file >= $maxrow){
 
  //comprobar si hay likes 
  if(!$row_c <= 0){
  #el resultado es(true).

  //comprobar si a una sola persona le gusta o a ambas.
  $echo .=($row_c == $maxrow)?
str_ireplace(',les',' les',"{$for}les gusta."):
str_ireplace(', y',' y',"{$for} y {$num} otros les gusta.");
  }
}else if($row_file == 1 AND $row_c == 1){
$echo .= str_ireplace(',le',' le',"{$for}le gusta.");
}
?>
</abbr>
<abbr>
<a class="btn-1" href="?plugger=<?=$_GET['plugger'];?>&like=<?=$_GET['plugger'];?>"><?=$get_like;?></a>
</abbr>
<abbr>
<?=$echo;?>
</abbr>

<div class="float-r">
<abbr><?=timestamp($rows->timestamp);?></abbr>
<img src="../icon.png" alt="icon">
 </div>
</div>
</div>
</div>

</div>
<!--//comentarios//-->
<?php
 $query= $db->connect()->query("SELECT  * FROM coments WHERE id_token = '{$rows->id_token}'");
 
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
	
	$nquery = $db->connect()->query("SELECT * FROM coments WHERE id_token = '{$rows->id_token}' ORDER BY Id DESC {$limit}");
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
?>
<div class="str borderlan">
<?PHP
$row_count = "";
if($row <= 1){
$row_count .= "{$row}  comentario";

}else{
$row_count .= "{$row} comentarios";
}
echo $row_count;
?>
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

 <form action="?<?=$http;?>" method="post">
 <div class="var_1">
<label for="comentpic">deja un comentario</label>
 <textarea id="comentpic" rows="4" name="texts" cols="10"></textarea>
 </div>
 <input type="submit" name="submit" class="btn btn-2 size-m" value="enviar">
 </form>
</div>

<?PHP
foreach($nquery->FETCHALL(PDO::FETCH_OBJ) as $row){
//var_dump($row);
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
<?PHP
if($row->id_user == $user->id){
echo "<div class='float-r'>
<a class='btn-1' arial-label='close' href='#'>&times;</a>
</div>";
}
?>


<div class="mini-profile">
 <div class="var_1">
 <?=
  valid_avatar($get_user);
  ?>
 </div>
 <div class="var_2">
 <a class="btn-1" href="profile.php?profile=<?=$get_user->id;?>"><?=$get_user->name_user."&nbsp;".$get_user->filset;?></a> -
 <span>
 <?PHP
  if(!empty($row->texts)){
  echo $row->texts;
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
<div class="pg">
<?=$paginationCtrls;?>
</div>
</body>
</html>