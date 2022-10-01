<?PHP
#CONEXION
require('../pdo/class.users.php');
#iniciar session
session_start();
#incluir clases 
$classU = new\users();
$db = new\db();
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
 <li><a href="../friends">AMIGOS<?=$status_add;?></a></li>
</ul>
</header>
<body>

<?php if(isset($_FILES['images'])){ 
//Funciones optimizar imagenes //Ruta de la carpeta donde se guardarán las imaagenes 
$patch='../pictures';
 //Parámetros optimización, resolución máxima permitida
$max_ancho = 1280;
$max_alto = 900;
if($_FILES['images']['type']=='image/png' || $_FILES['images']['type']=='image/jpeg' || $_FILES['images']['type']=='image/gif'){
 $upload_img = $db->connect()->prepare("
 INSERT INTO public(
 id_token,
 id_user,
 texts,
 img,
 timestamp,
 new_friend
 )
 VALUES(
 :token,
 :id_user,
 :texts,
 :name_img,
 :timestamp,
 :new_friend
 )
 ")->execute([
 ":token" => token(5),
 ":name_img" => $_FILES['images']['name'],
 ":texts" => cleaner($_POST["textpic"]),
 ":id_user" => $user->id,
 ":timestamp" => time(),
 ":new_friend" => ""
 ]);
 switch($upload_img){CASE true:header("location:/"); BREAK;}
$medidasimagen= getimagesize($_FILES['images']['tmp_name']);
//Si las imagenes tienen una resolución y un peso aceptable se suben tal cual
 	if($medidasimagen[0] < 1280 && $_FILES['images']['size'] < 100000){
	$nombrearchivo=$_FILES['images']['name'];
 //insertar la imagen en la tabla de publicaciónes
move_uploaded_file($_FILES['images']['tmp_name'], $patch.'/'.$nombrearchivo); 	 }else{ $nombrearchivo=$_FILES['images']['name']; //Redimensionar 
$rtOriginal=$_FILES['images']['tmp_name']; if($_FILES['images']['type']=='image/jpeg'){ $original = imagecreatefromjpeg($rtOriginal); } else if($_FILES['images']['type']=='image/png'){ $original = imagecreatefrompng($rtOriginal); } else if($_FILES['images']['type']=='image/gif'){ $original = imagecreatefromgif($rtOriginal); } list($ancho,$alto)=getimagesize($rtOriginal); $x_ratio = $max_ancho / $ancho; $y_ratio = $max_alto / $alto; if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){ $ancho_final = $ancho; $alto_final = $alto; } elseif (($x_ratio * $alto) < $max_alto){ $alto_final = ceil($x_ratio * $alto); $ancho_final = $max_ancho; } else{ $ancho_final = ceil($y_ratio * $ancho); $alto_final = $max_alto; } $lienzo=imagecreatetruecolor($ancho_final,$alto_final); imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto); //
imagedestroy($original); $cal=8; if($_FILES['images']['type']=='image/jpeg'){ imagejpeg($lienzo,$patch."/".$nombrearchivo); } else if($_FILES['images']['type']=='image/png'){ imagepng($lienzo,$patch."/".$nombrearchivo); } else if($_FILES['images']['type']=='image/gif'){ imagegif($lienzo,$patch."/".$nombrearchivo); } } 	}
else echo 'fichero no soportado'; }

?>
<div class="coment">
<form action="" method="post" class="formulario" enctype="multipart/form-data"> <div class="formulario-grupo"> 	<label for="images">Cargar imagen</label> 	<input type="file" name="images" id="images" accept="image/*">	
</div>
<div class="var_1">
<label>introduce algún texto</label>
<textArea name="textpic"></textarea>
</div>
<input type="submit" class="btn btn-2 size-m" value="Actualizar" class="boton"> </div>
</form>
</div>
</body>