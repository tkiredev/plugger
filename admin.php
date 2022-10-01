<?PHP
#CONEXION
require('pdo/class.users.php');
#iniciar session
session_start();
#incluir clases 
$classU = new\users();
$db = new\db();
$user = $classU->get_user($_SESSION['username']);

//verificar si entro el admin.
switch ($_SESSION['rol'] > 0 AND $classU->access == true || $_SESSION['rol'] >0 AND $classU->acces == false){
CASE false:
 header("location:/");
BREAK;
}

#Actualizar session de noticias.
if(isset($_POST['update'])){
$update = $db->connect()->prepare("
UPDATE notification SET notif = :notif
");
$update->execute([
 ':notif' => $_POST['textpic']
]);

}
#Destruir session

if(isset($_GET["destroy"])){
 if($_GET["destroy"] == true)	{
  session_start();
  session_destroy();
  session_unset();
  header('location: /');
  }
}
?>
<!DOCTYPE html>
<html lang="html">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content=":Plugger/ panel admin.">
<link rel="icon" href="icon.png" type="image/x-icon" sizes="50x50">
<title>Cpanel de Admin</title>
<!--//
EL CODIGO FUE ECHO APARTIR DE 20 DE OBTUBRE DEL 2019 ALAS 4:04pm.
//-->
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="img-logo">
<img src="loggo/loggo.png" alt="icon/plugger"/> 
</div>
<div>
<div class="notif">
<p>panel admin solo para personas autorizadas.</p>
</div>
<strong>bienvenido ¡<?=$user->name_user;?>!</strong>
</div>
<div class="user_status">
<?PHP
#MOSTRAR EL NÚMERO DE TODOS LOS ADMINISTRADORES.

$sql = $db->connect()->query("SELECT id FROM usernames WHERE rol = 'admin'")->rowcount();

#NUMERO DE USUARIOS REGISTRADOS.
$rowuser = $db->connect()->query("SELECT id FROM usernames")->rowcount('queryString');
?>
<abbr><?=$sql;?>&nbsp;administrador&nbsp;/&nbsp;<?=$rowuser;?>&nbsp;usuarios</abb>
</div>
<div class="access">
<p>¿seguro que quieres desactivar el servidor?
- nadien podrá ingresar.
- a los usuarios les cerrará la sesión.</p>
<?PHP
//desactivar el servidor o activarlo.
 //verificar es estado del servidor.
 $status = $db->connect()->query("SELECT * FROM notification WHERE access > 0 ")->rowcount();

 if(isset($_GET['server_status'])){
 if($status > 0){
 //servidor activo...
 $server_output = 0;
 }else{
 //servidor desactivado.
 $server_output = 1;
 }

 $server_status = $db->connect()->prepare("
 UPDATE notification SET 
 access = :status")->execute([
 ":status" => $_GET['server_status']
 ]);

 if($server_status == true)
 {
 header("location:{$_SERVER['PHP_SELF']}");
 }
 var_dump($server_status);
 }ELSE{
 if ($status < 1){
 //servidor activo...
 $server_output = 1;
 }else{
 //servidor desactivado.
 $server_output = 0;
 }
 }

?>
<a href="<?=($server_output > 0 )?"home.php":"#return_0";?>">probar demo</a>

<a href="?server_status=<?=$server_output;?>"><?=($server_output > 0)? "Activar":"desactivar";?></a>
</div>
<div class="coment">
<form action="admin.php#top" method="post">
<div class="var_1">
<label for="">Mensaje de banner</label>
<textarea rows="4" cols="10" name="textpic"><?=$classU->notif();?></textarea>
 </div>
<input type="submit" name="update" value="actualizar" class="btn btn-2 size-m"/>
</div>
</form>
</div>


<a class="btn-1" href="?destroy=true">cerrar sesión</a>

</body>
</html>