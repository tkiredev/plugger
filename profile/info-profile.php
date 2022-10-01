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
<!--//lista de información de usuario//-->
<?PHP
 $db = new\db();
if(isset($_POST['edit']) OR isset($_POST['update'])){
//agregar foto de perfil o/y datos de usuario.

if(isset($_POST['update'])){
 
 #validar si se eligió una foto(perfil)
 if(!empty($_FILES['images']['name'])){
 $patch='../pictures';
 //Parámetros optimización, resolución máxima permitida
$max_ancho = 1280;
$max_alto = 900;
if($_FILES['images']['type']=='image/png' || $_FILES['images']['type']=='image/jpeg' || $_FILES['images']['type']=='image/gif'){

$medidasimagen= getimagesize($_FILES['images']['tmp_name']);
//Si las imagenes tienen una resolución y un peso aceptable se suben tal cual
 	if($medidasimagen[0] < 1280 && $_FILES['images']['size'] < 100000){	$nombrearchivo=$_FILES['images']['name']; 	move_uploaded_file($_FILES['images']['tmp_name'], $patch.'/'.$nombrearchivo);
}
 //Si no, se generan nuevas imagenes optimizadas 
else { $nombrearchivo=$_FILES['images']['name']; //Redimensionar 
$rtOriginal=$_FILES['images']['tmp_name']; if($_FILES['images']['type']=='image/jpeg'){ $original = imagecreatefromjpeg($rtOriginal); } else if($_FILES['images']['type']=='image/png'){ $original = imagecreatefrompng($rtOriginal); } else if($_FILES['images']['type']=='image/gif'){ $original = imagecreatefromgif($rtOriginal); } list($ancho,$alto)=getimagesize($rtOriginal); $x_ratio = $max_ancho / $ancho; $y_ratio = $max_alto / $alto; if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){ $ancho_final = $ancho; $alto_final = $alto; } elseif (($x_ratio * $alto) < $max_alto){ $alto_final = ceil($x_ratio * $alto); $ancho_final = $max_ancho; } else{ $ancho_final = ceil($y_ratio * $ancho); $alto_final = $max_alto; } $lienzo=imagecreatetruecolor($ancho_final,$alto_final); imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto); //
imagedestroy($original); $cal=8; if($_FILES['images']['type']=='image/jpeg'){ imagejpeg($lienzo,$patch."/".$nombrearchivo); } else if($_FILES['images']['type']=='image/png'){ imagepng($lienzo,$patch."/".$nombrearchivo); } else if($_FILES['images']['type']=='image/gif'){ imagegif($lienzo,$patch."/".$nombrearchivo); } } 	} 	else echo 'fichero no soportado';
 $db->connect()->prepare("UPDATE usernames SET avatar = :value WHERE id = '$user->id'")->execute([":value" => $_FILES['images']['name']]);
 }
 //actualizar datos de usuario.
 $array = [
  "name_user" => cleaner($_POST['name_user']),
  "filset" => cleaner($_POST['filset'])
  ];
 if(empty($array['name_user']) AND strlen($array['name_user']) < 5){
  echo "<div class='notif'><p>
  se requiere tu nombre como dato mínimo.</p>
  </div>";
  }else{
 $valid = ($_POST['dia'] == "día" AND $_POST['mes'] == "mes" AND  $_POST["año"] == "año")? "personalizado":"{$_POST['dia']}/{$_POST['mes']}/{$_POST['año']}";

 $update = $db->connect()->prepare("UPDATE usernames SET
 name_user = '{$_POST['name_user']}',
 filset = '{$_POST['filset']}',
 my_info = '{$_POST['my_info']}',
 date_fech = '$valid',
 sexo = '{$_POST['sexo']}',
 mail = '{$_POST['mail']}'
 WHERE id = '$user->id'
")->execute();

  switch($update){
  CASE true:
   echo "<div class='notif'><p>
 Tus cambios han sido guardados</p>
  </div>"; 
  BREAK;
  DEFAULT:
  }
 }
}


