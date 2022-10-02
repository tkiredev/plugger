<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content=":Plugger te conecta con tus amigos">
<link rel="icon" href="icon.png" type="image/x-icon" sizes="50x50">
<title>:plugger</title>
<!--//
EL CODIGO FUE ECHO APARTIR DE 20 DE OBTUBRE DEL 2019 ALAS 4:04pm.
//-->
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<!--//anuncios//-->
<div class="anuncio">
</div>
<header>
<div class="img-logo">
<img src="loggo/loggo.png" alt="icon/plugger"> 
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
 <li><a href="../profile/index">PERFIL <?=$status_message;?></a></li>
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
<!--//Notifi//-->
<div class="notif">
<p><?=$classU->notif();?></p>
</div>
<div class="cont-profile">
<div class="profile">
<?PHP
//limitar carácteres.132
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
"<img class='img-m' src='../pictures/{$user->avatar}' alt='avatar/{$user->name_user}'>":
 "<img class='img-m' src='avatar.png' alt='{$user->name_user}'>";
echo $return;
?>
</div>
<div class="var_2">
<span class="font-max"><?=$user->name_user.'&nbsp;'.$user->filset;?></span>
<abbr><?=limit_caract($user->state);?></abbr>
<abbr><a class="btn-1" href="profile/state">Actualiza tu estado</a></abbr>
</div>
</div>
</div>
<div class="str"><a href="profile/uploadimg.php">
<input class="btn btn-2" type="button" value="+ Comparte tus fotos"/></a>
</div>

<!--//todas la publicación//-->
<?PHP 

 //incluir paginación.
 require_once ('pagination.php');

 $row = $db->connect()->query("SELECT * FROM public ORDER BY timestamp DESC {$limit}");
 foreach($row->FETCHALL(PDO::FETCH_OBJ) as $rows){

//--Validar si existe un me gusta en el post--//
#validar si ya esxiste el like.
$like = $db->connect()->query("SELECT id_user FROM likes WHERE id_token = '{$rows->id_token}' AND id_user  = '{$user->id}'")->rowcount();


$get_like = ($like >0)? "Ya no me gusta" : "Me gusta";

$get_user = $classU->get_user($rows->id_user);

#VALIDAR EL DUEÑO DE LA PUBLICASION.
 $active = "";
 $active .= ($rows->id_user == $user->id)?"active": "";
 
?>

<div class="cont-profile  <?=$active;?> borderlan">
<div class="profile">
<div class="var_1">
<?PHP

//comprobar a si existe un avatar.
$return = "";
$return .= strlen($get_user->avatar) >= 1 ?
"<img class='img' src='../pictures/{$get_user->avatar}' alt='avatar/{$get_user->name_user}'>":
 "<img class='img' src='avatar.png' alt='avatar/{$get_user->name_user}'>";

echo $return;
?>
</div>
<div class="var_2">
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

if($rows->id_user == $user->id){
echo "<div class='float-r'>
<a class='btn-1' arial-label='close' href='{$_SERVER['PHP_SELF']}?delete_post=true&post={$rows->id_token}'>&times;</a>
</div>";
}
?>
<span><a class="btn-1" tabindex="0" href="activities/profile?profile=<?=$get_user->id;?>"><?=$get_user->name_user.'&nbsp'.$get_user->filset;?></a></span>
<abbr>
<?PHP

//comprobar si hay texto o imagen.
 $img_text = "";
 if(strlen($rows->new_friend ) >=1){
  echo "<br>ahora es amigo de {$rows->new_friend}";
 }
//comprobar si hay texto o imagen.
 $img_text = "";
 if(strlen($rows->img) >= 1 && strlen($rows->texts) >= 1){
 //hay texto y imagen
 echo "<a href='../activities/index?plugger={$rows->id_token}'><img class='img-cont' src='../pictures/{$rows->img}'/></a>";
 print "<br>";
 echo $rows->texts;
 }else IF(strlen($rows->texts) >= 1){
 //hay texto
  print hastah($rows->texts);
 }else{
 //hay imágen.
 echo "<a href='../activities/index?plugger={$rows->id_token}'><img class='img-cont' src='../pictures/{$rows->img}' alt=''/></a>";
 }
?>
</abbr>
<?php
//comprobar los comentarios.
$getc = $classU->get_coment($rows->id_token);

$http = http_build_query(array(
	"plugger" => $rows->id_token
));

#GET LIKES
$get_likes = $classU->get_likes($rows->id_token);
 
//SISTEMA DE RELACIÓN DE KIKES.
$for =  "";
$maxrow  = 2;
$row_file = 0;
$token_row = $rows->id_token;

$sql = $db->connect()->query("SELECT * FROM likes WHERE id_token = '{$token_row}' ORDER BY id ASC LIMIT {$maxrow}");

 foreach($sql->fetchAll(PDO:: FETCH_OBJ) as $row){

$get_user = $classU->get_user($row->id_user);
$row_file = $sql->rowcount();

$for .= "<a class='btn-1' href='activities/profile/index?profile={$get_user->id}'>$get_user->name_user&nbsp;{$get_user->filset}</a>,";
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
<abbr>
<a class="btn-1" href="?like=<?=$rows->id_token;?>&id=<?=$rows->id_user;?>"><?=$get_like;?></a>&nbsp;<?PHP if($row_c > 0){
switch(strlen($rows->new_friend) >=1 ){
CASE false:
 echo "|<br><a class='btn-1' href='activities/index?{$http}'>Comentar&nbsp;({$getc->rowcount()})</a>";
BREAK;
}
}
else{
switch(strlen($rows->new_friend) >=1 ){
CASE false:
 echo "|&nbsp;<a class='btn-1' href='activities/index?{$http}'>Comentar&nbsp;({$getc->rowcount()})</a>";
BREAK;
}
} ?>
</abbr>
<abbr>
<?=$echo;?>
</abbr>
<div class="float-r">
<abbr><?=timestamp($rows->timestamp);?></abbr>
<img src="icon.png" alt="icon">
 </div>
</div>
</div>
</div>
<?PHP
}#ENDFOREACH.

#capturar el like
if(isset($_GET['like'])){

//--Validar si existe un me gusta en el post--//
#validar si ya esxiste el like.
$like = $db->connect()->query("SELECT id_user FROM likes WHERE id_token = '{$_GET['like']}' AND id_user  = '{$user->id}'")->rowcount();

 if(!empty($_GET["like"])){
 //listo para darle like 
 if($like > 0 ){
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
 // }# => ENDFRIENVALID.
 }
 }
?>
<!--//termina toda la extracción//-->
<div class="pg">
<?=$paginationCtrls;?>
</div>
<div class="anuncio">

</div>
</body>
</html>