?>
<div class="edit-profile">
<span>tu perfil no está configurado</span>
<strong>Crear su perfil</strong>
<form action="" method="post" enctype="multipart/form-data">
<strong>foto de perfil</strong>
<label>todavía no tienes foto de perfil</label>
<a href="#upload_photo" class="btn-1"><label for="select">Eligir una foto</label></a>
<input type="file" id="select" name="images" accept="image/*">
</div>
<ul class="profile_info">
<li style="font-style:italic;">* Datos obligaríos</li>
	<div class="var_1">
	<li><strong>Nombre(s)*</strong></li>
	<input type="text" value="<?=$user->name_user?>" name="name_user">
	</div>
	<div class="var_1">
	<li><strong>Apellido(s)*</strong></li>
	<input type="text" value="<?=$user->filset?>" name="filset">
	</div>
	<div class="var_1">
	<li><label>Acerca de mi</label></li>
	<input type="text" value="<?=$user->my_info;?>" name="my_info">
	</div>
  <div class="var_1">
  <li><strong>Fecha de nacimiento</strong></li>
  <?PHP
  $meses = [
  "enero",
  "febrero",
  "marzo",
  "abril",
  "mayo",
  "junio",
  "julio",
  "agosto",
  "septiembre",
  "obtubre",
  "noviembre",
  "diciembre"
  ];

  //mostrar los días.
  echo "<select name='dia'>";
  echo "<option>día</option>";
  for($i=1;$i< 31;$i++){
   echo "<option value='{$i}'>{$i}</option>";
  }
  echo "</select>";

  //mostrar los meses
  echo "<select name='mes'>";
  echo "<option>mes</option>";
  for($m=0;$m < count($meses) ;$m++){
   echo "<option value='{$meses[$m]}'>{$meses[$m]}</option>";
  }
  echo "</select>";
 //mostrar los años
  echo "<select name='año'>";
  echo "<option>año</option>";
  for($año= 1905;$año < 2021;$año++){
   echo "<option value='{$año}'>{$año}</option>";
  }
  echo "</select>";
  ?>
  </div>
  <div class="var_1">
	<li><strong>Sexo</strong></li>
	<select name="sexo">
    <option value="personalizado">personalizado</option>
    <option value="hombre">hombre</option>
    <option value="mujer">Mujer</option>
  </select>
	</div>
  <div class="var_1">
   <li><strong>Correo electrónico(importante)</strong></li>
   <input type="email" value="" name="mail">
  </div>
 <div class="var_1">
 <input type="submit" value="actualizar" class="btn btn-2 size-m" name="update">
 </div>
</form>
</ul>
<?php
}
else{
?>
<ul class="mini-menu">
  <li><a class="btn-1" href="activities.php">actividades</a></li>
  <li><a class="btn-1" href="../profile">conversiones</a></li>
  <li><a class="btn-1" href="#">fotos y videos</a></li>
  <li><strong>información de perfil</strong></li>
 </ul>
<ul class="profile_info">
<div class="var_1">
<li><strong>Nombre(s)</strong></li>
<li><?=$user->name_user;?></li>
</div>
<div class="var_1">
<li><strong>Apellido(s)</strong></li>
<li><?=$user->filset;?></li>
</div>
<div class="var_1">
<li><strong>Soy</strong></li>
<li><?=$user->sexo;?></li>
</div>
<div class="var_1">
<li><strong>Dinos acerca de ti(público)</strong></li>
<li><?=$user->my_info;?></li>
</div>
<div class="var_1">
<li><strong>Fecha de nacimiento</strong></li>
<li><?=$user->date_fech;?></li>
</div>
<div class="var_1">
<li><strong>Correo electrónico</strong></li>
<li><?=$user->mail;?></li>
</div>
<form action="" method="post">
<input type="submit" class="btn btn-2 size-m" name="edit" value="editar perfil"/>
</form>
</ul>
<?php }?>
</body>
</html>